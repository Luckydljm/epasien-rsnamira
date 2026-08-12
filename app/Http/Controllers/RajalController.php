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

            // 5. Data Resep Dokter berdasarkan no_rawat
            $resepList = DB::select(
                "SELECT ro.no_resep, ro.no_rawat, ro.tgl_perawatan, COALESCE(ro.jam, '00:00:00') AS jam, COALESCE(d.nm_dokter, 'Dokter') AS nm_dokter,
                        GROUP_CONCAT(CONCAT('<b>', COALESCE(db.nama_brng, rd.kode_brng), '</b> (', COALESCE(rd.jml, 1), ' ', COALESCE(db.kode_sat, ''), ') — <i>', COALESCE(NULLIF(rd.aturan_pakai, ''), '-'), '</i>') SEPARATOR '<br>') AS detail_obat
                 FROM resep_obat ro
                 JOIN reg_periksa rp ON ro.no_rawat = rp.no_rawat
                 LEFT JOIN dokter d ON ro.kd_dokter = d.kd_dokter
                 LEFT JOIN resep_dokter rd ON ro.no_resep = rd.no_resep
                 LEFT JOIN databarang db ON rd.kode_brng = db.kode_brng
                 WHERE ro.no_rawat = ?
                 GROUP BY ro.no_resep, ro.no_rawat, ro.tgl_perawatan, ro.jam, d.nm_dokter
                 ORDER BY ro.tgl_perawatan DESC, ro.jam DESC
                 LIMIT 50",
                [$noRawat]
            );

            // 6. Data Booking Operasi & LAPORAN OPERASI berdasarkan no_rawat
            $bookingOps = DB::select(
                "SELECT bo.*, po.nm_perawatan AS nama_paket, d.nm_dokter AS dokter_operator
                 FROM booking_operasi bo
                 JOIN reg_periksa rp ON bo.no_rawat = rp.no_rawat
                 LEFT JOIN paket_operasi po ON bo.kode_paket = po.kode_paket
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
        $q = $request->input('q', '');
        $data = DB::select(
            "SELECT kd_jenis_prw, nm_perawatan, total_byr FROM jns_perawatan_lab
             WHERE status = '1' AND nm_perawatan LIKE ? ORDER BY nm_perawatan ASC LIMIT 50",
            ["%{$q}%"]
        );
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
        $q = $request->input('q', '');
        $data = DB::select(
            "SELECT kd_jenis_prw, nm_perawatan, total_byr FROM jns_perawatan_radiologi
             WHERE status = '1' AND nm_perawatan LIKE ? ORDER BY nm_perawatan ASC LIMIT 50",
            ["%{$q}%"]
        );
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
            "SELECT kode_brng, nama_brng, kode_sat, ralanshare AS harga
             FROM databarang
             WHERE status = '1' AND (nama_brng LIKE ? OR kode_brng LIKE ?)
             ORDER BY nama_brng ASC LIMIT 50",
            ["%{$q}%", "%{$q}%"]
        );
        return response()->json($data);
    }

    public function simpanResep(Request $request)
    {
        try {
            $noRawat   = $request->input('no_rawat');
            $items     = $request->input('items', []); // array of ['kode_brng' => x, 'jml' => y, 'aturan_pakai' => z]
            $kdDokter  = session('auth_user.kode', '-');
            $tglResep  = now()->toDateString();
            $jamResep  = now()->toTimeString();

            if (empty($items)) {
                return response()->json(['success' => false, 'message' => 'Pilih minimal 1 obat untuk resep.'], 400);
            }

            // Generate No Resep: YYYYMMDDxxxx
            $prefix = date('Ymd');
            $lastResep = DB::table('resep_obat')->where('no_resep', 'LIKE', "{$prefix}%")->max('no_resep');
            $lastNum = $lastResep ? (int) substr($lastResep, -4) : 0;
            $noResep = $prefix . sprintf('%04d', $lastNum + 1);

            DB::table('resep_obat')->insert([
                'no_resep'      => $noResep,
                'tgl_perawatan' => $tglResep,
                'jam_perawatan' => $jamResep,
                'no_rawat'      => $noRawat,
                'kd_dokter'     => $kdDokter,
                'tgl_peresepan' => $tglResep,
                'jam_peresepan' => $jamResep,
                'status'        => 'ralan',
            ]);

            foreach ($items as $item) {
                if (!empty($item['kode_brng']) && !empty($item['jml'])) {
                    DB::table('resep_dokter')->insert([
                        'no_resep'     => $noResep,
                        'kode_brng'    => $item['kode_brng'],
                        'jml'          => $item['jml'],
                        'aturan_pakai' => $item['aturan_pakai'] ?? '-',
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => "E-Resep Dokter ({$noResep}) berhasil disimpan."]);
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
        $data = DB::select(
            "SELECT kode_paket, nm_perawatan FROM paket_operasi
             WHERE nm_perawatan LIKE ? OR kode_paket LIKE ?
             ORDER BY nm_perawatan ASC LIMIT 50",
            ["%{$q}%", "%{$q}%"]
        );
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
}
