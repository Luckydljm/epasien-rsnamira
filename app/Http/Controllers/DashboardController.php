<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard — menyesuaikan dengan Portal Aktif (Rawat Jalan vs Rawat Inap).
     */
    public function index()
    {
        $kodeUser     = session('auth_user.kode');
        $isAdmin      = session('auth_user.is_admin', false);
        $isDokter     = session('auth_user.is_dokter', false);
        $nmDokter     = session('auth_user.nm_dokter');
        $spesialis    = session('auth_user.spesialis');
        $activePortal = session('active_portal', 'rajal');

        // Dokter → filter per kd_dokter
        $kdDokterFilter = (!$isAdmin && $isDokter) ? $kodeUser : null;

        $stats             = $this->getStatistik($kdDokterFilter, $isAdmin, $activePortal);
        $kunjunganMingguan = $this->getKunjunganMingguan($kdDokterFilter, $activePortal);
        $pasienTerbaru     = $this->getPasienTerbaru($kdDokterFilter, $activePortal);

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
            'isDokter', 'isAdmin', 'dokterInfo', 'kdDokterFilter', 'activePortal'
        ));
    }

    /**
     * Statistik dashboard disesuaikan dengan activePortal (rajal vs ranap).
     */
    private function getStatistik(?string $kdDokter, bool $isAdmin, string $activePortal = 'rajal'): array
    {
        $today = now()->toDateString();

        $defaults = [
            'pasien_hari_ini' => 0,
            'rawat_jalan'     => 0,
            'rawat_inap'      => 0,
            'total_dokter'    => 0,
            'pasien_igd'      => 0,
            'kamar_terpakai'  => 0,
        ];

        try {
            if ($kdDokter !== null) {
                // Mode Dokter
                if ($activePortal === 'ranap') {
                    // Rawat Inap aktif milik dokter ini
                    $r = DB::selectOne(
                        "SELECT COUNT(DISTINCT ki.no_rawat) AS jml
                         FROM (
                             SELECT no_rawat FROM kamar_inap
                             WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'
                         ) ki
                         INNER JOIN reg_periksa rp ON rp.no_rawat = ki.no_rawat
                         LEFT JOIN (
                             SELECT no_rawat, MIN(kd_dokter) AS kd_dokter
                             FROM dpjp_ranap
                             GROUP BY no_rawat
                         ) dpjp ON dpjp.no_rawat = ki.no_rawat
                         WHERE rp.kd_dokter = ? OR dpjp.kd_dokter = ?",
                        [$kdDokter, $kdDokter]
                    );
                    $defaults['rawat_inap'] = (int) ($r?->jml ?? 0);

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

                    // Total pasien ranap masuk hari ini (milik dokter ini)
                    $r = DB::selectOne(
                        "SELECT COUNT(*) AS jml FROM reg_periksa
                         WHERE tgl_registrasi = ? AND status_lanjut = 'Ranap' AND kd_dokter = ?",
                        [$today, $kdDokter]
                    );
                    $defaults['pasien_hari_ini'] = (int) ($r?->jml ?? 0);
                } else {
                    // Rawat Jalan milik dokter ini hari ini
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
                    $defaults['pasien_hari_ini'] = $defaults['rawat_jalan'];

                    // Pasien IGD
                    $r = DB::selectOne(
                        "SELECT COUNT(*) AS jml
                         FROM reg_periksa rp
                         JOIN poliklinik pol ON pol.kd_poli = rp.kd_poli
                         WHERE rp.tgl_registrasi = ? AND rp.kd_dokter = ?
                           AND (pol.nm_poli LIKE '%IGD%' OR pol.nm_poli LIKE '%UGD%')",
                        [$today, $kdDokter]
                    );
                    $defaults['pasien_igd'] = (int) ($r?->jml ?? 0);

                    // Pasien terlayani
                    $r = DB::selectOne(
                        "SELECT COUNT(DISTINCT rp.no_rawat) AS jml
                         FROM reg_periksa rp
                         INNER JOIN pemeriksaan_ralan pr ON pr.no_rawat = rp.no_rawat
                         WHERE rp.tgl_registrasi = ? AND rp.kd_dokter = ?",
                        [$today, $kdDokter]
                    );
                    $defaults['total_dokter'] = (int) ($r?->jml ?? 0);
                }
            } else {
                // Mode Admin
                if ($activePortal === 'ranap') {
                    // Rawat Inap sedang dirawat
                    $r = DB::selectOne(
                        "SELECT COUNT(DISTINCT no_rawat) AS jml FROM kamar_inap
                         WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'"
                    );
                    $defaults['rawat_inap'] = (int) ($r?->jml ?? 0);

                    // Kamar terpakai
                    $r = DB::selectOne(
                        "SELECT COUNT(*) AS jml FROM kamar_inap
                         WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'"
                    );
                    $defaults['kamar_terpakai'] = (int) ($r?->jml ?? 0);

                    // Pasien ranap masuk hari ini
                    $r = DB::selectOne(
                        "SELECT COUNT(*) AS jml FROM reg_periksa
                         WHERE tgl_registrasi = ? AND status_lanjut = 'Ranap'",
                        [$today]
                    );
                    $defaults['pasien_hari_ini'] = (int) ($r?->jml ?? 0);
                } else {
                    // Rawat Jalan global hari ini
                    $r = DB::selectOne(
                        "SELECT COUNT(*) AS jml FROM reg_periksa
                         WHERE tgl_registrasi = ? AND status_lanjut = 'Ralan'",
                        [$today]
                    );
                    $defaults['rawat_jalan'] = (int) ($r?->jml ?? 0);
                    $defaults['pasien_hari_ini'] = $defaults['rawat_jalan'];

                    // Pasien IGD global
                    $r = DB::selectOne(
                        "SELECT COUNT(*) AS jml FROM reg_periksa rp
                         JOIN poliklinik p ON rp.kd_poli = p.kd_poli
                         WHERE rp.tgl_registrasi = ?
                           AND (p.nm_poli LIKE '%IGD%' OR p.nm_poli LIKE '%UGD%')",
                        [$today]
                    );
                    $defaults['pasien_igd'] = (int) ($r?->jml ?? 0);

                    // Dokter aktif
                    $r = DB::selectOne("SELECT COUNT(*) AS jml FROM dokter WHERE status = '1'");
                    $defaults['total_dokter'] = (int) ($r?->jml ?? 0);
                }
            }
        } catch (\Exception $e) {
            // Keep default
        }

        return $defaults;
    }

    /**
     * Pasien terbaru disesuaikan portal aktif.
     */
    private function getPasienTerbaru(?string $kdDokter = null, string $activePortal = 'rajal'): array
    {
        try {
            $today  = now()->toDateString();
            $params = [];
            $filter = '';

            if ($activePortal === 'ranap') {
                $filter .= "AND rp.status_lanjut = 'Ranap'";
            } else {
                $filter .= "AND (rp.status_lanjut = 'Ralan' OR pol.nm_poli LIKE '%IGD%' OR pol.nm_poli LIKE '%UGD%') AND rp.tgl_registrasi = ?";
                $params[] = $today;
            }

            if ($kdDokter !== null) {
                $filter   .= ' AND rp.kd_dokter = ?';
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
                 LEFT JOIN pasien p       ON rp.no_rkm_medis = p.no_rkm_medis
                 LEFT JOIN poliklinik pol ON rp.kd_poli     = pol.kd_poli
                 LEFT JOIN dokter d       ON rp.kd_dokter   = d.kd_dokter
                 LEFT JOIN (
                     SELECT DISTINCT no_rawat FROM pemeriksaan_ralan
                 ) pr ON pr.no_rawat = rp.no_rawat
                 WHERE 1=1 {$filter}
                 ORDER BY rp.tgl_registrasi DESC, rp.jam_reg DESC
                 LIMIT 20",
                $params
            ) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Data kunjungan 7 hari terakhir untuk grafik disesuaikan portal.
     */
    private function getKunjunganMingguan(?string $kdDokter = null, string $activePortal = 'rajal'): array
    {
        $data = [];
        try {
            $portalFilter = ($activePortal === 'ranap')
                ? " AND status_lanjut = 'Ranap'"
                : " AND (status_lanjut = 'Ralan' OR kd_poli IN (SELECT kd_poli FROM poliklinik WHERE nm_poli LIKE '%IGD%'))";

            $filter = $kdDokter !== null ? "{$portalFilter} AND kd_dokter = ?" : $portalFilter;

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
