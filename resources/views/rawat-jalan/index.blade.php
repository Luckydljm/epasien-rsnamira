@extends('layouts.app')

@section('title', 'Rawat Jalan')
@section('page-title', 'Rawat Jalan')

@push('styles')
<style>
    /* =============================================
       RAWAT JALAN — ePasien RS Namira
       2-Row Full Width Filter Card & 4 Stat Cards
    ============================================= */

    :root {
        --rajal-primary: #0d7044;
        --rajal-primary-dark: #042b1b;
        --rajal-accent: #1a9958;
        --rajal-border: #e2e8f0;
    }

    /* === Page Wrapper Margin & Padding === */
    .rajal-page-container {
        padding: 0 .25rem 1.5rem .25rem;
    }

    /* === Page Header Banner === */
    .page-header-banner {
        background: linear-gradient(135deg, #042b1b 0%, #0d7044 55%, #1a9958 100%);
        border-radius: 18px;
        padding: 1.6rem 2.2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 28px -5px rgba(13, 112, 68, 0.28);
    }
    .page-header-banner::before {
        content: '';
        position: absolute;
        top: -60px; right: -40px;
        width: 250px; height: 250px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
        pointer-events: none;
    }
    .page-header-banner::after {
        content: '';
        position: absolute;
        bottom: -70px; right: 120px;
        width: 180px; height: 180px;
        background: rgba(77,200,122,.08);
        border-radius: 50%;
        pointer-events: none;
    }
    .page-header-banner .content { position: relative; z-index: 2; }
    .page-header-banner h4 { font-size: 1.4rem; font-weight: 800; margin: 0 0 .3rem; letter-spacing: -.3px; }
    .page-header-banner p  { font-size: .865rem; color: rgba(255,255,255,.82); margin: 0; }

    .header-badge-time {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: .5rem 1.1rem;
        border-radius: 12px;
        text-align: right;
    }

    /* === Filter Card (2 Baris Penuh Kolom) === */
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--rajal-border);
        border-top: 3px solid var(--rajal-primary);
        padding: 1.25rem 1.6rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    }
    .filter-card .form-control {
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: .84rem;
        font-weight: 600;
        padding: .45rem .85rem;
        height: 38px;
        color: #0f172a;
    }
    .filter-card .form-control:focus {
        border-color: var(--rajal-primary);
        box-shadow: 0 0 0 3.5px rgba(13, 112, 68, 0.12);
    }
    .btn-filter-submit {
        background: var(--rajal-primary);
        color: #fff;
        border-radius: 10px;
        font-size: .83rem;
        font-weight: 700;
        padding: 0 1.3rem;
        height: 38px;
        border: none;
        transition: all .2s ease;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
    }
    .btn-filter-submit:hover {
        background: #085633;
        color: #fff;
        box-shadow: 0 4px 14px rgba(13, 112, 68, 0.28);
    }
    .btn-quick-date {
        font-size: .78rem;
        font-weight: 700;
        border-radius: 20px;
        padding: .38rem .95rem;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        text-decoration: none;
        transition: all .18s ease;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .btn-quick-date:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .btn-quick-date.active {
        background: rgba(13, 112, 68, 0.1) !important;
        color: #0d7044 !important;
        border-color: rgba(13, 112, 68, 0.3) !important;
        font-weight: 800 !important;
    }

    /* === 4 Stat Mini Cards Grid (Sejajar 4 Card) === */
    .mini-stat {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.15rem 1.3rem;
        border: 1px solid var(--rajal-border);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all .25s ease;
        height: 100%;
    }
    .mini-stat:hover {
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.07);
        transform: translateY(-2px);
        border-color: rgba(13, 112, 68, 0.35);
    }
    .mini-stat-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .mini-stat-icon.red    { background: rgba(225,29,72,.1);   color: #e11d48; }
    .mini-stat-icon.purple { background: rgba(124,58,237,.1);  color: #7c3aed; }
    .mini-stat-icon.green  { background: rgba(32,201,151,.12); color: #059669; }
    .mini-stat-icon.amber  { background: rgba(245,158,11,.12); color: #d97706; }

    .mini-stat-value  { font-size: 1.65rem; font-weight: 800; color: #0f172a; line-height: 1; }
    .mini-stat-label  { font-size: .72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .45px; margin-top: .35rem; white-space: nowrap; }

    /* === Table Card Container dengan Spasi / Padding === */
    .table-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid var(--rajal-border);
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.04);
        padding: .6rem;
    }
    .table-card-header {
        padding: 1.25rem 1.4rem;
        border-bottom: 1px solid var(--rajal-border);
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .8rem;
    }
    .section-title {
        font-size: .98rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: .6rem;
        letter-spacing: -.2px;
    }
    .section-title i { color: var(--rajal-primary); font-size: 1.15rem; }

    .table-container-inner {
        padding: 1.15rem 1.4rem 1.4rem 1.4rem;
    }

    /* === DataTables Custom Controls === */
    .dataTables_wrapper { font-size: .83rem; }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        padding: .5rem 0 1.1rem 0;
    }
    .dataTables_wrapper .dataTables_filter label {
        font-weight: 600;
        color: #475569;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 10px !important;
        border: 1.5px solid #cbd5e1 !important;
        padding: .4rem .85rem !important;
        font-size: .83rem !important;
        outline: none !important;
        transition: all .2s ease !important;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--rajal-primary) !important;
        box-shadow: 0 0 0 3.5px rgba(13, 112, 68, 0.12) !important;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px !important;
        border: 1.5px solid #cbd5e1 !important;
        padding: .3rem .6rem !important;
        font-size: .83rem !important;
    }
    .dataTables_wrapper .dataTables_info {
        padding: 1.1rem 0 .5rem 0;
        font-size: .8rem;
        color: #64748b;
        font-weight: 600;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding: 1.1rem 0 .5rem 0;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        padding: .32rem .78rem !important;
        font-size: .8rem !important;
        font-weight: 700 !important;
        border: 1px solid #e2e8f0 !important;
        margin: 0 2px !important;
        background: #fff !important;
        color: #475569 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--rajal-primary) !important;
        border-color: var(--rajal-primary) !important;
        color: #fff !important;
    }

    /* === Table Styling & Zebra Striping === */
    .table-custom { margin: 0; font-size: .825rem; width: 100%; border-radius: 12px; overflow: hidden; }
    .table-custom th {
        background: #f1f5f9;
        font-size: .71rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .65px;
        color: #334155;
        border-bottom: 2px solid #cbd5e1;
        padding: .95rem 1.05rem;
        white-space: nowrap;
        vertical-align: middle;
    }
    .table-custom td {
        padding: .9rem 1.05rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
        color: #0f172a;
        white-space: nowrap;
        transition: background .15s ease;
    }

    .table-custom tbody tr:nth-child(odd) td {
        background-color: #ffffff;
    }
    .table-custom tbody tr:nth-child(even) td {
        background-color: #f8fafc;
    }
    .table-custom tbody tr:hover td {
        background-color: #f0fdf4 !important;
    }

    /* === Patient Avatar === */
    .patient-avatar {
        width: 38px; height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0d7044, #1a9958);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 800;
        font-size: .75rem;
        flex-shrink: 0;
    }
    .patient-avatar.igd {
        background: linear-gradient(135deg, #e11d48, #f43f5e);
    }
    .patient-name {
        font-size: .86rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
    }
    .patient-sub {
        font-size: .71rem;
        color: #64748b;
        margin-top: .15rem;
    }

    /* === Chips & Badges === */
    .code-chip {
        display: inline-block;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: .15rem .45rem;
        font-size: .69rem;
        font-weight: 700;
        color: #475569;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }

    .no-rm-chip {
        display: inline-block;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        color: #0284c7;
        border-radius: 7px;
        padding: .18rem .55rem;
        font-size: .72rem;
        font-weight: 800;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }

    .no-rawat-badge {
        display: inline-block;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        border-radius: 7px;
        padding: .2rem .6rem;
        font-size: .73rem;
        font-weight: 800;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        letter-spacing: .2px;
    }

    .badge-poli-main {
        display: inline-flex; align-items: center; gap: .35rem;
        background: #ecfdf5;
        color: #047857;
        border-radius: 20px;
        padding: .25rem .8rem;
        font-size: .75rem;
        font-weight: 800;
        border: 1px solid #a7f3d0;
    }
    .badge-poli-igd {
        display: inline-flex; align-items: center; gap: .35rem;
        background: #fff1f2;
        color: #e11d48;
        border-radius: 20px;
        padding: .25rem .8rem;
        font-size: .75rem;
        font-weight: 800;
        border: 1px solid #fecdd3;
    }

    .badge-penjamin {
        display: inline-flex; align-items: center; gap: .3rem;
        background: #eef2ff;
        color: #4338ca;
        border-radius: 20px;
        padding: .22rem .7rem;
        font-size: .7rem;
        font-weight: 700;
        border: 1px solid #c7d2fe;
    }

    .badge-stts {
        display: inline-flex; align-items: center; gap: .35rem;
        border-radius: 20px;
        padding: .22rem .7rem;
        font-size: .7rem;
        font-weight: 700;
    }
    .badge-stts.sudah { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .badge-stts.belum { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .badge-stts.batal { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
    .badge-stts.other { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

    .badge-sudah-bayar {
        display: inline-flex; align-items: center; gap: .35rem;
        background: #ecfdf5; color: #047857;
        border-radius: 20px;
        padding: .22rem .7rem;
        font-size: .7rem;
        font-weight: 700;
        border: 1px solid #a7f3d0;
    }
    .badge-belum-bayar {
        display: inline-flex; align-items: center; gap: .35rem;
        background: #fff1f2; color: #be123c;
        border-radius: 20px;
        padding: .22rem .7rem;
        font-size: .7rem;
        font-weight: 700;
        border: 1px solid #fecdd3;
    }

    .badge-stts-poli-ded {
        display: inline-block;
        border-radius: 20px;
        padding: .24rem .8rem;
        font-size: .72rem;
        font-weight: 800;
        background: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
        text-align: center;
    }

    .badge-sep {
        display: inline-flex; align-items: center; gap: .3rem;
        background: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
        border-radius: 7px;
        padding: .2rem .6rem;
        font-size: .7rem;
        font-weight: 700;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
    .badge-sep.empty {
        background: #f8fafc;
        color: #94a3b8;
        border-color: #e2e8f0;
        font-family: inherit;
        font-weight: 600;
    }

    /* Status Dot */
    .status-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .dot-green { background: #047857; }
    .dot-amber { background: #d97706; animation: blink-amber 1.5s infinite; }
    @keyframes blink-amber {
        0%, 100% { opacity: 1; }
        50% { opacity: .35; }
    }

    .empty-state { text-align: center; padding: 4rem 1rem; color: #64748b; }
    .empty-state i { font-size: 3.2rem; margin-bottom: 1rem; opacity: .35; display: block; color: var(--rajal-primary); }
    .empty-state p { font-size: .95rem; margin: 0; }
</style>
@endpush

@section('content')

@php
    $todayDate = now()->toDateString();
    $yesterday = now()->subDay()->toDateString();
    $last7Days = now()->subDays(6)->toDateString();
    $startMonth = now()->startOfMonth()->toDateString();
    $endMonth   = now()->endOfMonth()->toDateString();

    $isSingleDay = ($tglAwal === $tglAkhir);
    $rangeLabel = $isSingleDay
        ? \Carbon\Carbon::parse($tglAwal)->locale('id')->isoFormat('dddd, D MMMM YYYY')
        : \Carbon\Carbon::parse($tglAwal)->locale('id')->isoFormat('D MMM YYYY') . ' s.d. ' . \Carbon\Carbon::parse($tglAkhir)->locale('id')->isoFormat('D MMM YYYY');

    // Helper untuk format nama dokter agar tidak pernah terjadi duplikasi "dr. dr."
    $formatDokterName = function(?string $name): string {
        if (empty($name)) return '-';
        $trimmed = trim($name);
        if (preg_match('/^(dr\b\.?|dokter\b)/i', $trimmed)) {
            return $trimmed;
        }
        return 'dr. ' . $trimmed;
    };

    $nmDokterFormatted = $isDokter ? $formatDokterName($nmDokter) : '';
@endphp

<div class="rajal-page-container">

    {{-- ===========================
         PAGE HEADER BANNER
    =========================== --}}
    <div class="page-header-banner">
        <div class="content d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4>
                    <i class="bi bi-clipboard2-pulse-fill me-2"></i>
                    Rawat Jalan
                    @if($isDokter)
                        <span style="font-size:.92rem;font-weight:600;opacity:.85;">— Pasien Saya</span>
                    @endif
                </h4>
                <p>
                    Daftar kunjungan pasien rawat jalan &amp; IGD ·
                    <span style="font-weight:700;color:#ffffff;">{{ $rangeLabel }}</span>
                    @if($isDokter)
                        · <span style="font-weight:700;">{{ $nmDokterFormatted }}</span>
                    @endif
                </p>
            </div>
            <div class="header-badge-time">
                <div style="font-size:.75rem;color:rgba(255,255,255,.75);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Update Waktu</div>
                <div style="font-size:1.15rem;font-weight:800;letter-spacing:.3px;" id="rajalClock"></div>
            </div>
        </div>
    </div>

    {{-- ===========================
         FILTER RANGE TANGGAL REGISTRASI (2 BARIS PENUH KOLOM)
    =========================== --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('rawat-jalan') }}" class="m-0">
            {{-- BARIS 1: PERIODE REGISTRASI FORM INPUTS --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-3" style="border-bottom: 1px dashed #e2e8f0;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-range-fill text-success fs-5"></i>
                    <span style="font-size:.9rem;font-weight:800;color:#1e293b;">Periode Registrasi:</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <input type="date" name="tgl_awal" value="{{ $tglAwal }}" class="form-control" style="width: 155px;" title="Tanggal Awal">
                        <span style="font-size:.82rem;font-weight:700;color:#64748b;">s.d.</span>
                        <input type="date" name="tgl_akhir" value="{{ $tglAkhir }}" class="form-control" style="width: 155px;" title="Tanggal Akhir">
                    </div>
                    <button type="submit" class="btn-filter-submit">
                        <i class="bi bi-funnel-fill"></i> Terapkan Filter
                    </button>
                </div>
            </div>

            {{-- BARIS 2: PILIHAN CEPAT (PRESETS) --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-muted fs-6"></i>
                    <span style="font-size:.78rem;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.3px;">Pilihan Cepat Periode:</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('rawat-jalan', ['tgl_awal' => $todayDate, 'tgl_akhir' => $todayDate]) }}"
                       class="btn-quick-date {{ ($tglAwal === $todayDate && $tglAkhir === $todayDate) ? 'active' : '' }}">
                        <i class="bi bi-calendar-day me-1"></i> Hari Ini
                    </a>
                    <a href="{{ route('rawat-jalan', ['tgl_awal' => $yesterday, 'tgl_akhir' => $yesterday]) }}"
                       class="btn-quick-date {{ ($tglAwal === $yesterday && $tglAkhir === $yesterday) ? 'active' : '' }}">
                        <i class="bi bi-calendar-minus me-1"></i> Kemarin
                    </a>
                    <a href="{{ route('rawat-jalan', ['tgl_awal' => $last7Days, 'tgl_akhir' => $todayDate]) }}"
                       class="btn-quick-date {{ ($tglAwal === $last7Days && $tglAkhir === $todayDate) ? 'active' : '' }}">
                        <i class="bi bi-calendar-week me-1"></i> 7 Hari Terakhir
                    </a>
                    <a href="{{ route('rawat-jalan', ['tgl_awal' => $startMonth, 'tgl_akhir' => $endMonth]) }}"
                       class="btn-quick-date {{ ($tglAwal === $startMonth && $tglAkhir === $endMonth) ? 'active' : '' }}">
                        <i class="bi bi-calendar-month me-1"></i> Bulan Ini
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ===========================
         4 MONITORING STAT CARDS (SEJAJAR 4 CARD)
    =========================== --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 mb-4">
        {{-- 1. Pasien IGD --}}
        <div class="col">
            <div class="mini-stat">
                <div class="mini-stat-icon red"><i class="bi bi-lightning-charge-fill"></i></div>
                <div>
                    <div class="mini-stat-value">{{ $statsRajal['igd'] }}</div>
                    <div class="mini-stat-label">Pasien IGD</div>
                </div>
            </div>
        </div>
        {{-- 2. Pasien Poli --}}
        <div class="col">
            <div class="mini-stat">
                <div class="mini-stat-icon purple"><i class="bi bi-building"></i></div>
                <div>
                    <div class="mini-stat-value">{{ $statsRajal['poli'] }}</div>
                    <div class="mini-stat-label">Pasien Poli</div>
                </div>
            </div>
        </div>
        {{-- 3. Sudah Terlayani --}}
        <div class="col">
            <div class="mini-stat">
                <div class="mini-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="mini-stat-value">{{ $statsRajal['terlayani'] }}</div>
                    <div class="mini-stat-label">Sudah Terlayani</div>
                </div>
            </div>
        </div>
        {{-- 4. Belum Terlayani --}}
        <div class="col">
            <div class="mini-stat">
                <div class="mini-stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="mini-stat-value">{{ $statsRajal['belum'] }}</div>
                    <div class="mini-stat-label">Belum Terlayani</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===========================
         TABEL PASIEN RAWAT JALAN
    =========================== --}}
    <div class="table-card">
        <div class="table-card-header">
            <h6 class="section-title">
                <i class="bi bi-list-ul"></i>
                @if($isDokter)
                    Pasien Rawat Jalan Saya — Periode: {{ $rangeLabel }}
                @else
                    Semua Pasien Rawat Jalan — Periode: {{ $rangeLabel }}
                @endif
            </h6>
            <div class="d-flex align-items-center gap-2">
                @if($isDokter)
                    <span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.73rem;font-weight:700;
                                 background:rgba(13,112,68,.1);color:#0d7044;border:1px solid rgba(13,112,68,.2);
                                 border-radius:20px;padding:.22rem .8rem;">
                        <i class="bi bi-person-badge-fill"></i> {{ $nmDokterFormatted }}
                    </span>
                @endif
                <span style="font-size:.78rem;font-weight:800;color:#0d7044;background:#ecfdf5;border:1px solid #a7f3d0;padding:.25rem .75rem;border-radius:20px;">
                    {{ count($pasienList) }} Pasien Terdaftar
                </span>
            </div>
        </div>

        @if(count($pasienList) > 0)
        <div class="table-container-inner">
            <div class="table-responsive">
                <table class="table table-custom" id="tabelRajal">
                    <thead>
                        <tr>
                            <th style="width:36px;text-align:center;">#</th>
                            {{-- 1. No. Rawat / RM (GABUNG) --}}
                            <th>No. Rawat / RM</th>
                            {{-- 2. Pasien --}}
                            <th>Pasien</th>
                            {{-- 3. Waktu Registrasi (GABUNG) --}}
                            <th>Waktu Registrasi</th>
                            {{-- 4. Dokter Dituju (GABUNG) --}}
                            <th>Dokter Dituju</th>
                            {{-- 5. Poliklinik (BADGE LANGSUNG) --}}
                            <th>Poliklinik</th>
                            {{-- 6. Penanggung Jawab (GABUNG) --}}
                            <th>Penanggung Jawab</th>
                            {{-- 7. No. HP Pasien (KOLOM TERPISAH DI SEBELAH PJ) --}}
                            <th>No. HP Pasien</th>
                            {{-- 8. Stts Poli (KOLOM TERPISAH BARU/LAMA) --}}
                            <th>Stts Poli</th>
                            {{-- 9. Jenis Bayar --}}
                            <th>Jenis Bayar</th>
                            {{-- 10. Status Pasien --}}
                            <th>Status Pasien</th>
                            {{-- 11. Status Bayar --}}
                            <th>Status Bayar</th>
                            {{-- 12. SEP --}}
                            <th>SEP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pasienList as $i => $p)
                        @php
                            $namaPasien = ucwords(strtolower($p->nm_pasien ?? '-'));
                            $namaDokter = $formatDokterName($p->nm_dokter ?? '');

                            // Status Pasien badge class
                            $stts = strtolower(trim($p->status_pasien ?? ''));
                            if (in_array($stts, ['sudah', 'selesai'])) {
                                $sttsCls = 'sudah';
                            } elseif (in_array($stts, ['belum', 'dalam antrian', ''])) {
                                $sttsCls = 'belum';
                            } elseif (in_array($stts, ['batal', 'cancel'])) {
                                $sttsCls = 'batal';
                            } else {
                                $sttsCls = 'other';
                            }

                            // Status Bayar
                            $statusBayar = strtolower(trim($p->status_bayar ?? ''));
                            $bayarSudah  = in_array($statusBayar, ['sudah', 'lunas', 'paid', '1', 'true', 'sudah bayar']);

                            // No. Telp Pasien
                            $noTlp = $p->no_tlp ?? null;

                            // SEP
                            $noSep = $p->no_sep ?? null;

                            // Penanggung Jawab & Alamat & Hubungan
                            $nmPj      = ucwords(strtolower($p->nm_penjamin ?? ''));
                            $alamatPj  = $p->alamat_pj ?? '';
                            $hubPj     = $p->hub_keluarga ?? '';

                            // Jenis Bayar
                            $jenisBayar = $p->jenis_bayar ?? '';

                            // Poliklinik Name
                            $nmPoli = ucwords(strtolower($p->nm_poli ?? '-'));
                            $isIgd  = ($p->jenis_kunjungan ?? '') === 'IGD';
                        @endphp
                        <tr>
                            {{-- # --}}
                            <td style="color:#94a3b8;font-size:.78rem;text-align:center;font-weight:600;">{{ $i + 1 }}</td>

                            {{-- 1. No. Rawat / RM (GABUNG) --}}
                            <td>
                                <div>
                                    <span class="no-rawat-badge" title="No. Rawat">
                                        {{ $p->no_rawat ?? '-' }}
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <span class="no-rm-chip" title="No. Rekam Medis">
                                        RM: {{ $p->no_rkm_medis ?? '-' }}
                                    </span>
                                </div>
                            </td>

                            {{-- 2. Pasien --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="patient-avatar {{ $isIgd ? 'igd' : '' }}">
                                        {{ strtoupper(substr($p->nm_pasien ?? 'P', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="patient-name">{{ $namaPasien }}</div>
                                        <div class="patient-sub">
                                            <span style="color:{{ ($p->jk ?? '') === 'L' ? '#0284c7' : '#ec4899' }};font-weight:700;">
                                                {{ ($p->jk ?? '') === 'L' ? '♂ Laki-laki' : '♀ Perempuan' }}
                                            </span>
                                            @if(!empty($p->tgl_lahir) && $p->tgl_lahir !== '0000-00-00')
                                                · {{ \Carbon\Carbon::parse($p->tgl_lahir)->age }} thn
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- 3. Waktu Registrasi (GABUNG: Tanggal & Jam) --}}
                            <td>
                                <div style="font-size:.82rem;font-weight:700;color:#0f172a;">
                                    @if(!empty($p->tgl_registrasi) && $p->tgl_registrasi !== '0000-00-00')
                                        {{ \Carbon\Carbon::parse($p->tgl_registrasi)->locale('id')->isoFormat('D MMM YYYY') }}
                                    @else
                                        —
                                    @endif
                                </div>
                                <div style="font-size:.75rem;font-weight:600;color:#64748b;margin-top:.1rem;">
                                    <i class="bi bi-clock me-1" style="font-size:.72rem;"></i>{{ substr($p->jam_reg ?? '', 0, 5) ?: '—' }} WIB
                                </div>
                            </td>

                            {{-- 4. Dokter Dituju (GABUNG: Dokter & Kode Dokter) --}}
                            <td>
                                <div style="font-size:.83rem;font-weight:700;color:#0f172a;">
                                    <i class="bi bi-person-fill me-1" style="color:#0d7044;font-size:.8rem;"></i>{{ $namaDokter }}
                                </div>
                                @if(!empty($p->kd_dokter))
                                    <div class="mt-1">
                                        <span class="code-chip">{{ $p->kd_dokter }}</span>
                                    </div>
                                @endif
                            </td>

                            {{-- 5. Poliklinik (BADGE LANGSUNG NAMA POLI / IGD) --}}
                            <td>
                                @if($isIgd)
                                    <span class="badge-poli-igd">
                                        <i class="bi bi-lightning-fill"></i> {{ $nmPoli }} (IGD)
                                    </span>
                                @else
                                    <span class="badge-poli-main">
                                        <i class="bi bi-building"></i> {{ $nmPoli }}
                                    </span>
                                @endif
                            </td>

                            {{-- 6. Penanggung Jawab (GABUNG: Nama PJ & Hub PJ) --}}
                            <td style="max-width:200px;">
                                <div style="font-size:.83rem;font-weight:700;color:#1e293b;">
                                    {{ $nmPj ?: '—' }}
                                    @if($hubPj)
                                        <span style="font-size:.68rem;font-weight:700;color:#475569;background:#e2e8f0;padding:.12rem .45rem;border-radius:6px;margin-left:.3rem;">
                                            {{ strtoupper($hubPj) }}
                                        </span>
                                    @endif
                                </div>
                                @if($alamatPj)
                                    <div style="font-size:.74rem;color:#64748b;margin-top:.15rem;line-height:1.3;white-space:normal;word-break:break-word;">
                                        {{ $alamatPj }}
                                    </div>
                                @endif
                            </td>

                            {{-- 7. No. HP Pasien (KOLOM TERPISAH DI SEBELAH PENANGGUNG JAWAB) --}}
                            <td>
                                @if($noTlp && $noTlp !== '000000000000000' && $noTlp !== '0')
                                    <a href="tel:{{ $noTlp }}"
                                       style="font-size:.78rem;color:#0284c7;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;background:#f0f9ff;border:1px solid #bae6fd;padding:.2rem .6rem;border-radius:8px;">
                                        <i class="bi bi-telephone-fill" style="font-size:.7rem;"></i>{{ $noTlp }}
                                    </a>
                                @else
                                    <span style="color:#94a3b8;font-size:.78rem;">—</span>
                                @endif
                            </td>

                            {{-- 8. Stts Poli (KOLOM TERPISAH DEDICATED) --}}
                            <td>
                                @if(($p->stts_poli ?? '') !== '')
                                    <span class="badge-stts-poli-ded">
                                        {{ ucfirst($p->stts_poli) }}
                                    </span>
                                @else
                                    <span style="color:#94a3b8;font-size:.78rem;">—</span>
                                @endif
                            </td>

                            {{-- 9. Jenis Bayar --}}
                            <td>
                                @if($jenisBayar)
                                    <span class="badge-penjamin">
                                        <i class="bi bi-credit-card-2-front-fill"></i>
                                        {{ $jenisBayar }}
                                    </span>
                                @else
                                    <span style="color:#94a3b8;font-size:.78rem;">—</span>
                                @endif
                            </td>

                            {{-- 10. Status Pasien --}}
                            <td>
                                <span class="badge-stts {{ $sttsCls }}">
                                    @if($sttsCls === 'sudah')
                                        <span class="status-dot dot-green"></span>
                                    @elseif($sttsCls === 'belum')
                                        <span class="status-dot dot-amber"></span>
                                    @endif
                                    {{ ucfirst($p->status_pasien ?? '—') }}
                                </span>
                            </td>

                            {{-- 11. Status Bayar --}}
                            <td>
                                @if(($p->status_bayar ?? '') !== '')
                                    @if($bayarSudah)
                                        <span class="badge-sudah-bayar">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Sudah Bayar
                                        </span>
                                    @else
                                        <span class="badge-belum-bayar">
                                            <i class="bi bi-exclamation-circle-fill"></i>
                                            Belum Bayar
                                        </span>
                                    @endif
                                @else
                                    <span style="color:#94a3b8;font-size:.78rem;">—</span>
                                @endif
                            </td>

                            {{-- 12. SEP --}}
                            <td>
                                @if($noSep)
                                    <span class="badge-sep">
                                        <i class="bi bi-shield-fill-check" style="color:#0284c7;"></i> {{ $noSep }}
                                    </span>
                                @else
                                    <span class="badge-sep empty">Belum Ada SEP</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            @if($isDokter)
                <p style="font-weight:700;color:#0f172a;">Belum ada pasien Anda pada periode {{ $rangeLabel }}</p>
                <p style="font-size:.82rem;margin-top:.35rem;color:#64748b;">
                    Pasien rawat jalan yang terdaftar atas nama <strong>{{ $nmDokterFormatted }}</strong> pada periode tersebut akan tampil di sini.
                </p>
            @else
                <p style="font-weight:700;color:#0f172a;">Belum ada pasien rawat jalan pada periode {{ $rangeLabel }}</p>
                <p style="font-size:.82rem;margin-top:.35rem;color:#64748b;">
                    Pilih periode tanggal registrasi lain atau buat pendaftaran kunjungan baru di SIMRS.
                </p>
            @endif
        </div>
        @endif
    </div>

    {{-- Footer Legend --}}
    <div class="d-flex align-items-center gap-4 mt-3.5 flex-wrap" style="font-size:.78rem;color:#64748b;padding:0 .25rem;">
        <span><span class="status-dot dot-green me-1" style="display:inline-block;"></span> <strong>Sudah Terlayani</strong> — Ada data pemeriksaan</span>
        <span><span class="status-dot dot-amber me-1" style="display:inline-block;"></span> <strong>Belum Terlayani</strong> — Belum ada data pemeriksaan</span>
        <span><i class="bi bi-lightning-fill text-danger me-1"></i> <strong>IGD</strong> — Instalasi Gawat Darurat</span>
        <span><i class="bi bi-shield-fill-check me-1" style="color:#0284c7;"></i> <strong>SEP</strong> — Surat Eligibilitas Peserta BPJS</span>
    </div>

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

    // === Initialize DataTables ===
    if ($('#tabelRajal').length > 0) {
        $('#tabelRajal').DataTable({
            // Default sort: No. Rawat / RM (kolom index 1)
            order: [[1, 'desc']],
            columnDefs: [
                { orderable: false, targets: [0] }
            ],
            language: {
                search:         'Cari Pasien:',
                lengthMenu:     'Tampilkan _MENU_ data',
                info:           'Menampilkan _START_–_END_ dari _TOTAL_ pasien',
                infoEmpty:      'Tidak ada data',
                infoFiltered:   '(difilter dari _MAX_ total)',
                zeroRecords:    'Tidak ditemukan data pasien yang sesuai',
                paginate: {
                    first:    '«',
                    last:     '»',
                    next:     '›',
                    previous: '‹'
                }
            }
        });
    }
});
</script>
@endpush
