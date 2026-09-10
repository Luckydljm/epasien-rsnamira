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
    public function getPasienDetail(Request $request, $no_rawat_b64)
    {
        try {
            $noRawat = base64_decode($no_rawat_b64);
            if (!str_contains($noRawat, '/')) {
                $noRawat = urldecode($no_rawat_b64);
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

            // 1. Data SOAP (pemeriksaan_ralan) berdasarkan no_rawat
            $soapList = DB::select(
                "SELECT pr.*, rp.tgl_registrasi, d.nm_dokter
                 FROM pemeriksaan_ralan pr
                 JOIN reg_periksa rp ON pr.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON rp.kd_dokter = d.kd_dokter
                 WHERE pr.no_rawat = ?
                 ORDER BY pr.tgl_perawatan DESC, pr.jam_rawat DESC
                 LIMIT 50",
                [$noRawat]
            );

            // 2. Data Awal Medis berdasarkan no_rawat
            $awalMedisList = DB::select(
                "SELECT pmr.no_rawat, pmr.tanggal, pmr.kd_dokter, d.nm_dokter, pmr.anamnesis, pmr.keluhan_utama, pmr.rps, pmr.rpd, pmr.diagnosis, pmr.tata, 'Medis Ralan General' AS departemen
                 FROM penilaian_medis_ralan pmr
                 JOIN reg_periksa rp ON pmr.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pmr.kd_dokter = d.kd_dokter
                 WHERE pmr.no_rawat = ?

                 UNION ALL

                 SELECT pd.no_rawat, pd.tanggal, pd.kd_dokter, d.nm_dokter, pd.anamnesis, pd.keluhan_utama, pd.rps, pd.rpd, pd.diagnosis, pd.terapi AS tata, 'Medis Penyakit Dalam' AS departemen
                 FROM penilaian_medis_ralan_penyakit_dalam pd
                 JOIN reg_periksa rp ON pd.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON pd.kd_dokter = d.kd_dokter
                 WHERE pd.no_rawat = ?

                 UNION ALL

                 SELECT igd.no_rawat, igd.tanggal, igd.kd_dokter, d.nm_dokter, igd.anamnesis, igd.keluhan_utama, igd.rps, igd.rpd, igd.diagnosis, igd.tata, 'Medis IGD' AS departemen
                 FROM penilaian_medis_igd igd
                 JOIN reg_periksa rp ON igd.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON igd.kd_dokter = d.kd_dokter
                 WHERE igd.no_rawat = ?

                 ORDER BY tanggal DESC
                 LIMIT 50",
                [$noRawat, $noRawat, $noRawat]
            );
            $awalMedis = $awalMedisList[0] ?? null;

            // 3. Permintaan & HASIL Laboratorium berdasarkan no_rawat
            $labOrders = DB::select(
                "SELECT pl.noorder, pl.no_rawat, pl.tgl_permintaan, pl.jam_permintaan, pl.diagnosa_klinis, pl.informasi_tambahan,
                        GROUP_CONCAT(jpl.nm_perawatan SEPARATOR ', ') AS detail_pemeriksaan
                 FROM permintaan_lab pl
                 JOIN reg_periksa rp ON pl.no_rawat = rp.no_rawat
                 LEFT JOIN permintaan_detail_permintaan_lab pdl ON pl.noorder = pdl.noorder
                 LEFT JOIN jns_perawatan_lab jpl ON pdl.kd_jenis_prw = jpl.kd_jenis_prw
                 WHERE pl.no_rawat = ?
                 GROUP BY pl.noorder, pl.no_rawat, pl.tgl_permintaan, pl.jam_permintaan, pl.diagnosa_klinis, pl.informasi_tambahan
                 ORDER BY pl.tgl_permintaan DESC, pl.jam_permintaan DESC
                 LIMIT 50",
                [$noRawat]
            );

            $labResults = DB::select(
                "SELECT dpl.no_rawat, dpl.tgl_periksa, dpl.jam, jpl.nm_perawatan,
                        COALESCE(tl.Pemeriksaan, jpl.nm_perawatan) AS nama_pemeriksaan,
                        COALESCE(dpl.nilai, '-') AS nilai,
                        COALESCE(dpl.nilai_rujukan, '-') AS nilai_rujukan,
                        COALESCE(dpl.keterangan, '') AS keterangan
                 FROM detail_periksa_lab dpl
                 JOIN reg_periksa rp ON dpl.no_rawat = rp.no_rawat
                 LEFT JOIN jns_perawatan_lab jpl ON dpl.kd_jenis_prw = jpl.kd_jenis_prw
                 LEFT JOIN template_laboratorium tl ON dpl.id_template = tl.id_template
                 WHERE dpl.no_rawat = ?
                 ORDER BY dpl.tgl_periksa DESC, dpl.jam DESC
                 LIMIT 100",
                [$noRawat]
            );

            // 4. Permintaan & HASIL Radiologi (Ekspertisi) berdasarkan no_rawat
            $radOrders = DB::select(
                "SELECT pr.noorder, pr.no_rawat, pr.tgl_permintaan, pr.jam_permintaan, pr.diagnosa_klinis, pr.informasi_tambahan,
                        GROUP_CONCAT(jpr.nm_perawatan SEPARATOR ', ') AS detail_pemeriksaan
                 FROM permintaan_radiologi pr
                 JOIN reg_periksa rp ON pr.no_rawat = rp.no_rawat
                 LEFT JOIN permintaan_pemeriksaan_radiologi pdr ON pr.noorder = pdr.noorder
                 LEFT JOIN jns_perawatan_radiologi jpr ON pdr.kd_jenis_prw = jpr.kd_jenis_prw
                 WHERE pr.no_rawat = ?
                 GROUP BY pr.noorder, pr.no_rawat, pr.tgl_permintaan, pr.jam_permintaan, pr.diagnosa_klinis, pr.informasi_tambahan
                 ORDER BY pr.tgl_permintaan DESC, pr.jam_permintaan DESC
                 LIMIT 50",
                [$noRawat]
            );

            $radResults = DB::select(
                "SELECT hr.no_rawat, hr.tgl_periksa, hr.jam, hr.hasil
                 FROM hasil_radiologi hr
                 JOIN reg_periksa rp ON hr.no_rawat = rp.no_rawat
                 WHERE hr.no_rawat = ?
                 ORDER BY hr.tgl_periksa DESC, hr.jam DESC
                 LIMIT 20",
                [$noRawat]
            );

            // 5. Data Resep Dokter berdasarkan no_rawat (Obat Jadi & Obat Racikan)
            $rawResep = DB::select(
                "SELECT ro.no_resep, ro.no_rawat, ro.tgl_perawatan, COALESCE(ro.jam, '00:00:00') AS jam,
                        ro.tgl_peresepan, ro.jam_peresepan, ro.status, ro.tgl_penyerahan, ro.jam_penyerahan,
                        COALESCE(d.nm_dokter, 'Dokter') AS nm_dokter
                 FROM resep_obat ro
                 LEFT JOIN dokter d ON ro.kd_dokter = d.kd_dokter
                 WHERE ro.no_rawat = ?
                 ORDER BY ro.tgl_perawatan DESC, ro.jam DESC
                 LIMIT 50",
                [$noRawat]
            );

            $resepList = [];
            foreach ($rawResep as $r) {
                // Non-racikan
                $obatList = DB::select(
                    "SELECT rd.kode_brng, rd.jml, rd.aturan_pakai, rd.keterangan,
                            db.nama_brng, db.kode_sat, db.ralanshare AS harga
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
                    'detail_obat'     => implode('<br>', $summaryParts),
                    'obat_list'       => $obatList,
                    'racik_list'      => $racikList,
                ];
            }

            // 6. Data Booking Operasi & LAPORAN OPERASI berdasarkan no_rawat
            $bookingOps = DB::select(
                "SELECT bo.*, po.nm_perawatan AS nama_paket, po.kelas, po.kategori, p.png_jawab, d.nm_dokter AS dokter_operator
                 FROM booking_operasi bo
                 JOIN reg_periksa rp ON bo.no_rawat = rp.no_rawat
                 LEFT JOIN paket_operasi po ON bo.kode_paket = po.kode_paket
                 LEFT JOIN penjab p ON po.kd_pj = p.kd_pj
                 LEFT JOIN dokter d ON bo.kd_dokter = d.kd_dokter
                 WHERE bo.no_rawat = ?
                 ORDER BY bo.tanggal DESC, bo.jam_mulai DESC
                 LIMIT 50",
                [$noRawat]
            );

            $laporanOps = DB::select(
                "SELECT lo.*, d.nm_dokter AS dokter_operator
                 FROM laporan_operasi lo
                 JOIN reg_periksa rp ON lo.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON rp.kd_dokter = d.kd_dokter
                 WHERE lo.no_rawat = ?
                 ORDER BY lo.tanggal DESC
                 LIMIT 20",
                [$noRawat]
            );

            // 7. Data Diagnosa Pasien (ICD-10)
            $diagnosaList = DB::select(
                "SELECT dp.no_rawat, dp.kd_penyakit, dp.prioritas, dp.status, dp.status_penyakit, py.nm_penyakit
                 FROM diagnosa_pasien dp
                 LEFT JOIN penyakit py ON py.kd_penyakit = dp.kd_penyakit
                 WHERE dp.no_rawat = ?
                 ORDER BY dp.prioritas ASC, dp.kd_penyakit ASC",
                [$noRawat]
            );

            // 8. Data Tindakan Rawat Jalan (rawat_jl_dr + rawat_jl_drpr)
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

                 ORDER BY tgl_perawatan DESC, jam_rawat DESC
                 LIMIT 200",
                [$noRawat, $noRawat]
            );

            // 9. Data Resume Pasien Rawat Jalan (resume_pasien)
            $resumePasien = DB::table('resume_pasien')
                ->where('no_rawat', $noRawat)
                ->first();

            return response()->json([
                'success'       => true,
                'pasien'        => $pasien,
                'soapList'      => $soapList,
                'awalMedis'     => $awalMedis,
                'awalMedisList' => $awalMedisList,
                'labOrders'     => $labOrders,
                'labResults'    => $labResults,
                'radOrders'     => $radOrders,
                'radResults'    => $radResults,
                'resepList'     => $resepList,
                'bookingOps'    => $bookingOps,
                'laporanOps'    => $laporanOps,
                'diagnosaList'  => $diagnosaList,
                'tindakanList'  => $tindakanList,
                'resumePasien'  => $resumePasien,
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
        $q = $request->input('q', '');
        $data = DB::select(
            "SELECT db.kode_brng, db.nama_brng, db.kode_sat, db.ralanshare AS harga, db.kapasitas,
                    COALESCE(SUM(gb.stok), 0) AS stok
             FROM databarang db
             LEFT JOIN gudangbarang gb ON db.kode_brng = gb.kode_brng
             WHERE db.status = '1' AND (db.nama_brng LIKE ? OR db.kode_brng LIKE ?)
             GROUP BY db.kode_brng, db.nama_brng, db.kode_sat, db.ralanshare, db.kapasitas
             ORDER BY db.nama_brng ASC LIMIT 50",
            ["%{$q}%", "%{$q}%"]
        );
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
            $noRawat   = $request->input('no_rawat');
            $items     = $request->input('items', []);   // Obat Jadi: [{kode_brng, jml, aturan_pakai, keterangan}]
            $racikan   = $request->input('racikan', []); // Obat Racik: [{nama_racik, kd_racik, jml_dr, aturan_pakai, keterangan, detail: [{kode_brng, p1, p2, kandungan, jml}]}]
            $kdDokter  = session('auth_user.kode', '-');
            $tglResep  = now()->toDateString();
            $jamResep  = now()->toTimeString();

            if (empty($items) && empty($racikan)) {
                return response()->json(['success' => false, 'message' => 'Pilih minimal 1 obat jadi atau racikan untuk resep.'], 400);
            }

            // Generate No Resep: YYYYMMDDxxxx
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
                $noRacik = sprintf('%02d', $idx + 1);
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

            return response()->json(['success' => true, 'message' => "E-Resep Dokter ({$noResep}) berhasil disimpan."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function hapusResep(Request $request)
    {
        try {
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
     * 6. MASTER & SIMPAN JADWAL / BOOKING OPERASI
     */
    public function masterOperasi(Request $request)
    {
        $q = $request->input('q', '');
        $data = DB::table('paket_operasi as po')
            ->leftJoin('penjab as p', 'po.kd_pj', '=', 'p.kd_pj')
            ->where(function ($w) use ($q) {
                $w->where('po.nm_perawatan', 'LIKE', "%{$q}%")
                  ->orWhere('po.kode_paket', 'LIKE', "%{$q}%");
            })
            ->select('po.kode_paket', 'po.nm_perawatan', 'po.kategori', 'po.kelas', 'p.png_jawab')
            ->orderBy('po.nm_perawatan', 'asc')
            ->limit(60)
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

    public function simpanBookingOperasi(Request $request)
    {
        try {
            $noRawat   = $request->input('no_rawat');
            $kodePaket = $request->input('kode_paket');
            $tanggal   = $request->input('tanggal', now()->toDateString());
            $jamMulai  = $request->input('jam_mulai', '08:00:00');
            $jamSelesai= $request->input('jam_selesai', '09:30:00');
            $kdDokter  = $request->input('kd_dokter') ?: session('auth_user.kode', '');
            $status    = $request->input('status', 'Menunggu');

            if (empty($kodePaket)) {
                return response()->json(['success' => false, 'message' => 'Pilih paket operasi terlebih dahulu.'], 400);
            }

            DB::table('booking_operasi')->insert([
                'no_rawat'   => $noRawat,
                'kode_paket' => $kodePaket,
                'tanggal'    => $tanggal,
                'jam_mulai'  => $jamMulai,
                'jam_selesai'=> $jamSelesai,
                'status'     => $status,
                'kd_dokter'  => $kdDokter,
            ]);

            return response()->json(['success' => true, 'message' => 'Jadwal Booking Operasi berhasil disimpan.']);
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
}
