@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* =============================================
       DASHBOARD PAGE — ePasien RS Namira
       Warna identik logo: Hijau #0d7044
    ============================================= */

    /* === Greeting Banner === */
    .greeting-banner {
        background: linear-gradient(135deg, #042b1b 0%, #063d26 35%, #0d7044 65%, #1a9958 100%);
        border-radius: var(--radius);
        padding: 1.75rem 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .greeting-banner::before {
        content: '';
        position: absolute;
        top: -60px; right: -40px;
        width: 280px; height: 280px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
    }
    .greeting-banner::after {
        content: '';
        position: absolute;
        bottom: -80px; right: 120px;
        width: 200px; height: 200px;
        background: rgba(77,200,122,.1);
        border-radius: 50%;
    }
    .greeting-banner .content { position: relative; z-index: 2; }
    .greeting-banner .greeting-time {
        font-size: .75rem;
        color: rgba(255,255,255,.6);
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: .4rem;
    }
    .greeting-banner h4 {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: .35rem;
        letter-spacing: -.3px;
    }
    .greeting-banner p {
        font-size: .875rem;
        color: rgba(255,255,255,.75);
        margin: 0;
    }
    .greeting-banner .live-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 20px;
        padding: .3rem .9rem;
        font-size: .75rem;
        font-weight: 600;
        color: #fff;
        backdrop-filter: blur(6px);
    }
    .live-dot {
        width: 7px; height: 7px;
        background: #6dd998;
        border-radius: 50%;
        animation: pulse-dot 1.8s infinite;
    }
    @keyframes pulse-dot {
        0%, 100% { box-shadow: 0 0 0 0 rgba(109,217,152,.5); }
        50% { box-shadow: 0 0 0 5px rgba(109,217,152,.0); }
    }

    /* === STAT CARDS === */
    .stat-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 1.4rem 1.5rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: all .25s ease;
        cursor: default;
        height: 100%;
    }
    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .stat-icon.blue   { background: rgba(13,112,68,.1);  color: #0d7044; }
    .stat-icon.green  { background: rgba(26,153,88,.12);  color: #1a9958; }
    .stat-icon.purple { background: rgba(6,61,38,.1);     color: #085c34; }
    .stat-icon.amber  { background: rgba(77,200,122,.12); color: #2dba6e; }
    .stat-icon.rose   { background: rgba(220,53,69,.1);   color: #dc3545; }
    .stat-icon.teal   { background: rgba(32,201,151,.12); color: #20c997; }

    .stat-info { flex: 1; min-width: 0; }
    .stat-label {
        font-size: .75rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: .4rem;
    }
    .stat-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -1px;
        line-height: 1;
        margin-bottom: .4rem;
    }
    .stat-desc {
        font-size: .775rem;
        color: var(--text-muted);
    }
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        font-size: .7rem;
        font-weight: 600;
        padding: .15rem .5rem;
        border-radius: 20px;
    }
    .stat-badge.up      { background: rgba(13,112,68,.1);  color: #0d7044; }
    .stat-badge.neutral { background: rgba(100,116,139,.1); color: var(--text-muted); }

    /* === SECTION HEADERS === */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .section-title i { color: var(--primary); }
    .section-action { font-size: .78rem; color: var(--primary); font-weight: 600; text-decoration: none; transition: color .2s; }
    .section-action:hover { color: var(--primary-dark); }

    /* === CHART CARD === */
    .chart-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 1.5rem;
        height: 100%;
    }

    /* === TABLE CARD === */
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
    }

    .table-custom {
        margin: 0;
        font-size: .845rem;
    }
    .table-custom th {
        background: #f8fafc;
        font-size: .72rem;
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
    .table-custom tbody tr {
        transition: background .15s;
    }
    .table-custom tbody tr:hover { background: #f8fafc; }

    /* Status badges */
    .badge-ralan {
        background: rgba(13,112,68,.1);
        color: #0d7044;
        border-radius: 20px;
        padding: .2rem .65rem;
        font-size: .7rem;
        font-weight: 700;
    }
    .badge-ranap {
        background: rgba(26,153,88,.1);
        color: #1a9958;
        border-radius: 20px;
        padding: .2rem .65rem;
        font-size: .7rem;
        font-weight: 700;
    }
    .badge-igd {
        background: rgba(220,53,69,.1);
        color: #dc3545;
        border-radius: 20px;
        padding: .2rem .65rem;
        font-size: .7rem;
        font-weight: 700;
    }

    /* Patient avatar */
    .patient-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1a9958, #4cc87a);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: .72rem;
        flex-shrink: 0;
    }

    /* === QUICK ACCESS BUTTONS === */
    .quick-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        padding: 1.25rem .75rem;
        background: var(--bg-card);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        text-decoration: none;
        color: var(--text-main);
        font-size: .78rem;
        font-weight: 600;
        text-align: center;
        transition: all .25s ease;
        cursor: pointer;
        height: 100%;
    }
    .quick-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(13,112,68,.04);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .quick-btn .qb-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        transition: transform .25s;
    }
    .quick-btn:hover .qb-icon { transform: scale(1.1); }
    .quick-btn.blue   .qb-icon { background: rgba(13,112,68,.12);  color: #0d7044; }
    .quick-btn.green  .qb-icon { background: rgba(26,153,88,.12);   color: #1a9958; }
    .quick-btn.purple .qb-icon { background: rgba(4,43,27,.1);      color: #085c34; }
    .quick-btn.amber  .qb-icon { background: rgba(77,200,122,.12);  color: #2dba6e; }
    .quick-btn.rose   .qb-icon { background: rgba(220,53,69,.1);    color: #dc3545; }
    .quick-btn.teal   .qb-icon { background: rgba(32,201,151,.12);  color: #20c997; }
    .quick-btn.slate  .qb-icon { background: rgba(100,116,139,.12); color: #64748b; }
    .quick-btn.cyan   .qb-icon { background: rgba(6,182,212,.12);   color: #06b6d4; }

    /* === Empty state === */
    .empty-state {
        text-align: center;
        padding: 2.5rem 1rem;
        color: var(--text-muted);
    }
    .empty-state i { font-size: 2.5rem; margin-bottom: .75rem; opacity: .4; }
    .empty-state p { font-size: .875rem; margin: 0; }

    /* Animate on load */
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-card, .chart-card, .table-card, .quick-btn {
        animation: fadeSlideUp .45s ease both;
    }
    .stat-card:nth-child(1) { animation-delay: .05s; }
    .stat-card:nth-child(2) { animation-delay: .10s; }
    .stat-card:nth-child(3) { animation-delay: .15s; }
    .stat-card:nth-child(4) { animation-delay: .20s; }
    .stat-card:nth-child(5) { animation-delay: .25s; }
    .stat-card:nth-child(6) { animation-delay: .30s; }
</style>
@endpush

@section('content')

{{-- ===========================
     GREETING BANNER
=========================== --}}
<div class="greeting-banner">
    <div class="content d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="greeting-time" id="greetingTime">Selamat Pagi</div>
            @if($isDokter)
                @php
                    $namaDokter = trim($dokterInfo->nm_dokter);
                    if (!Str::startsWith(strtolower($namaDokter), 'dr.') && !Str::startsWith(strtolower($namaDokter), 'dr ')) {
                        $namaDokter = 'dr. ' . $namaDokter;
                    }
                @endphp
                <h4>{{ $namaDokter }}</h4>
                <p>
                    {{ $dokterInfo->spesialis ? $dokterInfo->spesialis : 'Dokter' }} · Selamat datang di Dashboard {{ $activePortal === 'ranap' ? 'Rawat Inap' : 'Rawat Jalan' }} & Penunjang RS Namira
                </p>
            @else
                <h4>{{ session('auth_user.nama', 'Admin') }}</h4>
                <p>Selamat datang di Dashboard {{ $activePortal === 'ranap' ? 'Rawat Inap' : 'Rawat Jalan' }} & Penunjang RS Namira.</p>
            @endif
        </div>
        <div class="text-end">
            <div class="live-badge mb-2" style="{{ $activePortal === 'ranap' ? 'background:rgba(59,130,246,.2);border-color:rgba(59,130,246,.3);' : '' }}">
                <span class="live-dot" style="{{ $activePortal === 'ranap' ? 'background:#60a5fa;' : '' }}"></span>
                MODUL {{ $activePortal === 'ranap' ? 'RAWAT INAP' : 'RAWAT JALAN' }}
            </div>
            <div style="font-size:.8rem;color:rgba(255,255,255,.6);margin-top:.4rem;" id="liveClock"></div>
            <div style="font-size:.75rem;color:rgba(255,255,255,.45);">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0" style="font-size: 1.05rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi {{ $activePortal === 'ranap' ? 'bi-hospital' : 'bi-clipboard2-pulse-fill' }}" style="color: var(--primary);"></i>
            Data &amp; Statistik {{ $activePortal === 'ranap' ? 'Rawat Inap' : 'Rawat Jalan' }}
        </h5>
        <a href="{{ route('portal') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold" style="font-size:.78rem;">
            <i class="bi bi-door-open-fill me-1"></i> Ganti Portal Layanan
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    @if($activePortal === 'ranap')
        {{-- STAT CARDS RAWAT INAP ONLY --}}
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="bi bi-hospital-fill"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Pasien Dirawat Saat Ini</div>
                    <div class="stat-value">{{ $stats['rawat_inap'] }}</div>
                    <span class="stat-badge up"><i class="bi bi-person-check-fill"></i> Active Inpatient</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="stat-card">
                <div class="stat-icon teal"><i class="bi bi-door-open-fill"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Bed / Kamar Terpakai</div>
                    <div class="stat-value">{{ $stats['kamar_terpakai'] }}</div>
                    <span class="stat-badge neutral"><i class="bi bi-house"></i> Bangsal</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-calendar-plus-fill"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Ranap Masuk Hari Ini</div>
                    <div class="stat-value">{{ $stats['pasien_hari_ini'] }}</div>
                    <span class="stat-badge neutral"><i class="bi bi-clock"></i> Masuk Baru</span>
                </div>
            </div>
        </div>
    @else
        {{-- STAT CARDS RAWAT JALAN ONLY --}}
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-clipboard2-pulse-fill"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Pasien Rawat Jalan Hari Ini</div>
                    <div class="stat-value">{{ $stats['rawat_jalan'] }}</div>
                    <span class="stat-badge up"><i class="bi bi-arrow-up-short"></i> Poliklinik</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="stat-card">
                <div class="stat-icon rose"><i class="bi bi-lightning-charge-fill"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Pasien IGD / UGD</div>
                    <div class="stat-value">{{ $stats['pasien_igd'] }}</div>
                    <span class="stat-badge neutral"><i class="bi bi-clock"></i> Darurat</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Pasien Terlayani (RME)</div>
                    <div class="stat-value">{{ $stats['total_dokter'] }}</div>
                    <span class="stat-badge up"><i class="bi bi-check2-all"></i> Selesai</span>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ===========================
     GRAFIK + TABEL PASIEN
=========================== --}}
<div class="row g-3 mb-4">

    {{-- Grafik Kunjungan --}}
    <div class="col-12 col-xl-7">
        <div class="chart-card">
            <div class="section-header">
                <h6 class="section-title">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    {{ $isDokter ? 'Kunjungan Pasien — 7 Hari Terakhir' : 'Grafik Kunjungan 7 Hari Terakhir' }}
                </h6>
                <span style="font-size:.75rem;color:var(--text-muted);">
                    {{ $isDokter ? 'Data kunjungan 7 hari terakhir' : 'Data real-time dari SIMRS' }}
                </span>
            </div>
            <div style="position:relative;height:240px;">
                <canvas id="visitChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Distribusi Jenis Kunjungan --}}
    <div class="col-12 col-xl-5">
        <div class="chart-card">
            <div class="section-header">
                <h6 class="section-title">
                    <i class="bi bi-pie-chart-fill"></i>
                    Distribusi Kunjungan Hari Ini
                </h6>
            </div>
            <div style="position:relative;height:200px;display:flex;align-items:center;justify-content:center;">
                <canvas id="donutChart"></canvas>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-4 text-center">
                    <div style="font-size:.7rem;color:var(--text-muted);font-weight:600;">RALAN</div>
                    <div style="font-size:1.1rem;font-weight:800;color:#0d7044;">{{ $stats['rawat_jalan'] }}</div>
                </div>
                <div class="col-4 text-center">
                    <div style="font-size:.7rem;color:var(--text-muted);font-weight:600;">RANAP</div>
                    <div style="font-size:1.1rem;font-weight:800;color:#1a9958;">{{ $stats['rawat_inap'] }}</div>
                </div>
                <div class="col-4 text-center">
                    <div style="font-size:.7rem;color:var(--text-muted);font-weight:600;">IGD</div>
                    <div style="font-size:1.1rem;font-weight:800;color:#dc3545;">{{ $stats['pasien_igd'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===========================
     TABEL PASIEN TERBARU
=========================== --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-xl-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6 class="section-title mb-0">
                    <i class="bi bi-clock-history"></i>
                    @if($isDokter)
                        Pasien Hari Ini
                    @else
                        Registrasi Pasien Terbaru Hari Ini
                    @endif
                </h6>
                <div class="d-flex align-items-center gap-2">
                    @if($isDokter)
                        <span style="
                            display:inline-flex;align-items:center;gap:.35rem;
                            font-size:.72rem;font-weight:700;letter-spacing:.4px;
                            background:rgba(13,112,68,.1);color:#0d7044;
                            border:1px solid rgba(13,112,68,.2);
                            border-radius:20px;padding:.2rem .75rem;
                        ">
                            <i class="bi bi-person-badge-fill"></i>
                            {{ $dokterInfo->nm_dokter }}
                        </span>
                    @endif
                    <a href="#" class="section-action">Lihat Semua →</a>
                </div>
            </div>

            @if(count($pasienTerbaru) > 0)
            <div class="table-responsive">
                <table class="table table-custom table-hover" id="tabelPasienTerbaru">
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>No. Rawat</th>
                            <th>Jam</th>
                            <th>Poliklinik</th>
                            @if(!$isDokter)
                            <th>Dokter</th>
                            @endif
                            <th>Jenis</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pasienTerbaru as $p)
                        @php
                            $namaPasien = ucwords(strtolower($p->nm_pasien ?? '-'));
                            $namaDokter = $p->nm_dokter ? (Str::startsWith(strtolower($p->nm_dokter), 'dr.') ? $p->nm_dokter : 'dr. '.$p->nm_dokter) : '-';
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="patient-avatar">
                                        {{ strtoupper(substr($p->nm_pasien ?? 'P', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="patient-title-text">
                                            {{ $namaPasien }}
                                        </div>
                                        <div class="patient-sub-text">
                                            {{ $p->jk === 'L' ? '♂ Laki-laki' : '♀ Perempuan' }}
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
                                @if($p->status_lanjut === 'Ranap')
                                    <span class="badge-ranap">Ranap</span>
                                @elseif(str_contains(strtoupper($p->nm_poli ?? ''), 'IGD'))
                                    <span class="badge-igd">IGD</span>
                                @else
                                    <span class="badge-ralan">Ralan</span>
                                @endif
                            </td>
                            <td>
                                @if(($p->status_layanan ?? 'belum') === 'terlayani')
                                    <span style="display:inline-flex;align-items:center;gap:.3rem;
                                                background:rgba(13,112,68,.1);color:#0d7044;
                                                border:1px solid rgba(13,112,68,.18);
                                                border-radius:20px;padding:.18rem .6rem;
                                                font-size:.68rem;font-weight:700;">
                                        <span style="width:6px;height:6px;background:#0d7044;border-radius:50%;display:inline-block;"></span>
                                        Terlayani
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:.3rem;
                                                background:rgba(255,193,7,.1);color:#b88a00;
                                                border:1px solid rgba(255,193,7,.25);
                                                border-radius:20px;padding:.18rem .6rem;
                                                font-size:.68rem;font-weight:700;">
                                        <span style="width:6px;height:6px;background:#d4a017;border-radius:50%;display:inline-block;"></span>
                                        Menunggu
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
                <i class="bi bi-inbox d-block"></i>
                @if($isDokter)
                    <p>Belum ada pasien Anda hari ini</p>
                    <p style="font-size:.78rem;margin-top:.35rem;">Pasien yang terdaftar dengan nama dokter Anda akan muncul di sini.</p>
                @else
                    <p>Belum ada registrasi pasien hari ini</p>
                    <p style="font-size:.78rem;margin-top:.35rem;">Data akan muncul setelah ada kunjungan.</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Quick Access --}}
    <div class="col-12 col-xl-4">
        <div class="table-card">
            <div class="table-card-header">
                <h6 class="section-title mb-0">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    Akses Cepat
                </h6>
            </div>
            <div class="p-3">
                <div class="row g-2">
                    @if($activePortal === 'ranap')
                        <div class="col-6">
                            <a href="{{ route('rawat-inap') }}" class="quick-btn purple">
                                <div class="qb-icon"><i class="bi bi-hospital"></i></div>
                                Kamar Inap
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('rawat-inap') }}" class="quick-btn blue">
                                <div class="qb-icon"><i class="bi bi-building"></i></div>
                                Bangsal Ranap
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="quick-btn teal">
                                <div class="qb-icon"><i class="bi bi-eyedropper"></i></div>
                                Lab Ranap
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="quick-btn amber">
                                <div class="qb-icon"><i class="bi bi-camera-fill"></i></div>
                                Radiologi Ranap
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="quick-btn cyan">
                                <div class="qb-icon"><i class="bi bi-capsule-pill"></i></div>
                                Depo Inap
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="quick-btn green">
                                <div class="qb-icon"><i class="bi bi-file-earmark-medical-fill"></i></div>
                                RME Ranap
                            </a>
                        </div>
                    @else
                        <div class="col-6">
                            <a href="{{ route('rawat-jalan') }}" class="quick-btn green">
                                <div class="qb-icon"><i class="bi bi-clipboard2-pulse-fill"></i></div>
                                Poliklinik Ralan
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('rawat-jalan') }}" class="quick-btn rose">
                                <div class="qb-icon"><i class="bi bi-lightning-fill"></i></div>
                                IGD / UGD
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="quick-btn teal">
                                <div class="qb-icon"><i class="bi bi-eyedropper"></i></div>
                                Lab Ralan
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="quick-btn amber">
                                <div class="qb-icon"><i class="bi bi-camera-fill"></i></div>
                                Radiologi Ralan
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="quick-btn cyan">
                                <div class="qb-icon"><i class="bi bi-capsule-pill"></i></div>
                                Apotek Ralan
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="quick-btn blue">
                                <div class="qb-icon"><i class="bi bi-file-earmark-medical-fill"></i></div>
                                RME Ralan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===========================
     INFO SISTEM
=========================== --}}
<div class="row g-3 mb-2">
    <div class="col-12">
        <div class="card-modern p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-shield-check-fill text-success"></i>
                </div>
                <div>
                    <div style="font-size:.82rem;font-weight:600;color:var(--text-main);">Sistem Berjalan Normal</div>
                    <div style="font-size:.72rem;color:var(--text-muted);">
                        Pengguna: <strong>{{ session('auth_user.nama') }}</strong> ·
                        IP: {{ session('auth_user.ip') }}
                    </div>
                </div>
            </div>
            <div style="font-size:.72rem;color:var(--text-muted);">
                ePasien v1.0 · SIMRS Khanza Compatible
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    'use strict';

    // === Live Clock & Greeting ===
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const clock = document.getElementById('liveClock');
        if (clock) clock.textContent = `${h}:${m}:${s} WIB`;

        const greeting = document.getElementById('greetingTime');
        if (greeting) {
            const hour = now.getHours();
            if (hour < 11)      greeting.textContent = 'Selamat Pagi';
            else if (hour < 15) greeting.textContent = 'Selamat Siang';
            else if (hour < 18) greeting.textContent = 'Selamat Sore';
            else                greeting.textContent = 'Selamat Malam';
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // === Chart.js: Bar Chart Kunjungan Mingguan ===
    const visitCtx = document.getElementById('visitChart');
    if (visitCtx) {
        const labels = @json(array_column($kunjunganMingguan, 'tanggal'));
        const values = @json(array_column($kunjunganMingguan, 'jumlah'));

        new Chart(visitCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: values,
                    backgroundColor: function(ctx) {
                        const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 240);
                        gradient.addColorStop(0, 'rgba(13,112,68,.85)');
                        gradient.addColorStop(1, 'rgba(13,112,68,.12)');
                        return gradient;
                    },
                    borderColor: '#0d7044',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#042b1b',
                        titleColor: '#fff',
                        bodyColor: 'rgba(255,255,255,.75)',
                        borderColor: 'rgba(255,255,255,.1)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} kunjungan`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: 'Inter' }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,.04)', drawBorder: false },
                        ticks: {
                            font: { size: 11, family: 'Inter' },
                            color: '#94a3b8',
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // === Chart.js: Donut Chart Distribusi ===
    const donutCtx = document.getElementById('donutChart');
    if (donutCtx) {
        const ralan = {{ $stats['rawat_jalan'] }};
        const ranap = {{ $stats['rawat_inap'] }};
        const igd   = {{ $stats['pasien_igd'] }};
        const total = ralan + ranap + igd;

        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Rawat Jalan', 'Rawat Inap', 'IGD'],
                datasets: [{
                    data: total > 0 ? [ralan, ranap, igd] : [1, 1, 1],
                    backgroundColor: ['#0d7044', '#1a9958', '#dc3545'],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#042b1b',
                        titleColor: '#fff',
                        bodyColor: 'rgba(255,255,255,.75)',
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => {
                                if (total === 0) return ' Belum ada data';
                                const pct = ((ctx.parsed / total) * 100).toFixed(1);
                                return ` ${ctx.parsed} pasien (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // === Initialize DataTable Pasien Terbaru ===
    if ($('#tabelPasienTerbaru').length > 0) {
        $('#tabelPasienTerbaru').DataTable({
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
            order: [[2, 'desc']]
        });
    }
})();
</script>
@endpush
