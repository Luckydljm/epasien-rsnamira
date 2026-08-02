<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortalController extends Controller
{
    /**
     * Halaman Portal Utama — Pilihan Rawat Jalan & Rawat Inap
     */
    public function index()
    {
        $kodeUser  = session('auth_user.kode');
        $isAdmin   = session('auth_user.is_admin', false);
        $isDokter  = session('auth_user.is_dokter', false);
        $nmDokter  = session('auth_user.nm_dokter');
        $spesialis = session('auth_user.spesialis');

        $kdDokterFilter = (!$isAdmin && $isDokter) ? $kodeUser : null;

        $stats = $this->getPortalStats($kdDokterFilter);

        return view('portal.index', compact(
            'stats', 'isDokter', 'isAdmin', 'nmDokter', 'spesialis', 'kodeUser'
        ));
    }

    /**
     * Pilih moda layanan (rajal / ranap) dan simpan ke session active_portal
     */
    public function selectMode(string $mode)
    {
        if (in_array($mode, ['rajal', 'ranap'])) {
            session(['active_portal' => $mode]);
            return redirect()->route($mode === 'rajal' ? 'rawat-jalan' : 'rawat-inap');
        }
        return redirect()->route('portal');
    }

    /**
     * Ringkasan statistik untuk kartu portal
     */
    private function getPortalStats(?string $kdDokter): array
    {
        $today = now()->toDateString();
        $stats = [
            'rajal' => 0,
            'ranap' => 0,
        ];

        try {
            if ($kdDokter !== null) {
                // Pasien Rawat Jalan + IGD dokter ini hari ini
                $rRajal = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM reg_periksa rp
                     LEFT JOIN poliklinik pol ON pol.kd_poli = rp.kd_poli
                     WHERE rp.tgl_registrasi = ? AND rp.kd_dokter = ?
                       AND (rp.status_lanjut = 'Ralan'
                            OR pol.nm_poli LIKE '%IGD%'
                            OR pol.nm_poli LIKE '%UGD%')",
                    [$today, $kdDokter]
                );
                $stats['rajal'] = (int) ($rRajal?->jml ?? 0);

                // Pasien Rawat Inap aktif dokter ini (dirawat saat ini)
                $rRanap = DB::selectOne(
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
                $stats['ranap'] = (int) ($rRanap?->jml ?? 0);

            } else {
                // Rawat Jalan global hari ini
                $rRajal = DB::selectOne(
                    "SELECT COUNT(*) AS jml FROM reg_periksa
                     WHERE tgl_registrasi = ? AND status_lanjut = 'Ralan'",
                    [$today]
                );
                $stats['rajal'] = (int) ($rRajal?->jml ?? 0);

                // Rawat Inap global sedang dirawat
                $rRanap = DB::selectOne(
                    "SELECT COUNT(DISTINCT no_rawat) AS jml FROM kamar_inap
                     WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'"
                );
                $stats['ranap'] = (int) ($rRanap?->jml ?? 0);
            }
        } catch (\Exception $e) {
            // Keep default values on database error
        }

        return $stats;
    }
}
