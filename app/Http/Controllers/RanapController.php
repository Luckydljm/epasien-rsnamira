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

        $todayDate  = now()->toDateString();
        $yesterday  = now()->subDay()->toDateString();
        $last7Days  = now()->subDays(6)->toDateString();
        $startMonth = now()->startOfMonth()->toDateString();
        $endMonth   = now()->endOfMonth()->toDateString();

        $tglAwal   = $request->input('tgl_awal', $startMonth);
        $tglAkhir  = $request->input('tgl_akhir', $todayDate);
        $kdBangsal = $request->input('kd_bangsal', '');
        $kdPj      = $request->input('kd_pj', '');
        $q         = $request->input('q', '');
        $filterTgl = $request->has('tgl_awal');

        $filters = [
            'tgl_awal'         => $tglAwal,
            'tgl_akhir'        => $tglAkhir,
            'kd_bangsal'       => $kdBangsal,
            'kd_pj'            => $kdPj,
            'q'                => $q,
            'filter_tgl_aktif' => $filterTgl,
        ];

        $pasienRanap        = $this->getPasienRanap($kdDokterFilter, $filters);
        $pasienRanapSelesai = $this->getPasienRanapSelesai($kdDokterFilter, $filters);
        $statsRanap         = $this->getStatsRanap($kdDokterFilter);

        $listBangsal = DB::table('bangsal')->orderBy('nm_bangsal', 'asc')->get(['kd_bangsal', 'nm_bangsal']);
        $listPenjab  = DB::table('penjab')->where('status', '1')->orderBy('png_jawab', 'asc')->get(['kd_pj', 'png_jawab']);

        return view('rawat-inap.index', compact(
            'pasienRanap', 'pasienRanapSelesai', 'statsRanap',
            'isDokter', 'isAdmin', 'nmDokter', 'kdDokterFilter',
            'tglAwal', 'tglAkhir', 'kdBangsal', 'kdPj', 'q',
            'listBangsal', 'listPenjab',
            'todayDate', 'yesterday', 'last7Days', 'startMonth', 'endMonth'
        ));
    }

    /**
     * Detail pasien rawat inap — semua data rekam medis untuk panel dokter
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

            // === 1. Data Pasien & Registrasi (lengkap seperti DlgKamarInap) ===
            $pasien = DB::selectOne(
                "SELECT rp.no_rawat, rp.no_rkm_medis, rp.tgl_registrasi, rp.jam_reg,
                        rp.kd_dokter, rp.kd_poli, rp.kd_pj, rp.status_lanjut, rp.status_bayar,
                        rp.p_jawab, rp.hubunganpj, rp.almt_pj,
                        CONCAT(rp.umurdaftar, ' ', rp.sttsumur) AS umur_daftar,
                        p.nm_pasien, p.jk, p.tgl_lahir, p.no_tlp, p.alamat,
                        p.no_ktp, p.gol_darah, p.agama, p.pekerjaan,
                        CONCAT(IFNULL(p.alamat,''),
                               IF(kel.nm_kel IS NOT NULL AND kel.nm_kel != '', CONCAT(', Kel. ', kel.nm_kel), ''),
                               IF(kec.nm_kec IS NOT NULL AND kec.nm_kec != '', CONCAT(', Kec. ', kec.nm_kec), ''),
                               IF(kab.nm_kab IS NOT NULL AND kab.nm_kab != '', CONCAT(', ', kab.nm_kab), '')) AS alamat_lengkap,
                        pol.nm_poli, d.nm_dokter, d.nm_dokter AS nm_dokter_reg,
                        pjb.png_jawab AS jenis_bayar,
                        ki.kd_kamar, ki.tgl_masuk, ki.jam_masuk, ki.lama,
                        ki.diagnosa_awal, ki.diagnosa_akhir, ki.trf_kamar, ki.ttl_biaya, ki.stts_pulang,
                        ki.tgl_keluar, ki.jam_keluar,
                        k.kelas, bang.nm_bangsal,
                        sep.no_sep,
                        IFNULL((SELECT dokter.nm_dokter FROM dpjp_ranap
                                 INNER JOIN dokter ON dpjp_ranap.kd_dokter = dokter.kd_dokter
                                 WHERE dpjp_ranap.no_rawat = rp.no_rawat LIMIT 1), d.nm_dokter) AS nm_dpjp
                 FROM reg_periksa rp
                 LEFT JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
                 LEFT JOIN kelurahan kel ON kel.kd_kel = p.kd_kel
                 LEFT JOIN kecamatan kec ON kec.kd_kec = p.kd_kec
                 LEFT JOIN kabupaten kab ON kab.kd_kab = p.kd_kab
                 LEFT JOIN poliklinik pol ON rp.kd_poli = pol.kd_poli
                 LEFT JOIN dokter d ON rp.kd_dokter = d.kd_dokter
                 LEFT JOIN penjab pjb ON rp.kd_pj = pjb.kd_pj
                 LEFT JOIN (
                     SELECT ki1.no_rawat, ki1.kd_kamar, ki1.tgl_masuk, ki1.jam_masuk, ki1.lama,
                            ki1.diagnosa_awal, ki1.diagnosa_akhir, ki1.trf_kamar, ki1.ttl_biaya,
                            ki1.stts_pulang, ki1.tgl_keluar, ki1.jam_keluar
                     FROM kamar_inap ki1
                     WHERE ki1.tgl_keluar IS NULL OR ki1.tgl_keluar = '0000-00-00'
                 ) ki ON ki.no_rawat = rp.no_rawat
                 LEFT JOIN kamar k ON k.kd_kamar = ki.kd_kamar
                 LEFT JOIN bangsal bang ON bang.kd_bangsal = k.kd_bangsal
                 LEFT JOIN bridging_sep sep ON sep.no_rawat = rp.no_rawat
                 WHERE rp.no_rawat = ?
                 LIMIT 1",
                [$noRawat]
            );

            // === 1b. DPJP List ===
            $dpjpList = DB::select(
                "SELECT dr.kd_dokter, d.nm_dokter, s.nm_sps
                 FROM dpjp_ranap dr
                 LEFT JOIN dokter d ON d.kd_dokter = dr.kd_dokter
                 LEFT JOIN spesialis s ON s.kd_sps = d.kd_sps
                 WHERE dr.no_rawat = ?",
                [$noRawat]
            );

            if (!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.'], 404);
            }

            // === 2. SOAP / Catatan Perkembangan Harian ===
            $soapList = DB::select(
                "SELECT pr.no_rawat, pr.tgl_perawatan, pr.jam_rawat,
                        pr.suhu_tubuh, pr.tensi, pr.nadi, pr.respirasi,
                        pr.tinggi, pr.berat, pr.spo2, pr.gcs, pr.kesadaran,
                        pr.keluhan, pr.pemeriksaan, pr.penilaian, pr.rtl,
                        pr.instruksi, pr.evaluasi, pr.alergi,
                        COALESCE(pt.nama, d.nm_dokter, pr.nip) AS petugas
                 FROM pemeriksaan_ranap pr
                 LEFT JOIN petugas pt ON pt.nip = pr.nip
                 LEFT JOIN dokter d ON d.kd_dokter = pr.nip
                 WHERE pr.no_rawat = ?
                 ORDER BY pr.tgl_perawatan DESC, pr.jam_rawat DESC
                 LIMIT 100",
                [$noRawat]
            );

            // === 3. Diagnosa Pasien (ICD-10) ===
            $diagnosaList = DB::select(
                "SELECT dp.no_rawat, dp.kd_penyakit, dp.prioritas, dp.status,
                        py.nm_penyakit
                 FROM diagnosa_pasien dp
                 LEFT JOIN penyakit py ON py.kd_penyakit = dp.kd_penyakit
                 WHERE dp.no_rawat = ?
                 ORDER BY dp.prioritas ASC, dp.kd_penyakit ASC",
                [$noRawat]
            );

            // === 4. Tindakan Rawat Inap (rawat_inap_dr + rawat_inap_drpr) ===
            $tindakanList = DB::select(
                "SELECT rid.no_rawat, rid.tgl_perawatan, rid.jam_rawat,
                        rid.kd_jenis_prw, jp.nm_perawatan, rid.biaya_rawat AS total_byr,
                        COALESCE(d.nm_dokter, rid.kd_dokter) AS petugas,
                        'Dokter' AS jenis
                 FROM rawat_inap_dr rid
                 LEFT JOIN jns_perawatan_inap jp ON jp.kd_jenis_prw = rid.kd_jenis_prw
                 LEFT JOIN dokter d ON d.kd_dokter = rid.kd_dokter
                 WHERE rid.no_rawat = ?

                 UNION ALL

                 SELECT ridp.no_rawat, ridp.tgl_perawatan, ridp.jam_rawat,
                        ridp.kd_jenis_prw, jp.nm_perawatan, ridp.biaya_rawat AS total_byr,
                        COALESCE(d2.nm_dokter, pt.nama, ridp.nip) AS petugas,
                        'Dokter & Paramedis' AS jenis
                 FROM rawat_inap_drpr ridp
                 LEFT JOIN jns_perawatan_inap jp ON jp.kd_jenis_prw = ridp.kd_jenis_prw
                 LEFT JOIN dokter d2 ON d2.kd_dokter = ridp.kd_dokter
                 LEFT JOIN petugas pt ON pt.nip = ridp.nip
                 WHERE ridp.no_rawat = ?

                 ORDER BY tgl_perawatan DESC, jam_rawat DESC
                 LIMIT 200",
                [$noRawat, $noRawat]
            );

            // === 5. Resep Obat Ranap (Obat Jadi & Obat Racikan) ===
            $rawResepRanap = DB::select(
                "SELECT ro.no_resep, ro.no_rawat, ro.tgl_perawatan, COALESCE(ro.jam, '00:00:00') AS jam,
                        ro.tgl_peresepan, ro.jam_peresepan, ro.status, ro.tgl_penyerahan, ro.jam_penyerahan,
                        COALESCE(d.nm_dokter, 'Dokter') AS nm_dokter
                 FROM resep_obat ro
                 LEFT JOIN dokter d ON ro.kd_dokter = d.kd_dokter
                 WHERE ro.no_rawat = ?
                 ORDER BY ro.tgl_perawatan DESC, ro.jam DESC
                 LIMIT 100",
                [$noRawat]
            );

            $resepList = [];
            foreach ($rawResepRanap as $r) {
                // Non-racikan
                $obatList = DB::select(
                    "SELECT rd.kode_brng, rd.jml, rd.aturan_pakai, rd.keterangan,
                            db.nama_brng, db.kode_sat
                     FROM resep_dokter rd
                     LEFT JOIN databarang db ON rd.kode_brng = db.kode_brng
                     WHERE rd.no_resep = ?",
                    [$r->no_resep]
                );

                // Racikan
                $racikList = DB::select(
                    "SELECT rdr.no_racik, rdr.nama_racik, rdr.kd_racik, rdr.jml_dr, rdr.aturan_pakai, rdr.keterangan,
                            mr.nm_racik AS metode
                     FROM resep_dokter_racikan rdr
                     LEFT JOIN metode_racik mr ON rdr.kd_racik = mr.kd_racik
                     WHERE rdr.no_resep = ?
                     ORDER BY rdr.no_racik ASC",
                    [$r->no_resep]
                );

                foreach ($racikList as $rc) {
                    $rc->detail = DB::select(
                        "SELECT rdrd.kode_brng, rdrd.p1, rdrd.p2, rdrd.kandungan, rdrd.jml,
                                db.nama_brng, db.kode_sat
                         FROM resep_dokter_racikan_detail rdrd
                         LEFT JOIN databarang db ON rdrd.kode_brng = db.kode_brng
                         WHERE rdrd.no_resep = ? AND rdrd.no_racik = ?",
                        [$r->no_resep, $rc->no_racik]
                    );
                }

                // Summary HTML string
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
                    'detail_obat'     => implode('<br>', $summaryParts),
                    'obat_list'       => $obatList,
                    'racik_list'      => $racikList,
                ];
            }

            // === 6. Hasil Lab (jika ada) ===
            $labResults = DB::select(
                "SELECT dpl.no_rawat, dpl.tgl_periksa, dpl.jam,
                        COALESCE(tl.Pemeriksaan, jpl.nm_perawatan) AS nama_pemeriksaan,
                        COALESCE(dpl.nilai, '-') AS nilai,
                        COALESCE(dpl.nilai_rujukan, '-') AS nilai_rujukan,
                        COALESCE(tl.satuan, '') AS satuan,
                        COALESCE(dpl.keterangan, '') AS keterangan
                 FROM detail_periksa_lab dpl
                 JOIN reg_periksa rp ON dpl.no_rawat = rp.no_rawat
                 LEFT JOIN jns_perawatan_lab jpl ON dpl.kd_jenis_prw = jpl.kd_jenis_prw
                 LEFT JOIN template_laboratorium tl ON dpl.id_template = tl.id_template
                 WHERE dpl.no_rawat = ?
                 ORDER BY dpl.tgl_periksa DESC, dpl.jam DESC
                 LIMIT 200",
                [$noRawat]
            );

            $labOrders = DB::select(
                "SELECT pl.noorder, pl.tgl_permintaan, pl.jam_permintaan,
                        pl.diagnosa_klinis,
                        GROUP_CONCAT(jpl.nm_perawatan SEPARATOR ', ') AS detail_pemeriksaan
                 FROM permintaan_lab pl
                 LEFT JOIN permintaan_detail_permintaan_lab pdl ON pl.noorder = pdl.noorder
                 LEFT JOIN jns_perawatan_lab jpl ON pdl.kd_jenis_prw = jpl.kd_jenis_prw
                 WHERE pl.no_rawat = ?
                 GROUP BY pl.noorder, pl.tgl_permintaan, pl.jam_permintaan, pl.diagnosa_klinis
                 ORDER BY pl.tgl_permintaan DESC, pl.jam_permintaan DESC
                 LIMIT 50",
                [$noRawat]
            );

            // === 7. Hasil Radiologi ===
            $radResults = DB::select(
                "SELECT hr.no_rawat, hr.tgl_periksa, hr.jam, hr.hasil
                 FROM hasil_radiologi hr
                 WHERE hr.no_rawat = ?
                 ORDER BY hr.tgl_periksa DESC, hr.jam DESC
                 LIMIT 50",
                [$noRawat]
            );

            $radOrders = DB::select(
                "SELECT pr.noorder, pr.tgl_permintaan, pr.jam_permintaan,
                        pr.diagnosa_klinis,
                        GROUP_CONCAT(jpr.nm_perawatan SEPARATOR ', ') AS detail_pemeriksaan
                 FROM permintaan_radiologi pr
                 LEFT JOIN permintaan_pemeriksaan_radiologi pdr ON pr.noorder = pdr.noorder
                 LEFT JOIN jns_perawatan_radiologi jpr ON pdr.kd_jenis_prw = jpr.kd_jenis_prw
                 WHERE pr.no_rawat = ?
                 GROUP BY pr.noorder, pr.tgl_permintaan, pr.jam_permintaan, pr.diagnosa_klinis
                 ORDER BY pr.tgl_permintaan DESC, pr.jam_permintaan DESC
                 LIMIT 50",
                [$noRawat]
            );

            // DPJP sudah diambil di bagian 1b di atas

            // === 9. Riwayat Kamar ===
            $riwayatKamar = DB::select(
                "SELECT ki.kd_kamar, ki.tgl_masuk, ki.jam_masuk,
                        ki.tgl_keluar, ki.jam_keluar, ki.lama, ki.stts_pulang,
                        k.kelas, bang.nm_bangsal
                 FROM kamar_inap ki
                 LEFT JOIN kamar k ON k.kd_kamar = ki.kd_kamar
                 LEFT JOIN bangsal bang ON bang.kd_bangsal = k.kd_bangsal
                 WHERE ki.no_rawat = ?
                 ORDER BY ki.tgl_masuk DESC",
                [$noRawat]
            );

            // === 10. Penilaian Awal Medis Ranap ===
            $awalMedisRanap = DB::selectOne(
                "SELECT pmrn.*, d.nm_dokter
                 FROM penilaian_medis_ranap pmrn
                 LEFT JOIN dokter d ON d.kd_dokter = pmrn.kd_dokter
                 WHERE pmrn.no_rawat = ?
                 ORDER BY pmrn.tanggal DESC LIMIT 1",
                [$noRawat]
            );

            // === 11. Resume Medis Ranap (resume_pasien_ranap) ===
            $resumeRanap = DB::table('resume_pasien_ranap')
                ->where('no_rawat', $noRawat)
                ->first();

            // === 12. Booking Operasi (booking_operasi) ===
            $noRkmMedis = $pasien ? $pasien->no_rkm_medis : '';
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

            // === 13. Laporan Operasi (laporan_operasi) ===
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

            return response()->json([
                'success'        => true,
                'pasien'         => $pasien,
                'soapList'       => $soapList,
                'diagnosaList'   => $diagnosaList,
                'tindakanList'   => $tindakanList,
                'resepList'      => $resepList,
                'labResults'     => $labResults,
                'labOrders'      => $labOrders,
                'radResults'     => $radResults,
                'radOrders'      => $radOrders,
                'dpjpList'       => $dpjpList,
                'riwayatKamar'   => $riwayatKamar,
                'awalMedisRanap' => $awalMedisRanap,
                'resumeRanap'    => $resumeRanap,
                'bookingOps'     => $bookingOps,
                'laporanOps'     => $laporanOps,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Simpan SOAP / Catatan Perkembangan Harian Ranap (Dokter & Admin Utama)
     */
    public function simpanSoapRanap(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menyimpan data SOAP.'], 403);
            }

            $noRawat      = $request->input('no_rawat');
            $tglPerawatan = $request->input('tgl_perawatan', now()->toDateString());
            $jamRawat     = $request->input('jam_rawat', now()->toTimeString());
            $nip          = session('auth_user.kode', '-');

            DB::table('pemeriksaan_ranap')->updateOrInsert(
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
                    'nip'           => $nip,
                ]
            );

            return response()->json(['success' => true, 'message' => 'Catatan SOAP Ranap berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus SOAP Ranap (Hanya Dokter & Admin Utama)
     */
    public function hapusSoapRanap(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menghapus data SOAP.'], 403);
            }

            $noRawat      = $request->input('no_rawat');
            $tglPerawatan = $request->input('tgl_perawatan');
            $jamRawat     = $request->input('jam_rawat');

            if (empty($noRawat) || empty($tglPerawatan) || empty($jamRawat)) {
                return response()->json(['success' => false, 'message' => 'Data identifikasi pemeriksaan tidak lengkap.'], 400);
            }

            $deleted = DB::table('pemeriksaan_ranap')
                ->where('no_rawat', $noRawat)
                ->where('tgl_perawatan', $tglPerawatan)
                ->where('jam_rawat', $jamRawat)
                ->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Data SOAP Rawat Inap berhasil dihapus.']);
            }

            return response()->json(['success' => false, 'message' => 'Data SOAP tidak ditemukan atau sudah dihapus.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Simpan Diagnosa Pasien (ICD-10)
     */
    public function simpanDiagnosa(Request $request)
    {
        try {
            $noRawat   = $request->input('no_rawat');
            $kdPenyakit = $request->input('kd_penyakit');
            $prioritas = $request->input('prioritas', 1);
            $status    = $request->input('status', 'Ranap'); // Ralan / Ranap / IGD

            if (empty($kdPenyakit)) {
                return response()->json(['success' => false, 'message' => 'Kode penyakit harus diisi.'], 400);
            }

            // Cek apakah sudah ada
            $exists = DB::table('diagnosa_pasien')
                ->where('no_rawat', $noRawat)
                ->where('kd_penyakit', $kdPenyakit)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Diagnosa sudah ada dalam daftar.'], 400);
            }

            DB::table('diagnosa_pasien')->insert([
                'no_rawat'       => $noRawat,
                'kd_penyakit'    => $kdPenyakit,
                'prioritas'      => $prioritas,
                'status'         => $status,
                'status_penyakit'=> 'Baru',
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

    /**
     * Hapus Diagnosa Pasien (Dokter & Admin Utama)
     */
    public function hapusDiagnosa(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menghapus diagnosa.'], 403);
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

    /**
     * Simpan Tindakan Rawat Inap Dokter
     */
    public function simpanTindakan(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menyimpan tindakan.'], 403);
            }

            $noRawat      = $request->input('no_rawat');
            $kdJenisPrw   = $request->input('kd_jenis_prw');
            $tglPerawatan = $request->input('tgl_perawatan', now()->toDateString());
            $jamRawat     = $request->input('jam_rawat', now()->toTimeString());
            $nip          = session('auth_user.kode', '-');
            $jenis        = $request->input('jenis', 'Dokter'); // Dokter / Paramedis

            if (empty($kdJenisPrw)) {
                return response()->json(['success' => false, 'message' => 'Pilih jenis tindakan.'], 400);
            }

            // Ambil tarif dari master tindakan ranap
            $jenis_prw = DB::selectOne(
                "SELECT material, bhp, tarif_tindakandr, tarif_tindakanpr, kso, menejemen, total_byrdr, total_byrpr, total_byrdrpr 
                 FROM jns_perawatan_inap 
                 WHERE kd_jenis_prw = ?",
                [$kdJenisPrw]
            );

            if (!$jenis_prw) {
                return response()->json(['success' => false, 'message' => 'Data tarif tindakan tidak ditemukan.'], 404);
            }

            if ($jenis === 'Dokter') {
                DB::table('rawat_inap_dr')->insert([
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
                ]);
            } else {
                DB::table('rawat_inap_drpr')->insert([
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
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Tindakan berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus Tindakan Rawat Inap (Dokter & Admin Utama)
     */
    public function hapusTindakan(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menghapus tindakan.'], 403);
            }

            $noRawat    = $request->input('no_rawat');
            $kdJenisPrw = $request->input('kd_jenis_prw');
            $tgl        = $request->input('tgl_perawatan');
            $jam        = $request->input('jam_rawat');
            $jenis      = $request->input('jenis', 'Dokter');

            if ($jenis === 'Dokter') {
                DB::table('rawat_inap_dr')
                    ->where('no_rawat', $noRawat)
                    ->where('kd_jenis_prw', $kdJenisPrw)
                    ->where('tgl_perawatan', $tgl)
                    ->where('jam_rawat', $jam)
                    ->delete();
            } else {
                DB::table('rawat_inap_drpr')
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
     * Simpan / Update Penilaian Awal Medis Ranap (penilaian_medis_ranap)
     */
    public function simpanAwalMedisRanap(Request $request)
    {
        try {
            $noRawat  = $request->input('no_rawat');
            $kdDokter = session('auth_user.kode', '');

            DB::table('penilaian_medis_ranap')->updateOrInsert(
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
                    'mata'          => $request->input('mata', 'Normal'),
                    'gigi'          => $request->input('gigi', 'Normal'),
                    'tht'           => $request->input('tht', 'Normal'),
                    'thoraks'       => $request->input('thoraks', 'Normal'),
                    'jantung'       => $request->input('jantung', 'Normal'),
                    'paru'          => $request->input('paru', 'Normal'),
                    'abdomen'       => $request->input('abdomen', 'Normal'),
                    'genital'       => $request->input('genital', 'Normal'),
                    'ekstremitas'   => $request->input('ekstremitas', 'Normal'),
                    'kulit'         => $request->input('kulit', 'Normal'),
                    'ket_fisik'     => $request->input('ket_fisik', ''),
                    'ket_lokalis'   => $request->input('ket_lokalis', ''),
                    'lab'           => $request->input('lab', ''),
                    'rad'           => $request->input('rad', ''),
                    'penunjang'     => $request->input('penunjang', ''),
                    'diagnosis'     => $request->input('diagnosis', ''),
                    'tata'          => $request->input('tata', ''),
                    'edukasi'       => $request->input('edukasi', ''),
                ]
            );

            return response()->json(['success' => true, 'message' => 'Penilaian Awal Medis Ranap berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Simpan Resep Obat Ranap (Obat Jadi & Obat Racikan) - Dokter & Admin Utama
     */
    public function simpanResepRanap(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang membuat atau mengubah resep.'], 403);
            }

            $noRawat  = $request->input('no_rawat');
            $items    = $request->input('items', []);   // Obat Jadi: [{kode_brng, jml, aturan_pakai, keterangan}]
            $racikan  = $request->input('racikan', []); // Obat Racik: [{nama_racik, kd_racik, jml_dr, aturan_pakai, keterangan, detail: [{kode_brng, p1, p2, kandungan, jml}]}]
            $kdDokter = session('auth_user.kode', '-');
            $tglResep = $request->input('tgl_perawatan', now()->toDateString());
            $jamResep = now()->toTimeString();

            if (empty($items) && empty($racikan)) {
                return response()->json(['success' => false, 'message' => 'Pilih minimal 1 obat jadi atau racikan untuk resep.'], 400);
            }

            // Bangsal Depo Ranap: B0006
            $kdDepoRanap = 'B0006';

            // Validasi Obat Jadi Ranap
            foreach ($items as $idx => $item) {
                $kodeBrng = $item['kode_brng'] ?? '';
                $jml = isset($item['jml']) ? (float) $item['jml'] : 0;
                $aturanPakai = trim($item['aturan_pakai'] ?? '');

                if (empty($kodeBrng)) continue;

                $brng = DB::table('databarang')->where('kode_brng', $kodeBrng)->first();
                $namaBrng = $brng ? $brng->nama_brng : ($item['nama'] ?? $kodeBrng);

                if ($jml <= 0) {
                    return response()->json(['success' => false, 'message' => "Jumlah untuk obat '{$namaBrng}' harus lebih dari 0."], 422);
                }
                if (empty($aturanPakai) || $aturanPakai === '-') {
                    return response()->json(['success' => false, 'message' => "Aturan pakai untuk obat '{$namaBrng}' wajib diisi."], 422);
                }

                $stokRow = DB::table('gudangbarang')->where('kode_brng', $kodeBrng)->where('kd_bangsal', $kdDepoRanap)->first();
                $currentStok = $stokRow ? (float) $stokRow->stok : 0;
                if ($currentStok <= 0) {
                    return response()->json(['success' => false, 'message' => "Stok obat '{$namaBrng}' kosong di Depo Farmasi Ranap sehingga tidak dapat diresepkan."], 422);
                }
                if ($jml > $currentStok) {
                    return response()->json(['success' => false, 'message' => "Permintaan obat '{$namaBrng}' ({$jml}) melebihi stok yang tersedia di Depo Ranap ({$currentStok})."], 422);
                }
            }

            // Validasi Racikan Ranap
            foreach ($racikan as $rIdx => $racik) {
                $namaRacik = trim($racik['nama_racik'] ?? '');
                $jmlDr = isset($racik['jml_dr']) ? (int) $racik['jml_dr'] : 0;
                $aturanPakai = trim($racik['aturan_pakai'] ?? '');
                $detail = $racik['detail'] ?? [];

                if (empty($namaRacik)) {
                    return response()->json(['success' => false, 'message' => "Nama racikan ke-" . ($rIdx + 1) . " wajib diisi."], 422);
                }
                if ($jmlDr <= 0) {
                    return response()->json(['success' => false, 'message' => "Jumlah kemasan untuk racikan '{$namaRacik}' harus minimal 1."], 422);
                }
                if (empty($aturanPakai) || $aturanPakai === '-') {
                    return response()->json(['success' => false, 'message' => "Aturan pakai untuk racikan '{$namaRacik}' wajib diisi."], 422);
                }
                if (empty($detail)) {
                    return response()->json(['success' => false, 'message' => "Racikan '{$namaRacik}' belum memiliki bahan obat."], 422);
                }

                foreach ($detail as $det) {
                    $kodeBrng = $det['kode_brng'] ?? '';
                    $jmlBahan = isset($det['jml']) ? (float) $det['jml'] : 0;
                    if (empty($kodeBrng)) continue;

                    $brng = DB::table('databarang')->where('kode_brng', $kodeBrng)->first();
                    $namaBrng = $brng ? $brng->nama_brng : ($det['nama_brng'] ?? $kodeBrng);

                    if ($jmlBahan <= 0) {
                        return response()->json(['success' => false, 'message' => "Jumlah kebutuhan bahan '{$namaBrng}' pada racikan '{$namaRacik}' harus lebih dari 0."], 422);
                    }

                    $stokRow = DB::table('gudangbarang')->where('kode_brng', $kodeBrng)->where('kd_bangsal', $kdDepoRanap)->first();
                    $currentStok = $stokRow ? (float) $stokRow->stok : 0;
                    if ($currentStok <= 0) {
                        return response()->json(['success' => false, 'message' => "Bahan obat '{$namaBrng}' pada racikan '{$namaRacik}' kosong di Depo Farmasi."], 422);
                    }
                    if ($jmlBahan > $currentStok) {
                        return response()->json(['success' => false, 'message' => "Kebutuhan bahan racik '{$namaBrng}' ({$jmlBahan}) pada racikan '{$namaRacik}' melebihi stok yang tersedia ({$currentStok})."], 422);
                    }
                }
            }

            $existingNoResep = $request->input('no_resep');
            $isEdit = !empty($existingNoResep);

            if ($isEdit) {
                // Mode Edit Resep
                $resepExisting = DB::table('resep_obat')
                    ->where('no_resep', $existingNoResep)
                    ->where('no_rawat', $noRawat)
                    ->first();

                if (!$resepExisting) {
                    return response()->json(['success' => false, 'message' => 'Data resep tidak ditemukan untuk diedit.'], 404);
                }

                if ($resepExisting->tgl_penyerahan && $resepExisting->tgl_penyerahan != '0000-00-00') {
                    return response()->json(['success' => false, 'message' => 'Resep ini sudah diserahkan / divalidasi oleh Farmasi dan tidak dapat diubah.'], 400);
                }

                $noResep = $existingNoResep;

                // Bersihkan detail lama
                DB::table('resep_dokter_racikan_detail')->where('no_resep', $noResep)->delete();
                DB::table('resep_dokter_racikan')->where('no_resep', $noResep)->delete();
                DB::table('resep_dokter')->where('no_resep', $noResep)->delete();

                // Update data header
                DB::table('resep_obat')->where('no_resep', $noResep)->update([
                    'kd_dokter'     => $kdDokter,
                    'tgl_peresepan' => $tglResep,
                    'jam_peresepan' => $jamResep,
                ]);
            } else {
                // Generate No Resep: YYYYMMDDxxxx
                $prefix   = date('Ymd');
                $lastResep = DB::table('resep_obat')->where('no_resep', 'LIKE', "{$prefix}%")->max('no_resep');
                $lastNum  = $lastResep ? (int) substr($lastResep, -4) : 0;
                $noResep  = $prefix . sprintf('%04d', $lastNum + 1);

                DB::table('resep_obat')->insert([
                    'no_resep'       => $noResep,
                    'tgl_perawatan'  => $tglResep,
                    'jam'            => $jamResep,
                    'no_rawat'       => $noRawat,
                    'kd_dokter'      => $kdDokter,
                    'tgl_peresepan'  => $tglResep,
                    'jam_peresepan'  => $jamResep,
                    'status'         => 'ranap',
                    'tgl_penyerahan' => '0000-00-00',
                    'jam_penyerahan' => '00:00:00',
                ]);
            }

            // Simpan Obat Jadi
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

            // Simpan Obat Racikan
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

            $actionMsg = $isEdit ? 'berhasil diperbarui' : 'berhasil disimpan';
            return response()->json(['success' => true, 'message' => "E-Resep Ranap ({$noResep}) {$actionMsg}."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function hapusResep(Request $request)
    {
        try {
            if (!session('auth_user.is_admin') && !session('auth_user.is_dokter')) {
                return response()->json(['success' => false, 'message' => 'Hanya Dokter dan Admin Utama yang berwenang menghapus resep.'], 403);
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

    /**
     * Order Lab untuk Rawat Inap
     */
    public function simpanLabRanap(Request $request)
    {
        try {
            $noRawat       = $request->input('no_rawat');
            $items         = $request->input('items', []);
            $kdDokter      = session('auth_user.kode', '-');
            $tglPermintaan = now()->toDateString();
            $jamPermintaan = now()->toTimeString();

            if (empty($items)) {
                return response()->json(['success' => false, 'message' => 'Pilih minimal 1 jenis pemeriksaan Lab.'], 400);
            }

            $prefix    = 'PL' . date('Ymd');
            $lastOrder = DB::table('permintaan_lab')->where('noorder', 'LIKE', "{$prefix}%")->max('noorder');
            $lastNum   = $lastOrder ? (int) substr($lastOrder, -4) : 0;
            $noOrder   = $prefix . sprintf('%04d', $lastNum + 1);

            DB::table('permintaan_lab')->insert([
                'noorder'            => $noOrder,
                'no_rawat'           => $noRawat,
                'tgl_permintaan'     => $tglPermintaan,
                'jam_permintaan'     => $jamPermintaan,
                'tgl_sampel'         => $tglPermintaan,
                'jam_sampel'         => $jamPermintaan,
                'tgl_hasil'          => $tglPermintaan,
                'jam_hasil'          => $jamPermintaan,
                'dokter_perujuk'     => $kdDokter,
                'status'             => 'ranap',
                'informasi_tambahan' => $request->input('informasi_tambahan', ''),
                'diagnosa_klinis'    => $request->input('diagnosa_klinis', ''),
            ]);

            foreach ($items as $kdJenis) {
                DB::table('permintaan_detail_permintaan_lab')->insert([
                    'noorder'      => $noOrder,
                    'kd_jenis_prw' => $kdJenis,
                    'id_template'  => 0,
                    'stts_bayar'   => 'Belum',
                ]);
            }

            return response()->json(['success' => true, 'message' => "Permintaan Lab Ranap ({$noOrder}) berhasil dikirim."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Order Radiologi untuk Rawat Inap
     */
    public function simpanRadiologiRanap(Request $request)
    {
        try {
            $noRawat       = $request->input('no_rawat');
            $items         = $request->input('items', []);
            $kdDokter      = session('auth_user.kode', '-');
            $tglPermintaan = now()->toDateString();
            $jamPermintaan = now()->toTimeString();

            if (empty($items)) {
                return response()->json(['success' => false, 'message' => 'Pilih minimal 1 jenis pemeriksaan Radiologi.'], 400);
            }

            $prefix    = 'PR' . date('Ymd');
            $lastOrder = DB::table('permintaan_radiologi')->where('noorder', 'LIKE', "{$prefix}%")->max('noorder');
            $lastNum   = $lastOrder ? (int) substr($lastOrder, -4) : 0;
            $noOrder   = $prefix . sprintf('%04d', $lastNum + 1);

            DB::table('permintaan_radiologi')->insert([
                'noorder'            => $noOrder,
                'no_rawat'           => $noRawat,
                'tgl_permintaan'     => $tglPermintaan,
                'jam_permintaan'     => $jamPermintaan,
                'tgl_sampel'         => $tglPermintaan,
                'jam_sampel'         => $jamPermintaan,
                'tgl_hasil'          => $tglPermintaan,
                'jam_hasil'          => $jamPermintaan,
                'dokter_perujuk'     => $kdDokter,
                'status'             => 'ranap',
                'informasi_tambahan' => $request->input('informasi_tambahan', ''),
                'diagnosa_klinis'    => $request->input('diagnosa_klinis', ''),
            ]);

            foreach ($items as $kdJenis) {
                DB::table('permintaan_pemeriksaan_radiologi')->insert([
                    'noorder'      => $noOrder,
                    'kd_jenis_prw' => $kdJenis,
                    'stts_bayar'   => 'Belum',
                ]);
            }

            return response()->json(['success' => true, 'message' => "Permintaan Radiologi Ranap ({$noOrder}) berhasil dikirim."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== MASTER DATA ENDPOINTS ====================

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

    /** Cari tindakan rawat inap (disesuaikan dengan penjamin / cara bayar pasien) */
    public function masterTindakanRanap(Request $request)
    {
        $q       = $request->input('q', '');
        $noRawat = $request->input('no_rawat', '');

        $kdPj = null;
        if (!empty($noRawat)) {
            $kdPj = DB::table('reg_periksa')->where('no_rawat', $noRawat)->value('kd_pj');
        }

        $query = DB::table('jns_perawatan_inap as j')
            ->leftJoin('penjab as p', 'j.kd_pj', '=', 'p.kd_pj')
            ->where('j.status', '1');

        if (!empty($kdPj)) {
            $query->where(function($w) use ($kdPj) {
                $w->where('j.kd_pj', $kdPj)
                  ->orWhere('j.kd_pj', '-')
                  ->orWhereNull('j.kd_pj');
            });
        }

        if (!empty($q)) {
            $query->where(function($w) use ($q) {
                $w->where('j.nm_perawatan', 'LIKE', "%{$q}%")
                  ->orWhere('j.kd_jenis_prw', 'LIKE', "%{$q}%");
            });
        }

        $data = $query->select(
            'j.kd_jenis_prw',
            'j.nm_perawatan',
            'j.total_byrdr AS total_byr',
            'j.kd_pj',
            'p.png_jawab',
            'j.kelas'
        )->orderBy('j.nm_perawatan', 'asc')->limit(60)->get();

        return response()->json($data);
    }

    /** Cari obat / BHP (disesuaikan dengan kelas rawat inap pasien) */
    public function masterObatRanap(Request $request)
    {
        $q       = $request->input('q', '');
        $noRawat = $request->input('no_rawat', '');
        $kelas   = 'ralan';

        if (!empty($noRawat)) {
            $kamar = DB::table('kamar_inap')
                ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->where('kamar_inap.no_rawat', $noRawat)
                ->where(function($w) {
                    $w->whereNull('kamar_inap.tgl_keluar')
                      ->orWhere('kamar_inap.tgl_keluar', '0000-00-00');
                })
                ->select('kamar.kelas')
                ->first();
            if ($kamar && $kamar->kelas) {
                $kelas = strtolower($kamar->kelas);
            }
        }

        $priceCol = 'ralan';
        if (str_contains($kelas, 'vvip')) {
            $priceCol = 'vvip';
        } elseif (str_contains($kelas, 'vip')) {
            $priceCol = 'vip';
        } elseif (str_contains($kelas, '1') || str_contains($kelas, 'i ') || str_contains($kelas, 'kelas i')) {
            $priceCol = 'kelas1';
        } elseif (str_contains($kelas, '2') || str_contains($kelas, 'ii')) {
            $priceCol = 'kelas2';
        } elseif (str_contains($kelas, '3') || str_contains($kelas, 'iii')) {
            $priceCol = 'kelas3';
        }

        // Depo farmasi rawat inap: B0006
        $kdDepo = 'B0006';

        $data = DB::select(
            "SELECT db.kode_brng, db.nama_brng, db.kode_sat, db.kapasitas,
                    COALESCE(NULLIF(db.{$priceCol}, 0), db.ralan, 0) AS harga,
                    COALESCE(gb.stok_depo, 0) AS stok,
                    COALESCE(gb.stok_depo, 0) AS stok_depo,
                    COALESCE(gbt.stok_total, 0) AS stok_total,
                    COALESCE(k.nama, '-') AS kategori,
                    COALESCE(j.nama, '-') AS jenis
             FROM databarang db
             LEFT JOIN (
                 SELECT kode_brng, SUM(stok) AS stok_depo
                 FROM gudangbarang
                 WHERE kd_bangsal = ?
                 GROUP BY kode_brng
             ) gb ON db.kode_brng = gb.kode_brng
             LEFT JOIN (
                 SELECT kode_brng, SUM(stok) AS stok_total
                 FROM gudangbarang
                 GROUP BY kode_brng
             ) gbt ON db.kode_brng = gbt.kode_brng
             LEFT JOIN kategori_barang k ON db.kode_kategori = k.kode
             LEFT JOIN jenis j ON db.kdjns = j.kdjns
             WHERE db.status = '1' AND (db.nama_brng LIKE ? OR db.kode_brng LIKE ?)
             ORDER BY db.nama_brng ASC
             LIMIT 60",
            [$kdDepo, "%{$q}%", "%{$q}%"]
        );
        return response()->json($data);
    }

    /** Cari jenis pemeriksaan Lab (disesuaikan dengan penjamin pasien) */
    public function masterLabRanap(Request $request)
    {
        $q       = $request->input('q', '');
        $noRawat = $request->input('no_rawat', '');
        $kdPj    = null;
        if (!empty($noRawat)) {
            $kdPj = DB::table('reg_periksa')->where('no_rawat', $noRawat)->value('kd_pj');
        }

        $query = DB::table('jns_perawatan_lab as j')
            ->leftJoin('penjab as p', 'j.kd_pj', '=', 'p.kd_pj')
            ->where('j.status', '1');

        if (!empty($kdPj)) {
            $query->where(function($w) use ($kdPj) {
                $w->where('j.kd_pj', $kdPj)
                  ->orWhere('j.kd_pj', '-')
                  ->orWhereNull('j.kd_pj');
            });
        }

        if (!empty($q)) {
            $query->where('j.nm_perawatan', 'LIKE', "%{$q}%");
        }

        $data = $query->select('j.kd_jenis_prw', 'j.nm_perawatan', 'j.total_byr', 'p.png_jawab')
            ->orderBy('j.nm_perawatan', 'asc')->limit(50)->get();
        return response()->json($data);
    }

    /** Cari jenis pemeriksaan Radiologi (disesuaikan dengan penjamin pasien) */
    public function masterRadiologiRanap(Request $request)
    {
        $q       = $request->input('q', '');
        $noRawat = $request->input('no_rawat', '');
        $kdPj    = null;
        if (!empty($noRawat)) {
            $kdPj = DB::table('reg_periksa')->where('no_rawat', $noRawat)->value('kd_pj');
        }

        $query = DB::table('jns_perawatan_radiologi as j')
            ->leftJoin('penjab as p', 'j.kd_pj', '=', 'p.kd_pj')
            ->where('j.status', '1');

        if (!empty($kdPj)) {
            $query->where(function($w) use ($kdPj) {
                $w->where('j.kd_pj', $kdPj)
                  ->orWhere('j.kd_pj', '-')
                  ->orWhereNull('j.kd_pj');
            });
        }

        if (!empty($q)) {
            $query->where('j.nm_perawatan', 'LIKE', "%{$q}%");
        }

        $data = $query->select('j.kd_jenis_prw', 'j.nm_perawatan', 'j.total_byr', 'p.png_jawab')
            ->orderBy('j.nm_perawatan', 'asc')->limit(50)->get();
        return response()->json($data);
    }

    // ==================== PRIVATE QUERIES ====================

    /**
     * Ambil daftar pasien rawat inap yang SEDANG DIRAWAT saat ini.
     */
    private function getPasienRanap(?string $kdDokter = null, array $filters = []): array
    {
        try {
            $params      = [];
            $extraFilter = '';

            if ($kdDokter !== null) {
                $extraFilter .= ' AND (rp.kd_dokter = ? OR dpjp.kd_dokter = ?)';
                $params[]     = $kdDokter;
                $params[]     = $kdDokter;
            }

            if (!empty($filters['kd_bangsal'])) {
                $extraFilter .= ' AND k.kd_bangsal = ?';
                $params[]     = $filters['kd_bangsal'];
            }

            if (!empty($filters['kd_pj'])) {
                $extraFilter .= ' AND rp.kd_pj = ?';
                $params[]     = $filters['kd_pj'];
            }

            if (!empty($filters['q'])) {
                $kw           = '%' . $filters['q'] . '%';
                $extraFilter .= ' AND (p.nm_pasien LIKE ? OR rp.no_rkm_medis LIKE ? OR ki.no_rawat LIKE ?)';
                $params[]     = $kw;
                $params[]     = $kw;
                $params[]     = $kw;
            }

            if (!empty($filters['filter_tgl_aktif']) && !empty($filters['tgl_awal']) && !empty($filters['tgl_akhir'])) {
                $extraFilter .= ' AND ki.tgl_masuk BETWEEN ? AND ?';
                $params[]     = $filters['tgl_awal'];
                $params[]     = $filters['tgl_akhir'];
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
                    ki.diagnosa_awal,
                    ki.diagnosa_akhir,
                    ki.trf_kamar,
                    ki.ttl_biaya,
                    ki.stts_pulang,
                    rp.no_rkm_medis,
                    rp.tgl_registrasi,
                    rp.jam_reg,
                    rp.kd_dokter,
                    rp.status_bayar,
                    rp.p_jawab,
                    rp.hubunganpj,
                    rp.almt_pj,
                    CONCAT(rp.umurdaftar, ' ', rp.sttsumur) AS umur_daftar,
                    p.nm_pasien,
                    p.tgl_lahir,
                    p.jk,
                    p.alamat,
                    CONCAT(IFNULL(p.alamat,''),
                           IF(kel.nm_kel IS NOT NULL AND kel.nm_kel != '', CONCAT(', Kel. ', kel.nm_kel), ''),
                           IF(kec.nm_kec IS NOT NULL AND kec.nm_kec != '', CONCAT(', Kec. ', kec.nm_kec), ''),
                           IF(kab.nm_kab IS NOT NULL AND kab.nm_kab != '', CONCAT(', ', kab.nm_kab), '')) AS alamat_lengkap,
                    p.no_tlp,
                    p.agama,
                    p.pekerjaan,
                    p.gol_darah,
                    pol.nm_poli,
                    COALESCE(d_dpjp.nm_dokter, d.nm_dokter) AS nm_dokter,
                    COALESCE(s_dpjp.nm_sps, s.nm_sps) AS spesialis,
                    k.kd_bangsal,
                    k.kelas,
                    bang.nm_bangsal,
                    pjb.png_jawab AS jenis_bayar,
                    dp_utama.kd_penyakit,
                    pyk.nm_penyakit,
                    IFNULL(sep.no_sep, '') AS no_sep,
                    CASE
                        WHEN soap.no_rawat IS NOT NULL THEN 'terlayani'
                        ELSE 'belum'
                    END AS status_soap_hari_ini
                 FROM (
                     SELECT no_rawat, kd_kamar, tgl_masuk, jam_masuk,
                            tgl_keluar, jam_keluar, lama,
                            diagnosa_awal, diagnosa_akhir, trf_kamar, ttl_biaya, stts_pulang
                     FROM kamar_inap
                     WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'
                 ) ki
                 INNER JOIN reg_periksa rp  ON rp.no_rawat     = ki.no_rawat
                 LEFT JOIN  pasien p        ON p.no_rkm_medis  = rp.no_rkm_medis
                 LEFT JOIN  kelurahan kel   ON kel.kd_kel      = p.kd_kel
                 LEFT JOIN  kecamatan kec   ON kec.kd_kec      = p.kd_kec
                 LEFT JOIN  kabupaten kab   ON kab.kd_kab      = p.kd_kab
                 LEFT JOIN  poliklinik pol  ON pol.kd_poli     = rp.kd_poli
                 LEFT JOIN  dokter d        ON d.kd_dokter     = rp.kd_dokter
                 LEFT JOIN  spesialis s     ON s.kd_sps        = d.kd_sps
                 LEFT JOIN  penjab pjb      ON pjb.kd_pj       = rp.kd_pj
                 LEFT JOIN (
                     SELECT no_rawat, MIN(kd_dokter) AS kd_dokter
                     FROM dpjp_ranap GROUP BY no_rawat
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
                 LEFT JOIN bridging_sep sep ON sep.no_rawat    = ki.no_rawat
                 LEFT JOIN (
                     SELECT DISTINCT no_rawat FROM pemeriksaan_ranap
                     WHERE tgl_perawatan = CURDATE()
                 ) soap                     ON soap.no_rawat = ki.no_rawat
                 WHERE 1=1
                 {$extraFilter}
                 ORDER BY bang.nm_bangsal ASC, ki.tgl_masuk ASC, ki.jam_masuk ASC",
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
    private function getPasienRanapSelesai(?string $kdDokter = null, array $filters = []): array
    {
        try {
            $params      = [];
            $extraFilter = '';

            if ($kdDokter !== null) {
                $extraFilter .= ' AND (rp.kd_dokter = ? OR dpjp.kd_dokter = ?)';
                $params[]     = $kdDokter;
                $params[]     = $kdDokter;
            }

            if (!empty($filters['kd_bangsal'])) {
                $extraFilter .= ' AND k.kd_bangsal = ?';
                $params[]     = $filters['kd_bangsal'];
            }

            if (!empty($filters['kd_pj'])) {
                $extraFilter .= ' AND rp.kd_pj = ?';
                $params[]     = $filters['kd_pj'];
            }

            if (!empty($filters['q'])) {
                $kw           = '%' . $filters['q'] . '%';
                $extraFilter .= ' AND (p.nm_pasien LIKE ? OR rp.no_rkm_medis LIKE ? OR ki.no_rawat LIKE ?)';
                $params[]     = $kw;
                $params[]     = $kw;
                $params[]     = $kw;
            }

            if (!empty($filters['tgl_awal']) && !empty($filters['tgl_akhir'])) {
                $extraFilter .= ' AND ki.tgl_keluar BETWEEN ? AND ?';
                $params[]     = $filters['tgl_awal'];
                $params[]     = $filters['tgl_akhir'];
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
                    ki.diagnosa_awal,
                    ki.diagnosa_akhir,
                    ki.trf_kamar,
                    ki.ttl_biaya,
                    rp.no_rkm_medis,
                    rp.tgl_registrasi,
                    rp.jam_reg,
                    rp.kd_dokter,
                    rp.status_bayar,
                    rp.p_jawab,
                    rp.hubunganpj,
                    rp.almt_pj,
                    CONCAT(rp.umurdaftar, ' ', rp.sttsumur) AS umur_daftar,
                    p.nm_pasien,
                    p.tgl_lahir,
                    p.jk,
                    p.alamat,
                    CONCAT(IFNULL(p.alamat,''),
                           IF(kel.nm_kel IS NOT NULL AND kel.nm_kel != '', CONCAT(', Kel. ', kel.nm_kel), ''),
                           IF(kec.nm_kec IS NOT NULL AND kec.nm_kec != '', CONCAT(', Kec. ', kec.nm_kec), ''),
                           IF(kab.nm_kab IS NOT NULL AND kab.nm_kab != '', CONCAT(', ', kab.nm_kab), '')) AS alamat_lengkap,
                    p.no_tlp,
                    p.agama,
                    p.pekerjaan,
                    p.gol_darah,
                    pol.nm_poli,
                    COALESCE(d_dpjp.nm_dokter, d.nm_dokter) AS nm_dokter,
                    COALESCE(s_dpjp.nm_sps, s.nm_sps) AS spesialis,
                    k.kd_bangsal,
                    k.kelas,
                    bang.nm_bangsal,
                    pjb.png_jawab AS jenis_bayar,
                    dp_utama.kd_penyakit,
                    pyk.nm_penyakit,
                    IFNULL(sep.no_sep, '') AS no_sep
                 FROM kamar_inap ki
                 INNER JOIN reg_periksa rp  ON rp.no_rawat     = ki.no_rawat
                 LEFT JOIN  pasien p        ON p.no_rkm_medis  = rp.no_rkm_medis
                 LEFT JOIN  kelurahan kel   ON kel.kd_kel      = p.kd_kel
                 LEFT JOIN  kecamatan kec   ON kec.kd_kec      = p.kd_kec
                 LEFT JOIN  kabupaten kab   ON kab.kd_kab      = p.kd_kab
                 LEFT JOIN  poliklinik pol  ON pol.kd_poli     = rp.kd_poli
                 LEFT JOIN  dokter d        ON d.kd_dokter     = rp.kd_dokter
                 LEFT JOIN  spesialis s     ON s.kd_sps        = d.kd_sps
                 LEFT JOIN  penjab pjb      ON pjb.kd_pj       = rp.kd_pj
                 LEFT JOIN (
                     SELECT no_rawat, MIN(kd_dokter) AS kd_dokter
                     FROM dpjp_ranap GROUP BY no_rawat
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
                 LEFT JOIN bridging_sep sep ON sep.no_rawat    = ki.no_rawat
                 WHERE ki.tgl_keluar IS NOT NULL
                   AND ki.tgl_keluar != '0000-00-00'
                   AND ki.stts_pulang != '-'
                 {$extraFilter}
                 ORDER BY ki.tgl_keluar DESC, ki.jam_keluar DESC
                 LIMIT 300",
                $params
            );

            return $result ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Statistik rawat inap
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
            'soap_hari_ini'  => 0,
        ];

        try {
            $params       = [];
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
                                         AND k.kelas NOT LIKE '%2%' AND k.kelas NOT LIKE '%3%' AND k.kelas NOT LIKE '%VIP%'
                                        THEN ki.no_rawat END) AS kelas_i,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND (k.kelas LIKE '%2%' OR (k.kelas LIKE '%II%' AND k.kelas NOT LIKE '%III%'))
                                         AND k.kelas NOT LIKE '%VIP%'
                                        THEN ki.no_rawat END) AS kelas_ii,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND (k.kelas LIKE '%3%' OR k.kelas LIKE '%III%')
                                        THEN ki.no_rawat END) AS kelas_iii,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND (k.kelas IS NULL OR k.kelas = '')
                                        THEN ki.no_rawat END) AS tanpa_kelas,
                    COALESCE(AVG(CASE WHEN ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00'
                                      THEN DATEDIFF(CURDATE(), ki.tgl_masuk) END), 0) AS rata_lama_inap,
                    COUNT(DISTINCT CASE WHEN (ki.tgl_keluar IS NULL OR ki.tgl_keluar = '0000-00-00') AND soap.no_rawat IS NOT NULL
                                        THEN ki.no_rawat END) AS soap_hari_ini
                 FROM kamar_inap ki
                 INNER JOIN reg_periksa rp ON rp.no_rawat = ki.no_rawat
                 LEFT JOIN (
                     SELECT no_rawat, MIN(kd_dokter) AS kd_dokter
                     FROM dpjp_ranap GROUP BY no_rawat
                 ) dpjp ON dpjp.no_rawat = ki.no_rawat
                 LEFT JOIN kamar k ON k.kd_kamar = ki.kd_kamar
                 LEFT JOIN (
                     SELECT DISTINCT no_rawat FROM pemeriksaan_ranap WHERE tgl_perawatan = CURDATE()
                 ) soap ON soap.no_rawat = ki.no_rawat
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
                $defaults['soap_hari_ini']  = (int)   ($rows[0]->soap_hari_ini  ?? 0);
            }
        } catch (\Exception $e) {
            // Return defaults jika error
        }

        return $defaults;
    }

    /**
     * SIMPAN RESUME MEDIS PASIEN RAWAT INAP (resume_pasien_ranap)
     */
    public function simpanResumeRanap(Request $request)
    {
        try {
            $noRawat  = $request->input('no_rawat');
            $kdDokter = session('auth_user.kode', '-');

            if (!$noRawat) {
                return response()->json(['success' => false, 'message' => 'No. Rawat tidak valid.'], 400);
            }

            $data = [
                'kd_dokter'             => $kdDokter,
                'diagnosa_awal'         => $request->input('diagnosa_awal', '-'),
                'alasan'                => $request->input('alasan', '-'),
                'keluhan_utama'         => $request->input('keluhan_utama', '-'),
                'pemeriksaan_fisik'     => $request->input('pemeriksaan_fisik', '-'),
                'jalannya_penyakit'     => $request->input('jalannya_penyakit', '-'),
                'pemeriksaan_penunjang' => $request->input('pemeriksaan_penunjang', '-'),
                'hasil_laborat'         => $request->input('hasil_laborat', '-'),
                'tindakan_dan_operasi'  => $request->input('tindakan_dan_operasi', '-'),
                'obat_di_rs'            => $request->input('obat_di_rs', '-'),
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
                'alergi'                => $request->input('alergi', '-'),
                'diet'                  => $request->input('diet', '-'),
                'lab_belum'             => $request->input('lab_belum', '-'),
                'edukasi'               => $request->input('edukasi', '-'),
                'cara_keluar'           => $request->input('cara_keluar', 'Atas Izin Dokter'),
                'ket_keluar'            => $request->input('ket_keluar', ''),
                'keadaan'               => $request->input('keadaan', 'Membaik'),
                'ket_keadaan'           => $request->input('ket_keadaan', ''),
                'dilanjutkan'           => $request->input('dilanjutkan', 'Kembali Ke RS'),
                'ket_dilanjutkan'       => $request->input('ket_dilanjutkan', ''),
                'kontrol'               => $request->input('kontrol') ?: null,
                'obat_pulang'           => $request->input('obat_pulang', '-'),
                'pemeriksaan_lain'      => $request->input('pemeriksaan_lain', ''),
            ];

            DB::table('resume_pasien_ranap')->updateOrInsert(
                ['no_rawat' => $noRawat],
                $data
            );

            return response()->json(['success' => true, 'message' => 'Resume Medis Pasien Rawat Inap berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTemplateResume(Request $request)
    {
        $kdDokter = session('auth_user.kode', '-');
        $q = $request->input('q', '');

        $query = DB::table('template_resume_pasien')
            ->where('tipe', 'ranap')
            ->where(function ($w) use ($kdDokter) {
                $w->where('kd_dokter', $kdDokter)
                  ->orWhereNull('kd_dokter')
                  ->orWhere('kd_dokter', '');
            });

        if (!empty($q)) {
            $query->where(function ($w) use ($q) {
                $w->where('nama_template', 'LIKE', "%{$q}%")
                  ->orWhere('diagnosa_awal', 'LIKE', "%{$q}%")
                  ->orWhere('diagnosa_utama', 'LIKE', "%{$q}%")
                  ->orWhere('keluhan_utama', 'LIKE', "%{$q}%");
            });
        }

        $templates = $query->orderBy('nama_template', 'ASC')->get();

        // Khanza examination templates
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
                'tipe'                  => 'ranap',
                'nama_template'         => $namaTemplate,
                'diagnosa_awal'         => $request->input('diagnosa_awal', ''),
                'alasan'                => $request->input('alasan', ''),
                'keluhan_utama'         => $request->input('keluhan_utama', ''),
                'pemeriksaan_fisik'     => $request->input('pemeriksaan_fisik', ''),
                'jalannya_penyakit'     => $request->input('jalannya_penyakit', ''),
                'pemeriksaan_penunjang' => $request->input('pemeriksaan_penunjang', ''),
                'hasil_laborat'         => $request->input('hasil_laborat', ''),
                'tindakan_dan_operasi'  => $request->input('tindakan_dan_operasi', ''),
                'obat_di_rs'            => $request->input('obat_di_rs', ''),
                'diagnosa_utama'        => $request->input('diagnosa_utama', ''),
                'kd_diagnosa_utama'     => $request->input('kd_diagnosa_utama', ''),
                'diagnosa_sekunder'     => $request->input('diagnosa_sekunder', ''),
                'kd_diagnosa_sekunder'  => $request->input('kd_diagnosa_sekunder', ''),
                'diagnosa_sekunder2'    => $request->input('diagnosa_sekunder2', ''),
                'kd_diagnosa_sekunder2' => $request->input('kd_diagnosa_sekunder2', ''),
                'diagnosa_sekunder3'    => $request->input('diagnosa_sekunder3', ''),
                'kd_diagnosa_sekunder3' => $request->input('kd_diagnosa_sekunder3', ''),
                'prosedur_utama'        => $request->input('prosedur_utama', ''),
                'kd_prosedur_utama'     => $request->input('kd_prosedur_utama', ''),
                'prosedur_sekunder'     => $request->input('prosedur_sekunder', ''),
                'kd_prosedur_sekunder'  => $request->input('kd_prosedur_sekunder', ''),
                'alergi'                => $request->input('alergi', ''),
                'diet'                  => $request->input('diet', ''),
                'lab_belum'             => $request->input('lab_belum', ''),
                'edukasi'               => $request->input('edukasi', ''),
                'cara_keluar'           => $request->input('cara_keluar', 'Atas Izin Dokter'),
                'ket_keluar'            => $request->input('ket_keluar', ''),
                'keadaan'               => $request->input('keadaan', 'Membaik'),
                'ket_keadaan'           => $request->input('ket_keadaan', ''),
                'dilanjutkan'           => $request->input('dilanjutkan', 'Kembali Ke RS'),
                'ket_dilanjutkan'       => $request->input('ket_dilanjutkan', ''),
                'kontrol'               => $request->input('kontrol', ''),
                'obat_pulang'           => $request->input('obat_pulang', ''),
                'updated_at'            => now(),
            ];

            if (!empty($id)) {
                DB::table('template_resume_pasien')->where('id', $id)->update($data);
                $msg = 'Template Resume Ranap berhasil diperbarui.';
            } else {
                $data['created_at'] = now();
                $id = DB::table('template_resume_pasien')->insertGetId($data);
                $msg = 'Template Resume Ranap berhasil disimpan.';
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
            return response()->json(['success' => true, 'message' => 'Template Resume Ranap berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ===================================================================
    // OPERASI (JADWAL OPERASI & LAPORAN OPERASI STANDAR SIMRS-NAMIRA)
    // ===================================================================

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
            ->leftJoin('kamar_inap as ki', function ($j) {
                $j->on('rp.no_rawat', '=', 'ki.no_rawat')
                  ->where('ki.stts_pulang', '!=', 'Pindah Kamar');
            })
            ->leftJoin('kamar as k', 'ki.kd_kamar', '=', 'k.kd_kamar')
            ->leftJoin('bangsal as b', 'k.kd_bangsal', '=', 'b.kd_bangsal')
            ->where('lo.no_rawat', $noRawat);

        if (!empty($tanggal)) {
            $lapQuery->where('lo.tanggal', $tanggal);
        }

        $lap = $lapQuery->select('lo.*', 'p.nm_pasien', 'p.no_rkm_medis', 'p.jk', 'p.tgl_lahir', 'rp.umurdaftar', 'rp.sttsumur', 'd.nm_dokter as operator', 'b.nm_bangsal as nm_poli')
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
