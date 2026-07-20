<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Share data sidebar ke semua view yang menggunakan layouts.app.
     * Data count otomatis difilter berdasarkan sesi login:
     *  - Dokter  → hanya pasiennya sendiri
     *  - Admin   → semua pasien
     */
    public function compose(View $view): void
    {
        // Hanya jalankan jika user sudah login
        if (! session()->has('auth_user')) {
            $view->with('sidebarCounts', ['rajal' => 0, 'ranap' => 0]);
            return;
        }

        $kodeUser = session('auth_user.kode');
        $isAdmin  = session('auth_user.is_admin', false);
        $isDokter = session('auth_user.is_dokter', false);

        // Filter dokter jika bukan admin
        $kdDokter = (!$isAdmin && $isDokter) ? $kodeUser : null;

        $view->with('sidebarCounts', [
            'rajal' => $this->countRajal($kdDokter),
            'ranap' => $this->countRanap($kdDokter),
        ]);
    }

    /**
     * Hitung pasien rawat jalan + IGD hari ini (yang aktif).
     */
    private function countRajal(?string $kdDokter): int
    {
        try {
            $today  = now()->toDateString();
            $params = [$today];
            $filter = '';

            if ($kdDokter !== null) {
                $filter   = 'AND rp.kd_dokter = ?';
                $params[] = $kdDokter;
            }

            $row = DB::selectOne(
                "SELECT COUNT(*) AS jml
                 FROM reg_periksa rp
                 LEFT JOIN poliklinik pol ON pol.kd_poli = rp.kd_poli
                 WHERE rp.tgl_registrasi = ?
                   AND (rp.status_lanjut = 'Ralan'
                        OR pol.nm_poli LIKE '%IGD%'
                        OR pol.nm_poli LIKE '%UGD%')
                 {$filter}",
                $params
            );

            return (int) ($row?->jml ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Hitung pasien rawat inap yang SEDANG DIRAWAT saat ini
     * (berdasarkan kamar_inap.tgl_keluar NULL/0 — sumber kebenaran).
     */
    private function countRanap(?string $kdDokter): int
    {
        try {
            $params = [];
            $filter = '';

            if ($kdDokter !== null) {
                $filter   = 'AND rp.kd_dokter = ?';
                $params[] = $kdDokter;
            }

            $row = DB::selectOne(
                "SELECT COUNT(DISTINCT ki.no_rawat) AS jml
                 FROM (
                     SELECT no_rawat
                     FROM kamar_inap
                     WHERE tgl_keluar IS NULL OR tgl_keluar = '0000-00-00'
                 ) ki
                 INNER JOIN reg_periksa rp ON rp.no_rawat = ki.no_rawat
                 WHERE 1=1
                 {$filter}",
                $params
            );

            return (int) ($row?->jml ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
