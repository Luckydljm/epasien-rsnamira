@extends('layouts.app')

@section('title', 'Rawat Inap')
@section('page-title', 'Rawat Inap')

@push('styles')
<style>
    /* =============================================
       RAWAT INAP PAGE — ePasien RS Namira
    ============================================= */

    /* === Page Header === */
    .page-header-banner {
        background: linear-gradient(135deg, #042b1b 0%, #085c34 50%, #0d7044 100%);
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
        background: rgba(255,255,255,.04);
        border-radius: 50%;
    }
    .page-header-banner .content { position: relative; z-index: 2; }
    .page-header-banner h4 { font-size: 1.35rem; font-weight: 800; margin: 0 0 .25rem; }
    .page-header-banner p  { font-size: .855rem; color: rgba(255,255,255,.72); margin: 0; }

    /* === Mini Stat Cards === */
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
    .mini-stat-icon.purple { background: rgba(139,92,246,.12); color: #7c3aed; }
    .mini-stat-icon.amber  { background: rgba(245,158,11,.12); color: #b45309; }
    .mini-stat-icon.indigo { background: rgba(99,102,241,.12); color: #4f46e5; }
    .mini-stat-icon.teal   { background: rgba(20,184,166,.12); color: #0d9488; }
    .mini-stat-icon.cyan   { background: rgba(6,182,212,.12);  color: #0891b2; }
    .mini-stat-icon.rose   { background: rgba(244,63,94,.12);  color: #be123c; }
    .mini-stat-value { font-size: 1.6rem; font-weight: 800; color: var(--text-main); line-height: 1; }
    .mini-stat-label { font-size: .72rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; margin-top: .2rem; }

    /* === Custom Nav Tabs === */
    .nav-tabs-custom {
        border-bottom: 1px solid var(--border);
        gap: .5rem;
    }
    .nav-tabs-custom .nav-link {
        border: 1px solid transparent;
        border-radius: 12px 12px 0 0;
        color: var(--text-muted);
        font-size: .875rem;
        padding: .65rem 1.25rem;
        transition: all .2s;
        background: rgba(0,0,0,.02);
    }
    .nav-tabs-custom .nav-link:hover {
        background: rgba(13,112,68,.06);
        color: var(--primary);
    }
    .nav-tabs-custom .nav-link.active {
        background: var(--bg-card);
        border-color: var(--border) var(--border) transparent;
        color: var(--primary);
        box-shadow: 0 -2px 6px rgba(0,0,0,.03);
    }

    /* === Table Card === */
    .table-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .table-card-header {
        padding: 1.1rem 1.4rem 0 1.4rem;
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

    /* === Table Non-wrapping & Horizontal Scroll === */
    .table-card .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    .table-custom th,
    .table-custom td {
        white-space: nowrap !important;
    }

    /* === Patient Avatar === */
    .patient-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #085c34, #0d7044);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: .75rem;
        flex-shrink: 0;
    }

    /* === Info Card di dalam tabel === */
    .ranap-info-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: rgba(13,112,68,.07);
        border: 1px solid rgba(13,112,68,.15);
        border-radius: 8px;
        padding: .25rem .65rem;
        font-size: .74rem;
        font-weight: 600;
        color: #085c34;
        margin: .15rem .1rem;
        white-space: nowrap !important;
    }
    .ranap-info-chip.lama {
        background: rgba(245,158,11,.07);
        border-color: rgba(245,158,11,.2);
        color: #92400e;
    }
    .ranap-info-chip.diagnosa {
        background: rgba(6,182,212,.07);
        border-color: rgba(6,182,212,.18);
        color: #0369a1;
        max-width: none !important;
        white-space: nowrap !important;
    }

    /* === Kelas Badges === */
    .badge-kelas-vvip  { background: rgba(139,92,246,.15); color: #6d28d9; border: 1px solid rgba(139,92,246,.3); border-radius: 20px; padding: .25rem .7rem; font-size: .68rem; font-weight: 800; }
    .badge-kelas-vip   { background: rgba(234,179,8,.12);  color: #854d0e; border: 1px solid rgba(234,179,8,.25); border-radius: 20px; padding: .25rem .7rem; font-size: .68rem; font-weight: 700; }
    .badge-kelas-i     { background: rgba(99,102,241,.1);  color: #4f46e5; border: 1px solid rgba(99,102,241,.2); border-radius: 20px; padding: .25rem .7rem; font-size: .68rem; font-weight: 700; }
    .badge-kelas-ii    { background: rgba(20,184,166,.1);  color: #0d9488; border: 1px solid rgba(20,184,166,.2); border-radius: 20px; padding: .25rem .7rem; font-size: .68rem; font-weight: 700; }
    .badge-kelas-iii   { background: rgba(13,112,68,.1);   color: #0d7044; border: 1px solid rgba(13,112,68,.2);  border-radius: 20px; padding: .25rem .7rem; font-size: .68rem; font-weight: 700; }
    .badge-kelas-other { background: rgba(100,116,139,.1); color: #475569; border: 1px solid rgba(100,116,139,.2); border-radius: 20px; padding: .25rem .7rem; font-size: .68rem; font-weight: 700; }

    /* === Status dirawat badge === */
    .badge-dirawat {
        display: inline-flex; align-items: center; gap: .35rem;
        background: rgba(13,112,68,.1);
        color: #0d7044;
        border-radius: 20px;
        padding: .25rem .75rem;
        font-size: .72rem;
        font-weight: 700;
        border: 1px solid rgba(13,112,68,.18);
        white-space: nowrap !important;
    }
    .dot-pulse {
        width: 7px; height: 7px;
        background: #0d7044;
        border-radius: 50%;
        animation: pulse-green2 2s infinite;
    }
    @keyframes pulse-green2 {
        0%,100% { box-shadow: 0 0 0 0 rgba(13,112,68,.4); }
        50% { box-shadow: 0 0 0 4px rgba(13,112,68,.0); }
    }

    /* === Empty state === */
    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--text-muted); }
    .empty-state i { font-size: 2.8rem; margin-bottom: .75rem; opacity: .35; display: block; }
    .empty-state p { font-size: .875rem; margin: 0; }

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
    .mini-stat:nth-child(5) { animation-delay: .25s; }
    .mini-stat:nth-child(6) { animation-delay: .30s; }
    .mini-stat:nth-child(7) { animation-delay: .35s; }
    .mini-stat:nth-child(8) { animation-delay: .40s; }
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
                <i class="bi bi-hospital-fill me-2"></i>
                Rawat Inap
                @if($isDokter)
                    <span style="font-size:.9rem;font-weight:600;opacity:.8;">— Pasien Saya</span>
                @endif
            </h4>
            <p>
                Monitoring dan kelola pasien rawat inap
                @if($isDokter)
                    · <span style="font-weight:600;">dr. {{ $nmDokter }}</span>
                @endif
                · Per {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}
            </p>
        </div>
        <div class="text-end">
            <div style="font-size:.82rem;color:rgba(255,255,255,.65);">Update real-time</div>
            <div style="font-size:1.05rem;font-weight:700;" id="ranapClock"></div>
        </div>
    </div>
</div>

{{-- ===========================
     MINI STAT CARDS (8 CARDS LENGKAP)
=========================== --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon blue"><i class="bi bi-hospital-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['total_ranap'] }}</div>
                <div class="mini-stat-label">Sedang Dirawat</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['total_selesai'] }}</div>
                <div class="mini-stat-label">Sudah Pulang</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon purple"><i class="bi bi-gem"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['kelas_vvip'] }}</div>
                <div class="mini-stat-label">Kelas VVIP</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon amber"><i class="bi bi-star-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['kelas_vip'] }}</div>
                <div class="mini-stat-label">Kelas VIP</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon indigo"><i class="bi bi-1-circle-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['kelas_i'] }}</div>
                <div class="mini-stat-label">Kelas 1</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon teal"><i class="bi bi-2-circle-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['kelas_ii'] }}</div>
                <div class="mini-stat-label">Kelas 2</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon cyan"><i class="bi bi-3-circle-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['kelas_iii'] }}</div>
                <div class="mini-stat-label">Kelas 3</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon rose"><i class="bi bi-calendar-week-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['rata_lama_inap'] }}</div>
                <div class="mini-stat-label">Rata-rata Hari</div>
            </div>
        </div>
    </div>
</div>

{{-- Info jika ada pasien ranap tanpa data kelas kamar --}}
@if($statsRanap['tanpa_kelas'] > 0)
<div class="alert d-flex align-items-center gap-2 mb-3"
     style="border-radius:12px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);color:#92400e;font-size:.845rem;">
    <i class="bi bi-info-circle-fill" style="font-size:1.1rem;flex-shrink:0;"></i>
    <span>
        <strong>{{ $statsRanap['tanpa_kelas'] }} pasien</strong> sedang dirawat tetapi belum memiliki data kelas kamar yang tercatat.
    </span>
</div>
@endif

{{-- ===========================
     TABEL PASIEN RAWAT INAP (2 TAB)
=========================== --}}
<div class="table-card">
    <div class="table-card-header bg-white pb-0 border-bottom-0">
        <ul class="nav nav-tabs nav-tabs-custom flex-grow-1" id="ranapTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold d-flex align-items-center gap-2" id="dirawat-tab" data-bs-toggle="tab" data-bs-target="#dirawatPane" type="button" role="tab" aria-controls="dirawatPane" aria-selected="true">
                    <i class="bi bi-hospital-fill text-success"></i>
                    <span>Masih Dirawat</span>
                    <span class="badge bg-success rounded-pill ms-1" style="font-size:.72rem;">{{ count($pasienRanap) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold d-flex align-items-center gap-2" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesaiPane" type="button" role="tab" aria-controls="selesaiPane" aria-selected="false">
                    <i class="bi bi-check-circle-fill text-primary"></i>
                    <span>Sudah Pulang / Selesai</span>
                    <span class="badge bg-secondary rounded-pill ms-1" style="font-size:.72rem;">{{ count($pasienRanapSelesai) }}</span>
                </button>
            </li>
        </ul>
        @if($isDokter)
        <div class="py-2">
            <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;font-weight:700;
                         background:rgba(13,112,68,.1);color:#0d7044;border:1px solid rgba(13,112,68,.2);
                         border-radius:20px;padding:.2rem .75rem;">
                <i class="bi bi-person-badge-fill"></i> dr. {{ $nmDokter }}
            </span>
        </div>
        @endif
    </div>

    <div class="tab-content" id="ranapTabContent">
        {{-- TAB 1: MASIH DIRAWAT --}}
        <div class="tab-pane fade show active p-3" id="dirawatPane" role="tabpanel" aria-labelledby="dirawat-tab">
            @if(count($pasienRanap) > 0)
            <div class="table-responsive">
                <table class="table table-custom table-hover" id="tabelRanapAktif">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Pasien</th>
                            <th>No. Rawat</th>
                            <th>Masuk</th>
                            <th>Bangsal / Kamar</th>
                            <th>Kelas</th>
                            <th>Lama Inap</th>
                            @if(!$isDokter)
                            <th>Dokter / Spesialis</th>
                            @endif
                            <th>Diagnosa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pasienRanap as $i => $p)
                        @php
                            $tglMasuk = ($p->tgl_masuk && $p->tgl_masuk !== '0000-00-00')
                                ? \Carbon\Carbon::parse($p->tgl_masuk) : null;
                            $lamaHari = $tglMasuk ? $tglMasuk->diffInDays(now()) : ($p->lama ?? 0);
                            $kelas = strtoupper($p->kelas ?? '');
                            $namaPasien = ucwords(strtolower($p->nm_pasien ?? '-'));
                            $namaDokter = $p->nm_dokter ? (Str::startsWith(strtolower($p->nm_dokter), 'dr.') ? $p->nm_dokter : 'dr. '.$p->nm_dokter) : '-';
                        @endphp
                        <tr>
                            <td style="color:var(--text-muted);font-size:.8rem;">{{ $i + 1 }}</td>
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
                                    {{ $tglMasuk ? $tglMasuk->locale('id')->isoFormat('D MMM YYYY') : '-' }}
                                </div>
                                @if($p->jam_masuk)
                                <div style="font-size:.72rem;color:#64748b;">
                                    <i class="bi bi-clock me-1"></i>{{ substr($p->jam_masuk, 0, 5) }} WIB
                                </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:.83rem;font-weight:600;color:#1e293b;">
                                    {{ ucwords(strtolower($p->nm_bangsal ?? $p->kd_bangsal ?? '-')) }}
                                </div>
                                @if($p->kd_kamar)
                                <div style="font-size:.72rem;color:#64748b;">
                                    <i class="bi bi-door-open me-1"></i>Kamar {{ $p->kd_kamar }}
                                </div>
                                @endif
                            </td>
                            <td>
                                @if(str_contains($kelas, 'VVIP'))
                                    <span class="badge-kelas-vvip"><i class="bi bi-gem me-1"></i>{{ $p->kelas }}</span>
                                @elseif(str_contains($kelas, 'VIP'))
                                    <span class="badge-kelas-vip"><i class="bi bi-star-fill me-1"></i>{{ $p->kelas }}</span>
                                @elseif(str_contains($kelas, '3') || str_contains($kelas, 'III'))
                                    <span class="badge-kelas-iii">{{ $p->kelas }}</span>
                                @elseif(str_contains($kelas, '2') || str_contains($kelas, 'II'))
                                    <span class="badge-kelas-ii">{{ $p->kelas }}</span>
                                @elseif(str_contains($kelas, '1') || str_contains($kelas, 'I'))
                                    <span class="badge-kelas-i">{{ $p->kelas }}</span>
                                @else
                                    <span class="badge-kelas-other">{{ $p->kelas ?? '-' }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="ranap-info-chip lama">
                                    <i class="bi bi-calendar-week"></i>
                                    {{ $lamaHari }} hari
                                </span>
                            </td>
                            @if(!$isDokter)
                            <td>
                                <div style="font-size:.83rem;font-weight:600;color:#1e293b;">
                                    {{ $namaDokter }}
                                </div>
                                @if($p->spesialis)
                                <div style="font-size:.72rem;color:#64748b;">
                                    {{ $p->spesialis }}
                                </div>
                                @endif
                            </td>
                            @endif
                            <td>
                                @if($p->nm_penyakit)
                                    <span class="ranap-info-chip diagnosa" title="{{ $p->nm_penyakit }}">
                                        <i class="bi bi-file-medical"></i>
                                        {{ $p->nm_penyakit }}
                                    </span>
                                    @if($p->kd_penyakit)
                                    <div style="font-size:.68rem;color:#64748b;margin-top:.2rem;">
                                        ICD: {{ $p->kd_penyakit }}
                                    </div>
                                    @endif
                                @else
                                    <span style="color:#94a3b8;font-size:.8rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-dirawat">
                                    <span class="dot-pulse"></span>
                                    Dirawat
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="bi bi-hospital"></i>
                @if($isDokter)
                    <p style="font-weight:600;">Tidak ada pasien rawat inap Anda yang sedang dirawat saat ini</p>
                    <p style="font-size:.8rem;margin-top:.35rem;color:var(--text-muted);">
                        Pasien yang dirawat atas nama <strong>dr. {{ $nmDokter }}</strong> akan tampil di sini.
                    </p>
                @else
                    <p style="font-weight:600;">Tidak ada pasien rawat inap yang sedang dirawat saat ini</p>
                    <p style="font-size:.8rem;margin-top:.35rem;color:var(--text-muted);">
                        Data akan muncul ketika ada pasien yang sedang menjalani rawat inap.
                    </p>
                @endif
            </div>
            @endif
        </div>

        {{-- TAB 2: SUDAH SELESAI / PULANG --}}
        <div class="tab-pane fade p-3" id="selesaiPane" role="tabpanel" aria-labelledby="selesai-tab">
            @if(count($pasienRanapSelesai) > 0)
            <div class="table-responsive">
                <table class="table table-custom table-hover" id="tabelRanapSelesai">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Pasien</th>
                            <th>No. Rawat</th>
                            <th>Masuk — Keluar</th>
                            <th>Bangsal / Kamar</th>
                            <th>Kelas</th>
                            <th>Lama</th>
                            @if(!$isDokter)
                            <th>Dokter / Spesialis</th>
                            @endif
                            <th>Diagnosa</th>
                            <th>Status Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pasienRanapSelesai as $i => $p)
                        @php
                            $tglMasuk  = ($p->tgl_masuk && $p->tgl_masuk !== '0000-00-00')
                                ? \Carbon\Carbon::parse($p->tgl_masuk) : null;
                            $tglKeluar = ($p->tgl_keluar && $p->tgl_keluar !== '0000-00-00')
                                ? \Carbon\Carbon::parse($p->tgl_keluar) : null;
                            $lamaHari  = $p->lama ?? ($tglMasuk && $tglKeluar ? $tglMasuk->diffInDays($tglKeluar) : 0);
                            $kelas     = strtoupper($p->kelas ?? '');
                            $stts      = trim($p->stts_pulang ?? '-');
                            $namaPasien = ucwords(strtolower($p->nm_pasien ?? '-'));
                            $namaDokter = $p->nm_dokter ? (Str::startsWith(strtolower($p->nm_dokter), 'dr.') ? $p->nm_dokter : 'dr. '.$p->nm_dokter) : '-';

                            // Badge Status Pulang
                            $badgeClass = 'bg-secondary text-white';
                            if (in_array($stts, ['Sehat', 'Sembuh', 'Membaik', 'Atas Persetujuan Dokter'])) {
                                $badgeClass = 'bg-success text-white';
                            } elseif (in_array($stts, ['Rujuk', 'APS', 'Atas Permintaan Sendiri', 'Pulang Paksa'])) {
                                $badgeClass = 'bg-warning text-dark';
                            } elseif (in_array($stts, ['+', 'Meninggal'])) {
                                $badgeClass = 'bg-danger text-white';
                            }
                        @endphp
                        <tr>
                            <td style="color:var(--text-muted);font-size:.8rem;">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="patient-avatar" style="background:linear-gradient(135deg, #475569, #64748b);">
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
                                <div style="font-size:.8rem;font-weight:600;color:#1e293b;">
                                    <i class="bi bi-box-arrow-in-right text-success me-1"></i>
                                    {{ $tglMasuk ? $tglMasuk->locale('id')->isoFormat('D MMM YYYY') : '-' }}
                                </div>
                                <div style="font-size:.8rem;font-weight:600;color:#64748b;">
                                    <i class="bi bi-box-arrow-right text-danger me-1"></i>
                                    {{ $tglKeluar ? $tglKeluar->locale('id')->isoFormat('D MMM YYYY') : '-' }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size:.83rem;font-weight:600;color:#1e293b;">
                                    {{ ucwords(strtolower($p->nm_bangsal ?? $p->kd_bangsal ?? '-')) }}
                                </div>
                                @if($p->kd_kamar)
                                <div style="font-size:.72rem;color:#64748b;">
                                    <i class="bi bi-door-open me-1"></i>Kamar {{ $p->kd_kamar }}
                                </div>
                                @endif
                            </td>
                            <td>
                                @if(str_contains($kelas, 'VVIP'))
                                    <span class="badge-kelas-vvip"><i class="bi bi-gem me-1"></i>{{ $p->kelas }}</span>
                                @elseif(str_contains($kelas, 'VIP'))
                                    <span class="badge-kelas-vip"><i class="bi bi-star-fill me-1"></i>{{ $p->kelas }}</span>
                                @elseif(str_contains($kelas, '3') || str_contains($kelas, 'III'))
                                    <span class="badge-kelas-iii">{{ $p->kelas }}</span>
                                @elseif(str_contains($kelas, '2') || str_contains($kelas, 'II'))
                                    <span class="badge-kelas-ii">{{ $p->kelas }}</span>
                                @elseif(str_contains($kelas, '1') || str_contains($kelas, 'I'))
                                    <span class="badge-kelas-i">{{ $p->kelas }}</span>
                                @else
                                    <span class="badge-kelas-other">{{ $p->kelas ?? '-' }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="ranap-info-chip lama">
                                    <i class="bi bi-calendar-check"></i>
                                    {{ $lamaHari }} hari
                                </span>
                            </td>
                            @if(!$isDokter)
                            <td>
                                <div style="font-size:.83rem;font-weight:600;color:#1e293b;">
                                    {{ $namaDokter }}
                                </div>
                                @if($p->spesialis)
                                <div style="font-size:.72rem;color:#64748b;">
                                    {{ $p->spesialis }}
                                </div>
                                @endif
                            </td>
                            @endif
                            <td>
                                @if($p->nm_penyakit)
                                    <span class="ranap-info-chip diagnosa" title="{{ $p->nm_penyakit }}">
                                        <i class="bi bi-file-medical"></i>
                                        {{ $p->nm_penyakit }}
                                    </span>
                                    @if($p->kd_penyakit)
                                    <div style="font-size:.68rem;color:#64748b;margin-top:.2rem;">
                                        ICD: {{ $p->kd_penyakit }}
                                    </div>
                                    @endif
                                @else
                                    <span style="color:#94a3b8;font-size:.8rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }}" style="font-size:.72rem;padding:.25rem .65rem;border-radius:20px;font-weight:600;">
                                    {{ $stts }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="bi bi-check-circle"></i>
                <p style="font-weight:600;">Belum ada riwayat pasien rawat inap yang selesai/pulang</p>
            </div>
            @endif
        </div>
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
        const el = document.getElementById('ranapClock');
        if (el) el.textContent = `${h}:${m}:${s} WIB`;
    }
    tick();
    setInterval(tick, 1000);

    // === Initialize DataTable: Masih Dirawat ===
    if ($('#tabelRanapAktif').length > 0) {
        $('#tabelRanapAktif').DataTable({
            order: [[3, 'desc']], // Urutkan berdasarkan tgl masuk terbaru
            columnDefs: [
                { orderable: false, targets: [0] }
            ]
        });
    }

    // === Initialize DataTable: Sudah Pulang / Selesai ===
    if ($('#tabelRanapSelesai').length > 0) {
        var dtSelesai = $('#tabelRanapSelesai').DataTable({
            order: [[3, 'desc']], // Urutkan berdasarkan tgl keluar terbaru
            columnDefs: [
                { orderable: false, targets: [0] }
            ]
        });

        // Penyesuaian kolom DataTables saat ganti Tab
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    }
});
</script>
@endpush
