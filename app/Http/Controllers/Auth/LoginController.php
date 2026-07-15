<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\UserKhanza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        if (session()->has('auth_user')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses login — kompatibel dengan sistem SIMRS Khanza
     * Cek tabel admin dulu (Admin Utama), lalu tabel user (pegawai)
     */
    public function login(Request $request)
    {
        $request->validate([
            'id_user'  => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:100'],
        ], [
            'id_user.required'  => 'ID User wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // === Rate Limiting: max 5 percobaan per IP per menit ===
        $throttleKey = 'login.' . $request->ip();
        $maxAttempts = (int) env('LOGIN_MAX_ATTEMPTS', 5);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('id_user'))
                ->with('error', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
        }

        $idUser  = trim($request->input('id_user'));
        $password = trim($request->input('password'));

        // === CEK 1: Admin Utama (tabel admin) ===
        if (Admin::verifyLogin($idUser, $password)) {
            RateLimiter::clear($throttleKey);
            $this->createSession($request, 'Admin Utama', 'Admin Utama', true);
            $this->logTracker($request, $idUser, 'Admin Utama', 'Login');
            return redirect()->route('dashboard')
                ->with('success', 'Selamat datang, Admin Utama!');
        }

        // === CEK 2: User Biasa (tabel user) ===
        $user = UserKhanza::verifyLogin($idUser, $password);
        if ($user) {
            RateLimiter::clear($throttleKey);
            $namaPegawai = UserKhanza::getNamaPegawai($user->kode_user ?? $idUser);
            $this->createSession($request, $user->kode_user ?? $idUser, $namaPegawai, false, (array) $user);
            $this->logTracker($request, $idUser, $namaPegawai, 'Login');
            return redirect()->route('dashboard')
                ->with('success', "Selamat datang, {$namaPegawai}!");
        }

        // === LOGIN GAGAL ===
        RateLimiter::hit($throttleKey, 60);
        $remaining = $maxAttempts - RateLimiter::attempts($throttleKey);
        Log::warning('Login gagal', [
            'id_user' => $idUser,
            'ip'      => $request->ip(),
            'ua'      => $request->userAgent(),
        ]);

        return back()
            ->withInput($request->only('id_user'))
            ->with('error', "ID User atau password salah. Sisa percobaan: {$remaining}.");
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $kode = session('auth_user.kode', '-');
        $nama  = session('auth_user.nama', '-');

        $this->logTracker($request, $kode, $nama, 'Logout');

        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Buat session setelah login berhasil
     */
    private function createSession(Request $request, string $kode, string $nama, bool $isAdmin, array $aksesData = []): void
    {
        $request->session()->regenerate();

        $akses = $isAdmin ? $this->getAdminAkses() : $aksesData;

        session([
            'auth_user' => [
                'kode'     => $kode,
                'nama'     => $nama,
                'is_admin' => $isAdmin,
                'ip'       => $request->ip(),
                'login_at' => now()->toDateTimeString(),
                'akses'    => $akses,
            ],
        ]);
    }

    /**
     * Catat aktivitas login/logout ke tabel tracker (jika ada)
     */
    private function logTracker(Request $request, string $idUser, string $nama, string $aksi): void
    {
        try {
            DB::table('tracker')->insert([
                'id_user'    => $idUser,
                'nama'       => $nama,
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit($request->userAgent(), 490),
                'aksi'       => $aksi,
                'tgl_aksi'   => now()->toDateString(),
                'jam_aksi'   => now()->toTimeString(),
            ]);
        } catch (\Exception $e) {
            // Tabel tracker mungkin belum ada — skip saja
            Log::info('Tracker skip: ' . $e->getMessage());
        }
    }

    /**
     * Hak akses penuh untuk Admin Utama
     */
    private function getAdminAkses(): array
    {
        return [
            'registrasi'           => true,
            'kasir_ralan'          => true,
            'billing_ralan'        => true,
            'billing_ranap'        => true,
            'kamar_inap'           => true,
            'tindakan_ranap'       => true,
            'igd'                  => true,
            'permintaan_lab'       => true,
            'periksa_lab'          => true,
            'permintaan_radiologi' => true,
            'periksa_radiologi'    => true,
            'penjualan_obat'       => true,
            'resep_obat'           => true,
            'resep_dokter'         => true,
            'keuangan'             => true,
            'pengeluaran'          => true,
            'laporan'              => true,
            'admin'                => true,
            'user'                 => true,
        ];
    }
}
