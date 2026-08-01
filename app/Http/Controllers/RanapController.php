<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RanapController extends Controller
{
    /**
     * Halaman Rawat Inap
     */
    public function index(Request $request)
    {
        $kodeUser  = session('auth_user.kode');
        $isAdmin   = session('auth_user.is_admin', false);
        $isDokter  = session('auth_user.is_dokter', false);
        $nmDokter  = session('auth_user.nm_dokter');

        // Filter by dokter jika yang login adalah dokter
        $kdDokterFilter = (!$isAdmin && $isDokter) ? $kodeUser : null;

        $pasienRanap        = $this->getPasienRanap($kdDokterFilter);
        $pasienRanapSelesai = $this->getPasienRanapSelesai($kdDokterFilter);
        $statsRanap         = $this->getStatsRanap($kdDokterFilter);

        return view('rawat-inap.index', compact(
            'pasienRanap', 'pasienRanapSelesai', 'statsRanap',
            'isDokter', 'isAdmin', 'nmDokter', 'kdDokterFilter'
        ));
    }

    /**
     * Ambil daftar pasien rawat inap yang SEDANG DIRAWAT saat ini.
     *
     * Sumber kebenaran: tabel kamar_inap
     *   → tgl_keluar IS NULL atau '0000-00-00' = masih dirawat
     *   → tgl_keluar terisi tanggal valid = sudah pulang (TIDAK ditampilkan)
     *
     * Jika $kdDokter diisi → filter hanya pasien dokter tersebut.
     */
    private function getPasienRanap(?string $kdDokter = null): array
    {
        try {
            $params = [];
            $dokterFilter = '';

            if ($kdDokter !== null) {
                $dokterFilter = 'AND (rp.kd_dokter = ? OR dpjp.kd_dokter = ?)';
                $params[]     = $kdDokter;
                $params[]     = $kdDokter;
            }

            // Subquery: 1 record kamar aktif per no_rawat (belum keluar)
            // Didrive dari kamar_inap agar hanya pasien yang benar-benar masih dirawat
            $result = DB::select(
                "SELECT
                    ki.no_rawat,
                    ki.kd_kamar,
                    ki.tgl_masuk,
                    ki.jam_masuk,
                    ki.tgl_keluar,
                    ki.jam_keluar,
                    ki.lama,
                    rp.no_rkm_medis,
                    rp.tgl_registrasi,
                    rp.jam_reg,
                    rp.kd_dokter,
                    p.nm_pasien,
                    p.tgl_lahir,
                    p.jk,
                    p.alamat,
                    pol.nm_poli,
                    COALESCE(d_dpjp.nm_dokter, d.nm_dokter) AS nm_dokter,
                    COALESCE(s_dpjp.nm_sps, s.nm_sps) AS spesialis,
                    k.kd_bangsal,
                    k.kelas,
                    bang.nm_bangsal,
                    -- Diagnosa utama (1 per no_rawat dari diagnosa_pasien)
                    dp_utama.kd_penyakit,
                    pyk.nm_penyakit
                 FROM (
                     -- Ambil 1 record kamar aktif per no_rawat (yang belum keluar)
                     SELECT no_rawat, kd_kamar, tgl_masuk, jam_masuk,
                            tgl_keluar, jam_keluar, lama
                     FROM kamar_inap
                     WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'
                 ) ki
                 INNER JOIN reg_periksa rp  ON rp.no_rawat     = ki.no_rawat
                 LEFT JOIN  pasien p        ON p.no_rkm_medis  = rp.no_rkm_medis
                 LEFT JOIN  poliklinik pol  ON pol.kd_poli     = rp.kd_poli
                 LEFT JOIN  dokter d        ON d.kd_dokter     = rp.kd_dokter
                 LEFT JOIN  spesialis s     ON s.kd_sps        = d.kd_sps
                 LEFT JOIN (
                     SELECT no_rawat, MIN(kd_dokter) AS kd_dokter
                     FROM dpjp_ranap
                     GROUP BY no_rawat
                 ) dpjp                     ON dpjp.no_rawat    = ki.no_rawat
                 LEFT JOIN  dokter d_dpjp   ON d_dpjp.kd_dokter = dpjp.kd_dokter
                 LEFT JOIN  spesialis s_dpjp ON s_dpjp.kd_sps   = d_dpjp.kd_sps
                 LEFT JOIN  kamar k         ON k.kd_kamar      = ki.kd_kamar
                 LEFT JOIN  bangsal bang    ON bang.kd_bangsal = k.kd_bangsal
                 LEFT JOIN (
                     SELECT no_rawat, MIN(kd_penyakit) AS kd_penyakit
                     FROM diagnosa_pasien
                     WHERE prioritas = 1 OR prioritas IS NULL
                     GROUP BY no_rawat
                 ) dp_utama                 ON dp_utama.no_rawat = ki.no_rawat
                 LEFT JOIN penyakit pyk     ON pyk.kd_penyakit = dp_utama.kd_penyakit
                 WHERE 1=1
                 {$dokterFilter}
                 ORDER BY ki.tgl_masuk DESC, ki.jam_masuk DESC",
                $params
            );

            return $result ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Ambil daftar pasien rawat inap yang SUDAH PULANG / SELESAI DIRAWAT.
     */
    private function getPasienRanapSelesai(?string $kdDokter = null): array
    {
        try {
            $params = [];
            $dokterFilter = '';

            if ($kdDokter !== null) {
                $dokterFilter = 'AND (rp.kd_dokter = ? OR dpjp.kd_dokter = ?)';
                $params[]     = $kdDokter;
                $params[]     = $kdDokter;
            }

            $result = DB::select(
                "SELECT
                    ki.no_rawat,
                    ki.kd_kamar,
                    ki.tgl_masuk,
                    ki.jam_masuk,
                    ki.tgl_keluar,
                    ki.jam_keluar,
                    ki.lama,
                    ki.stts_pulang,
                    rp.no_rkm_medis,
                    rp.tgl_registrasi,
                    rp.jam_reg,
                    rp.kd_dokter,
                    p.nm_pasien,
                    p.tgl_lahir,
                    p.jk,
                    p.alamat,
                    pol.nm_poli,
                    COALESCE(d_dpjp.nm_dokter, d.nm_dokter) AS nm_dokter,
                    COALESCE(s_dpjp.nm_sps, s.nm_sps) AS spesialis,
                    k.kd_bangsal,
                    k.kelas,
                    bang.nm_bangsal,
                    dp_utama.kd_penyakit,
                    pyk.nm_penyakit
                 FROM kamar_inap ki
                 INNER JOIN reg_periksa rp  ON rp.no_rawat     = ki.no_rawat
                 LEFT JOIN  pasien p        ON p.no_rkm_medis  = rp.no_rkm_medis
                 LEFT JOIN  poliklinik pol  ON pol.kd_poli     = rp.kd_poli
                 LEFT JOIN  dokter d        ON d.kd_dokter     = rp.kd_dokter
                 LEFT JOIN  spesialis s     ON s.kd_sps        = d.kd_sps
                 LEFT JOIN (
                     SELECT no_rawat, MIN(kd_dokter) AS kd_dokter
                     FROM dpjp_ranap
                     GROUP BY no_rawat
                 ) dpjp                     ON dpjp.no_rawat    = ki.no_rawat
                 LEFT JOIN  dokter d_dpjp   ON d_dpjp.kd_dokter = dpjp.kd_dokter
                 LEFT JOIN  spesialis s_dpjp ON s_dpjp.kd_sps   = d_dpjp.kd_sps
                 LEFT JOIN  kamar k         ON k.kd_kamar      = ki.kd_kamar
                 LEFT JOIN  bangsal bang    ON bang.kd_bangsal = k.kd_bangsal
                 LEFT JOIN (
                     SELECT no_rawat, MIN(kd_penyakit) AS kd_penyakit
                     FROM diagnosa_pasien
                     WHERE prioritas = 1 OR prioritas IS NULL
                     GROUP BY no_rawat
                 ) dp_utama                 ON dp_utama.no_rawat = ki.no_rawat
                 LEFT JOIN penyakit pyk     ON pyk.kd_penyakit = dp_utama.kd_penyakit
                 WHERE ki.tgl_keluar IS NOT NULL
                   AND ki.tgl_keluar != '0000-00-00'
                   AND ki.stts_pulang != '-'
                 {$dokterFilter}
                 ORDER BY ki.tgl_keluar DESC, ki.jam_keluar DESC
                 LIMIT 500",
                $params
            );

            return $result ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Statistik rawat inap — hanya pasien yang sedang dirawat & selesai.
     */
    private function getStatsRanap(?string $kdDokter = null): array
    {
        $defaults = [
            'total_ranap'    => 0,
            'total_selesai'  => 0,
            'kelas_vvip'     => 0,
            'kelas_vip'      => 0,
            'kelas_i'        => 0,
            'kelas_ii'       => 0,
            'kelas_iii'      => 0,
            'tanpa_kelas'    => 0,
            'rata_lama_inap' => 0,
        ];

        try {
            $params = [];
            $dokterFilter = '';

            if ($kdDokter !== null) {
                $dokterFilter = 'AND (rp.kd_dokter = ? OR dpjp.kd_dokter = ?)';
                $params[]     = $kdDokter;
                $params[]     = $kdDokter;
            }

            $rows = DB::select(
                "SELECT
                    COUNT(DISTINCT CASE WHEN ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00'
                                        THEN ki.no_rawat END) AS total_ranap,
                    COUNT(DISTINCT CASE WHEN ki.tgl_keluar IS NOT NULL AND ki.tgl_keluar != '0000-00-00' AND ki.stts_pulang != '-'
                                        THEN ki.no_rawat END) AS total_selesai,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND k.kelas LIKE '%VVIP%'
                                        THEN ki.no_rawat END) AS kelas_vvip,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND k.kelas LIKE '%VIP%' AND k.kelas NOT LIKE '%VVIP%'
                                        THEN ki.no_rawat END) AS kelas_vip,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND (k.kelas LIKE '%1%' OR k.kelas LIKE '%Kelas I%')
                                         AND k.kelas NOT LIKE '%2%'
                                         AND k.kelas NOT LIKE '%3%'
                                         AND k.kelas NOT LIKE '%VIP%'
                                        THEN ki.no_rawat END) AS kelas_i,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND (k.kelas LIKE '%2%' OR (k.kelas LIKE '%II%' AND k.kelas NOT LIKE '%III%'))
                                         AND k.kelas NOT LIKE '%VIP%'
                                        THEN ki.no_rawat END) AS kelas_ii,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND (k.kelas LIKE '%3%' OR k.kelas LIKE '%III%')
                                        THEN ki.no_rawat END) AS kelas_iii,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND (k.kelas IS NULL OR k.kelas = '')
                                        THEN ki.no_rawat END) AS tanpa_kelas,
                    COALESCE(AVG(CASE WHEN ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00'
                                      THEN DATEDIFF(CURDATE(), ki.tgl_masuk) END), 0) AS rata_lama_inap
                 FROM kamar_inap ki
                 INNER JOIN reg_periksa rp ON rp.no_rawat = ki.no_rawat
                 LEFT JOIN (
                     SELECT no_rawat, MIN(kd_dokter) AS kd_dokter
                     FROM dpjp_ranap
                     GROUP BY no_rawat
                 ) dpjp ON dpjp.no_rawat = ki.no_rawat
                 LEFT JOIN kamar k ON k.kd_kamar  = ki.kd_kamar
                 WHERE 1=1
                 {$dokterFilter}",
                $params
            );

            if (!empty($rows)) {
                $defaults['total_ranap']    = (int)   ($rows[0]->total_ranap    ?? 0);
                $defaults['total_selesai']  = (int)   ($rows[0]->total_selesai  ?? 0);
                $defaults['kelas_vvip']     = (int)   ($rows[0]->kelas_vvip     ?? 0);
                $defaults['kelas_vip']      = (int)   ($rows[0]->kelas_vip      ?? 0);
                $defaults['kelas_i']        = (int)   ($rows[0]->kelas_i        ?? 0);
                $defaults['kelas_ii']       = (int)   ($rows[0]->kelas_ii       ?? 0);
                $defaults['kelas_iii']      = (int)   ($rows[0]->kelas_iii      ?? 0);
                $defaults['tanpa_kelas']    = (int)   ($rows[0]->tanpa_kelas    ?? 0);
                $defaults['rata_lama_inap'] = round((float) ($rows[0]->rata_lama_inap ?? 0), 1);
            }
        } catch (\Exception $e) {
            // Return defaults jika error
        }

        return $defaults;
    }
}
