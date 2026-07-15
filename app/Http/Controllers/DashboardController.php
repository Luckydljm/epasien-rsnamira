<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard
     */
    public function index()
    {
        $stats             = $this->getStatistik();
        $kunjunganMingguan = $this->getKunjunganMingguan();

        // Deteksi apakah user yang login adalah dokter
        $kodeUser   = session('auth_user.kode');
        $isAdmin    = session('auth_user.is_admin', false);
        $dokterInfo = $this->getDokterByKode($kodeUser);
        $isDokter   = !$isAdmin && $dokterInfo !== null;

        $pasienTerbaru = $this->getPasienTerbaru($isDokter ? $kodeUser : null);

        return view('dashboard.index', compact(
            'stats', 'pasienTerbaru', 'kunjunganMingguan',
            'isDokter', 'dokterInfo'
        ));
    }

    /**
     * Ambil statistik hari ini dari database SIMRS Khanza
     */
    private function getStatistik(): array
    {
        $today = now()->toDateString();
        $defaultStats = [
            'pasien_hari_ini'  => 0,
            'rawat_jalan'      => 0,
            'rawat_inap'       => 0,
            'total_dokter'     => 0,
            'pasien_igd'       => 0,
            'kamar_terpakai'   => 0,
        ];

        try {
            // Total kunjungan hari ini (ralan + ranap)
            $totalHariIni = DB::selectOne(
                "SELECT COUNT(*) as jml FROM reg_periksa WHERE tgl_registrasi = ?",
                [$today]
            );
            $defaultStats['pasien_hari_ini'] = $totalHariIni?->jml ?? 0;

            // Rawat Jalan (stts_pulang != 'Ranap' dan bukan ranap)
            $ralan = DB::selectOne(
                "SELECT COUNT(*) as jml FROM reg_periksa
                 WHERE tgl_registrasi = ?
                   AND status_lanjut = 'Ralan'",
                [$today]
            );
            $defaultStats['rawat_jalan'] = $ralan?->jml ?? 0;

            // Rawat Inap
            $ranap = DB::selectOne(
                "SELECT COUNT(*) as jml FROM reg_periksa
                 WHERE tgl_registrasi = ?
                   AND status_lanjut = 'Ranap'",
                [$today]
            );
            $defaultStats['rawat_inap'] = $ranap?->jml ?? 0;

            // Total Dokter Aktif
            $dokter = DB::selectOne(
                "SELECT COUNT(*) as jml FROM dokter WHERE status = '1'"
            );
            $defaultStats['total_dokter'] = $dokter?->jml ?? 0;

            // Pasien IGD hari ini
            $igd = DB::selectOne(
                "SELECT COUNT(*) as jml FROM reg_periksa rp
                 JOIN poliklinik p ON rp.kd_poli = p.kd_poli
                 WHERE rp.tgl_registrasi = ?
                   AND p.nm_poli LIKE '%IGD%'",
                [$today]
            );
            $defaultStats['pasien_igd'] = $igd?->jml ?? 0;

            // Kamar terpakai
            $kamar = DB::selectOne(
                "SELECT COUNT(*) as jml FROM kamar_inap
                 WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'"
            );
            $defaultStats['kamar_terpakai'] = $kamar?->jml ?? 0;

        } catch (\Exception $e) {
            // Database belum ada datanya — return default
        }

        return $defaultStats;
    }

    /**
     * Pasien terbaru hari ini.
     * Jika $kdDokter diisi → filter hanya pasien dokter tersebut.
     * Jika null            → tampilkan semua pasien.
     */
    private function getPasienTerbaru(?string $kdDokter = null): array
    {
        try {
            $today = now()->toDateString();

            if ($kdDokter !== null) {
                // Filter khusus pasien dokter yang login
                $result = DB::select(
                    "SELECT rp.no_rawat, rp.tgl_registrasi, rp.jam_reg,
                            p.nm_pasien, p.tgl_lahir, p.jk,
                            pol.nm_poli, rp.status_lanjut, rp.kd_dokter,
                            d.nm_dokter
                     FROM reg_periksa rp
                     LEFT JOIN pasien p       ON rp.no_rkm_medis = p.no_rkm_medis
                     LEFT JOIN poliklinik pol  ON rp.kd_poli      = pol.kd_poli
                     LEFT JOIN dokter d        ON rp.kd_dokter    = d.kd_dokter
                     WHERE rp.tgl_registrasi = ?
                       AND rp.kd_dokter      = ?
                     ORDER BY rp.jam_reg DESC
                     LIMIT 15",
                    [$today, $kdDokter]
                );
            } else {
                // Tampilkan semua pasien hari ini
                $result = DB::select(
                    "SELECT rp.no_rawat, rp.tgl_registrasi, rp.jam_reg,
                            p.nm_pasien, p.tgl_lahir, p.jk,
                            pol.nm_poli, rp.status_lanjut, rp.kd_dokter,
                            d.nm_dokter
                     FROM reg_periksa rp
                     LEFT JOIN pasien p       ON rp.no_rkm_medis = p.no_rkm_medis
                     LEFT JOIN poliklinik pol  ON rp.kd_poli      = pol.kd_poli
                     LEFT JOIN dokter d        ON rp.kd_dokter    = d.kd_dokter
                     WHERE rp.tgl_registrasi = ?
                     ORDER BY rp.jam_reg DESC
                     LIMIT 15",
                    [$today]
                );
            }

            return $result ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Cek apakah kode user adalah dokter aktif.
     * Mengembalikan data dokter (object) atau null.
     */
    private function getDokterByKode(?string $kode): ?object
    {
        if (!$kode) return null;
        try {
            return DB::selectOne(
                "SELECT kd_dokter, nm_dokter FROM dokter
                 WHERE kd_dokter = ? AND status = '1' LIMIT 1",
                [$kode]
            ) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Data kunjungan 7 hari terakhir untuk grafik
     */
    private function getKunjunganMingguan(): array
    {
        $data = [];
        try {
            for ($i = 6; $i >= 0; $i--) {
                $tgl = now()->subDays($i)->toDateString();
                $result = DB::selectOne(
                    "SELECT COUNT(*) as jml FROM reg_periksa WHERE tgl_registrasi = ?",
                    [$tgl]
                );
                $data[] = [
                    'tanggal' => now()->subDays($i)->locale('id')->isoFormat('ddd, D MMM'),
                    'jumlah'  => $result?->jml ?? 0,
                ];
            }
        } catch (\Exception $e) {
            // Isi dummy data jika db belum ada
            for ($i = 6; $i >= 0; $i--) {
                $data[] = [
                    'tanggal' => now()->subDays($i)->locale('id')->isoFormat('ddd, D MMM'),
                    'jumlah'  => 0,
                ];
            }
        }
        return $data;
    }
}
