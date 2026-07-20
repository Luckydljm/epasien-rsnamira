<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RajalController extends Controller
{
    /**
     * Halaman Rawat Jalan (termasuk IGD)
     */
    public function index(Request $request)
    {
        $kodeUser   = session('auth_user.kode');
        $isAdmin    = session('auth_user.is_admin', false);
        $isDokter   = session('auth_user.is_dokter', false);
        $nmDokter   = session('auth_user.nm_dokter');

        // Filter by dokter jika yang login adalah dokter
        $kdDokterFilter = (!$isAdmin && $isDokter) ? $kodeUser : null;

        $pasienList  = $this->getPasienRajal($kdDokterFilter);
        $statsRajal  = $this->getStatsRajal($kdDokterFilter);

        return view('rawat-jalan.index', compact(
            'pasienList', 'statsRajal',
            'isDokter', 'isAdmin', 'nmDokter', 'kdDokterFilter'
        ));
    }

    /**
     * Ambil daftar pasien Rawat Jalan + IGD hari ini
     * Jika $kdDokter diisi → filter pasien dokter tersebut
     */
    private function getPasienRajal(?string $kdDokter = null): array
    {
        try {
            $today  = now()->toDateString();
            $params = [$today];
            $dokterFilter = '';

            if ($kdDokter !== null) {
                $dokterFilter = 'AND rp.kd_dokter = ?';
                $params[]     = $kdDokter;
            }

            $result = DB::select(
                "SELECT
                    rp.no_rawat,
                    rp.no_rkm_medis,
                    rp.tgl_registrasi,
                    rp.jam_reg,
                    rp.kd_dokter,
                    rp.status_lanjut,
                    p.nm_pasien,
                    p.tgl_lahir,
                    p.jk,
                    p.alamat,
                    pol.nm_poli,
                    d.nm_dokter,
                    -- Status terlayani: cek ada tidaknya data di pemeriksaan_ralan
                    CASE
                        WHEN pr.no_rawat IS NOT NULL THEN 'terlayani'
                        ELSE 'belum'
                    END AS status_layanan,
                    -- Jenis: IGD atau Ralan biasa
                    CASE
                        WHEN pol.nm_poli LIKE '%IGD%' OR pol.nm_poli LIKE '%UGD%' THEN 'IGD'
                        ELSE 'Ralan'
                    END AS jenis_kunjungan
                 FROM reg_periksa rp
                 LEFT JOIN pasien p         ON rp.no_rkm_medis = p.no_rkm_medis
                 LEFT JOIN poliklinik pol   ON rp.kd_poli       = pol.kd_poli
                 LEFT JOIN dokter d         ON rp.kd_dokter     = d.kd_dokter
                 LEFT JOIN (
                     SELECT DISTINCT no_rawat FROM pemeriksaan_ralan
                 ) pr ON pr.no_rawat = rp.no_rawat
                 WHERE rp.tgl_registrasi = ?
                   AND (rp.status_lanjut = 'Ralan'
                        OR pol.nm_poli LIKE '%IGD%'
                        OR pol.nm_poli LIKE '%UGD%')
                 {$dokterFilter}
                 ORDER BY rp.jam_reg ASC",
                $params
            );

            return $result ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Statistik ringkasan rawat jalan hari ini
     */
    private function getStatsRajal(?string $kdDokter = null): array
    {
        $defaults = [
            'total'      => 0,
            'terlayani'  => 0,
            'belum'      => 0,
            'igd'        => 0,
        ];

        try {
            $today  = now()->toDateString();
            $params = [$today];
            $dokterFilter = '';

            if ($kdDokter !== null) {
                $dokterFilter = 'AND rp.kd_dokter = ?';
                $params[]     = $kdDokter;
            }

            $rows = DB::select(
                "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN pr.no_rawat IS NOT NULL THEN 1 ELSE 0 END) as terlayani,
                    SUM(CASE WHEN pr.no_rawat IS NULL THEN 1 ELSE 0 END) as belum,
                    SUM(CASE WHEN pol.nm_poli LIKE '%IGD%' OR pol.nm_poli LIKE '%UGD%' THEN 1 ELSE 0 END) as igd
                 FROM reg_periksa rp
                 LEFT JOIN poliklinik pol ON rp.kd_poli = pol.kd_poli
                 LEFT JOIN (SELECT DISTINCT no_rawat FROM pemeriksaan_ralan) pr ON pr.no_rawat = rp.no_rawat
                 WHERE rp.tgl_registrasi = ?
                   AND (rp.status_lanjut = 'Ralan'
                        OR pol.nm_poli LIKE '%IGD%'
                        OR pol.nm_poli LIKE '%UGD%')
                 {$dokterFilter}",
                $params
            );

            if (!empty($rows)) {
                $defaults['total']     = (int) ($rows[0]->total ?? 0);
                $defaults['terlayani'] = (int) ($rows[0]->terlayani ?? 0);
                $defaults['belum']     = (int) ($rows[0]->belum ?? 0);
                $defaults['igd']       = (int) ($rows[0]->igd ?? 0);
            }
        } catch (\Exception $e) {
            // Return defaults jika error
        }

        return $defaults;
    }
}
