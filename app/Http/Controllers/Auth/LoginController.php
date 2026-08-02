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
            return redirect()->route('portal');
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
            return redirect()->route('portal')
                ->with('success', 'Selamat datang, Admin Utama!');
        }

        // === CEK 2: User Biasa (tabel user) ===
        $user = UserKhanza::verifyLogin($idUser, $password);
        if ($user) {
            $kodeUser = $user->kode_user ?? $idUser;

            // === CEK AKSES: Hanya dokter yang boleh login dari tabel user ===
            $dokterInfo = $this->getDokterByKode($kodeUser);
            if ($dokterInfo === null) {
                // Bukan dokter — tolak akses
                RateLimiter::hit($throttleKey, 60);
                Log::warning('Login ditolak (bukan dokter)', [
                    'id_user' => $idUser,
                    'kode'    => $kodeUser,
                    'ip'      => $request->ip(),
                ]);
                return back()
                    ->withInput($request->only('id_user'))
                    ->with('error', 'Akses ditolak. Hanya dokter dan admin yang dapat mengakses sistem ini.');
            }

            RateLimiter::clear($throttleKey);
            $namaPegawai = $dokterInfo->nm_dokter ?? UserKhanza::getNamaPegawai($kodeUser);
            $this->createSession($request, $kodeUser, $namaPegawai, false, (array) $user, $dokterInfo);
            $this->logTracker($request, $idUser, $namaPegawai, 'Login');
            return redirect()->route('portal')
                ->with('success', "Selamat datang, dr. {$namaPegawai}!");
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
    private function createSession(Request $request, string $kode, string $nama, bool $isAdmin, array $aksesData = [], ?object $dokterInfo = null): void
    {
        $request->session()->regenerate();

        $akses = $isAdmin ? $this->getAdminAkses() : $aksesData;

        session([
            'auth_user' => [
                'kode'       => $kode,
                'nama'       => $nama,
                'is_admin'   => $isAdmin,
                'is_dokter'  => $dokterInfo !== null,
                'nm_dokter'  => $dokterInfo?->nm_dokter,
                'spesialis'  => $dokterInfo?->spesialis ?? null,
                'ip'         => $request->ip(),
                'login_at'   => now()->toDateTimeString(),
                'akses'      => $akses,
            ],
        ]);
    }

    /**
     * Cek apakah kode user adalah dokter aktif di tabel dokter.
     * Mengembalikan data dokter (termasuk nama spesialisasi) atau null.
     *
     * Kolom yang benar di SIMRS Khanza:
     *   - dokter.kd_sps  → FK ke tabel spesialis
     *   - spesialis.nm_sps → nama spesialisasi
     */
    private function getDokterByKode(string $kode): ?object
    {
        try {
            // TRIM untuk menghindari whitespace/null byte sisa AES_DECRYPT
            $kodeBersih = trim($kode);
            if ($kodeBersih === '') return null;

            return DB::selectOne(
                "SELECT d.kd_dokter, d.nm_dokter, d.kd_sps,
                        s.nm_sps AS spesialis
                 FROM dokter d
                 LEFT JOIN spesialis s ON s.kd_sps = d.kd_sps
                 WHERE d.kd_dokter = ? AND d.status = '1'
                 LIMIT 1",
                [$kodeBersih]
            ) ?: null;
        } catch (\Exception $e) {
            Log::warning('getDokterByKode error: ' . $e->getMessage(), ['kode' => $kode]);
            return null;
        }
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
