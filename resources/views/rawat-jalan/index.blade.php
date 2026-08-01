@extends('layouts.app')

@section('title', 'Rawat Jalan')
@section('page-title', 'Rawat Jalan')

@push('styles')
<style>
    /* =============================================
       RAWAT JALAN PAGE — ePasien RS Namira
    ============================================= */

    /* === Page Header === */
    .page-header-banner {
        background: linear-gradient(135deg, #042b1b 0%, #0d7044 60%, #1a9958 100%);
        border-radius: var(--radius);
        padding: 1.5rem 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .page-header-banner::before {
        content: '';
        position: absolute;
        top: -50px; right: -30px;
        width: 220px; height: 220px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
    }
    .page-header-banner::after {
        content: '';
        position: absolute;
        bottom: -60px; right: 100px;
        width: 160px; height: 160px;
        background: rgba(77,200,122,.08);
        border-radius: 50%;
    }
    .page-header-banner .content { position: relative; z-index: 2; }
    .page-header-banner h4 { font-size: 1.35rem; font-weight: 800; margin: 0 0 .25rem; }
    .page-header-banner p  { font-size: .855rem; color: rgba(255,255,255,.72); margin: 0; }

    /* === Stat Mini Cards === */
    .mini-stat {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 1.1rem 1.3rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: .9rem;
        transition: all .25s;
        height: 100%;
    }
    .mini-stat:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
    .mini-stat-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .mini-stat-icon.blue   { background: rgba(13,112,68,.1);   color: #0d7044; }
    .mini-stat-icon.green  { background: rgba(32,201,151,.12); color: #20c997; }
    .mini-stat-icon.amber  { background: rgba(255,193,7,.12);  color: #d4a017; }
    .mini-stat-icon.red    { background: rgba(220,53,69,.1);   color: #dc3545; }
    .mini-stat-value  { font-size: 1.6rem; font-weight: 800; color: var(--text-main); line-height: 1; }
    .mini-stat-label  { font-size: .72rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; margin-top: .2rem; }

    /* === Filter Bar === */
    .filter-bar {
        background: var(--bg-card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        padding: 1rem 1.3rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }
    .filter-bar .form-control, .filter-bar .form-select {
        border-radius: 10px;
        border-color: var(--border);
        font-size: .845rem;
        height: 38px;
    }
    .filter-bar .form-control:focus, .filter-bar .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(13,112,68,.12);
    }
    .filter-btn {
        height: 38px;
        border-radius: 10px;
        font-size: .845rem;
        font-weight: 600;
        padding: 0 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border: 1.5px solid var(--primary);
        background: var(--primary);
        color: #fff;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
    }
    .filter-btn:hover { background: var(--primary-dark); border-color: var(--primary-dark); color: #fff; }

    /* === Table Card === */
    .table-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .table-card-header {
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
    }
    .section-title {
        font-size: .95rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .section-title i { color: var(--primary); }

    .table-custom { margin: 0; font-size: .845rem; }
    .table-custom th {
        background: #f8fafc;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
        padding: .75rem 1rem;
        white-space: nowrap;
    }
    .table-custom td {
        padding: .75rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-main);
    }
    .table-custom tr:last-child td { border-bottom: none; }
    .table-custom tbody tr { transition: background .15s; }
    .table-custom tbody tr:hover { background: #f8fafc; }

    /* === Patient Avatar === */
    .patient-avatar {
        width: 34px; height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1a9958, #4cc87a);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: .73rem;
        flex-shrink: 0;
    }
    .patient-avatar.igd { background: linear-gradient(135deg, #dc3545, #e8647a); }

    /* === Status Badges === */
    .badge-terlayani {
        display: inline-flex; align-items: center; gap: .3rem;
        background: rgba(13,112,68,.1);
        color: #0d7044;
        border-radius: 20px;
        padding: .22rem .7rem;
        font-size: .7rem;
        font-weight: 700;
        border: 1px solid rgba(13,112,68,.18);
    }
    .badge-belum {
        display: inline-flex; align-items: center; gap: .3rem;
        background: rgba(255,193,7,.1);
        color: #b88a00;
        border-radius: 20px;
        padding: .22rem .7rem;
        font-size: .7rem;
        font-weight: 700;
        border: 1px solid rgba(255,193,7,.25);
    }
    .badge-igd {
        display: inline-flex; align-items: center; gap: .3rem;
        background: rgba(220,53,69,.1);
        color: #dc3545;
        border-radius: 20px;
        padding: .22rem .7rem;
        font-size: .7rem;
        font-weight: 700;
        border: 1px solid rgba(220,53,69,.2);
    }
    .badge-ralan {
        display: inline-flex; align-items: center; gap: .3rem;
        background: rgba(26,153,88,.1);
        color: #1a9958;
        border-radius: 20px;
        padding: .22rem .7rem;
        font-size: .7rem;
        font-weight: 700;
        border: 1px solid rgba(26,153,88,.2);
    }

    /* === Empty state === */
    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--text-muted); }
    .empty-state i { font-size: 2.8rem; margin-bottom: .75rem; opacity: .35; display: block; }
    .empty-state p { font-size: .875rem; margin: 0; }

    /* === Dot terlayani / belum === */
    .status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 3px;
    }
    .dot-green { background: #0d7044; }
    .dot-amber { background: #d4a017; animation: blink-amber 1.5s infinite; }
    @keyframes blink-amber {
        0%, 100% { opacity: 1; }
        50% { opacity: .35; }
    }

    /* Animate on load */
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .mini-stat, .table-card { animation: fadeSlideUp .4s ease both; }
    .mini-stat:nth-child(1) { animation-delay: .05s; }
    .mini-stat:nth-child(2) { animation-delay: .10s; }
    .mini-stat:nth-child(3) { animation-delay: .15s; }
    .mini-stat:nth-child(4) { animation-delay: .20s; }
</style>
@endpush

@section('content')

{{-- ===========================
     PAGE HEADER
=========================== --}}
<div class="page-header-banner">
    <div class="content d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4>
                <i class="bi bi-clipboard2-pulse-fill me-2"></i>
                Rawat Jalan
                @if($isDokter)
                    <span style="font-size:.9rem;font-weight:600;opacity:.8;">— Pasien Saya</span>
                @endif
            </h4>
            <p>
                Daftar pasien rawat jalan & IGD hari ini ·
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                @if($isDokter)
                    · <span style="font-weight:600;">dr. {{ $nmDokter }}</span>
                @endif
            </p>
        </div>
        <div class="text-end">
            <div style="font-size:.82rem;color:rgba(255,255,255,.65);">Update terakhir</div>
            <div style="font-size:1.05rem;font-weight:700;" id="rajalClock"></div>
        </div>
    </div>
</div>

{{-- ===========================
     MINI STAT CARDS
=========================== --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon blue"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRajal['total'] }}</div>
                <div class="mini-stat-label">Total Pasien</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRajal['terlayani'] }}</div>
                <div class="mini-stat-label">Sudah Terlayani</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRajal['belum'] }}</div>
                <div class="mini-stat-label">Belum Terlayani</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon red"><i class="bi bi-lightning-charge-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRajal['igd'] }}</div>
                <div class="mini-stat-label">Pasien IGD</div>
            </div>
        </div>
    </div>
</div>

{{-- ===========================
     TABEL PASIEN
=========================== --}}
<div class="table-card">
    <div class="table-card-header">
        <h6 class="section-title">
            <i class="bi bi-list-ul"></i>
            @if($isDokter)
                Pasien Rawat Jalan Saya — Hari Ini
            @else
                Semua Pasien Rawat Jalan & IGD — Hari Ini
            @endif
        </h6>
        <div class="d-flex align-items-center gap-2">
            @if($isDokter)
                <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;font-weight:700;
                             background:rgba(13,112,68,.1);color:#0d7044;border:1px solid rgba(13,112,68,.2);
                             border-radius:20px;padding:.2rem .75rem;">
                    <i class="bi bi-person-badge-fill"></i> dr. {{ $nmDokter }}
                </span>
            @endif
            <span style="font-size:.75rem;color:var(--text-muted);">
                {{ count($pasienList) }} pasien
            </span>
        </div>
    </div>

    @if(count($pasienList) > 0)
    <div class="table-responsive">
        <table class="table table-custom table-hover" id="tabelRajal">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Pasien</th>
                    <th>No. Rawat</th>
                    <th>Jam</th>
                    <th>Poliklinik</th>
                    @if(!$isDokter)
                    <th>Dokter</th>
                    @endif
                    <th>Jenis</th>
                    <th>Status Layanan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pasienList as $i => $p)
                @php
                    $namaPasien = ucwords(strtolower($p->nm_pasien ?? '-'));
                    $namaDokter = $p->nm_dokter ? (Str::startsWith(strtolower($p->nm_dokter), 'dr.') ? $p->nm_dokter : 'dr. '.$p->nm_dokter) : '-';
                @endphp
                <tr>
                    <td style="color:var(--text-muted);font-size:.8rem;">{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="patient-avatar {{ $p->jenis_kunjungan === 'IGD' ? 'igd' : '' }}">
                                {{ strtoupper(substr($p->nm_pasien ?? 'P', 0, 2)) }}
                            </div>
                            <div>
                                <div class="patient-title-text">
                                    {{ $namaPasien }}
                                </div>
                                <div class="patient-sub-text">
                                    {{ $p->jk === 'L' ? '♂ Laki-laki' : '♀ Perempuan' }}
                                    @if($p->tgl_lahir && $p->tgl_lahir !== '0000-00-00')
                                        · {{ \Carbon\Carbon::parse($p->tgl_lahir)->age }} thn
                                    @endif
                                    · RM: {{ $p->no_rkm_medis ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="no-rawat-badge">
                            {{ $p->no_rawat }}
                        </span>
                    </td>
                    <td>
                        <div style="font-size:.83rem;font-weight:600;color:#1e293b;">
                            {{ substr($p->jam_reg ?? '', 0, 5) }} WIB
                        </div>
                    </td>
                    <td style="font-size:.83rem;font-weight:600;color:#1e293b;">
                        {{ ucwords(strtolower($p->nm_poli ?? '-')) }}
                    </td>
                    @if(!$isDokter)
                    <td>
                        <div style="font-size:.83rem;font-weight:600;color:#1e293b;">
                            {{ $namaDokter }}
                        </div>
                    </td>
                    @endif
                    <td>
                        @if($p->jenis_kunjungan === 'IGD')
                            <span class="badge-igd">
                                <i class="bi bi-lightning-fill"></i> IGD
                            </span>
                        @else
                            <span class="badge-ralan">
                                <i class="bi bi-clipboard2-pulse-fill"></i> Ralan
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($p->status_layanan === 'terlayani')
                            <span class="badge-terlayani">
                                <span class="status-dot dot-green"></span>
                                Sudah Terlayani
                            </span>
                        @else
                            <span class="badge-belum">
                                <span class="status-dot dot-amber"></span>
                                Belum Terlayani
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        @if($isDokter)
            <p style="font-weight:600;">Belum ada pasien Anda hari ini</p>
            <p style="font-size:.8rem;margin-top:.35rem;color:var(--text-muted);">
                Pasien rawat jalan yang terdaftar atas nama <strong>dr. {{ $nmDokter }}</strong> akan tampil di sini.
            </p>
        @else
            <p style="font-weight:600;">Belum ada pasien rawat jalan hari ini</p>
            <p style="font-size:.8rem;margin-top:.35rem;color:var(--text-muted);">
                Data akan muncul setelah ada pendaftaran kunjungan.
            </p>
        @endif
    </div>
    @endif
</div>

{{-- Legend --}}
<div class="d-flex align-items-center gap-3 mt-3 flex-wrap" style="font-size:.775rem;color:var(--text-muted);">
    <span><span class="status-dot dot-green" style="display:inline-block;"></span> <strong>Sudah Terlayani</strong> — Ada data pemeriksaan</span>
    <span><span class="status-dot dot-amber" style="display:inline-block;"></span> <strong>Belum Terlayani</strong> — Belum ada data pemeriksaan</span>
    <span><i class="bi bi-lightning-fill text-danger"></i> <strong>IGD</strong> — Pasien Instalasi Gawat Darurat (sudah termasuk dalam list ini)</span>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // === Live Clock ===
    function tick() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('rajalClock');
        if (el) el.textContent = `${h}:${m}:${s} WIB`;
    }
    tick();
    setInterval(tick, 1000);

    // === Initialize DataTable ===
    if ($('#tabelRajal').length > 0) {
        $('#tabelRajal').DataTable({
            order: [[3, 'asc']], // Urutkan berdasarkan jam registrasi
            columnDefs: [
                { orderable: false, targets: [0] } // nonaktifkan sorting nomor urut
            ]
        });
    }
});
</script>
@endpush
