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
     * Ambil detail pasien & data rekam medis aktif untuk Offcanvas Dokter
     */
    public function getPasienDetail(Request $request, $no_rawat_b64 = null)
    {
        try {
            $raw = $no_rawat_b64 ?: $request->input('no_rawat') ?: $request->input('no_rawat_b64');
            if (empty($raw)) {
                return response()->json(['success' => false, 'message' => 'No. Rawat tidak valid.'], 400);
            }

            if (strpos($raw, '/') !== false && strlen($raw) >= 14) {
                $noRawat = $raw;
            } else {
                $decoded = base64_decode(strtr($raw, '-_', '+/'), true);
                if ($decoded && strpos($decoded, '/') !== false) {
                    $noRawat = $decoded;
                } else {
                    $noRawat = urldecode($raw);
                }
            }

            // Pasien demography & reg_periksa
            $pasien = DB::selectOne(
                "SELECT rp.no_rawat, rp.no_rkm_medis, rp.tgl_registrasi, rp.jam_reg, rp.kd_dokter, rp.kd_poli, rp.kd_pj,
                        p.nm_pasien, p.jk, p.tgl_lahir, p.no_tlp, p.alamat,
                        pol.nm_poli, d.nm_dokter, pjb.png_jawab AS jenis_bayar
                 FROM reg_periksa rp
                 LEFT JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
                 LEFT JOIN poliklinik pol ON rp.kd_poli = pol.kd_poli
                 LEFT JOIN dokter d ON rp.kd_dokter = d.kd_dokter
                 LEFT JOIN penjab pjb ON rp.kd_pj = pjb.kd_pj
                 WHERE rp.no_rawat = ?
                 LIMIT 1",
                [$noRawat]
            );

            if (!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.'], 404);
            }

            $noRkmMedis = $pasien->no_rkm_medis;

            // 1. Data SOAP (pemeriksaan_ralan & pemeriksaan_ranap) berdasarkan no_rkm_medis pasien
            $soapList = DB::select(
                "SELECT pr.no_rawat, pr.tgl_perawatan, pr.jam_rawat,
                        pr.suhu_tubuh, pr.tensi, pr.nadi, pr.respirasi,
                        pr.tinggi, pr.berat, pr.spo2, pr.gcs, pr.kesadaran,
                        pr.keluhan, pr.pemeriksaan, pr.penilaian, pr.rtl,
                        pr.instruksi, pr.evaluasi, pr.alergi, pr.lingkar_perut,
                        rp.tgl_registrasi, COALESCE(d.nm_dokter, pt.nama, pr.nip) AS petugas,
                        COALESCE(d.nm_dokter, pt.nama, pr.nip) AS nm_dokter,
                        pol.nm_poli, 'Ralan' AS jenis_rawat,
                        IF(pr.no_rawat = ?, 1, 0) AS is_current
                 FROM pemeriksaan_ralan pr
                 JOIN reg_periksa rp ON pr.no_rawat = rp.no_rawat
                 LEFT JOIN poliklinik pol ON rp.kd_poli = pol.kd_poli
                 LEFT JOIN dokter d ON rp.kd_dokter = d.kd_dokter
                 LEFT JOIN petugas pt ON pr.nip = pt.nip
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT prn.no_rawat, prn.tgl_perawatan, prn.jam_rawat,
                        prn.suhu_tubuh, prn.tensi, prn.nadi, prn.respirasi,
                        prn.tinggi, prn.berat, prn.spo2, prn.gcs, prn.kesadaran,
                        prn.keluhan, prn.pemeriksaan, prn.penilaian, prn.rtl,
                        prn.instruksi, prn.evaluasi, prn.alergi, '' AS lingkar_perut,
                        rp2.tgl_registrasi, COALESCE(d2.nm_dokter, pt2.nama, prn.nip) AS petugas,
                        COALESCE(d2.nm_dokter, pt2.nama, prn.nip) AS nm_dokter,
                        'Rawat Inap' AS nm_poli, 'Ranap' AS jenis_rawat,
                        IF(prn.no_rawat = ?, 1, 0) AS is_current
                 FROM pemeriksaan_ranap prn
                 JOIN reg_periksa rp2 ON prn.no_rawat = rp2.no_rawat
                 LEFT JOIN dokter d2 ON rp2.kd_dokter = d2.kd_dokter
                 LEFT JOIN petugas pt2 ON prn.nip = pt2.nip
                 WHERE rp2.no_rkm_medis = ?

                 ORDER BY tgl_perawatan DESC, jam_rawat DESC
                 LIMIT 100",
                [$noRawat, $noRkmMedis, $noRawat, $noRkmMedis]
            );

            // 2. Data Awal Medis berdasarkan no_rkm_medis (Semua Poli Spesialis, IGD, dan Ranap)
            $awalMedisList = DB::select(
                "SELECT pmr.no_rawat, pmr.tanggal, pmr.kd_dokter, d.nm_dokter, pmr.anamnesis, pmr.keluhan_utama, pmr.rps, pmr.rpd, pmr.rpk, pmr.rpo, pmr.alergi, pmr.diagnosis, pmr.tata, 'Medis Ralan Umum' AS departemen, IF(pmr.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan pmr
                 JOIN reg_periksa rp ON pmr.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pmr.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pd.no_rawat, pd.tanggal, pd.kd_dokter, d.nm_dokter, pd.anamnesis, pd.keluhan_utama, pd.rps, pd.rpd, '' AS rpk, pd.rpo, pd.alergi, pd.diagnosis, pd.terapi AS tata, 'Poli Penyakit Dalam' AS departemen, IF(pd.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_penyakit_dalam pd
                 JOIN reg_periksa rp ON pd.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pd.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT igd.no_rawat, igd.tanggal, igd.kd_dokter, d.nm_dokter, igd.anamnesis, igd.keluhan_utama, igd.rps, igd.rpd, igd.rpk, igd.rpo, igd.alergi, igd.diagnosis, igd.tata, 'Medis IGD' AS departemen, IF(igd.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_igd igd
                 JOIN reg_periksa rp ON igd.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON igd.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pmrn.no_rawat, pmrn.tanggal, pmrn.kd_dokter, d.nm_dokter, pmrn.anamnesis, pmrn.keluhan_utama, pmrn.rps, pmrn.rpd, pmrn.rpk, pmrn.rpo, pmrn.alergi, pmrn.diagnosis, pmrn.tata, 'Medis Rawat Inap' AS departemen, IF(pmrn.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ranap pmrn
                 JOIN reg_periksa rp ON pmrn.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pmrn.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pkk.no_rawat, pkk.tanggal, pkk.kd_dokter, d.nm_dokter, pkk.anamnesis, pkk.keluhan_utama, pkk.rps, pkk.rpd, pkk.rpk, pkk.rpo, '' AS alergi, pkk.diagnosis, pkk.terapi AS tata, 'Poli Kulit & Kelamin' AS departemen, IF(pkk.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_kulitdankelamin pkk
                 JOIN reg_periksa rp ON pkk.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pkk.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pma.no_rawat, pma.tanggal, pma.kd_dokter, d.nm_dokter, pma.anamnesis, pma.keluhan_utama, pma.rps, pma.rpd, '' AS rpk, pma.rpo, pma.alergi, pma.diagnosis, pma.terapi AS tata, 'Poli Spesialis Mata' AS departemen, IF(pma.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_mata pma
                 JOIN reg_periksa rp ON pma.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pma.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pmb.no_rawat, pmb.tanggal, pmb.kd_dokter, d.nm_dokter, pmb.anamnesis, pmb.keluhan_utama, pmb.rps, pmb.rpd, '' AS rpk, pmb.rpo, pmb.alergi, pmb.diagnosis, pmb.terapi AS tata, 'Poli Spesialis Bedah' AS departemen, IF(pmb.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_bedah pmb
                 JOIN reg_periksa rp ON pmb.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pmb.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT ptht.no_rawat, ptht.tanggal, ptht.kd_dokter, d.nm_dokter, ptht.anamnesis, ptht.keluhan_utama, ptht.rps, ptht.rpd, '' AS rpk, ptht.rpo, ptht.alergi, ptht.diagnosis, COALESCE(ptht.tatalaksana, ptht.terapi) AS tata, 'Poli Spesialis THT' AS departemen, IF(ptht.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_tht ptht
                 JOIN reg_periksa rp ON ptht.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON ptht.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pbm.no_rawat, pbm.tanggal, pbm.kd_dokter, d.nm_dokter, pbm.anamnesis, pbm.keluhan_utama, pbm.rps, '' AS rpd, pbm.rpk, '' AS rpo, pbm.alergi, pbm.diagnosis, pbm.terapi AS tata, 'Poli Bedah Mulut' AS departemen, IF(pbm.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_bedah_mulut pbm
                 JOIN reg_periksa rp ON pbm.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pbm.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT prk.no_rawat, prk.tanggal, prk.kd_dokter, d.nm_dokter, prk.anamnesis, prk.keluhan_utama, prk.rps, prk.rpd, prk.rpk, prk.rpo, prk.alergi, prk.diagnosis, prk.tata, 'Ranap Kebidanan & Kandungan' AS departemen, IF(prk.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ranap_kandungan prk
                 JOIN reg_periksa rp ON prk.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON prk.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT prn.no_rawat, prn.tanggal, prn.kd_dokter, d.nm_dokter, '' AS anamnesis, prn.keterangan_faktor_risiko_neonatal AS keluhan_utama, '' AS rps, '' AS rpd, '' AS rpk, '' AS rpo, '' AS alergi, prn.diagnosis, prn.tata, 'Ranap Neonatus' AS departemen, IF(prn.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ranap_neonatus prn
                 JOIN reg_periksa rp ON prn.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON prn.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pok.no_rawat, pok.tanggal, pok.kd_dokter, d.nm_dokter, pok.anamnesis, pok.keluhan_utama, pok.rps, pok.rpd, pok.rpk, pok.rpo, pok.alergi, pok.diagnosis, pok.tata, 'Poli Kandungan & Obgyn' AS departemen, IF(pok.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_kandungan pok
                 JOIN reg_periksa rp ON pok.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pok.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pan.no_rawat, pan.tanggal, pan.kd_dokter, d.nm_dokter, pan.anamnesis, pan.keluhan_utama, pan.rps, pan.rpd, pan.rpk, pan.rpo, pan.alergi, pan.diagnosis, pan.tata, 'Poli Spesialis Anak' AS departemen, IF(pan.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_anak pan
                 JOIN reg_periksa rp ON pan.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pan.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pnr.no_rawat, pnr.tanggal, pnr.kd_dokter, d.nm_dokter, pnr.anamnesis, pnr.keluhan_utama, pnr.rps, pnr.rpd, '' AS rpk, pnr.rpo, pnr.alergi, pnr.diagnosis, pnr.terapi AS tata, 'Poli Saraf / Neurologi' AS departemen, IF(pnr.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_neurologi pnr
                 JOIN reg_periksa rp ON pnr.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pnr.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT ppr.no_rawat, ppr.tanggal, ppr.kd_dokter, d.nm_dokter, ppr.anamnesis, ppr.keluhan_utama, ppr.rps, ppr.rpd, '' AS rpk, ppr.rpo, ppr.alergi, ppr.diagnosis, ppr.terapi AS tata, 'Poli Spesialis Paru' AS departemen, IF(ppr.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_paru ppr
                 JOIN reg_periksa rp ON ppr.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON ppr.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pps.no_rawat, pps.tanggal, pps.kd_dokter, d.nm_dokter, pps.anamnesis, pps.keluhan_utama, '' AS rps, pps.riwayat_penyakit_dahulu AS rpd, pps.faktor_keturunan AS rpk, pps.riwayat_obat_diminum AS rpo, pps.riwayat_alergi AS alergi, pps.diagnosis, pps.instruksi_medis AS tata, 'Gawat Darurat Psikiatri' AS departemen, IF(pps.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_medis_ralan_gawat_darurat_psikiatri pps
                 JOIN reg_periksa rp ON pps.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pps.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 ORDER BY tanggal DESC
                 LIMIT 100",
                [
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis,
                    $noRawat, $noRkmMedis
                ]
            );

            // 2b. Data Awal Keperawatan & Kebidanan (Ralan, IGD, Ranap, Kebidanan)
            $keperawatanList = DB::select(
                "SELECT pakr.no_rawat, pakr.tanggal, pakr.informasi AS anamnesis, pakr.keluhan_utama,
                        pakr.rpd, pakr.rpk, pakr.rpo, pakr.alergi,
                        CONCAT_WS(', ',
                            IF(pakr.td != '', CONCAT('TD: ', pakr.td, ' mmHg'), NULL),
                            IF(pakr.nadi != '', CONCAT('N: ', pakr.nadi, ' x/m'), NULL),
                            IF(pakr.rr != '', CONCAT('RR: ', pakr.rr, ' x/m'), NULL),
                            IF(pakr.suhu != '', CONCAT('Suhu: ', pakr.suhu, ' °C'), NULL),
                            IF(pakr.gcs != '', CONCAT('GCS: ', pakr.gcs), NULL)
                        ) AS vital_signs,
                        COALESCE(pt.nama, pakr.nip) AS petugas,
                        'Keperawatan Ralan' AS departemen,
                        IF(pakr.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_awal_keperawatan_ralan pakr
                 JOIN reg_periksa rp ON pakr.no_rawat = rp.no_rawat
                 LEFT JOIN petugas pt ON pakr.nip = pt.nip
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT paki.no_rawat, paki.tanggal, paki.informasi AS anamnesis, paki.keluhan_utama,
                        paki.rpd, '' AS rpk, paki.rpo, '' AS alergi,
                        CONCAT_WS(', ',
                            IF(paki.tekanan != '', CONCAT('TD: ', paki.tekanan, ' mmHg'), NULL),
                            IF(paki.pupil != '', CONCAT('Pupil: ', paki.pupil), NULL)
                        ) AS vital_signs,
                        COALESCE(pt.nama, paki.nip) AS petugas,
                        'Keperawatan IGD' AS departemen,
                        IF(paki.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_awal_keperawatan_igd paki
                 JOIN reg_periksa rp ON paki.no_rawat = rp.no_rawat
                 LEFT JOIN petugas pt ON paki.nip = pt.nip
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pakrn.no_rawat, pakrn.tanggal, pakrn.informasi AS anamnesis, pakrn.rps AS keluhan_utama,
                        pakrn.rpd, pakrn.rpk, pakrn.rpo, pakrn.riwayat_alergi AS alergi,
                        CONCAT_WS(', ',
                            IF(pakrn.pemeriksaan_td != '', CONCAT('TD: ', pakrn.pemeriksaan_td, ' mmHg'), NULL),
                            IF(pakrn.pemeriksaan_nadi != '', CONCAT('N: ', pakrn.pemeriksaan_nadi, ' x/m'), NULL),
                            IF(pakrn.pemeriksaan_rr != '', CONCAT('RR: ', pakrn.pemeriksaan_rr, ' x/m'), NULL),
                            IF(pakrn.pemeriksaan_suhu != '', CONCAT('S: ', pakrn.pemeriksaan_suhu, ' °C'), NULL),
                            IF(pakrn.pemeriksaan_spo2 != '', CONCAT('SpO2: ', pakrn.pemeriksaan_spo2, ' %'), NULL),
                            IF(pakrn.pemeriksaan_gcs != '', CONCAT('GCS: ', pakrn.pemeriksaan_gcs), NULL)
                        ) AS vital_signs,
                        COALESCE(pt.nama, pakrn.nip1) AS petugas,
                        'Keperawatan Ranap' AS departemen,
                        IF(pakrn.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_awal_keperawatan_ranap pakrn
                 JOIN reg_periksa rp ON pakrn.no_rawat = rp.no_rawat
                 LEFT JOIN petugas pt ON pakrn.nip1 = pt.nip
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT pakb.no_rawat, pakb.tanggal, pakb.informasi AS anamnesis, pakb.keluhan_utama,
                        '' AS rpd, '' AS rpk, '' AS rpo, '' AS alergi,
                        CONCAT_WS(', ',
                            IF(pakb.td != '', CONCAT('TD: ', pakb.td, ' mmHg'), NULL),
                            IF(pakb.nadi != '', CONCAT('N: ', pakb.nadi, ' x/m'), NULL),
                            IF(pakb.suhu != '', CONCAT('Suhu: ', pakb.suhu, ' °C'), NULL),
                            IF(pakb.tfu != '', CONCAT('TFU: ', pakb.tfu), NULL),
                            IF(pakb.tbj != '', CONCAT('TBJ: ', pakb.tbj), NULL)
                        ) AS vital_signs,
                        COALESCE(pt.nama, pakb.nip) AS petugas,
                        'Kebidanan & Kandungan' AS departemen,
                        IF(pakb.no_rawat = ?, 1, 0) AS is_current
                 FROM penilaian_awal_keperawatan_kebidanan pakb
                 JOIN reg_periksa rp ON pakb.no_rawat = rp.no_rawat
                 LEFT JOIN petugas pt ON pakb.nip = pt.nip
                 WHERE rp.no_rkm_medis = ?

                 ORDER BY tanggal DESC
                 LIMIT 50",
                [$noRawat, $noRkmMedis, $noRawat, $noRkmMedis, $noRawat, $noRkmMedis, $noRawat, $noRkmMedis]
            );
            
            // Prioritaskan awal medis dari kunjungan ini jika ada, atau fallback ke yang terbaru
            $awalMedis = null;
            foreach ($awalMedisList as $am) {
                if ($am->no_rawat === $noRawat) {
                    $awalMedis = $am;
                    break;
                }
            }
            if (!$awalMedis && !empty($awalMedisList)) {
                $awalMedis = $awalMedisList[0];
            }

            // 3. Permintaan & HASIL Laboratorium berdasarkan no_rkm_medis
            $labOrders = DB::select(
                "SELECT pl.noorder, pl.no_rawat, pl.tgl_permintaan, pl.jam_permintaan, pl.diagnosa_klinis, pl.informasi_tambahan,
                        GROUP_CONCAT(jpl.nm_perawatan SEPARATOR ', ') AS detail_pemeriksaan,
                        IF(pl.no_rawat = ?, 1, 0) AS is_current
                 FROM permintaan_lab pl
                 JOIN reg_periksa rp ON pl.no_rawat = rp.no_rawat
                 LEFT JOIN permintaan_detail_permintaan_lab pdl ON pl.noorder = pdl.noorder
                 LEFT JOIN jns_perawatan_lab jpl ON pdl.kd_jenis_prw = jpl.kd_jenis_prw
                 WHERE rp.no_rkm_medis = ?
                 GROUP BY pl.noorder, pl.no_rawat, pl.tgl_permintaan, pl.jam_permintaan, pl.diagnosa_klinis, pl.informasi_tambahan
                 ORDER BY pl.tgl_permintaan DESC, pl.jam_permintaan DESC
                 LIMIT 50",
                [$noRawat, $noRkmMedis]
            );

            $labResults = DB::select(
                "SELECT dpl.no_rawat, dpl.tgl_periksa, dpl.jam, jpl.nm_perawatan,
                        COALESCE(tl.Pemeriksaan, jpl.nm_perawatan) AS nama_pemeriksaan,
                        COALESCE(dpl.nilai, '-') AS nilai,
                        COALESCE(dpl.nilai_rujukan, '-') AS nilai_rujukan,
                        COALESCE(tl.satuan, '') AS satuan,
                        COALESCE(dpl.keterangan, '') AS keterangan,
                        IF(dpl.no_rawat = ?, 1, 0) AS is_current
                 FROM detail_periksa_lab dpl
                 JOIN reg_periksa rp ON dpl.no_rawat = rp.no_rawat
                 LEFT JOIN jns_perawatan_lab jpl ON dpl.kd_jenis_prw = jpl.kd_jenis_prw
                 LEFT JOIN template_laboratorium tl ON dpl.id_template = tl.id_template
                 WHERE rp.no_rkm_medis = ?
                 ORDER BY dpl.tgl_periksa DESC, dpl.jam DESC
                 LIMIT 200",
                [$noRawat, $noRkmMedis]
            );

            // 4. Permintaan & HASIL Radiologi berdasarkan no_rkm_medis
            $radOrders = DB::select(
                "SELECT pr.noorder, pr.no_rawat, pr.tgl_permintaan, pr.jam_permintaan, pr.diagnosa_klinis, pr.informasi_tambahan,
                        GROUP_CONCAT(jpr.nm_perawatan SEPARATOR ', ') AS detail_pemeriksaan,
                        IF(pr.no_rawat = ?, 1, 0) AS is_current
                 FROM permintaan_radiologi pr
                 JOIN reg_periksa rp ON pr.no_rawat = rp.no_rawat
                 LEFT JOIN permintaan_pemeriksaan_radiologi pdr ON pr.noorder = pdr.noorder
                 LEFT JOIN jns_perawatan_radiologi jpr ON pdr.kd_jenis_prw = jpr.kd_jenis_prw
                 WHERE rp.no_rkm_medis = ?
                 GROUP BY pr.noorder, pr.no_rawat, pr.tgl_permintaan, pr.jam_permintaan, pr.diagnosa_klinis, pr.informasi_tambahan
                 ORDER BY pr.tgl_permintaan DESC, pr.jam_permintaan DESC
                 LIMIT 50",
                [$noRawat, $noRkmMedis]
            );

            $radResults = DB::select(
                "SELECT hr.no_rawat, hr.tgl_periksa, hr.jam, hr.hasil,
                        IF(hr.no_rawat = ?, 1, 0) AS is_current
                 FROM hasil_radiologi hr
                 JOIN reg_periksa rp ON hr.no_rawat = rp.no_rawat
                 WHERE rp.no_rkm_medis = ?
                 ORDER BY hr.tgl_periksa DESC, hr.jam DESC
                 LIMIT 30",
                [$noRawat, $noRkmMedis]
            );

            // 5. Data Resep Dokter berdasarkan no_rkm_medis (Obat Jadi & Obat Racikan)
            $rawResep = DB::select(
                "SELECT ro.no_resep, ro.no_rawat, ro.tgl_perawatan, COALESCE(ro.jam, '00:00:00') AS jam,
                        ro.tgl_peresepan, ro.jam_peresepan, ro.status, ro.tgl_penyerahan, ro.jam_penyerahan,
                        COALESCE(d.nm_dokter, 'Dokter') AS nm_dokter,
                        IF(ro.no_rawat = ?, 1, 0) AS is_current
                 FROM resep_obat ro
                 JOIN reg_periksa rp ON ro.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON ro.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?
                 ORDER BY ro.tgl_peresepan DESC, ro.jam_peresepan DESC, ro.tgl_perawatan DESC, ro.jam DESC
                 LIMIT 60",
                [$noRawat, $noRkmMedis]
            );

            $resepList = [];
            foreach ($rawResep as $r) {
                // Non-racikan
                $obatList = DB::select(
                    "SELECT rd.kode_brng, rd.jml, rd.aturan_pakai, rd.keterangan,
                            db.nama_brng, db.kode_sat, db.ralan AS harga, db.kapasitas,
                            COALESCE(gb.stok, 0) AS stok, COALESCE(gb.stok, 0) AS stok_depo
                     FROM resep_dokter rd
                     LEFT JOIN databarang db ON rd.kode_brng = db.kode_brng
                     LEFT JOIN gudangbarang gb ON rd.kode_brng = gb.kode_brng AND gb.kd_bangsal = 'G002'
                     WHERE rd.no_resep = ?",
                    [$r->no_resep]
                );

                // Racikan
                $racikList = DB::select(
                    "SELECT rdr.no_racik, rdr.nama_racik, rdr.kd_racik, rdr.jml_dr, rdr.aturan_pakai, rdr.keterangan,
                            COALESCE(mr.nm_racik, 'Racikan') AS metode
                     FROM resep_dokter_racikan rdr
                     LEFT JOIN metode_racik mr ON rdr.kd_racik = mr.kd_racik
                     WHERE rdr.no_resep = ?
                     ORDER BY rdr.no_racik ASC",
                    [$r->no_resep]
                );

                foreach ($racikList as $rc) {
                    $rc->detail = DB::select(
                        "SELECT rdrd.kode_brng, rdrd.p1, rdrd.p2, rdrd.kandungan, rdrd.jml,
                                db.nama_brng, db.kode_sat, db.kapasitas,
                                COALESCE(gb.stok, 0) AS stok, COALESCE(gb.stok, 0) AS stok_depo
                         FROM resep_dokter_racikan_detail rdrd
                         LEFT JOIN databarang db ON rdrd.kode_brng = db.kode_brng
                         LEFT JOIN gudangbarang gb ON rdrd.kode_brng = gb.kode_brng AND gb.kd_bangsal = 'G002'
                         WHERE rdrd.no_resep = ? AND rdrd.no_racik = ?",
                        [$r->no_resep, $rc->no_racik]
                    );
                    // Alias detail_racik for frontend compatibility
                    $rc->detail_racik = $rc->detail;
                }

                // Summary HTML string for quick display
                $summaryParts = [];
                foreach ($obatList as $ob) {
                    $summaryParts[] = "<b>{$ob->nama_brng}</b> ({$ob->jml} {$ob->kode_sat}) — <i>" . ($ob->aturan_pakai ?: '-') . "</i>";
                }
                foreach ($racikList as $rc) {
                    $detStr = [];
                    foreach ($rc->detail as $dt) {
                        $detStr[] = "{$dt->nama_brng} ({$dt->jml} {$dt->kode_sat})";
                    }
                    $summaryParts[] = "<span class='badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-1.5 py-0.5 me-1'>Racikan {$rc->metode}</span> <b>{$rc->nama_racik}</b> ({$rc->jml_dr} kemasan) — <i>{$rc->aturan_pakai}</i> [" . implode(', ', $detStr) . "]";
                }

                $canDelete = (empty($r->tgl_penyerahan) || $r->tgl_penyerahan == '0000-00-00');

                $resepList[] = [
                    'no_resep'        => $r->no_resep,
                    'no_rawat'        => $r->no_rawat,
                    'tgl_perawatan'   => $r->tgl_perawatan,
                    'jam'             => $r->jam,
                    'tgl_peresepan'   => $r->tgl_peresepan,
                    'jam_peresepan'   => $r->jam_peresepan,
                    'status'          => $r->status,
                    'tgl_penyerahan'  => $r->tgl_penyerahan,
                    'jam_penyerahan'  => $r->jam_penyerahan,
                    'nm_dokter'       => $r->nm_dokter,
                    'can_delete'      => $canDelete,
                    'is_current'      => (bool) $r->is_current,
                    'detail_obat'     => implode('<br>', $summaryParts),
                    'obat_list'       => $obatList,
                    'obat_non_racik'  => $obatList, // alias
                    'racik_list'      => $racikList,
                    'obat_racikan'    => $racikList, // alias
                ];
            }

            // 6. Data Booking Operasi & LAPORAN OPERASI berdasarkan no_rkm_medis
            $bookingOps = DB::select(
                "SELECT bo.*, po.nm_perawatan AS nama_paket, po.kelas, po.kategori, p.png_jawab,
                        d.nm_dokter AS dokter_operator, ro.nm_ruang_ok,
                        IF(bo.no_rawat = ?, 1, 0) AS is_current
                 FROM booking_operasi bo
                 JOIN reg_periksa rp ON bo.no_rawat = rp.no_rawat
                 LEFT JOIN paket_operasi po ON bo.kode_paket = po.kode_paket
                 LEFT JOIN penjab p ON po.kd_pj = p.kd_pj
                 LEFT JOIN dokter d ON bo.kd_dokter = d.kd_dokter
                 LEFT JOIN ruang_ok ro ON bo.kd_ruang_ok = ro.kd_ruang_ok
                 WHERE rp.no_rkm_medis = ?
                 ORDER BY bo.tanggal DESC, bo.jam_mulai DESC
                 LIMIT 50",
                [$noRawat, $noRkmMedis]
            );

            $laporanOps = DB::select(
                "SELECT lo.*, d.nm_dokter AS dokter_operator,
                        IF(lo.no_rawat = ?, 1, 0) AS is_current
                 FROM laporan_operasi lo
                 JOIN reg_periksa rp ON lo.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON rp.kd_dokter = d.kd_dokter
                 WHERE rp.no_rkm_medis = ?
                 ORDER BY lo.tanggal DESC
                 LIMIT 30",
                [$noRawat, $noRkmMedis]
            );

            // 6c. Data Riwayat Tindakan Operasi Selesai (tabel operasi)
            $tagihanOperasi = DB::select(
                "SELECT o.no_rawat, o.tgl_operasi, o.kode_paket, o.status, o.kategori, o.jenis_anasthesi,
                        po.nm_perawatan AS nama_paket, po.kelas,
                        d1.nm_dokter AS operator1_nama,
                        d2.nm_dokter AS dokter_anestesi_nama,
                        (o.biayaoperator1 + o.biayaoperator2 + o.biayaoperator3 + o.biayaasisten_operator1 +
                         o.biayaasisten_operator2 + o.biayaasisten_operator3 + o.biayainstrumen +
                         o.biayadokter_anak + o.biayaperawaat_resusitas + o.biayadokter_anestesi +
                         o.biayaasisten_anestesi + o.biayaasisten_anestesi2 + o.biayabidan +
                         o.biayabidan2 + o.biayabidan3 + o.biayaperawat_luar + o.biayaalat +
                         o.biayasewaok + o.akomodasi + o.bagian_rs + o.biaya_omloop + o.biaya_omloop2 +
                         o.biaya_omloop3 + o.biaya_omloop4 + o.biaya_omloop5 + o.biayasarpras +
                         o.biaya_dokter_pjanak + o.biaya_dokter_umum) AS total_biaya,
                        IF(o.no_rawat = ?, 1, 0) AS is_current
                 FROM operasi o
                 JOIN reg_periksa rp ON o.no_rawat = rp.no_rawat
                 LEFT JOIN paket_operasi po ON o.kode_paket = po.kode_paket
                 LEFT JOIN dokter d1 ON o.operator1 = d1.kd_dokter
                 LEFT JOIN dokter d2 ON o.dokter_anestesi = d2.kd_dokter
                 WHERE rp.no_rkm_medis = ?
                 ORDER BY o.tgl_operasi DESC
                 LIMIT 30",
                [$noRawat, $noRkmMedis]
            );

            // 7. Data Diagnosa Pasien (ICD-10) untuk no_rawat aktif & riwayat lampau
            $diagnosaList = DB::select(
                "SELECT dp.no_rawat, dp.kd_penyakit, dp.prioritas, dp.status, dp.status_penyakit, py.nm_penyakit
                 FROM diagnosa_pasien dp
                 LEFT JOIN penyakit py ON py.kd_penyakit = dp.kd_penyakit
                 WHERE dp.no_rawat = ?
                 ORDER BY dp.prioritas ASC, dp.kd_penyakit ASC",
                [$noRawat]
            );

            $riwayatDiagnosa = DB::select(
                "SELECT dp.no_rawat, dp.kd_penyakit, dp.prioritas, dp.status, dp.status_penyakit, py.nm_penyakit, rp.tgl_registrasi
                 FROM diagnosa_pasien dp
                 JOIN reg_periksa rp ON dp.no_rawat = rp.no_rawat
                 LEFT JOIN penyakit py ON py.kd_penyakit = dp.kd_penyakit
                 WHERE rp.no_rkm_medis = ?
                 ORDER BY rp.tgl_registrasi DESC, dp.prioritas ASC
                 LIMIT 50",
                [$noRkmMedis]
            );

            // 7b. Data Prosedur Pasien (ICD-9-CM) untuk no_rawat aktif & riwayat lampau
            $prosedurList = DB::select(
                "SELECT pp.no_rawat, pp.kode, pp.status, pp.prioritas,
                        COALESCE(i9.deskripsi_panjang, i9.deskripsi_pendek, pp.kode) AS nama_prosedur
                 FROM prosedur_pasien pp
                 LEFT JOIN icd9 i9 ON pp.kode = i9.kode
                 WHERE pp.no_rawat = ?
                 ORDER BY pp.prioritas ASC, pp.kode ASC",
                [$noRawat]
            );

            $riwayatProsedur = DB::select(
                "SELECT pp.no_rawat, pp.kode, pp.status, pp.prioritas, rp.tgl_registrasi,
                        COALESCE(i9.deskripsi_panjang, i9.deskripsi_pendek, pp.kode) AS nama_prosedur
                 FROM prosedur_pasien pp
                 JOIN reg_periksa rp ON pp.no_rawat = rp.no_rawat
                 LEFT JOIN icd9 i9 ON pp.kode = i9.kode
                 WHERE rp.no_rkm_medis = ?
                 ORDER BY rp.tgl_registrasi DESC, pp.prioritas ASC
                 LIMIT 50",
                [$noRkmMedis]
            );

            // 8. Data Tindakan Rawat Jalan untuk no_rawat aktif (Dokter, Paramedis, Dokter & Paramedis)
            $tindakanList = DB::select(
                "SELECT rjd.no_rawat, rjd.tgl_perawatan, rjd.jam_rawat, rjd.kd_jenis_prw, jp.nm_perawatan, rjd.biaya_rawat AS total_byr,
                        COALESCE(d.nm_dokter, rjd.kd_dokter) AS petugas, 'Dokter' AS jenis, p.png_jawab
                 FROM rawat_jl_dr rjd
                 LEFT JOIN jns_perawatan jp ON jp.kd_jenis_prw = rjd.kd_jenis_prw
                 LEFT JOIN penjab p ON jp.kd_pj = p.kd_pj
                 LEFT JOIN dokter d ON d.kd_dokter = rjd.kd_dokter
                 WHERE rjd.no_rawat = ?

                 UNION ALL

                 SELECT rjdp.no_rawat, rjdp.tgl_perawatan, rjdp.jam_rawat, rjdp.kd_jenis_prw, jp.nm_perawatan, rjdp.biaya_rawat AS total_byr,
                        COALESCE(d2.nm_dokter, pt.nama, rjdp.nip) AS petugas, 'Dokter & Paramedis' AS jenis, p2.png_jawab
                 FROM rawat_jl_drpr rjdp
                 LEFT JOIN jns_perawatan jp ON jp.kd_jenis_prw = rjdp.kd_jenis_prw
                 LEFT JOIN penjab p2 ON jp.kd_pj = p2.kd_pj
                 LEFT JOIN dokter d2 ON d2.kd_dokter = rjdp.kd_dokter
                 LEFT JOIN petugas pt ON pt.nip = rjdp.nip
                 WHERE rjdp.no_rawat = ?

                 UNION ALL

                 SELECT rjp.no_rawat, rjp.tgl_perawatan, rjp.jam_rawat, rjp.kd_jenis_prw, jp.nm_perawatan, rjp.biaya_rawat AS total_byr,
                        COALESCE(pt2.nama, rjp.nip) AS petugas, 'Paramedis' AS jenis, p3.png_jawab
                 FROM rawat_jl_pr rjp
                 LEFT JOIN jns_perawatan jp ON jp.kd_jenis_prw = rjp.kd_jenis_prw
                 LEFT JOIN penjab p3 ON jp.kd_pj = p3.kd_pj
                 LEFT JOIN petugas pt2 ON pt2.nip = rjp.nip
                 WHERE rjp.no_rawat = ?

                 ORDER BY tgl_perawatan DESC, jam_rawat DESC
                 LIMIT 200",
                [$noRawat, $noRawat, $noRawat]
            );

            // 8b. Riwayat Tindakan Medis Lampau (Ralan & Ranap) Seluruh Kunjungan
            $riwayatTindakan = DB::select(
                "SELECT rjd.no_rawat, rjd.tgl_perawatan, rjd.jam_rawat, rjd.kd_jenis_prw, jp.nm_perawatan, rjd.biaya_rawat AS total_byr,
                        COALESCE(d.nm_dokter, rjd.kd_dokter) AS petugas, 'Dokter (Ralan)' AS jenis, p.png_jawab, rp.tgl_registrasi
                 FROM rawat_jl_dr rjd
                 JOIN reg_periksa rp ON rjd.no_rawat = rp.no_rawat
                 LEFT JOIN jns_perawatan jp ON jp.kd_jenis_prw = rjd.kd_jenis_prw
                 LEFT JOIN penjab p ON jp.kd_pj = p.kd_pj
                 LEFT JOIN dokter d ON d.kd_dokter = rjd.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT rjdp.no_rawat, rjdp.tgl_perawatan, rjdp.jam_rawat, rjdp.kd_jenis_prw, jp.nm_perawatan, rjdp.biaya_rawat AS total_byr,
                        COALESCE(d2.nm_dokter, pt.nama, rjdp.nip) AS petugas, 'Dr & Paramedis (Ralan)' AS jenis, p2.png_jawab, rp.tgl_registrasi
                 FROM rawat_jl_drpr rjdp
                 JOIN reg_periksa rp ON rjdp.no_rawat = rp.no_rawat
                 LEFT JOIN jns_perawatan jp ON jp.kd_jenis_prw = rjdp.kd_jenis_prw
                 LEFT JOIN penjab p2 ON jp.kd_pj = p2.kd_pj
                 LEFT JOIN dokter d2 ON d2.kd_dokter = rjdp.kd_dokter
                 LEFT JOIN petugas pt ON pt.nip = rjdp.nip
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT rjp.no_rawat, rjp.tgl_perawatan, rjp.jam_rawat, rjp.kd_jenis_prw, jp.nm_perawatan, rjp.biaya_rawat AS total_byr,
                        COALESCE(pt2.nama, rjp.nip) AS petugas, 'Paramedis (Ralan)' AS jenis, p3.png_jawab, rp.tgl_registrasi
                 FROM rawat_jl_pr rjp
                 JOIN reg_periksa rp ON rjp.no_rawat = rp.no_rawat
                 LEFT JOIN jns_perawatan jp ON jp.kd_jenis_prw = rjp.kd_jenis_prw
                 LEFT JOIN penjab p3 ON jp.kd_pj = p3.kd_pj
                 LEFT JOIN petugas pt2 ON pt2.nip = rjp.nip
                 WHERE rp.no_rkm_medis = ?

                 UNION ALL

                 SELECT rid.no_rawat, rid.tgl_perawatan, rid.jam_rawat, rid.kd_jenis_prw, jpi.nm_perawatan, rid.biaya_rawat AS total_byr,
                        COALESCE(di.nm_dokter, rid.kd_dokter) AS petugas, 'Dokter (Ranap)' AS jenis, pi.png_jawab, rp.tgl_registrasi
                 FROM rawat_inap_dr rid
                 JOIN reg_periksa rp ON rid.no_rawat = rp.no_rawat
                 LEFT JOIN jns_perawatan_inap jpi ON jpi.kd_jenis_prw = rid.kd_jenis_prw
                 LEFT JOIN penjab pi ON jpi.kd_pj = pi.kd_pj
                 LEFT JOIN dokter di ON di.kd_dokter = rid.kd_dokter
                 WHERE rp.no_rkm_medis = ?

                 ORDER BY tgl_perawatan DESC, jam_rawat DESC
                 LIMIT 100",
                [$noRkmMedis, $noRkmMedis, $noRkmMedis, $noRkmMedis]
            );

            // 9. Data Resume Pasien Rawat Jalan (resume_pasien)
            $resumePasien = DB::table('resume_pasien')
                ->where('no_rawat', $noRawat)
                ->first();

            $riwayatResume = DB::table('resume_pasien as rp_resume')
                ->join('reg_periksa as rp', 'rp_resume.no_rawat', '=', 'rp.no_rawat')
                ->where('rp.no_rkm_medis', $noRkmMedis)
                ->orderBy('rp.tgl_registrasi', 'DESC')
                ->limit(10)
                ->select('rp_resume.*', 'rp.tgl_registrasi')
                ->get();

            // 9b. Data Resume Rawat Inap (resume_pasien_ranap / Discharge Summary)
            $resumeRanap = DB::table('resume_pasien_ranap')
                ->where('no_rawat', $noRawat)
                ->first();

            $riwayatResumeRanap = DB::table('resume_pasien_ranap as rpr')
                ->join('reg_periksa as rp', 'rpr.no_rawat', '=', 'rp.no_rawat')
                ->where('rp.no_rkm_medis', $noRkmMedis)
                ->orderBy('rp.tgl_registrasi', 'DESC')
                ->limit(10)
                ->select('rpr.*', 'rp.tgl_registrasi')
                ->get();

            return response()->json([
                'success'            => true,
                'pasien'             => $pasien,
                'soapList'           => $soapList,
                'awalMedis'          => $awalMedis,
                'awalMedisList'      => $awalMedisList,
                'keperawatanList'    => $keperawatanList,
                'labOrders'          => $labOrders,
                'labResults'         => $labResults,
                'radOrders'          => $radOrders,
                'radResults'         => $radResults,
                'resepList'          => $resepList,
                'bookingOps'         => $bookingOps,
                'laporanOps'         => $laporanOps,
                'tagihanOperasi'     => $tagihanOperasi,
                'diagnosaList'       => $diagnosaList,
                'riwayatDiagnosa'    => $riwayatDiagnosa,
                'prosedurList'       => $prosedurList,
                'riwayatProsedur'    => $riwayatProsedur,
                'tindakanList'       => $tindakanList,
                'riwayatTindakan'    => $riwayatTindakan,
                'resumePasien'       => $resumePasien,
                'riwayatResume'      => $riwayatResume,
                'resumeRanap'        => $resumeRanap,
                'riwayatResumeRanap' => $riwayatResumeRanap,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 1. SIMPAN SOAP (pemeriksaan_ralan)
     */
    public function simpanSoap(Request $request)
    {
        try {
            $isAdmin  = session('auth_user.is_admin', false);
            $isDokter = session('auth_user.is_dokter', false);
            if (!$isAdmin && !$isDokter) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya dokter dan admin utama yang berhak mengisi atau mengubah SOAP.'], 403);
            }

            $noRawat = $request->input('no_rawat');
            $tglPerawatan = $request->input('tgl_perawatan', now()->toDateString());
            $jamRawat     = $request->input('jam_rawat', now()->toTimeString());
            $nip          = session('auth_user.kode', '-');

            DB::table('pemeriksaan_ralan')->updateOrInsert(
                [
                    'no_rawat'      => $noRawat,
                    'tgl_perawatan' => $tglPerawatan,
                    'jam_rawat'     => $jamRawat,
                ],
                [
                    'suhu_tubuh'    => $request->input('suhu_tubuh', ''),
                    'tensi'         => $request->input('tensi', ''),
                    'nadi'          => $request->input('nadi', ''),
                    'respirasi'     => $request->input('respirasi', ''),
                    'tinggi'        => $request->input('tinggi', ''),
                    'berat'         => $request->input('berat', ''),
                    'spo2'          => $request->input('spo2', ''),
                    'gcs'           => $request->input('gcs', ''),
                    'kesadaran'     => $request->input('kesadaran', 'Compos Mentis'),
                    'keluhan'       => $request->input('keluhan', ''),
                    'pemeriksaan'   => $request->input('pemeriksaan', ''),
                    'penilaian'     => $request->input('penilaian', ''),
                    'rtl'           => $request->input('rtl', ''),
                    'instruksi'     => $request->input('instruksi', ''),
                    'evaluasi'      => $request->input('evaluasi', ''),
                    'alergi'        => $request->input('alergi', ''),
                    'lingkar_perut' => $request->input('lingkar_perut', ''),
                    'nip'           => $nip,
                ]
            );

            return response()->json(['success' => true, 'message' => 'Data SOAP berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function hapusSoap(Request $request)
    {
        try {
            $isAdmin  = session('auth_user.is_admin', false);
            $isDokter = session('auth_user.is_dokter', false);
            if (!$isAdmin && !$isDokter) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya dokter dan admin utama yang berhak menghapus data SOAP.'], 403);
            }

            $noRawat      = $request->input('no_rawat');
            $tglPerawatan = $request->input('tgl_perawatan');
            $jamRawat     = $request->input('jam_rawat');

            if (!$noRawat || !$tglPerawatan || !$jamRawat) {
                return response()->json(['success' => false, 'message' => 'Parameter no_rawat, tgl_perawatan, dan jam_rawat diperlukan.'], 400);
            }

            DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $noRawat)
                ->where('tgl_perawatan', $tglPerawatan)
                ->where('jam_rawat', $jamRawat)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Data SOAP berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 2. SIMPAN AWAL MEDIS (penilaian_medis_ralan)
     */
    public function simpanAwalMedis(Request $request)
    {
        try {
            $noRawat  = $request->input('no_rawat');
            $kdDokter = session('auth_user.kode', '');

            DB::table('penilaian_medis_ralan')->updateOrInsert(
                ['no_rawat' => $noRawat],
                [
                    'tanggal'       => now()->toDateTimeString(),
                    'kd_dokter'     => $kdDokter,
                    'anamnesis'     => $request->input('anamnesis', 'Autoanamnesis'),
                    'hubungan'      => $request->input('hubungan', ''),
                    'keluhan_utama' => $request->input('keluhan_utama', ''),
                    'rps'           => $request->input('rps', ''),
                    'rpd'           => $request->input('rpd', ''),
                    'rpk'           => $request->input('rpk', ''),
                    'rpo'           => $request->input('rpo', ''),
                    'alergi'        => $request->input('alergi', ''),
                    'keadaan'       => $request->input('keadaan', 'Sehat'),
                    'gcs'           => $request->input('gcs', '15'),
                    'kesadaran'     => $request->input('kesadaran', 'Compos Mentis'),
                    'td'            => $request->input('td', ''),
                    'nadi'          => $request->input('nadi', ''),
                    'rr'            => $request->input('rr', ''),
                    'suhu'          => $request->input('suhu', ''),
                    'spo'           => $request->input('spo', ''),
                    'bb'            => $request->input('bb', ''),
                    'tb'            => $request->input('tb', ''),
                    'kepala'        => $request->input('kepala', 'Normal'),
                    'gigi'          => $request->input('gigi', 'Normal'),
                    'tht'           => $request->input('tht', 'Normal'),
                    'thoraks'       => $request->input('thoraks', 'Normal'),
                    'abdomen'       => $request->input('abdomen', 'Normal'),
                    'genital'       => $request->input('genital', 'Normal'),
                    'ekstremitas'   => $request->input('ekstremitas', 'Normal'),
                    'kulit'         => $request->input('kulit', 'Normal'),
                    'ket_fisik'     => $request->input('ket_fisik', ''),
                    'ket_lokalis'   => $request->input('ket_lokalis', ''),
                    'penunjang'     => $request->input('penunjang', ''),
                    'diagnosis'     => $request->input('diagnosis', ''),
                    'tata'          => $request->input('tata', ''),
                    'konsulrujuk'   => $request->input('konsulrujuk', ''),
                ]
            );

            return response()->json(['success' => true, 'message' => 'Penilaian Awal Medis berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 3. MASTER & SIMPAN LABORATORIUM
     */
    public function masterLab(Request $request)
    {
        $q       = $request->input('q', '');
        $noRawat = $request->input('no_rawat', '');
        $kdPj    = $request->input('kd_pj', '');

        if (!empty($noRawat) && empty($kdPj)) {
            $kdPj = DB::table('reg_periksa')->where('no_rawat', $noRawat)->value('kd_pj');
        }

        $query = DB::table('jns_perawatan_lab as j')
            ->leftJoin('penjab as p', 'j.kd_pj', '=', 'p.kd_pj')
            ->where('j.status', '1');

        if (!empty($kdPj)) {
            $query->where(function ($w) use ($kdPj) {
                $w->where('j.kd_pj', $kdPj)
                  ->orWhere('j.kd_pj', '-')
                  ->orWhereNull('j.kd_pj');
            });
        }

        if (!empty($q)) {
            $query->where('j.nm_perawatan', 'LIKE', "%{$q}%");
        }

        $data = $query->select(
            'j.kd_jenis_prw',
            'j.nm_perawatan',
            'j.total_byr',
            'j.kd_pj',
            'p.png_jawab',
            'j.kelas'
        )->orderBy('j.nm_perawatan', 'asc')->limit(50)->get();

        return response()->json($data);
    }

    public function simpanLab(Request $request)
    {
        try {
            $noRawat      = $request->input('no_rawat');
            $items        = $request->input('items', []);
            $kdDokter     = session('auth_user.kode', '-');
            $tglPermintaan = now()->toDateString();
            $jamPermintaan = now()->toTimeString();

            if (empty($items)) {
                return response()->json(['success' => false, 'message' => 'Pilih minimal 1 jenis pemeriksaan Lab.'], 400);
            }

            // Generate No Order: PLYYYYMMDDxxxx
            $prefix = 'PL' . date('Ymd');
            $lastOrder = DB::table('permintaan_lab')->where('noorder', 'LIKE', "{$prefix}%")->max('noorder');
            $lastNum = $lastOrder ? (int) substr($lastOrder, -4) : 0;
            $noOrder = $prefix . sprintf('%04d', $lastNum + 1);

            DB::table('permintaan_lab')->insert([
                'noorder'           => $noOrder,
                'no_rawat'          => $noRawat,
                'tgl_permintaan'    => $tglPermintaan,
                'jam_permintaan'    => $jamPermintaan,
                'tgl_sampel'        => $tglPermintaan,
                'jam_sampel'        => $jamPermintaan,
                'tgl_hasil'         => $tglPermintaan,
                'jam_hasil'         => $jamPermintaan,
                'dokter_perujuk'    => $kdDokter,
                'status'            => 'ralan',
                'informasi_tambahan'=> $request->input('informasi_tambahan', ''),
                'diagnosa_klinis'   => $request->input('diagnosa_klinis', ''),
            ]);

            foreach ($items as $kdJenis) {
                DB::table('permintaan_detail_permintaan_lab')->insert([
                    'noorder'      => $noOrder,
                    'kd_jenis_prw' => $kdJenis,
                    'id_template'  => 0,
                    'stts_bayar'   => 'Belum',
                ]);
            }

            return response()->json(['success' => true, 'message' => "Permintaan Lab ({$noOrder}) berhasil dikirim."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 4. MASTER & SIMPAN RADIOLOGI
     */
    public function masterRadiologi(Request $request)
    {
        $q       = $request->input('q', '');
        $noRawat = $request->input('no_rawat', '');
        $kdPj    = $request->input('kd_pj', '');

        if (!empty($noRawat) && empty($kdPj)) {
            $kdPj = DB::table('reg_periksa')->where('no_rawat', $noRawat)->value('kd_pj');
        }

        $query = DB::table('jns_perawatan_radiologi as j')
            ->leftJoin('penjab as p', 'j.kd_pj', '=', 'p.kd_pj')
            ->where('j.status', '1');

        if (!empty($kdPj)) {
            $query->where(function ($w) use ($kdPj) {
                $w->where('j.kd_pj', $kdPj)
                  ->orWhere('j.kd_pj', '-')
                  ->orWhereNull('j.kd_pj');
            });
        }

        if (!empty($q)) {
            $query->where('j.nm_perawatan', 'LIKE', "%{$q}%");
        }

        $data = $query->select(
            'j.kd_jenis_prw',
            'j.nm_perawatan',
            'j.total_byr',
            'j.kd_pj',
            'p.png_jawab',
            'j.kelas'
        )->orderBy('j.nm_perawatan', 'asc')->limit(50)->get();

        return response()->json($data);
    }

    public function simpanRadiologi(Request $request)
    {
        try {
            $noRawat      = $request->input('no_rawat');
            $items        = $request->input('items', []);
            $kdDokter     = session('auth_user.kode', '-');
            $tglPermintaan = now()->toDateString();
            $jamPermintaan = now()->toTimeString();

            if (empty($items)) {
                return response()->json(['success' => false, 'message' => 'Pilih minimal 1 jenis pemeriksaan Radiologi.'], 400);
            }

            // Generate No Order: PRYYYYMMDDxxxx
            $prefix = 'PR' . date('Ymd');
            $lastOrder = DB::table('permintaan_radiologi')->where('noorder', 'LIKE', "{$prefix}%")->max('noorder');
            $lastNum = $lastOrder ? (int) substr($lastOrder, -4) : 0;
            $noOrder = $prefix . sprintf('%04d', $lastNum + 1);

            DB::table('permintaan_radiologi')->insert([
                'noorder'           => $noOrder,
                'no_rawat'          => $noRawat,
                'tgl_permintaan'    => $tglPermintaan,
                'jam_permintaan'    => $jamPermintaan,
                'tgl_sampel'        => $tglPermintaan,
                'jam_sampel'        => $jamPermintaan,
                'tgl_hasil'         => $tglPermintaan,
                'jam_hasil'         => $jamPermintaan,
                'dokter_perujuk'    => $kdDokter,
                'status'            => 'ralan',
                'informasi_tambahan'=> $request->input('informasi_tambahan', ''),
                'diagnosa_klinis'   => $request->input('diagnosa_klinis', ''),
            ]);

            foreach ($items as $kdJenis) {
                DB::table('permintaan_pemeriksaan_radiologi')->insert([
                    'noorder'      => $noOrder,
                    'kd_jenis_prw' => $kdJenis,
                    'stts_bayar'   => 'Belum',
                ]);
            }

            return response()->json(['success' => true, 'message' => "Permintaan Radiologi ({$noOrder}) berhasil dikirim."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 5. MASTER & SIMPAN RESEP DOKTER
     */
    public function masterObat(Request $request)
    {
        $q         = $request->input('q', '');
        $hanyaStok = filter_var($request->input('hanya_stok', false), FILTER_VALIDATE_BOOLEAN);
        $kategori  = $request->input('kategori', '');
        
        // Menggunakan 1 depo obat yaitu DEPO FARMASI RALAN (G002)
        $kdDepo = 'G002';
        $lokasi = DB::table('set_lokasi')->first();
        if ($lokasi && !empty($lokasi->kd_bangsal)) {
            $kdDepo = $lokasi->kd_bangsal;
        }

        $query = DB::table('databarang as db')
            ->leftJoin('gudangbarang as gb', function ($join) use ($kdDepo) {
                $join->on('db.kode_brng', '=', 'gb.kode_brng')
                     ->where('gb.kd_bangsal', '=', $kdDepo);
            })
            ->leftJoin('kategori_barang as k', 'db.kode_kategori', '=', 'k.kode')
            ->leftJoin('jenis as j', 'db.kdjns', '=', 'j.kdjns')
            ->where('db.status', '1');

        if (!empty($q)) {
            $query->where(function ($w) use ($q) {
                $w->where('db.nama_brng', 'LIKE', "%{$q}%")
                  ->orWhere('db.kode_brng', 'LIKE', "%{$q}%")
                  ->orWhere('k.nama', 'LIKE', "%{$q}%")
                  ->orWhere('j.nama', 'LIKE', "%{$q}%");
            });
        }

        if (!empty($kategori)) {
            $query->where('db.kode_kategori', $kategori);
        }

        if ($hanyaStok) {
            $query->where('gb.stok', '>', 0);
        }

        $data = $query->select(
            'db.kode_brng',
            'db.nama_brng',
            'db.kode_sat',
            'db.ralan as harga',
            'db.kapasitas',
            DB::raw('COALESCE(gb.stok, 0) as stok'),
            DB::raw('COALESCE(gb.stok, 0) as stok_depo'),
            DB::raw("COALESCE(k.nama, '-') as kategori"),
            DB::raw("COALESCE(j.nama, '-') as jenis")
        )->orderBy('db.nama_brng', 'asc')->get();

        return response()->json($data);
    }

    public function masterAturanPakai(?Request $request = null)
    {
        $q = $request ? $request->input('q', '') : '';
        $data = DB::table('master_aturan_pakai')
            ->where('aturan', 'LIKE', "%{$q}%")
            ->orderBy('aturan', 'ASC')
            ->limit(35)
            ->pluck('aturan');
        return response()->json($data);
    }

    public function simpanResep(Request $request)
    {
        try {
            $isAdmin  = session('auth_user.is_admin', false);
            $isDokter = session('auth_user.is_dokter', false);
            if (!$isAdmin && !$isDokter) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya dokter dan admin utama yang berhak membuat atau mengubah resep obat.'], 403);
            }

            $noRawat     = $request->input('no_rawat');
            $noResepEdit = $request->input('no_resep'); // Jika ada, ini adalah mode EDIT
            $items       = $request->input('items', []);   // Obat Jadi: [{kode_brng, jml, aturan_pakai, keterangan}]
            $racikan     = $request->input('racikan', []); // Obat Racik: [{nama_racik, kd_racik, jml_dr, aturan_pakai, keterangan, detail: [{kode_brng, p1, p2, kandungan, jml}]}]
            $kdDokter    = session('auth_user.kode', '-');
            $tglResep    = now()->toDateString();
            $jamResep    = now()->toTimeString();

            if (empty($items) && empty($racikan)) {
                return response()->json(['success' => false, 'message' => 'Pilih minimal 1 obat jadi atau racikan untuk resep.'], 400);
            }

            // === 1. VALIDASI KETAT OBAT JADI (NON-RACIK) ===
            foreach ($items as $idx => $item) {
                $kodeBrng = $item['kode_brng'] ?? '';
                $jml = isset($item['jml']) ? (float) $item['jml'] : 0;
                $aturanPakai = trim($item['aturan_pakai'] ?? '');

                if (empty($kodeBrng)) {
                    continue;
                }

                $brng = DB::table('databarang')->where('kode_brng', $kodeBrng)->first();
                $namaBrng = $brng ? $brng->nama_brng : ($item['nama'] ?? $kodeBrng);

                if ($jml <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Jumlah untuk obat '{$namaBrng}' harus lebih dari 0."
                    ], 422);
                }

                if (empty($aturanPakai) || $aturanPakai === '-') {
                    return response()->json([
                        'success' => false,
                        'message' => "Aturan pakai untuk obat '{$namaBrng}' wajib diisi."
                    ], 422);
                }

                // Cek stok riil Depo Farmasi Ralan (G002)
                $stokRow = DB::table('gudangbarang')
                    ->where('kode_brng', $kodeBrng)
                    ->where('kd_bangsal', 'G002')
                    ->first();
                $currentStok = $stokRow ? (float) $stokRow->stok : 0;

                if ($currentStok <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok obat '{$namaBrng}' habis/kosong di Depo Farmasi Ralan (G002) sehingga tidak dapat diresepkan."
                    ], 422);
                }

                if ($jml > $currentStok) {
                    return response()->json([
                        'success' => false,
                        'message' => "Jumlah permintaan obat '{$namaBrng}' ({$jml}) melebihi stok yang tersedia di Depo Farmasi Ralan ({$currentStok})."
                    ], 422);
                }
            }

            // === 2. VALIDASI KETAT OBAT RACIKAN ===
            foreach ($racikan as $rIdx => $racik) {
                $namaRacik = trim($racik['nama_racik'] ?? '');
                $jmlDr = isset($racik['jml_dr']) ? (int) $racik['jml_dr'] : 0;
                $aturanPakai = trim($racik['aturan_pakai'] ?? '');
                $detail = $racik['detail'] ?? [];

                if (empty($namaRacik)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Nama racikan ke-" . ($rIdx + 1) . " wajib diisi."
                    ], 422);
                }

                if ($jmlDr <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Jumlah kemasan untuk racikan '{$namaRacik}' harus minimal 1."
                    ], 422);
                }

                if (empty($aturanPakai) || $aturanPakai === '-') {
                    return response()->json([
                        'success' => false,
                        'message' => "Aturan pakai untuk racikan '{$namaRacik}' wajib diisi."
                    ], 422);
                }

                if (empty($detail)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Racikan '{$namaRacik}' belum memiliki bahan obat. Masukkan minimal 1 bahan racik."
                    ], 422);
                }

                foreach ($detail as $dIdx => $det) {
                    $kodeBrng = $det['kode_brng'] ?? '';
                    $jmlBahan = isset($det['jml']) ? (float) $det['jml'] : 0;

                    if (empty($kodeBrng)) {
                        continue;
                    }

                    $brng = DB::table('databarang')->where('kode_brng', $kodeBrng)->first();
                    $namaBrng = $brng ? $brng->nama_brng : ($det['nama_brng'] ?? $kodeBrng);

                    if ($jmlBahan <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => "Jumlah kebutuhan bahan '{$namaBrng}' pada racikan '{$namaRacik}' harus lebih dari 0."
                        ], 422);
                    }

                    // Cek stok riil Depo Farmasi Ralan (G002)
                    $stokRow = DB::table('gudangbarang')
                        ->where('kode_brng', $kodeBrng)
                        ->where('kd_bangsal', 'G002')
                        ->first();
                    $currentStok = $stokRow ? (float) $stokRow->stok : 0;

                    if ($currentStok <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => "Bahan obat '{$namaBrng}' pada racikan '{$namaRacik}' habis/kosong di Depo Farmasi Ralan (G002)."
                        ], 422);
                    }

                    if ($jmlBahan > $currentStok) {
                        return response()->json([
                            'success' => false,
                            'message' => "Kebutuhan bahan racik '{$namaBrng}' ({$jmlBahan}) pada racikan '{$namaRacik}' melebihi stok yang tersedia di Depo Farmasi Ralan ({$currentStok})."
                        ], 422);
                    }
                }
            }

            if (!empty($noResepEdit)) {
                // === MODE EDIT RESEP ===
                $existing = DB::table('resep_obat')->where('no_resep', $noResepEdit)->first();
                if (!$existing) {
                    return response()->json(['success' => false, 'message' => 'Resep obat tidak ditemukan.'], 404);
                }
                if ($existing->tgl_penyerahan && $existing->tgl_penyerahan != '0000-00-00') {
                    return response()->json(['success' => false, 'message' => 'Resep ini sudah diserahkan / divalidasi oleh Farmasi dan tidak dapat diubah lagi.'], 400);
                }

                $noResep = $noResepEdit;

                // Hapus data item & racikan lama
                DB::table('resep_dokter_racikan_detail')->where('no_resep', $noResep)->delete();
                DB::table('resep_dokter_racikan')->where('no_resep', $noResep)->delete();
                DB::table('resep_dokter')->where('no_resep', $noResep)->delete();

                // Perbarui resep_obat
                DB::table('resep_obat')->where('no_resep', $noResep)->update([
                    'kd_dokter'     => $kdDokter,
                    'tgl_peresepan' => $tglResep,
                    'jam_peresepan' => $jamResep,
                ]);
            } else {
                // === MODE RESEP BARU ===
                $prefix = date('Ymd');
                $lastResep = DB::table('resep_obat')->where('no_resep', 'LIKE', "{$prefix}%")->max('no_resep');
                $lastNum = $lastResep ? (int) substr($lastResep, -4) : 0;
                $noResep = $prefix . sprintf('%04d', $lastNum + 1);

                DB::table('resep_obat')->insert([
                    'no_resep'       => $noResep,
                    'tgl_perawatan'  => $tglResep,
                    'jam'            => $jamResep,
                    'no_rawat'       => $noRawat,
                    'kd_dokter'      => $kdDokter,
                    'tgl_peresepan'  => $tglResep,
                    'jam_peresepan'  => $jamResep,
                    'status'         => 'ralan',
                    'tgl_penyerahan' => '0000-00-00',
                    'jam_penyerahan' => '00:00:00',
                ]);
            }

            // Simpan Obat Jadi (resep_dokter)
            foreach ($items as $item) {
                if (!empty($item['kode_brng']) && !empty($item['jml'])) {
                    DB::table('resep_dokter')->insert([
                        'no_resep'     => $noResep,
                        'kode_brng'    => $item['kode_brng'],
                        'jml'          => $item['jml'],
                        'aturan_pakai' => $item['aturan_pakai'] ?? '-',
                        'keterangan'   => $item['keterangan'] ?? '',
                    ]);
                }
            }

            // Simpan Obat Racikan (resep_dokter_racikan & detail)
            foreach ($racikan as $idx => $racik) {
                $noRacik = (string) ($racik['no_racik'] ?? ($idx + 1));
                DB::table('resep_dokter_racikan')->insert([
                    'no_resep'     => $noResep,
                    'no_racik'     => $noRacik,
                    'nama_racik'   => $racik['nama_racik'] ?? ('Racikan ' . ($idx + 1)),
                    'kd_racik'     => $racik['kd_racik'] ?? 'R01',
                    'jml_dr'       => $racik['jml_dr'] ?? 10,
                    'aturan_pakai' => $racik['aturan_pakai'] ?? '-',
                    'keterangan'   => $racik['keterangan'] ?? '',
                ]);

                foreach ($racik['detail'] ?? [] as $det) {
                    if (!empty($det['kode_brng'])) {
                        DB::table('resep_dokter_racikan_detail')->insert([
                            'no_resep'  => $noResep,
                            'no_racik'  => $noRacik,
                            'kode_brng' => $det['kode_brng'],
                            'p1'        => $det['p1'] ?? 1,
                            'p2'        => $det['p2'] ?? 1,
                            'kandungan' => $det['kandungan'] ?? '-',
                            'jml'       => $det['jml'] ?? 1,
                        ]);
                    }
                }
            }

            $actionText = !empty($noResepEdit) ? 'diperbarui' : 'disimpan & dikirim ke Farmasi';
            return response()->json([
                'success'  => true,
                'no_resep' => $noResep,
                'message'  => "E-Resep Dokter ({$noResep}) berhasil {$actionText}."
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function hapusResep(Request $request)
    {
        try {
            $isAdmin  = session('auth_user.is_admin', false);
            $isDokter = session('auth_user.is_dokter', false);
            if (!$isAdmin && !$isDokter) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya dokter dan admin utama yang berhak membatalkan resep obat.'], 403);
            }

            $noResep = $request->input('no_resep');
            $noRawat = $request->input('no_rawat');

            $resep = DB::table('resep_obat')
                ->where('no_resep', $noResep)
                ->where('no_rawat', $noRawat)
                ->first();

            if (!$resep) {
                return response()->json(['success' => false, 'message' => 'Resep obat tidak ditemukan.'], 404);
            }

            if ($resep->tgl_penyerahan && $resep->tgl_penyerahan != '0000-00-00') {
                return response()->json(['success' => false, 'message' => 'Resep ini sudah diserahkan / divalidasi oleh Farmasi dan tidak dapat dihapus.'], 400);
            }

            DB::table('resep_dokter_racikan_detail')->where('no_resep', $noResep)->delete();
            DB::table('resep_dokter_racikan')->where('no_resep', $noResep)->delete();
            DB::table('resep_dokter')->where('no_resep', $noResep)->delete();
            DB::table('resep_obat')->where('no_resep', $noResep)->delete();

            return response()->json(['success' => true, 'message' => "Resep ({$noResep}) berhasil dibatalkan/dihapus."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 6. MASTER & SIMPAN JADWAL / BOOKING OPERASI & LAPORAN OPERASI
     */
    public function masterOperasi(Request $request)
    {
        $q = $request->input('q', '');
        $kategori = $request->input('kategori', '');
        $kelas = $request->input('kelas', '');

        $query = DB::table('paket_operasi as po')
            ->leftJoin('penjab as p', 'po.kd_pj', '=', 'p.kd_pj')
            ->where('po.status', '1');

        if (!empty($q)) {
            $query->where(function ($w) use ($q) {
                $w->where('po.nm_perawatan', 'LIKE', "%{$q}%")
                  ->orWhere('po.kode_paket', 'LIKE', "%{$q}%");
            });
        }

        if (!empty($kategori)) {
            $query->where('po.kategori', $kategori);
        }

        if (!empty($kelas)) {
            $query->where(function ($w) use ($kelas) {
                $w->where('po.kelas', $kelas)->orWhere('po.kelas', '-');
            });
        }

        $data = $query->select(
            'po.kode_paket',
            'po.nm_perawatan',
            'po.kategori',
            'po.kelas',
            'po.operator1',
            'p.png_jawab',
            DB::raw('(po.operator1 + po.operator2 + po.operator3 +
                      po.asisten_operator1 + po.asisten_operator2 + po.asisten_operator3 +
                      po.instrumen + po.dokter_anak + po.perawaat_resusitas +
                      po.alat + po.dokter_anestesi + po.asisten_anestesi + po.asisten_anestesi2 +
                      po.bidan + po.bidan2 + po.bidan3 + po.perawat_luar + po.sewa_ok +
                      po.akomodasi + po.bagian_rs + po.omloop + po.omloop2 + po.omloop3 +
                      po.omloop4 + po.omloop5 + po.sarpras + po.dokter_pjanak + po.dokter_umum) AS total_tarif')
        )
        ->orderBy('po.nm_perawatan', 'asc')
        ->limit(100)
        ->get();

        return response()->json($data);
    }

    public function masterDokter(Request $request)
    {
        $q = $request->input('q', '');
        $data = DB::select(
            "SELECT kd_dokter, nm_dokter FROM dokter
             WHERE status = '1' AND (nm_dokter LIKE ? OR kd_dokter LIKE ?)
             ORDER BY nm_dokter ASC LIMIT 50",
            ["%{$q}%", "%{$q}%"]
        );
        return response()->json($data);
    }

    public function masterRuangOk(Request $request)
    {
        $data = DB::table('ruang_ok')
            ->select('kd_ruang_ok', 'nm_ruang_ok')
            ->orderBy('kd_ruang_ok', 'asc')
            ->get();
        return response()->json($data);
    }

    public function masterTemplateLaporanOperasi(Request $request)
    {
        $q = $request->input('q', '');
        $data = DB::table('template_laporan_operasi')
            ->where(function ($w) use ($q) {
                if (!empty($q)) {
                    $w->where('nama_operasi', 'LIKE', "%{$q}%")
                      ->orWhere('no_template', 'LIKE', "%{$q}%")
                      ->orWhere('diagnosa_preop', 'LIKE', "%{$q}%")
                      ->orWhere('diagnosa_postop', 'LIKE', "%{$q}%");
                }
            })
            ->select('no_template', 'nama_operasi', 'diagnosa_preop', 'diagnosa_postop', 'jaringan_dieksisi', 'permintaan_pa', 'laporan_operasi')
            ->orderBy('nama_operasi', 'asc')
            ->limit(50)
            ->get();
        return response()->json($data);
    }

    public function simpanBookingOperasi(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menjadwalkan operasi.'], 403);
            }

            $noRawat    = $request->input('no_rawat');
            $kodePaket  = $request->input('kode_paket');
            $tanggal    = $request->input('tanggal', now()->toDateString());
            $jamMulai   = $request->input('jam_mulai', '08:00:00');
            $jamSelesai = $request->input('jam_selesai', '09:30:00');
            $kdDokter   = $request->input('kd_dokter') ?: session('auth_user.kode', '');
            $kdRuangOk  = $request->input('kd_ruang_ok') ?: 'O1';
            $status     = $request->input('status', 'Menunggu');

            if (empty($kodePaket)) {
                return response()->json(['success' => false, 'message' => 'Pilih paket operasi terlebih dahulu.'], 400);
            }

            if (strlen($jamMulai) == 5) $jamMulai .= ':00';
            if (strlen($jamSelesai) == 5) $jamSelesai .= ':00';

            if (!in_array($status, ['Menunggu', 'Proses Operasi', 'Selesai'])) {
                $status = 'Menunggu';
            }

            // Pengecekan Bentrok Jadwal Operasi (Collision Check) persis Khanza DlgBookingOperasi.java
            $bentrok = DB::table('booking_operasi')
                ->where('tanggal', $tanggal)
                ->where('kd_ruang_ok', $kdRuangOk)
                ->where('no_rawat', '<>', $noRawat)
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                      ->orWhere(function ($sub) use ($jamMulai, $jamSelesai) {
                          $sub->where('jam_mulai', '<=', $jamMulai)
                              ->where('jam_selesai', '>=', $jamMulai);
                      });
                })
                ->count();

            if ($bentrok > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Jadwal bentrok dengan jam mulai operasi yang lain di Kamar Bedah ({$kdRuangOk})! Silakan pilih jam atau kamar OK lain."
                ], 400);
            }

            $oldKodePaket = $request->input('old_kode_paket');
            $oldTanggal   = $request->input('old_tanggal');
            $oldJamMulai  = $request->input('old_jam_mulai');

            if (!empty($oldKodePaket) && !empty($oldTanggal)) {
                if (strlen($oldJamMulai) == 5) $oldJamMulai .= ':00';
                $updateQuery = DB::table('booking_operasi')
                    ->where('no_rawat', $noRawat)
                    ->where('kode_paket', $oldKodePaket)
                    ->where('tanggal', $oldTanggal);
                if (!empty($oldJamMulai)) {
                    $updateQuery->where('jam_mulai', $oldJamMulai);
                }
                $updateQuery->update([
                    'kode_paket'  => $kodePaket,
                    'tanggal'     => $tanggal,
                    'jam_mulai'   => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'status'      => $status,
                    'kd_dokter'   => $kdDokter,
                    'kd_ruang_ok' => $kdRuangOk,
                ]);
                $msg = 'Jadwal Booking Operasi berhasil diperbarui.';
            } else {
                $existing = DB::table('booking_operasi')
                    ->where('no_rawat', $noRawat)
                    ->where('kode_paket', $kodePaket)
                    ->where('tanggal', $tanggal)
                    ->first();

                if ($existing) {
                    DB::table('booking_operasi')
                        ->where('no_rawat', $noRawat)
                        ->where('kode_paket', $kodePaket)
                        ->where('tanggal', $tanggal)
                        ->update([
                            'jam_mulai'   => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                            'status'      => $status,
                            'kd_dokter'   => $kdDokter,
                            'kd_ruang_ok' => $kdRuangOk,
                        ]);
                    $msg = 'Jadwal Booking Operasi berhasil diperbarui.';
                } else {
                    DB::table('booking_operasi')->insert([
                        'no_rawat'    => $noRawat,
                        'kode_paket'  => $kodePaket,
                        'tanggal'     => $tanggal,
                        'jam_mulai'   => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                        'status'      => $status,
                        'kd_dokter'   => $kdDokter,
                        'kd_ruang_ok' => $kdRuangOk,
                    ]);
                    $msg = 'Jadwal Booking Operasi berhasil disimpan.';
                }
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function hapusBookingOperasi(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menghapus booking operasi.'], 403);
            }

            $noRawat   = $request->input('no_rawat');
            $kodePaket = $request->input('kode_paket');
            $tanggal   = $request->input('tanggal');

            if (!$noRawat || !$kodePaket || !$tanggal) {
                return response()->json(['success' => false, 'message' => 'Parameter no_rawat, kode_paket, dan tanggal wajib disertakan.'], 400);
            }

            DB::table('booking_operasi')
                ->where('no_rawat', $noRawat)
                ->where('kode_paket', $kodePaket)
                ->where('tanggal', $tanggal)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Jadwal Booking Operasi berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function simpanLaporanOperasi(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menyimpan Laporan Operasi.'], 403);
            }

            $noRawat = $request->input('no_rawat');
            if (empty($noRawat)) {
                return response()->json(['success' => false, 'message' => 'No. Rawat tidak valid.'], 400);
            }

            $tanggalLaporan    = $request->input('tanggal') ?: now()->toDateTimeString();
            $tglMulaiOperasi   = $request->input('tgl_operasi') ?: $tanggalLaporan;
            $tglSelesaiOperasi = $request->input('selesaioperasi') ?: $tanggalLaporan;

            $diagnosaPreop     = $request->input('diagnosa_preop', '-');
            $diagnosaPostop    = $request->input('diagnosa_postop', '-');
            $jaringanDieksekusi= $request->input('jaringan_dieksekusi', '-');
            $permintaanPa      = $request->input('permintaan_pa', 'Tidak');
            $jenisAnasthesi    = $request->input('jenis_anasthesi', '-');
            $kategori          = $request->input('kategori', '-');
            $laporanOperasi    = $request->input('laporan_operasi', '');

            if (empty($laporanOperasi)) {
                return response()->json(['success' => false, 'message' => 'Uraian Laporan Operasi wajib diisi.'], 400);
            }

            $validKategori = ['-', 'Khusus', 'Besar', 'Sedang', 'Kecil', 'Elektive', 'Emergency'];
            if (!in_array($kategori, $validKategori)) {
                $kategori = '-';
            }

            $permintaanPa = ($permintaanPa === 'Ya') ? 'Ya' : 'Tidak';

            $oldTanggal = $request->input('old_tanggal');
            if (!empty($oldTanggal)) {
                DB::table('laporan_operasi')
                    ->where('no_rawat', $noRawat)
                    ->where('tanggal', $oldTanggal)
                    ->delete();
            } else {
                DB::table('laporan_operasi')
                    ->where('no_rawat', $noRawat)
                    ->where('tanggal', $tanggalLaporan)
                    ->delete();
            }

            DB::table('laporan_operasi')->insert([
                'no_rawat'            => $noRawat,
                'tanggal'             => $tanggalLaporan,
                'diagnosa_preop'      => $diagnosaPreop,
                'diagnosa_postop'     => $diagnosaPostop,
                'jaringan_dieksekusi' => $jaringanDieksekusi,
                'selesaioperasi'      => $tglSelesaiOperasi,
                'permintaan_pa'       => $permintaanPa,
                'laporan_operasi'     => $laporanOperasi,
                'tgl_operasi'         => $tglMulaiOperasi,
                'jenis_anasthesi'     => $jenisAnasthesi,
                'kategori'            => $kategori,
            ]);

            return response()->json(['success' => true, 'message' => 'Laporan Operasi berhasil disimpan sesuai standar SIMRS.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function hapusLaporanOperasi(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menghapus Laporan Operasi.'], 403);
            }

            $noRawat = $request->input('no_rawat');
            $tanggal = $request->input('tanggal');

            if (!$noRawat || !$tanggal) {
                return response()->json(['success' => false, 'message' => 'No. Rawat dan tanggal laporan wajib disertakan.'], 400);
            }

            DB::table('laporan_operasi')
                ->where('no_rawat', $noRawat)
                ->where('tanggal', $tanggal)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Laporan Operasi berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ===================================================================
    // DIAGNOSA ICD-10 RAWAT JALAN
    // ===================================================================

    /** Cari diagnosa ICD-10 */
    public function masterDiagnosa(Request $request)
    {
        $q    = $request->input('q', '');
        $data = DB::select(
            "SELECT kd_penyakit, nm_penyakit FROM penyakit
             WHERE (kd_penyakit LIKE ? OR nm_penyakit LIKE ?)
               AND length(kd_penyakit) > 2
             ORDER BY kd_penyakit ASC LIMIT 30",
            ["%{$q}%", "%{$q}%"]
        );
        return response()->json($data);
    }

    /** Simpan diagnosa ICD-10 Rajal */
    public function simpanDiagnosa(Request $request)
    {
        try {
            $noRawat    = $request->input('no_rawat');
            $kdPenyakit = $request->input('kd_penyakit');
            $prioritas  = $request->input('prioritas', 1);

            if (empty($kdPenyakit)) {
                return response()->json(['success' => false, 'message' => 'Kode penyakit harus diisi.'], 400);
            }

            $exists = DB::table('diagnosa_pasien')
                ->where('no_rawat', $noRawat)
                ->where('kd_penyakit', $kdPenyakit)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Diagnosa sudah ada dalam daftar.'], 400);
            }

            DB::table('diagnosa_pasien')->insert([
                'no_rawat'        => $noRawat,
                'kd_penyakit'     => $kdPenyakit,
                'prioritas'       => $prioritas,
                'status'          => 'Ralan',
                'status_penyakit' => 'Baru',
            ]);

            $penyakit = DB::selectOne("SELECT nm_penyakit FROM penyakit WHERE kd_penyakit = ?", [$kdPenyakit]);

            return response()->json([
                'success'     => true,
                'message'     => "Diagnosa {$kdPenyakit} berhasil ditambahkan.",
                'nm_penyakit' => $penyakit?->nm_penyakit ?? $kdPenyakit,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Hapus diagnosa Rajal */
    public function hapusDiagnosa(Request $request)
    {
        try {
            $isAdmin  = session('auth_user.is_admin', false);
            $isDokter = session('auth_user.is_dokter', false);
            if (!$isAdmin && !$isDokter) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya dokter dan admin utama yang berhak menghapus diagnosa.'], 403);
            }

            $noRawat    = $request->input('no_rawat');
            $kdPenyakit = $request->input('kd_penyakit');

            DB::table('diagnosa_pasien')
                ->where('no_rawat', $noRawat)
                ->where('kd_penyakit', $kdPenyakit)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Diagnosa berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ===================================================================
    // TINDAKAN RAWAT JALAN
    // ===================================================================

    /** Cari tindakan Rajal (jns_perawatan) - disesuaikan dengan penjamin pasien */
    public function masterTindakan(Request $request)
    {
        $q       = $request->input('q', '');
        $noRawat = $request->input('no_rawat', '');
        $kdPj    = $request->input('kd_pj', '');

        if (!empty($noRawat) && empty($kdPj)) {
            $reg = DB::table('reg_periksa')->where('no_rawat', $noRawat)->first(['kd_pj']);
            if ($reg) {
                $kdPj = $reg->kd_pj;
            }
        }

        $query = DB::table('jns_perawatan as j')
            ->leftJoin('penjab as p', 'j.kd_pj', '=', 'p.kd_pj')
            ->where('j.status', '1');

        if (!empty($kdPj)) {
            $query->where(function ($w) use ($kdPj) {
                $w->where('j.kd_pj', $kdPj)
                  ->orWhere('j.kd_pj', '-')
                  ->orWhereNull('j.kd_pj');
            });
        }

        if (!empty($q)) {
            $query->where('j.nm_perawatan', 'LIKE', "%{$q}%");
        }

        $data = $query->select('j.kd_jenis_prw', 'j.nm_perawatan', 'j.total_byrdr AS total_byr', 'p.png_jawab')
            ->orderBy('j.nm_perawatan', 'asc')
            ->limit(50)
            ->get();

        return response()->json($data);
    }

    /** Simpan tindakan Rajal (rawat_jl_dr / rawat_jl_drpr) */
    public function simpanTindakan(Request $request)
    {
        try {
            $isAdmin  = session('auth_user.is_admin', false);
            $isDokter = session('auth_user.is_dokter', false);
            if (!$isAdmin && !$isDokter) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya dokter dan admin utama yang berhak menambah tindakan.'], 403);
            }

            $noRawat      = $request->input('no_rawat');
            $kdJenisPrw   = $request->input('kd_jenis_prw');
            $tglPerawatan = $request->input('tgl_perawatan', now()->toDateString());
            $jamRawat     = $request->input('jam_rawat', now()->toTimeString());
            $nip          = session('auth_user.kode', '-');
            $jenis        = $request->input('jenis', 'Dokter');

            if (empty($kdJenisPrw)) {
                return response()->json(['success' => false, 'message' => 'Pilih jenis tindakan.'], 400);
            }

            $jenis_prw = DB::selectOne(
                "SELECT material, bhp, tarif_tindakandr, tarif_tindakanpr, kso, menejemen, total_byrdr, total_byrpr, total_byrdrpr 
                 FROM jns_perawatan 
                 WHERE kd_jenis_prw = ?",
                [$kdJenisPrw]
            );

            if (!$jenis_prw) {
                return response()->json(['success' => false, 'message' => 'Data tarif tindakan tidak ditemukan.'], 404);
            }

            if ($jenis === 'Dokter') {
                DB::table('rawat_jl_dr')->insert([
                    'no_rawat'         => $noRawat,
                    'kd_jenis_prw'     => $kdJenisPrw,
                    'kd_dokter'        => $nip,
                    'tgl_perawatan'    => $tglPerawatan,
                    'jam_rawat'        => $jamRawat,
                    'material'         => $jenis_prw->material ?? 0,
                    'bhp'              => $jenis_prw->bhp ?? 0,
                    'tarif_tindakandr' => $jenis_prw->tarif_tindakandr ?? 0,
                    'kso'              => $jenis_prw->kso ?? 0,
                    'menejemen'        => $jenis_prw->menejemen ?? 0,
                    'biaya_rawat'      => $jenis_prw->total_byrdr ?? 0,
                    'stts_bayar'       => 'Belum',
                ]);
            } elseif ($jenis === 'Paramedis') {
                DB::table('rawat_jl_pr')->insert([
                    'no_rawat'         => $noRawat,
                    'kd_jenis_prw'     => $kdJenisPrw,
                    'nip'              => $nip,
                    'tgl_perawatan'    => $tglPerawatan,
                    'jam_rawat'        => $jamRawat,
                    'material'         => $jenis_prw->material ?? 0,
                    'bhp'              => $jenis_prw->bhp ?? 0,
                    'tarif_tindakanpr' => $jenis_prw->tarif_tindakanpr ?? 0,
                    'kso'              => $jenis_prw->kso ?? 0,
                    'menejemen'        => $jenis_prw->menejemen ?? 0,
                    'biaya_rawat'      => $jenis_prw->total_byrpr ?? 0,
                    'stts_bayar'       => 'Belum',
                ]);
            } else {
                DB::table('rawat_jl_drpr')->insert([
                    'no_rawat'         => $noRawat,
                    'kd_jenis_prw'     => $kdJenisPrw,
                    'kd_dokter'        => $nip,
                    'nip'              => $nip,
                    'tgl_perawatan'    => $tglPerawatan,
                    'jam_rawat'        => $jamRawat,
                    'material'         => $jenis_prw->material ?? 0,
                    'bhp'              => $jenis_prw->bhp ?? 0,
                    'tarif_tindakandr' => $jenis_prw->tarif_tindakandr ?? 0,
                    'tarif_tindakanpr' => $jenis_prw->tarif_tindakanpr ?? 0,
                    'kso'              => $jenis_prw->kso ?? 0,
                    'menejemen'        => $jenis_prw->menejemen ?? 0,
                    'biaya_rawat'      => $jenis_prw->total_byrdrpr ?? 0,
                    'stts_bayar'       => 'Belum',
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Tindakan Rawat Jalan berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Hapus tindakan Rajal */
    public function hapusTindakan(Request $request)
    {
        try {
            $isAdmin  = session('auth_user.is_admin', false);
            $isDokter = session('auth_user.is_dokter', false);
            if (!$isAdmin && !$isDokter) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya dokter dan admin utama yang berhak menghapus tindakan.'], 403);
            }

            $noRawat    = $request->input('no_rawat');
            $kdJenisPrw = $request->input('kd_jenis_prw');
            $tgl        = $request->input('tgl_perawatan');
            $jam        = $request->input('jam_rawat');
            $jenis      = $request->input('jenis', 'Dokter');

            if ($jenis === 'Dokter') {
                DB::table('rawat_jl_dr')
                    ->where('no_rawat', $noRawat)
                    ->where('kd_jenis_prw', $kdJenisPrw)
                    ->where('tgl_perawatan', $tgl)
                    ->where('jam_rawat', $jam)
                    ->delete();
            } elseif ($jenis === 'Paramedis') {
                DB::table('rawat_jl_pr')
                    ->where('no_rawat', $noRawat)
                    ->where('kd_jenis_prw', $kdJenisPrw)
                    ->where('tgl_perawatan', $tgl)
                    ->where('jam_rawat', $jam)
                    ->delete();
            } else {
                DB::table('rawat_jl_drpr')
                    ->where('no_rawat', $noRawat)
                    ->where('kd_jenis_prw', $kdJenisPrw)
                    ->where('tgl_perawatan', $tgl)
                    ->where('jam_rawat', $jam)
                    ->delete();
            }

            return response()->json(['success' => true, 'message' => 'Tindakan berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
                    rp.p_jawab       AS nm_penjamin,
                    rp.almt_pj       AS alamat_pj,
                    rp.hubunganpj    AS hub_keluarga,
                    rp.biaya_reg     AS biaya_daftar,
                    rp.stts          AS status_pasien,
                    rp.status_bayar,
                    rp.status_poli   AS stts_poli,
                    rp.status_lanjut,
                    p.nm_pasien,
                    p.jk,
                    p.tgl_lahir,
                    p.no_tlp,
                    pol.nm_poli,
                    d.nm_dokter,
                    pjb.png_jawab    AS jenis_bayar,
                    sep.no_sep,
                    CASE
                        WHEN pr.no_rawat IS NOT NULL THEN 'terlayani'
                        ELSE 'belum'
                    END AS status_layanan,
                    CASE
                        WHEN pol.nm_poli LIKE '%IGD%' OR pol.nm_poli LIKE '%UGD%' THEN 'IGD'
                        ELSE 'Ralan'
                    END AS jenis_kunjungan
                 FROM reg_periksa rp
                 LEFT JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
                 LEFT JOIN poliklinik pol ON rp.kd_poli = pol.kd_poli
                 LEFT JOIN dokter d ON rp.kd_dokter = d.kd_dokter
                 LEFT JOIN penjab pjb ON rp.kd_pj = pjb.kd_pj
                 LEFT JOIN (
                     SELECT DISTINCT no_rawat FROM pemeriksaan_ralan
                 ) pr ON pr.no_rawat = rp.no_rawat
                 LEFT JOIN bridging_sep sep ON sep.no_rawat = rp.no_rawat
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
                 LEFT JOIN poliklinik pol ON rp.kd_poli = pol.kd_poli
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
            // Return defaults
        }

        return $defaults;
    }

    /**
     * SIMPAN RESUME MEDIS PASIEN RAWAT JALAN (resume_pasien)
     */
    public function simpanResume(Request $request)
    {
        try {
            $noRawat  = $request->input('no_rawat');
            $kdDokter = session('auth_user.kode', '-');

            if (!$noRawat) {
                return response()->json(['success' => false, 'message' => 'No. Rawat tidak valid.'], 400);
            }

            $data = [
                'kd_dokter'             => $kdDokter,
                'keluhan_utama'         => $request->input('keluhan_utama', '-'),
                'jalannya_penyakit'     => $request->input('jalannya_penyakit', '-'),
                'pemeriksaan_penunjang' => $request->input('pemeriksaan_penunjang', '-'),
                'hasil_laborat'         => $request->input('hasil_laborat', '-'),
                'diagnosa_utama'        => $request->input('diagnosa_utama', '-'),
                'kd_diagnosa_utama'     => $request->input('kd_diagnosa_utama', ''),
                'diagnosa_sekunder'     => $request->input('diagnosa_sekunder', ''),
                'kd_diagnosa_sekunder'  => $request->input('kd_diagnosa_sekunder', ''),
                'diagnosa_sekunder2'    => $request->input('diagnosa_sekunder2', ''),
                'kd_diagnosa_sekunder2' => $request->input('kd_diagnosa_sekunder2', ''),
                'diagnosa_sekunder3'    => $request->input('diagnosa_sekunder3', ''),
                'kd_diagnosa_sekunder3' => $request->input('kd_diagnosa_sekunder3', ''),
                'diagnosa_sekunder4'    => $request->input('diagnosa_sekunder4', ''),
                'kd_diagnosa_sekunder4' => $request->input('kd_diagnosa_sekunder4', ''),
                'prosedur_utama'        => $request->input('prosedur_utama', ''),
                'kd_prosedur_utama'     => $request->input('kd_prosedur_utama', ''),
                'prosedur_sekunder'     => $request->input('prosedur_sekunder', ''),
                'kd_prosedur_sekunder'  => $request->input('kd_prosedur_sekunder', ''),
                'prosedur_sekunder2'    => $request->input('prosedur_sekunder2', ''),
                'kd_prosedur_sekunder2' => $request->input('kd_prosedur_sekunder2', ''),
                'prosedur_sekunder3'    => $request->input('prosedur_sekunder3', ''),
                'kd_prosedur_sekunder3' => $request->input('kd_prosedur_sekunder3', ''),
                'kondisi_pulang'        => $request->input('kondisi_pulang', 'Hidup'),
                'obat_pulang'           => $request->input('obat_pulang', '-'),
                'pemeriksaan_lain'      => $request->input('pemeriksaan_lain', ''),
            ];

            DB::table('resume_pasien')->updateOrInsert(
                ['no_rawat' => $noRawat],
                $data
            );

            return response()->json(['success' => true, 'message' => 'Resume Medis Pasien Rawat Jalan berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTemplateResume(Request $request)
    {
        $kdDokter = session('auth_user.kode', '-');
        $q = $request->input('q', '');

        $query = DB::table('template_resume_pasien')
            ->where('tipe', 'ralan')
            ->where(function ($w) use ($kdDokter) {
                $w->where('kd_dokter', $kdDokter)
                  ->orWhereNull('kd_dokter')
                  ->orWhere('kd_dokter', '');
            });

        if (!empty($q)) {
            $query->where(function ($w) use ($q) {
                $w->where('nama_template', 'LIKE', "%{$q}%")
                  ->orWhere('diagnosa_utama', 'LIKE', "%{$q}%")
                  ->orWhere('keluhan_utama', 'LIKE', "%{$q}%");
            });
        }

        $templates = $query->orderBy('nama_template', 'ASC')->get();

        // Juga cek apakah ada template dari template_pemeriksaan_dokter (SIMRS Khanza standard)
        $khanzaTemplates = DB::table('template_pemeriksaan_dokter as t')
            ->leftJoin('dokter as d', 't.kd_dokter', '=', 'd.kd_dokter')
            ->where(function ($w) use ($kdDokter) {
                $w->where('t.kd_dokter', $kdDokter)
                  ->orWhereNull('t.kd_dokter')
                  ->orWhere('t.kd_dokter', '');
            });
        if (!empty($q)) {
            $khanzaTemplates->where(function ($w) use ($q) {
                $w->where('t.no_template', 'LIKE', "%{$q}%")
                  ->orWhere('t.keluhan', 'LIKE', "%{$q}%")
                  ->orWhere('t.penilaian', 'LIKE', "%{$q}%");
            });
        }
        $khanzaList = $khanzaTemplates->select('t.*', 'd.nm_dokter')->limit(20)->get();

        return response()->json([
            'success' => true,
            'templates' => $templates,
            'khanzaTemplates' => $khanzaList
        ]);
    }

    public function simpanTemplateResume(Request $request)
    {
        try {
            $id = $request->input('id');
            $namaTemplate = $request->input('nama_template');
            if (empty($namaTemplate)) {
                return response()->json(['success' => false, 'message' => 'Nama template wajib diisi.'], 422);
            }

            $kdDokter = session('auth_user.kode', '-');

            $data = [
                'kd_dokter'             => $kdDokter,
                'tipe'                  => 'ralan',
                'nama_template'         => $namaTemplate,
                'keluhan_utama'         => $request->input('keluhan_utama', ''),
                'jalannya_penyakit'     => $request->input('jalannya_penyakit', ''),
                'pemeriksaan_fisik'     => $request->input('pemeriksaan_fisik', ''),
                'pemeriksaan_penunjang' => $request->input('pemeriksaan_penunjang', ''),
                'hasil_laborat'         => $request->input('hasil_laborat', ''),
                'diagnosa_utama'        => $request->input('diagnosa_utama', ''),
                'kd_diagnosa_utama'     => $request->input('kd_diagnosa_utama', ''),
                'diagnosa_sekunder'     => $request->input('diagnosa_sekunder', ''),
                'kd_diagnosa_sekunder'  => $request->input('kd_diagnosa_sekunder', ''),
                'diagnosa_sekunder2'    => $request->input('diagnosa_sekunder2', ''),
                'kd_diagnosa_sekunder2' => $request->input('kd_diagnosa_sekunder2', ''),
                'prosedur_utama'        => $request->input('prosedur_utama', ''),
                'kd_prosedur_utama'     => $request->input('kd_prosedur_utama', ''),
                'kondisi_pulang'        => $request->input('kondisi_pulang', 'Membaik'),
                'obat_pulang'           => $request->input('obat_pulang', ''),
                'edukasi'               => $request->input('edukasi', ''),
                'updated_at'            => now(),
            ];

            if (!empty($id)) {
                DB::table('template_resume_pasien')->where('id', $id)->update($data);
                $msg = 'Template Resume berhasil diperbarui.';
            } else {
                $data['created_at'] = now();
                $id = DB::table('template_resume_pasien')->insertGetId($data);
                $msg = 'Template Resume berhasil disimpan.';
            }

            return response()->json(['success' => true, 'message' => $msg, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function hapusTemplateResume(Request $request)
    {
        try {
            $id = $request->input('id');
            if (empty($id)) {
                return response()->json(['success' => false, 'message' => 'ID template tidak valid.'], 400);
            }
            DB::table('template_resume_pasien')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Template Resume berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Master Prosedur ICD-9-CM */
    public function masterProsedur(Request $request)
    {
        $q = $request->input('q', '');
        $data = DB::table('icd9')
            ->where('status', '1')
            ->where(function ($w) use ($q) {
                if (!empty($q)) {
                    $w->where('kode', 'LIKE', "%{$q}%")
                      ->orWhere('deskripsi_panjang', 'LIKE', "%{$q}%")
                      ->orWhere('deskripsi_pendek', 'LIKE', "%{$q}%");
                }
            })
            ->select('kode', 'deskripsi_panjang', 'deskripsi_pendek')
            ->orderBy('kode', 'asc')
            ->limit(50)
            ->get();

        return response()->json($data);
    }

    /** Simpan Prosedur Pasien ICD-9-CM */
    public function simpanProsedur(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya dokter dan admin utama yang berhak menambah prosedur ICD-9.'], 403);
            }

            $noRawat   = $request->input('no_rawat');
            $kode      = $request->input('kode');
            $prioritas = $request->input('prioritas', 1);
            $status    = $request->input('status', 'Ralan');

            if (empty($noRawat) || empty($kode)) {
                return response()->json(['success' => false, 'message' => 'No. Rawat dan Kode Prosedur wajib diisi.'], 400);
            }

            DB::table('prosedur_pasien')->updateOrInsert(
                [
                    'no_rawat' => $noRawat,
                    'kode'     => $kode,
                ],
                [
                    'status'    => $status,
                    'prioritas' => $prioritas,
                ]
            );

            return response()->json(['success' => true, 'message' => "Prosedur ICD-9 ({$kode}) berhasil disimpan."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Hapus Prosedur Pasien ICD-9-CM */
    public function hapusProsedur(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya dokter dan admin utama yang berhak menghapus prosedur ICD-9.'], 403);
            }

            $noRawat = $request->input('no_rawat');
            $kode    = $request->input('kode');

            if (empty($noRawat) || empty($kode)) {
                return response()->json(['success' => false, 'message' => 'Parameter no_rawat dan kode wajib disertakan.'], 400);
            }

            DB::table('prosedur_pasien')
                ->where('no_rawat', $noRawat)
                ->where('kode', $kode)
                ->delete();

            return response()->json(['success' => true, 'message' => "Prosedur ICD-9 ({$kode}) berhasil dihapus."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Cetak Formulir Laporan Operasi Standar SIMRS */
    public function cetakLaporanOperasi(Request $request, $no_rawat = null)
    {
        $raw = $no_rawat ?: $request->input('no_rawat');
        if ($raw) {
            $decoded = base64_decode($raw, true);
            $noRawat = ($decoded && strpos($decoded, '/') !== false) ? $decoded : $raw;
        } else {
            $noRawat = null;
        }

        $tanggal = $request->input('tanggal');

        $lapQuery = DB::table('laporan_operasi as lo')
            ->join('reg_periksa as rp', 'lo.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->leftJoin('poliklinik as pol', 'rp.kd_poli', '=', 'pol.kd_poli')
            ->where('lo.no_rawat', $noRawat);

        if (!empty($tanggal)) {
            $lapQuery->where('lo.tanggal', $tanggal);
        }

        $lap = $lapQuery->select('lo.*', 'p.nm_pasien', 'p.no_rkm_medis', 'p.jk', 'p.tgl_lahir', 'rp.umurdaftar', 'rp.sttsumur', 'd.nm_dokter as operator', 'pol.nm_poli')
            ->orderByDesc('lo.tanggal')
            ->first();

        if (!$lap) {
            return response("Data Laporan Operasi ({$noRawat}" . ($tanggal ? " - {$tanggal}" : "") . ") tidak ditemukan.", 404);
        }

        $setting = DB::table('setting')->first() ?: (object)[
            'nama_instansi'    => 'RS NAMIRA',
            'alamat_instansi'  => 'Jl. KH. Ahmad Dahlan No. 1, Pancor, Selong',
            'kabupaten'        => 'Kabupaten Lombok Timur',
            'propinsi'         => 'Nusa Tenggara Barat',
            'kontak'           => '(0376) 21123 / 22211',
            'email'            => 'rsnamira@gmail.com',
        ];

        return view('rawat-jalan.cetak-laporan-operasi', compact('lap', 'setting'));
    }

    /** Cetak Bukti Jadwal Booking Operasi Standar SIMRS */
    public function cetakBookingOperasi(Request $request, $no_rawat = null)
    {
        $raw = $no_rawat ?: $request->input('no_rawat');
        if ($raw) {
            $decoded = base64_decode($raw, true);
            $noRawat = ($decoded && strpos($decoded, '/') !== false) ? $decoded : $raw;
        } else {
            $noRawat = null;
        }

        $kodePaket = $request->input('kode_paket');
        $tanggal   = $request->input('tanggal');

        $bookingQuery = DB::table('booking_operasi as bo')
            ->join('reg_periksa as rp', 'bo.no_rawat', '=', 'rp.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('paket_operasi as po', 'bo.kode_paket', '=', 'po.kode_paket')
            ->leftJoin('dokter as d', 'bo.kd_dokter', '=', 'd.kd_dokter')
            ->leftJoin('ruang_ok as ro', 'bo.kd_ruang_ok', '=', 'ro.kd_ruang_ok')
            ->where('bo.no_rawat', $noRawat);

        if (!empty($kodePaket)) {
            $bookingQuery->where('bo.kode_paket', $kodePaket);
        }
        if (!empty($tanggal)) {
            $bookingQuery->where('bo.tanggal', $tanggal);
        }

        $booking = $bookingQuery->select('bo.*', 'p.nm_pasien', 'p.no_rkm_medis', 'p.jk', 'p.tgl_lahir', 'rp.umurdaftar', 'rp.sttsumur', 'po.nm_perawatan as nama_paket', 'po.kelas', 'd.nm_dokter as operator', 'ro.nm_ruang_ok')
            ->orderByDesc('bo.tanggal')
            ->first();

        if (!$booking) {
            return response("Data Booking Operasi ({$noRawat}) tidak ditemukan.", 404);
        }

        $setting = DB::table('setting')->first() ?: (object)[
            'nama_instansi'    => 'RS NAMIRA',
            'alamat_instansi'  => 'Jl. KH. Ahmad Dahlan No. 1, Pancor, Selong',
            'kabupaten'        => 'Kabupaten Lombok Timur',
            'propinsi'         => 'Nusa Tenggara Barat',
            'kontak'           => '(0376) 21123 / 22211',
            'email'            => 'rsnamira@gmail.com',
        ];

        return view('rawat-jalan.cetak-booking-operasi', compact('booking', 'setting'));
    }
}
