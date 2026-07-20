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
    .mini-stat-icon.purple { background: rgba(139,92,246,.1);  color: #7c3aed; }
    .mini-stat-icon.teal   { background: rgba(32,201,151,.12); color: #20c997; }
    .mini-stat-icon.amber  { background: rgba(245,158,11,.1);  color: #b45309; }
    .mini-stat-icon.cyan   { background: rgba(6,182,212,.1);   color: #0891b2; }
    .mini-stat-icon.rose   { background: rgba(244,63,94,.1);   color: #be123c; }
    .mini-stat-value { font-size: 1.6rem; font-weight: 800; color: var(--text-main); line-height: 1; }
    .mini-stat-label { font-size: .72rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; margin-top: .2rem; }

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

    /* === Table === */
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
        background: linear-gradient(135deg, #085c34, #0d7044);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: .73rem;
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
    }
    .ranap-info-chip.kelas {
        background: rgba(139,92,246,.07);
        border-color: rgba(139,92,246,.18);
        color: #6d28d9;
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
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* === Kelas Badges === */
    .badge-kelas-vip   { background: rgba(234,179,8,.12);  color: #854d0e; border: 1px solid rgba(234,179,8,.25); border-radius: 20px; padding: .2rem .6rem; font-size: .68rem; font-weight: 700; }
    .badge-kelas-i     { background: rgba(139,92,246,.1);  color: #6d28d9; border: 1px solid rgba(139,92,246,.2); border-radius: 20px; padding: .2rem .6rem; font-size: .68rem; font-weight: 700; }
    .badge-kelas-ii    { background: rgba(59,130,246,.1);  color: #1d4ed8; border: 1px solid rgba(59,130,246,.2); border-radius: 20px; padding: .2rem .6rem; font-size: .68rem; font-weight: 700; }
    .badge-kelas-iii   { background: rgba(13,112,68,.1);   color: #0d7044; border: 1px solid rgba(13,112,68,.2);  border-radius: 20px; padding: .2rem .6rem; font-size: .68rem; font-weight: 700; }
    .badge-kelas-other { background: rgba(100,116,139,.1); color: #475569; border: 1px solid rgba(100,116,139,.2); border-radius: 20px; padding: .2rem .6rem; font-size: .68rem; font-weight: 700; }

    /* === Status dirawat badge === */
    .badge-dirawat {
        display: inline-flex; align-items: center; gap: .3rem;
        background: rgba(13,112,68,.1);
        color: #0d7044;
        border-radius: 20px;
        padding: .22rem .7rem;
        font-size: .7rem;
        font-weight: 700;
        border: 1px solid rgba(13,112,68,.18);
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
                Pasien yang sedang menjalani rawat inap
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
     MINI STAT CARDS
=========================== --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="mini-stat">
            <div class="mini-stat-icon blue"><i class="bi bi-hospital-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['total_ranap'] }}</div>
                <div class="mini-stat-label">Total Ranap</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="mini-stat">
            <div class="mini-stat-icon amber"><i class="bi bi-star-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['kelas_vip'] }}</div>
                <div class="mini-stat-label">Kelas VIP</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="mini-stat">
            <div class="mini-stat-icon purple"><i class="bi bi-1-circle-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['kelas_i'] }}</div>
                <div class="mini-stat-label">Kelas I</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="mini-stat">
            <div class="mini-stat-icon teal"><i class="bi bi-2-circle-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['kelas_ii'] }}</div>
                <div class="mini-stat-label">Kelas II</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="mini-stat">
            <div class="mini-stat-icon cyan"><i class="bi bi-3-circle-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['kelas_iii'] }}</div>
                <div class="mini-stat-label">Kelas III</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
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
     TABEL PASIEN RAWAT INAP
=========================== --}}
<div class="table-card">
    <div class="table-card-header">
        <h6 class="section-title">
            <i class="bi bi-bed"></i>
            @if($isDokter)
                Pasien Ranap Saya — Sedang Dirawat
            @else
                Semua Pasien Rawat Inap — Sedang Dirawat
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
                {{ count($pasienRanap) }} pasien
            </span>
        </div>
    </div>

    @if(count($pasienRanap) > 0)
    <div class="table-responsive">
        <table class="table table-custom" id="tabelRanap">
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
                    // Hitung lama inap
                    $tglMasuk = ($p->tgl_masuk && $p->tgl_masuk !== '0000-00-00')
                        ? \Carbon\Carbon::parse($p->tgl_masuk) : null;
                    $lamaHari = $tglMasuk ? $tglMasuk->diffInDays(now()) : ($p->lama ?? 0);
                    // Kelas
                    $kelas = strtoupper($p->kelas ?? '');
                @endphp
                <tr>
                    <td style="color:var(--text-muted);font-size:.8rem;">{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="patient-avatar">
                                {{ strtoupper(substr($p->nm_pasien ?? 'P', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.855rem;">
                                    {{ $p->nm_pasien ?? '-' }}
                                </div>
                                <div style="font-size:.72rem;color:var(--text-muted);">
                                    {{ $p->jk === 'L' ? '♂ Laki-laki' : '♀ Perempuan' }}
                                    @if($p->tgl_lahir && $p->tgl_lahir !== '0000-00-00')
                                        · {{ \Carbon\Carbon::parse($p->tgl_lahir)->age }} thn
                                    @endif
                                    · {{ $p->no_rkm_medis ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:.78rem;font-family:monospace;color:var(--text-muted);">
                            {{ $p->no_rawat }}
                        </span>
                    </td>
                    <td>
                        <div style="font-size:.83rem;font-weight:600;">
                            {{ $tglMasuk ? $tglMasuk->locale('id')->isoFormat('D MMM YYYY') : '-' }}
                        </div>
                        @if($p->jam_masuk)
                        <div style="font-size:.72rem;color:var(--text-muted);">
                            Pukul {{ substr($p->jam_masuk, 0, 5) }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:.83rem;font-weight:600;">
                            {{ $p->nm_bangsal ?? $p->kd_bangsal ?? '-' }}
                        </div>
                        @if($p->kd_kamar)
                        <div style="font-size:.72rem;color:var(--text-muted);">
                            <i class="bi bi-door-open"></i> Kamar {{ $p->kd_kamar }}
                        </div>
                        @endif
                    </td>
                    <td>
                        @if(str_contains($kelas, 'VIP'))
                            <span class="badge-kelas-vip"><i class="bi bi-star-fill"></i> VIP</span>
                        @elseif(str_contains($kelas, 'III'))
                            <span class="badge-kelas-iii">Kelas III</span>
                        @elseif(str_contains($kelas, 'II'))
                            <span class="badge-kelas-ii">Kelas II</span>
                        @elseif(str_contains($kelas, 'I'))
                            <span class="badge-kelas-i">Kelas I</span>
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
                        <div style="font-size:.83rem;font-weight:600;">
                            {{ $p->nm_dokter ? 'dr. '.$p->nm_dokter : '-' }}
                        </div>
                        @if($p->spesialis)
                        <div style="font-size:.72rem;color:var(--text-muted);">
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
                            <div style="font-size:.68rem;color:var(--text-muted);margin-top:.2rem;">
                                ICD: {{ $p->kd_penyakit }}
                            </div>
                            @endif
                        @else
                            <span style="color:var(--text-muted);font-size:.8rem;">—</span>
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
            <p style="font-weight:600;">Tidak ada pasien rawat inap Anda saat ini</p>
            <p style="font-size:.8rem;margin-top:.35rem;color:var(--text-muted);">
                Pasien yang dirawat atas nama <strong>dr. {{ $nmDokter }}</strong> akan tampil di sini.
            </p>
        @else
            <p style="font-weight:600;">Tidak ada pasien rawat inap saat ini</p>
            <p style="font-size:.8rem;margin-top:.35rem;color:var(--text-muted);">
                Data akan muncul ketika ada pasien yang sedang menjalani rawat inap.
            </p>
        @endif
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
(function () {
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
})();
</script>
@endpush
