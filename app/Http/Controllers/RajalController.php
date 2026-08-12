<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RajalController extends Controller
{
    /**
     * Halaman Rawat Jalan
     */
    public function index(Request $request)
    {
        $kodeUser   = session('auth_user.kode');
        $isAdmin    = session('auth_user.is_admin', false);
        $isDokter   = session('auth_user.is_dokter', false);
        $nmDokter   = session('auth_user.nm_dokter');

        // Filter by dokter jika yang login adalah dokter
        $kdDokterFilter = (!$isAdmin && $isDokter) ? $kodeUser : null;

        // Tanggal Awal & Akhir dari request filter (default: hari ini)
        $tglAwal  = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');

        // Dukungan fallback jika request menggunakan tgl_registrasi tunggal
        if (!$tglAwal && $request->has('tgl_registrasi')) {
            $tglAwal  = $request->input('tgl_registrasi');
            $tglAkhir = $request->input('tgl_registrasi');
        }

        $tglAwal  = $tglAwal ?: now()->toDateString();
        $tglAkhir = $tglAkhir ?: $tglAwal;

        $pasienList  = $this->getPasienRajal($kdDokterFilter, $tglAwal, $tglAkhir);
        $statsRajal  = $this->getStatsRajal($kdDokterFilter, $tglAwal, $tglAkhir);

        return view('rawat-jalan.index', compact(
            'pasienList', 'statsRajal',
            'isDokter', 'isAdmin', 'nmDokter', 'kdDokterFilter',
            'tglAwal', 'tglAkhir'
        ));
    }

    /**
     * Ambil daftar pasien Rawat Jalan berdasarkan range tanggal registrasi & filter dokter
     */
    private function getPasienRajal(?string $kdDokter = null, string $tglAwal = '', string $tglAkhir = ''): array
    {
        try {
            $awal  = !empty($tglAwal) ? $tglAwal : now()->toDateString();
            $akhir = !empty($tglAkhir) ? $tglAkhir : $awal;

            $params = [$awal, $akhir];
            $dokterFilter = '';

            if ($kdDokter !== null) {
                $dokterFilter = 'AND rp.kd_dokter = ?';
                $params[]     = $kdDokter;
            }

            $result = DB::select(
                "SELECT
                    rp.no_rawat,
                    rp.no_rkm_medis,
                    rp.no_reg,
                    rp.tgl_registrasi,
                    rp.jam_reg,
                    rp.kd_dokter,
                    rp.kd_poli,
                    rp.kd_pj,

                    -- Data dari reg_periksa (penanggung jawab & registrasi)
                    rp.p_jawab       AS nm_penjamin,
                    rp.almt_pj       AS alamat_pj,
                    rp.hubunganpj    AS hub_keluarga,
                    rp.biaya_reg     AS biaya_daftar,
                    rp.stts          AS status_pasien,
                    rp.status_bayar,
                    rp.status_poli   AS stts_poli,
                    rp.status_lanjut,

                    -- Pasien
                    p.nm_pasien,
                    p.jk,
                    p.tgl_lahir,
                    p.no_tlp,

                    -- Poliklinik
                    pol.nm_poli,

                    -- Dokter
                    d.nm_dokter,

                    -- Nama penjamin / jenis bayar berdasarkan kd_pj dari tabel penjab
                    pjb.png_jawab    AS jenis_bayar,

                    -- SEP BPJS (jika ada di bridging_sep)
                    sep.no_sep,

                    -- Status terlayani: cek data di pemeriksaan_ralan
                    CASE
                        WHEN pr.no_rawat IS NOT NULL THEN 'terlayani'
                        ELSE 'belum'
                    END AS status_layanan,

                    -- Jenis kunjungan (IGD / Ralan)
                    CASE
                        WHEN pol.nm_poli LIKE '%IGD%' OR pol.nm_poli LIKE '%UGD%' THEN 'IGD'
                        ELSE 'Ralan'
                    END AS jenis_kunjungan

                 FROM reg_periksa rp
                 LEFT JOIN pasien p
                        ON rp.no_rkm_medis = p.no_rkm_medis
                 LEFT JOIN poliklinik pol
                        ON rp.kd_poli       = pol.kd_poli
                 LEFT JOIN dokter d
                        ON rp.kd_dokter     = d.kd_dokter
                 LEFT JOIN penjab pjb
                        ON rp.kd_pj         = pjb.kd_pj
                 LEFT JOIN (
                     SELECT DISTINCT no_rawat FROM pemeriksaan_ralan
                 ) pr ON pr.no_rawat = rp.no_rawat
                 LEFT JOIN bridging_sep sep
                        ON sep.no_rawat     = rp.no_rawat
                 WHERE rp.tgl_registrasi BETWEEN ? AND ?
                   AND (rp.status_lanjut = 'Ralan'
                        OR pol.nm_poli LIKE '%IGD%'
                        OR pol.nm_poli LIKE '%UGD%')
                 {$dokterFilter}
                 ORDER BY rp.tgl_registrasi DESC, rp.jam_reg DESC, rp.no_rawat DESC",
                $params
            );

            return $result ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Statistik ringkasan rawat jalan berdasarkan range tanggal registrasi
     * 5 Card Monitoring:
     *   1. Total Pasien
     *   2. Pasien IGD
     *   3. Pasien Poli
     *   4. Sudah Terlayani
     *   5. Belum Terlayani
     */
    private function getStatsRajal(?string $kdDokter = null, string $tglAwal = '', string $tglAkhir = ''): array
    {
        $defaults = [
            'total'      => 0,
            'igd'        => 0,
            'poli'       => 0,
            'terlayani'  => 0,
            'belum'      => 0,
        ];

        try {
            $awal  = !empty($tglAwal) ? $tglAwal : now()->toDateString();
            $akhir = !empty($tglAkhir) ? $tglAkhir : $awal;

            $params = [$awal, $akhir];
            $dokterFilter = '';

            if ($kdDokter !== null) {
                $dokterFilter = 'AND rp.kd_dokter = ?';
                $params[]     = $kdDokter;
            }

            $rows = DB::select(
                "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN pol.nm_poli LIKE '%IGD%' OR pol.nm_poli LIKE '%UGD%' THEN 1 ELSE 0 END) as igd,
                    SUM(CASE WHEN pol.nm_poli NOT LIKE '%IGD%' AND pol.nm_poli NOT LIKE '%UGD%' THEN 1 ELSE 0 END) as poli,
                    SUM(CASE WHEN pr.no_rawat IS NOT NULL THEN 1 ELSE 0 END) as terlayani,
                    SUM(CASE WHEN pr.no_rawat IS NULL THEN 1 ELSE 0 END) as belum
                 FROM reg_periksa rp
                 LEFT JOIN poliklinik pol
                        ON rp.kd_poli = pol.kd_poli
                 LEFT JOIN (
                     SELECT DISTINCT no_rawat FROM pemeriksaan_ralan
                 ) pr ON pr.no_rawat = rp.no_rawat
                 WHERE rp.tgl_registrasi BETWEEN ? AND ?
                   AND (rp.status_lanjut = 'Ralan'
                        OR pol.nm_poli LIKE '%IGD%'
                        OR pol.nm_poli LIKE '%UGD%')
                 {$dokterFilter}",
                $params
            );

            if (!empty($rows)) {
                $defaults['total']     = (int) ($rows[0]->total ?? 0);
                $defaults['igd']       = (int) ($rows[0]->igd ?? 0);
                $defaults['poli']      = (int) ($rows[0]->poli ?? 0);
                $defaults['terlayani'] = (int) ($rows[0]->terlayani ?? 0);
                $defaults['belum']     = (int) ($rows[0]->belum ?? 0);
            }
        } catch (\Exception $e) {
            // Return defaults jika error
        }

        return $defaults;
    }
}
