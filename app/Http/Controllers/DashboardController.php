<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard.
     * - Dokter login → semua data difilter per dokter ybs
     * - Admin login  → full akses, semua data tampil
     */
    public function index()
    {
        $kodeUser  = session('auth_user.kode');
        $isAdmin   = session('auth_user.is_admin', false);
        $isDokter  = session('auth_user.is_dokter', false);
        $nmDokter  = session('auth_user.nm_dokter');
        $spesialis = session('auth_user.spesialis');

        // Jika dokter → filter berdasarkan kd_dokter, jika admin → null (semua)
        $kdDokterFilter = (!$isAdmin && $isDokter) ? $kodeUser : null;

        $stats             = $this->getStatistik($kdDokterFilter, $isAdmin);
        $kunjunganMingguan = $this->getKunjunganMingguan($kdDokterFilter);
        $pasienTerbaru     = $this->getPasienTerbaru($kdDokterFilter);

        // Info dokter yang login (diambil dari session)
        $dokterInfo = null;
        if ($isDokter) {
            $dokterInfo = (object) [
                'kd_dokter' => $kodeUser,
                'nm_dokter' => $nmDokter,
                'spesialis' => $spesialis,
            ];
        }

        return view('dashboard.index', compact(
            'stats', 'pasienTerbaru', 'kunjunganMingguan',
            'isDokter', 'isAdmin', 'dokterInfo', 'kdDokterFilter'
        ));
    }

    /**
     * Statistik dashboard.
     * Jika $kdDokter diisi → semua angka adalah milik dokter tsb.
     * Jika null (admin)    → angka global RS.
     *
     * Mode dokter:
     *   pasien_hari_ini  = pasien terdaftar dengan dokter ini hari ini
     *   rawat_jalan      = pasien ralan dokter ini hari ini
     *   rawat_inap       = pasien ranap dokter ini yang SEDANG dirawat
     *   pasien_igd       = pasien IGD dengan dokter ini hari ini
     *   total_dokter     = jumlah pasien selesai terlayani hari ini (bukan total dokter)
     *   kamar_terpakai   = jumlah kamar yang dihuni pasien dokter ini
     *
     * Mode admin:
     *   semua stat = data global RS seperti biasa
     */
    private function getStatistik(?string $kdDokter, bool $isAdmin): array
    {
        $today = now()->toDateString();

        $defaults = [
            'pasien_hari_ini' => 0,
            'rawat_jalan'     => 0,
            'rawat_inap'      => 0,
            'total_dokter'    => 0,   // admin: jml dokter aktif | dokter: jml pasien terlayani
            'pasien_igd'      => 0,
            'kamar_terpakai'  => 0,
        ];

        try {
            if ($kdDokter !== null) {
                // ============================================================
                // MODE DOKTER: semua data difilter per kd_dokter
                // ============================================================

                // Total pasien terdaftar hari ini (milik dokter ini)
                $r = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM reg_periksa
                     WHERE tgl_registrasi = ? AND kd_dokter = ?",
                    [$today, $kdDokter]
                );
                $defaults['pasien_hari_ini'] = (int) ($r?->jml ?? 0);

                // Rawat Jalan dokter ini hari ini
                $r = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM reg_periksa rp
                     LEFT JOIN poliklinik pol ON pol.kd_poli = rp.kd_poli
                     WHERE rp.tgl_registrasi = ? AND rp.kd_dokter = ?
                       AND (rp.status_lanjut = 'Ralan'
                            OR pol.nm_poli LIKE '%IGD%'
                            OR pol.nm_poli LIKE '%UGD%')",
                    [$today, $kdDokter]
                );
                $defaults['rawat_jalan'] = (int) ($r?->jml ?? 0);

                // Rawat Inap (rekap keseluruhan registrasi hari ini milik dokter ini)
                $r = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM reg_periksa
                     WHERE tgl_registrasi = ? AND status_lanjut = 'Ranap' AND kd_dokter = ?",
                    [$today, $kdDokter]
                );
                $defaults['rawat_inap'] = (int) ($r?->jml ?? 0);

                // Pasien IGD dokter ini hari ini
                $r = DB::selectOne(
                    "SELECT COUNT(*) AS jml
                     FROM reg_periksa rp
                     JOIN poliklinik pol ON pol.kd_poli = rp.kd_poli
                     WHERE rp.tgl_registrasi = ? AND rp.kd_dokter = ?
                       AND (pol.nm_poli LIKE '%IGD%' OR pol.nm_poli LIKE '%UGD%')",
                    [$today, $kdDokter]
                );
                $defaults['pasien_igd'] = (int) ($r?->jml ?? 0);

                // Pasien terlayani hari ini (ada data di pemeriksaan_ralan)
                $r = DB::selectOne(
                    "SELECT COUNT(DISTINCT rp.no_rawat) AS jml
                     FROM reg_periksa rp
                     INNER JOIN pemeriksaan_ralan pr ON pr.no_rawat = rp.no_rawat
                     WHERE rp.tgl_registrasi = ? AND rp.kd_dokter = ?",
                    [$today, $kdDokter]
                );
                $defaults['total_dokter'] = (int) ($r?->jml ?? 0);

                // Kamar yang dihuni pasien dokter ini
                $r = DB::selectOne(
                    "SELECT COUNT(DISTINCT ki.no_rawat) AS jml
                     FROM (
                         SELECT no_rawat FROM kamar_inap
                         WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'
                     ) ki
                     INNER JOIN reg_periksa rp ON rp.no_rawat = ki.no_rawat
                     WHERE rp.kd_dokter = ?",
                    [$kdDokter]
                );
                $defaults['kamar_terpakai'] = (int) ($r?->jml ?? 0);

            } else {
                // ============================================================
                // MODE ADMIN: data global RS
                // ============================================================

                // Total kunjungan hari ini
                $r = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM reg_periksa WHERE tgl_registrasi = ?",
                    [$today]
                );
                $defaults['pasien_hari_ini'] = (int) ($r?->jml ?? 0);

                // Rawat Jalan
                $r = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM reg_periksa
                     WHERE tgl_registrasi = ? AND status_lanjut = 'Ralan'",
                    [$today]
                );
                $defaults['rawat_jalan'] = (int) ($r?->jml ?? 0);

                // Rawat Inap sedang dirawat (dari kamar_inap)
                $r = DB::selectOne(
                    "SELECT COUNT(DISTINCT no_rawat) AS jml FROM kamar_inap
                     WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'"
                );
                $defaults['rawat_inap'] = (int) ($r?->jml ?? 0);

                // Total Dokter Aktif
                $r = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM dokter WHERE status = '1'"
                );
                $defaults['total_dokter'] = (int) ($r?->jml ?? 0);

                // Pasien IGD hari ini
                $r = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM reg_periksa rp
                     JOIN poliklinik p ON rp.kd_poli = p.kd_poli
                     WHERE rp.tgl_registrasi = ?
                       AND (p.nm_poli LIKE '%IGD%' OR p.nm_poli LIKE '%UGD%')",
                    [$today]
                );
                $defaults['pasien_igd'] = (int) ($r?->jml ?? 0);

                // Kamar terpakai (semua)
                $r = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM kamar_inap
                     WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'"
                );
                $defaults['kamar_terpakai'] = (int) ($r?->jml ?? 0);
            }
        } catch (\Exception $e) {
            // Return default jika DB error
        }

        return $defaults;
    }

    /**
     * Pasien terbaru hari ini.
     * Jika $kdDokter → hanya pasien dokter tsb.
     * Jika null (admin) → semua pasien.
     */
    private function getPasienTerbaru(?string $kdDokter = null): array
    {
        try {
            $today  = now()->toDateString();
            $params = [$today];
            $filter = '';

            if ($kdDokter !== null) {
                $filter   = 'AND rp.kd_dokter = ?';
                $params[] = $kdDokter;
            }

            return DB::select(
                "SELECT rp.no_rawat, rp.tgl_registrasi, rp.jam_reg,
                        p.nm_pasien, p.tgl_lahir, p.jk,
                        pol.nm_poli, rp.status_lanjut, rp.kd_dokter,
                        d.nm_dokter,
                        CASE
                            WHEN pr.no_rawat IS NOT NULL THEN 'terlayani'
                            ELSE 'belum'
                        END AS status_layanan
                 FROM reg_periksa rp
                 LEFT JOIN pasien p      ON rp.no_rkm_medis = p.no_rkm_medis
                 LEFT JOIN poliklinik pol ON rp.kd_poli     = pol.kd_poli
                 LEFT JOIN dokter d       ON rp.kd_dokter   = d.kd_dokter
                 LEFT JOIN (
                     SELECT DISTINCT no_rawat FROM pemeriksaan_ralan
                 ) pr ON pr.no_rawat = rp.no_rawat
                 WHERE rp.tgl_registrasi = ?
                 {$filter}
                 ORDER BY rp.jam_reg DESC
                 LIMIT 20",
                $params
            ) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Data kunjungan 7 hari terakhir untuk grafik.
     * Jika $kdDokter → hanya pasien dokter tsb.
     * Jika null (admin) → semua pasien RS.
     */
    private function getKunjunganMingguan(?string $kdDokter = null): array
    {
        $data = [];
        try {
            $filter = $kdDokter !== null ? 'AND kd_dokter = ?' : '';

            for ($i = 6; $i >= 0; $i--) {
                $tgl    = now()->subDays($i)->toDateString();
                $params = $kdDokter !== null ? [$tgl, $kdDokter] : [$tgl];

                $result = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM reg_periksa
                     WHERE tgl_registrasi = ? {$filter}",
                    $params
                );
                $data[] = [
                    'tanggal' => now()->subDays($i)->locale('id')->isoFormat('ddd, D MMM'),
                    'jumlah'  => (int) ($result?->jml ?? 0),
                ];
            }
        } catch (\Exception $e) {
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
