@extends('layouts.app')

@section('title', 'Rawat Inap')
@section('page-title', 'Rawat Inap')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
/* ============================================================
   RAWAT INAP — ePasien RS Namira
   Full Doctor Panel with Tabs: SOAP | Diagnosa | Tindakan |
   Resep | Lab/Radiologi | Riwayat
============================================================ */
:root {
    --ranap-primary:    #1e40af;
    --ranap-dark:       #0c1f6e;
    --ranap-accent:     #3b82f6;
    --ranap-border:     #e2e8f0;
    --ranap-success:    #059669;
    --ranap-warning:    #d97706;
    --ranap-danger:     #dc2626;
}

.ranap-page-container { padding: 0 .25rem 2rem .25rem; }

/* ── Page Header ── */
.page-header-banner {
    background: linear-gradient(135deg, #0c1f6e 0%, #1e40af 55%, #3b82f6 100%);
    border-radius: 18px;
    padding: 1.6rem 2.2rem;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 32px -5px rgba(30,64,175,.32);
}
.page-header-banner::before {
    content:''; position:absolute; top:-60px; right:-40px;
    width:250px; height:250px; background:rgba(255,255,255,.05);
    border-radius:50%; pointer-events:none;
}
.page-header-banner::after {
    content:''; position:absolute; bottom:-70px; right:120px;
    width:180px; height:180px; background:rgba(100,160,255,.07);
    border-radius:50%; pointer-events:none;
}
.page-header-banner .content { position:relative; z-index:2; }
.page-header-banner h4 { font-size:1.4rem; font-weight:800; margin:0 0 .3rem; letter-spacing:-.3px; }
.page-header-banner p  { font-size:.865rem; color:rgba(255,255,255,.82); margin:0; }
.header-badge-time {
    background:rgba(255,255,255,.12); backdrop-filter:blur(8px);
    border:1px solid rgba(255,255,255,.2); padding:.5rem 1.1rem;
    border-radius:12px; text-align:right;
}

/* ── Stat Mini Cards ── */
.mini-stat {
    background:#fff; border-radius:16px; padding:1.15rem 1.3rem;
    border:1px solid var(--ranap-border);
    box-shadow:0 4px 14px rgba(0,0,0,.03);
    display:flex; align-items:center; gap:1rem;
    transition:all .25s ease; height:100%;
}
.mini-stat:hover {
    box-shadow:0 10px 24px rgba(0,0,0,.07);
    transform:translateY(-2px);
    border-color:rgba(30,64,175,.3);
}
.mini-stat-icon {
    width:48px; height:48px; border-radius:14px;
    display:flex; align-items:center; justify-content:center;
    font-size:1.3rem; flex-shrink:0;
}
.mini-stat-icon.blue   { background:rgba(30,64,175,.1);  color:#1e40af; }
.mini-stat-icon.green  { background:rgba(5,150,105,.1);  color:#059669; }
.mini-stat-icon.amber  { background:rgba(217,119,6,.1);  color:#d97706; }
.mini-stat-icon.purple { background:rgba(124,58,237,.1); color:#7c3aed; }
.mini-stat-icon.teal   { background:rgba(20,184,166,.1); color:#0d9488; }
.mini-stat-icon.rose   { background:rgba(225,29,72,.1);  color:#e11d48; }
.mini-stat-icon.indigo { background:rgba(99,102,241,.1); color:#4f46e5; }
.mini-stat-icon.cyan   { background:rgba(6,182,212,.1);  color:#0891b2; }
.mini-stat-value  { font-size:1.65rem; font-weight:800; color:#0f172a; line-height:1; }
.mini-stat-label  { font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.45px; margin-top:.35rem; white-space:nowrap; }

/* ── Tab Cards ── */
.tab-card-container {
    background:#fff; border-radius:18px;
    border:1px solid var(--ranap-border);
    overflow:hidden; margin-bottom:1.5rem;
    box-shadow:0 4px 20px rgba(0,0,0,.04);
}
.tab-nav-header {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:.8rem;
    padding:1.1rem 1.4rem;
    background:linear-gradient(180deg,#f8fafc,#fff);
    border-bottom:1px solid var(--ranap-border);
}
.tab-nav-header .nav-pills { gap:.4rem; flex-wrap:wrap; }
.tab-nav-header .nav-pills .nav-link {
    border-radius:12px; font-size:.78rem; font-weight:700;
    padding:.45rem 1rem; color:#475569;
    background:transparent; border:1.5px solid transparent;
    transition:all .2s;
}
.tab-nav-header .nav-pills .nav-link:hover {
    background:#eff6ff; color:#1e40af;
    border-color:rgba(30,64,175,.2);
}
.tab-nav-header .nav-pills .nav-link.active {
    background:var(--ranap-primary); color:#fff;
    border-color:var(--ranap-primary);
    box-shadow:0 4px 12px rgba(30,64,175,.3);
}
.tab-content-body { padding:1.4rem; }

/* ── Table Styling ── */
.table-card { background:#fff; border-radius:16px; border:1px solid var(--ranap-border); box-shadow:0 4px 16px rgba(0,0,0,.03); }
.table-card-header {
    padding:1.15rem 1.4rem; border-bottom:1px solid var(--ranap-border);
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:.8rem;
}
.section-title { font-size:.95rem; font-weight:800; color:#0f172a; margin:0; display:flex; align-items:center; gap:.6rem; }
.section-title i { color:var(--ranap-primary); }

.table-custom { margin:0; font-size:.815rem; width:100% !important; border-collapse:separate; border-spacing:0; }
.table-custom th {
    background:#f8fafc; font-size:.7rem; font-weight:800;
    text-transform:uppercase; letter-spacing:.6px; color:#475569;
    border-top:1px solid #e2e8f0; border-bottom:2px solid #cbd5e1;
    padding:.85rem .95rem; white-space:nowrap; vertical-align:middle;
}
.table-custom td {
    padding:.85rem .95rem; vertical-align:middle;
    border-bottom:1px solid #f1f5f9; color:#0f172a;
    transition:background .15s ease;
}
.table-custom tbody tr:nth-child(even) td { background:#fafcff; }
.table-custom tbody tr:hover td { background:#eff6ff !important; }
.table-custom .patient-cell { display:flex; align-items:flex-start; gap:.75rem; }
.table-custom .col-subtext { font-size:.71rem; color:#64748b; line-height:1.35; }
.table-custom .badge-rm {
    font-size:.67rem; font-weight:700; padding:.2rem .45rem;
    border-radius:6px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;
}
.table-custom .badge-meta-sm {
    font-size:.66rem; font-weight:600; padding:.18rem .45rem;
    border-radius:6px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;
}
.table-custom .diag-masuk-pill {
    font-size:.62rem; font-weight:700; padding:.12rem .38rem;
    border-radius:4px; background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe;
}
.table-custom .diag-akhir-pill {
    font-size:.62rem; font-weight:700; padding:.12rem .38rem;
    border-radius:4px; background:#fae8ff; color:#86198f; border:1px solid #f5d0fe;
}

/* ── Patient Avatar ── */
.patient-avatar {
    width:38px; height:38px; border-radius:50%;
    background:linear-gradient(135deg,#1e40af,#3b82f6);
    display:inline-flex; align-items:center; justify-content:center;
    color:#fff; font-weight:800; font-size:.95rem; flex-shrink:0;
}
.patient-name { font-weight:700; color:#0f172a; font-size:.875rem; }
.patient-meta { font-size:.73rem; color:#64748b; font-weight:500; }

/* ── Badges ── */
.badge-ward {
    background:rgba(30,64,175,.08); color:#1e40af;
    border:1px solid rgba(30,64,175,.2); border-radius:20px;
    font-size:.71rem; font-weight:700; padding:.28rem .75rem; white-space:nowrap;
}
.badge-kelas {
    border-radius:20px; font-size:.7rem; font-weight:700; padding:.28rem .7rem;
}
.badge-kelas.vvip  { background:#fef3c7; color:#92400e; }
.badge-kelas.vip   { background:#fce7f3; color:#9d174d; }
.badge-kelas.k1    { background:#ede9fe; color:#5b21b6; }
.badge-kelas.k2    { background:#dbeafe; color:#1e40af; }
.badge-kelas.k3    { background:#dcfce7; color:#166534; }
.badge-kelas.icu   { background:#fee2e2; color:#991b1b; }
.badge-kelas.other { background:#f1f5f9; color:#475569; }

.badge-status-ranap {
    border-radius:20px; font-size:.7rem; font-weight:700; padding:.28rem .75rem; white-space:nowrap;
}
.badge-status-ranap.aktif   { background:rgba(5,150,105,.1); color:#059669; }
.badge-status-ranap.selesai { background:rgba(100,116,139,.1); color:#475569; }
.badge-status-ranap.soap-ok { background:rgba(99,102,241,.1); color:#4f46e5; }
.badge-status-ranap.soap-no { background:rgba(245,158,11,.1); color:#d97706; }

/* ── Action Button ── */
.btn-detail-ranap {
    background:var(--ranap-primary); color:#fff;
    border-radius:10px; font-size:.77rem; font-weight:700;
    padding:.42rem 1rem; border:none; cursor:pointer;
    transition:all .2s; display:inline-flex; align-items:center; gap:.4rem;
}
.btn-detail-ranap:hover {
    background:var(--ranap-dark); color:#fff;
    box-shadow:0 4px 14px rgba(30,64,175,.3); transform:translateY(-1px);
}

/* ── Lama Rawat indicator ── */
.lama-indicator {
    display:inline-flex; align-items:center; gap:.4rem;
    font-size:.8rem; font-weight:700;
}
.lama-indicator.short { color:#059669; }
.lama-indicator.medium { color:#d97706; }
.lama-indicator.long  { color:#dc2626; }

/* ── Filter Card & Buttons ── */
.filter-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--ranap-border);
    border-top: 3px solid var(--ranap-primary);
    padding: 1.25rem 1.6rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 16px rgba(0,0,0,.03);
}
.filter-card .form-control,
.filter-card .form-select {
    border-radius: 10px;
    border: 1.5px solid #cbd5e1;
    font-size: .84rem;
    font-weight: 600;
    padding: .45rem .85rem;
    height: 38px;
    color: #0f172a;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus {
    border-color: var(--ranap-primary);
    box-shadow: 0 0 0 3.5px rgba(30, 64, 175, 0.12);
}
.btn-filter-submit {
    background: var(--ranap-primary);
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
    background: var(--ranap-dark);
    color: #fff;
    box-shadow: 0 4px 14px rgba(30, 64, 175, 0.28);
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
    background: #eff6ff;
    color: #1e40af;
    border-color: #93c5fd;
}
.btn-quick-date.active {
    background: rgba(30, 64, 175, 0.1) !important;
    color: #1e40af !important;
    border-color: rgba(30, 64, 175, 0.35) !important;
    font-weight: 800 !important;
}

/* ============================================================
   OFFCANVAS PELAYANAN RANAP — 1 LAYAR PENUH (SPLIT LAYOUT)
============================================================ */
.offcanvas-pelayanan {
    width: 100vw !important;
    max-width: 100vw !important;
    height: 100vh !important;
    max-height: 100vh !important;
    top: 0 !important; right: 0 !important; bottom: 0 !important; left: 0 !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    display: flex !important;
    flex-direction: column !important;
    background: #ffffff !important;
    overflow: hidden !important;
}
.offcanvas-pelayanan .offcanvas-header {
    background: linear-gradient(135deg, #0c1f6e, #1e40af);
    color: #fff;
    padding: 0.85rem 1.5rem;
    flex: 0 0 auto !important;
}
.offcanvas-pelayanan .offcanvas-title {
    font-size: 1.15rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: .55rem;
}
.offcanvas-pelayanan .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Split Container: Sidebar Kiri & Area Kerja Kanan */
.emr-split-container {
    display: flex;
    flex-direction: row;
    flex: 1 1 auto;
    height: calc(100vh - 58px);
    overflow: hidden;
}

/* Section Kiri: Sidebar Pasien — Full Height, Flex Column */
.emr-sidebar-left {
    width: 310px;
    flex: 0 0 310px;
    background: #f8fafc;
    border-right: 1.5px solid #cbd5e1;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    padding: 0;
}

/* Patient Card Styles */
.patient-card-header {
    background: linear-gradient(160deg, #0c1f6e 0%, #1e40af 100%);
    padding: 1rem 1rem .85rem;
    color: #fff;
    flex-shrink: 0;
}
.patient-avatar-lg {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(255,255,255,0.2);
    color: #fff;
    font-size: 1.1rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid rgba(255,255,255,0.3);
}
.patient-name-text {
    font-size: .92rem;
    font-weight: 800;
    color: #fff;
    line-height: 1.25;
    word-break: break-word;
}
.patient-badge-status {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.3);
    color: #93c5fd;
    border-radius: 20px;
    padding: .15rem .55rem;
    font-size: .67rem;
    font-weight: 700;
    margin-top: .3rem;
}
.patient-id-strip {
    background: #f1f5f9;
    padding: .55rem 1rem;
    display: flex;
    flex-direction: column;
    gap: .35rem;
    border-bottom: 1.5px solid #e2e8f0;
    flex-shrink: 0;
}
.patient-id-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 20px;
}
.patient-id-label {
    font-size: .67rem;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}
.patient-info-grid {
    background: #fff;
    flex: 1;
    overflow-y: auto;
}
.patient-info-row {
    display: flex;
    align-items: flex-start;
    padding: .44rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    gap: .5rem;
}
.patient-info-row:last-child { border-bottom: none; }
.patient-info-label {
    font-size: .67rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .25px;
    white-space: nowrap;
    min-width: 86px;
    padding-top: .08rem;
}
.patient-info-val {
    font-size: .8rem;
    font-weight: 700;
    color: #1e293b;
    word-break: break-word;
    flex: 1;
    line-height: 1.35;
}

/* Section Kanan: Main Canvas Form & Workspace */
.emr-main-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: #ffffff;
}
.nav-tabs-pelayanan {
    border-bottom: 2.5px solid #e2e8f0;
    background: #ffffff;
    padding: 0 1.25rem;
    gap: .35rem;
    flex: 0 0 auto !important;
}
.nav-tabs-pelayanan .nav-link {
    border: none;
    color: #64748b;
    font-size: .86rem;
    font-weight: 800;
    padding: 0.85rem 1.1rem;
    border-bottom: 3.5px solid transparent;
    transition: all .2s ease;
    display: flex;
    align-items: center;
    gap: .45rem;
    cursor: pointer;
}
.nav-tabs-pelayanan .nav-link:hover {
    color: var(--ranap-primary);
}
.nav-tabs-pelayanan .nav-link.active {
    color: var(--ranap-primary);
    border-bottom-color: var(--ranap-primary);
    background: transparent;
}
.emr-workspace-body {
    overflow-y: auto;
    flex: 1;
    padding: 1.25rem;
    background: #f8fafc;
}

/* Tab Panels */
.ranap-tab-panel { display: none; }
.ranap-tab-panel.active { display: block; }

/* Select2 Fixes & z-index */
.select2-container { z-index: 99999 !important; }
.select2-container--open { z-index: 99999 !important; }
.select2-dropdown {
    z-index: 99999 !important;
    border-radius: 10px !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    border: 1px solid #cbd5e1 !important;
}
.select2-search--dropdown { z-index: 100000 !important; padding: 8px !important; }
.select2-search__field {
    z-index: 100001 !important;
    border-radius: 8px !important;
    border: 1.5px solid #cbd5e1 !important;
    padding: 6px 10px !important;
}
.select2-search__field:focus { border-color: var(--ranap-primary) !important; outline: none !important; }

/* SOAP Form */
.soap-form-card {
    background: #fff; border-radius: 16px;
    border: 1px solid var(--ranap-border);
    padding: 1.3rem; margin-bottom: 1.2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.soap-vitals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px,1fr));
    gap: .75rem; margin-bottom: 1rem;
}
.vital-input-group label {
    font-size: .71rem; font-weight: 700; color: #475569;
    text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: .35rem; display: block;
}
.vital-input-group input, .vital-input-group select {
    width: 100%; border: 1.5px solid #cbd5e1; border-radius: 10px;
    padding: .45rem .75rem; font-size: .84rem; font-weight: 600;
    color: #0f172a; outline: none; transition: all .2s;
}
.vital-input-group input:focus, .vital-input-group select:focus {
    border-color: var(--ranap-primary);
    box-shadow: 0 0 0 3px rgba(30,64,175,.1);
}
.soap-section-label {
    font-size: .72rem; font-weight: 800; color: #475569;
    text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: .4rem;
}
.soap-textarea {
    width: 100%; border: 1.5px solid #cbd5e1; border-radius: 12px;
    padding: .7rem .9rem; font-size: .84rem; color: #0f172a;
    resize: vertical; min-height: 72px; outline: none;
    transition: all .2s; font-family: inherit;
}
.soap-textarea:focus {
    border-color: var(--ranap-primary);
    box-shadow: 0 0 0 3px rgba(30,64,175,.1);
}
.soap-grid-2 {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: .85rem;
}
@media (max-width: 576px) {
    .soap-grid-2 { grid-template-columns: 1fr; }
}

/* Diagnosa List */
.diagnosa-item {
    display: flex; align-items: center; justify-content: space-between;
    background: #eff6ff; border: 1px solid #bfdbfe;
    border-radius: 12px; padding: .7rem 1rem;
    margin-bottom: .6rem; gap: .75rem; flex-wrap: wrap;
}
.diagnosa-item .kd { font-weight: 800; color: #1e40af; font-size: .85rem; }
.diagnosa-item .nm { font-size: .8rem; color: #475569; flex:1; }
.diagnosa-item .prioritas {
    background: #fff; border: 1px solid #bfdbfe;
    border-radius: 20px; font-size: .7rem; font-weight: 700;
    padding: .2rem .65rem; color: #1e40af; white-space: nowrap;
}
.btn-hapus-diagnosa {
    background: none; border: none; color: #dc2626;
    cursor: pointer; padding: .2rem; border-radius: 8px;
    transition: all .2s;
}
.btn-hapus-diagnosa:hover { background: #fee2e2; }

/* Tindakan List */
.tindakan-item {
    display: flex; align-items: center; justify-content: space-between;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: 12px; padding: .7rem 1rem;
    margin-bottom: .6rem; gap: .75rem; flex-wrap: wrap;
}
.tindakan-item .nm { font-weight: 700; color: #0f172a; font-size: .83rem; flex:1; }
.tindakan-item .jenis { font-size: .7rem; font-weight: 700; border-radius: 20px; padding: .2rem .65rem; }
.tindakan-item .jenis.dokter    { background: #dbeafe; color: #1e40af; }
.tindakan-item .jenis.paramedis { background: #ede9fe; color: #5b21b6; }
.tindakan-item .tarif { font-size: .78rem; color: #059669; font-weight: 700; white-space: nowrap; }
.tindakan-item .waktu { font-size: .7rem; color: #64748b; white-space: nowrap; }

/* Resep obat list */
.resep-item {
    background: #fefce8; border: 1px solid #fde68a;
    border-radius: 12px; padding: .85rem 1rem;
    margin-bottom: .6rem;
}
.resep-item .no-resep { font-size: .7rem; font-weight: 800; color: #92400e; }
.resep-item .obat-list { font-size: .8rem; color: #0f172a; margin-top: .35rem; line-height: 1.6; }
.resep-item .waktu { font-size: .7rem; color: #64748b; margin-top: .3rem; }

/* Timeline riwayat */
.timeline-riwayat { position: relative; padding-left: 1.5rem; }
.timeline-riwayat::before {
    content: ''; position: absolute; left: .35rem; top: 0; bottom: 0;
    width: 2px; background: linear-gradient(180deg, #1e40af, #bfdbfe);
}
.timeline-item {
    position: relative; margin-bottom: 1.1rem;
}
.timeline-dot {
    position: absolute; left: -1.25rem; top: .3rem;
    width: 12px; height: 12px; border-radius: 50%;
    background: #1e40af; border: 2px solid #fff;
    box-shadow: 0 0 0 2px #bfdbfe;
}
.timeline-card {
    background: #fff; border: 1px solid var(--ranap-border);
    border-radius: 14px; padding: .9rem 1.1rem;
    box-shadow: 0 2px 6px rgba(0,0,0,.04);
}
.timeline-date {
    font-size: .72rem; font-weight: 800; color: #1e40af;
    text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: .5rem;
}
.timeline-soap-row {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: .6rem; margin-bottom: .5rem;
}
@media (max-width:576px) { .timeline-soap-row { grid-template-columns: 1fr; } }
.soap-field label { font-size: .65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
.soap-field p { font-size: .8rem; color: #0f172a; margin: 0; line-height: 1.5; }

/* Lab / Radiologi */
.lab-result-row {
    display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
    align-items: center; gap: .5rem;
    padding: .55rem .75rem; border-radius: 8px;
    margin-bottom: .4rem; font-size: .8rem;
}
.lab-result-row:nth-child(even) { background: #f8fafc; }
.lab-result-row .pemeriksaan { font-weight: 600; color: #0f172a; }
.lab-result-row .nilai { font-weight: 800; color: #1e40af; }
.lab-result-row .rujukan { color: #64748b; font-size: .75rem; }
.lab-result-row .keterangan { font-size: .72rem; }
.abnormal { color: #dc2626 !important; }

/* Empty state */
.empty-state {
    text-align: center; padding: 3rem 1rem; color: #94a3b8;
}
.empty-state i { font-size: 2.5rem; display: block; margin-bottom: .75rem; }
.empty-state p { font-size: .85rem; font-weight: 600; margin: 0; }

/* Buttons */
.btn-primary-ranap {
    background: var(--ranap-primary); color: #fff;
    border: none; border-radius: 12px;
    font-size: .83rem; font-weight: 700;
    padding: .6rem 1.3rem; cursor: pointer;
    display: inline-flex; align-items: center; gap: .5rem;
    transition: all .2s;
}
.btn-primary-ranap:hover {
    background: var(--ranap-dark); color: #fff;
    box-shadow: 0 4px 14px rgba(30,64,175,.3);
}
.btn-success-ranap {
    background: var(--ranap-success); color: #fff;
    border: none; border-radius: 12px;
    font-size: .83rem; font-weight: 700;
    padding: .6rem 1.3rem; cursor: pointer;
    display: inline-flex; align-items: center; gap: .5rem;
    transition: all .2s;
}
.btn-success-ranap:hover { background: #047857; color: #fff; box-shadow: 0 4px 14px rgba(5,150,105,.3); }
.btn-outline-ranap {
    background: transparent; color: var(--ranap-primary);
    border: 1.5px solid var(--ranap-primary); border-radius: 12px;
    font-size: .83rem; font-weight: 700;
    padding: .6rem 1.3rem; cursor: pointer;
    display: inline-flex; align-items: center; gap: .5rem;
    transition: all .2s;
}
.btn-outline-ranap:hover { background: var(--ranap-primary); color: #fff; }

/* Obat item in resep form */
.obat-cart-item {
    display: grid;
    grid-template-columns: 1fr 80px 1fr auto;
    gap: .6rem; align-items: center;
    background: #fff; border: 1.5px solid #e2e8f0;
    border-radius: 12px; padding: .7rem .9rem;
    margin-bottom: .6rem;
}
@media (max-width: 576px) {
    .obat-cart-item { grid-template-columns: 1fr auto; }
}
.obat-cart-name { font-size: .82rem; font-weight: 700; color: #0f172a; }
.obat-cart-input {
    border: 1.5px solid #cbd5e1; border-radius: 8px;
    padding: .35rem .6rem; font-size: .82rem; font-weight: 600;
    width: 100%; outline: none; transition: all .2s;
}
.obat-cart-input:focus { border-color: var(--ranap-primary); box-shadow: 0 0 0 3px rgba(30,64,175,.1); }
.btn-rm-obat {
    background: #fee2e2; color: #dc2626; border: none;
    border-radius: 8px; width: 30px; height: 30px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .2s; font-size: .85rem; flex-shrink: 0;
}
.btn-rm-obat:hover { background: #dc2626; color: #fff; }

/* Select2 overrides */
.select2-container--bootstrap-5 .select2-selection {
    border-radius: 10px !important;
    border: 1.5px solid #cbd5e1 !important;
    font-size: .84rem !important;
    min-height: 38px !important;
}
.select2-container--bootstrap-5 .select2-selection:focus,
.select2-container--bootstrap-5.select2-container--focus .select2-selection {
    border-color: var(--ranap-primary) !important;
    box-shadow: 0 0 0 3px rgba(30,64,175,.1) !important;
}

/* Loading spinner */
.loading-overlay {
    display: none; position: absolute; inset: 0;
    background: rgba(255,255,255,.85); z-index: 100;
    align-items: center; justify-content: center;
    border-radius: 16px;
}
.loading-overlay.show { display: flex; }

/* ── Responsive overrides ── */
@media (max-width: 992px) {
    .emr-split-container { flex-direction: column !important; height: auto !important; overflow-y: auto !important; }
    .emr-sidebar-left { width: 100% !important; flex: 0 0 auto !important; height: auto !important; border-right: none !important; border-bottom: 2px solid #cbd5e1 !important; }
    .patient-info-grid { max-height: 260px !important; }
}
@media (max-width: 768px) {
    .soap-vitals-grid { grid-template-columns: repeat(2, 1fr); }
    .tab-nav-header { padding: .8rem 1rem; }
    .ranap-tab-panels { padding: 1rem; }
    .obat-cart-item { grid-template-columns: 1fr 1fr; }
    .lab-result-row { grid-template-columns: 2fr 1fr; }
}
@media (max-width: 576px) {
    .patient-card-header { padding: .85rem; }
}

/* DataTables custom */
.dataTables_wrapper { font-size: .83rem; }
.dataTables_wrapper .dataTables_filter label { font-weight: 600; color: #475569; }
.dataTables_wrapper .dataTables_filter input {
    border-radius: 10px !important; border: 1.5px solid #cbd5e1 !important;
    padding: .4rem .85rem !important; font-size: .83rem !important;
}
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: var(--ranap-primary) !important;
    box-shadow: 0 0 0 3px rgba(30,64,175,.12) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px !important; padding: .32rem .78rem !important;
    font-size: .8rem !important; font-weight: 700 !important;
    border: 1px solid #e2e8f0 !important; margin: 0 2px !important;
    background: #fff !important; color: #475569 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--ranap-primary) !important;
    border-color: var(--ranap-primary) !important;
    color: #fff !important;
}

/* Toast notification */
#ranap-toast-container {
    position: fixed; bottom: 1.5rem; right: 1.5rem;
    z-index: 9999; display: flex; flex-direction: column; gap: .5rem;
}
.ranap-toast {
    background: #1e293b; color: #fff;
    border-radius: 14px; padding: .85rem 1.25rem;
    font-size: .83rem; font-weight: 600; min-width: 280px;
    display: flex; align-items: center; gap: .75rem;
    box-shadow: 0 8px 24px rgba(0,0,0,.25);
    animation: slideInRight .3s ease;
}
.ranap-toast.success { border-left: 4px solid #059669; }
.ranap-toast.error   { border-left: 4px solid #dc2626; }
.ranap-toast.warning { border-left: 4px solid #d97706; }
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to   { transform: translateX(0);   opacity: 1; }
}
</style>
@endpush

@section('content')
<div class="ranap-page-container">

{{-- ═══════════════════════════════════════════
     PAGE HEADER
═══════════════════════════════════════════ --}}
<div class="page-header-banner">
    <div class="content d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-hospital me-2"></i>Rawat Inap</h4>
            <p>
                @if($isDokter)
                    Rekam medis pasien rawat inap dr. <strong>{{ $nmDokter }}</strong>
                @else
                    Semua pasien rawat inap RS Namira
                @endif
            </p>
        </div>
        <div class="header-badge-time text-end d-none d-md-block">
            <div style="font-size:.7rem; opacity:.75; text-transform:uppercase; letter-spacing:.5px;">Tanggal</div>
            <div style="font-size:.95rem; font-weight:800;" id="live-date">{{ now()->locale('id')->isoFormat('ddd, D MMMM Y') }}</div>
            <div style="font-size:.8rem; font-weight:700; opacity:.9;" id="live-time"></div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     FILTER PASIEN RAWAT INAP (2 BARIS LENGKAP SEPERTI RAWAT JALAN)
═══════════════════════════════════════════ --}}
<div class="filter-card">
    <form method="GET" action="{{ route('rawat-inap') }}" class="m-0">
        {{-- BARIS 1: PERIODE, BANGSAL, PENJAMIN, SEARCH & ACTION BUTTONS --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-3" style="border-bottom: 1px dashed #e2e8f0;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar-range-fill text-primary fs-5"></i>
                <span style="font-size:.9rem;font-weight:800;color:#1e293b;">Filter Pasien Ranap:</span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Range Tanggal --}}
                <div class="d-flex align-items-center gap-1">
                    <input type="date" name="tgl_awal" value="{{ $tglAwal }}" class="form-control" style="width: 145px;" title="Tanggal Awal">
                    <span style="font-size:.82rem;font-weight:700;color:#64748b;">s.d.</span>
                    <input type="date" name="tgl_akhir" value="{{ $tglAkhir }}" class="form-control" style="width: 145px;" title="Tanggal Akhir">
                </div>

                {{-- Dropdown Bangsal --}}
                <div style="min-width: 160px;">
                    <select name="kd_bangsal" class="form-select" title="Pilih Bangsal">
                        <option value="">Semua Bangsal</option>
                        @foreach($listBangsal as $b)
                            <option value="{{ $b->kd_bangsal }}" {{ ($kdBangsal == $b->kd_bangsal) ? 'selected' : '' }}>
                                {{ $b->nm_bangsal }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropdown Penjamin --}}
                <div style="min-width: 160px;">
                    <select name="kd_pj" class="form-select" title="Pilih Penjamin">
                        <option value="">Semua Penjamin</option>
                        @foreach($listPenjab as $pj)
                            <option value="{{ $pj->kd_pj }}" {{ ($kdPj == $pj->kd_pj) ? 'selected' : '' }}>
                                {{ $pj->png_jawab }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian --}}
                <div style="min-width: 170px;">
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Nama / RM / No Rawat...">
                </div>

                <button type="submit" class="btn-filter-submit">
                    <i class="bi bi-funnel-fill"></i> Terapkan Filter
                </button>
                @if($tglAwal != $startMonth || $tglAkhir != $todayDate || !empty($kdBangsal) || !empty($kdPj) || !empty($q))
                    <a href="{{ route('rawat-inap') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1" style="height:38px; border-radius:10px; font-size:.82rem; font-weight:700;">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                @endif
            </div>
        </div>

        {{-- BARIS 2: PILIHAN CEPAT (PRESETS) --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-muted fs-6"></i>
                <span style="font-size:.78rem;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.3px;">Pilihan Cepat Periode:</span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('rawat-inap', array_merge(request()->except(['tgl_awal', 'tgl_akhir']), ['tgl_awal' => $todayDate, 'tgl_akhir' => $todayDate])) }}"
                   class="btn-quick-date {{ ($tglAwal === $todayDate && $tglAkhir === $todayDate) ? 'active' : '' }}">
                    <i class="bi bi-calendar-day me-1"></i> Hari Ini
                </a>
                <a href="{{ route('rawat-inap', array_merge(request()->except(['tgl_awal', 'tgl_akhir']), ['tgl_awal' => $yesterday, 'tgl_akhir' => $yesterday])) }}"
                   class="btn-quick-date {{ ($tglAwal === $yesterday && $tglAkhir === $yesterday) ? 'active' : '' }}">
                    <i class="bi bi-calendar-minus me-1"></i> Kemarin
                </a>
                <a href="{{ route('rawat-inap', array_merge(request()->except(['tgl_awal', 'tgl_akhir']), ['tgl_awal' => $last7Days, 'tgl_akhir' => $todayDate])) }}"
                   class="btn-quick-date {{ ($tglAwal === $last7Days && $tglAkhir === $todayDate) ? 'active' : '' }}">
                    <i class="bi bi-calendar-week me-1"></i> 7 Hari Terakhir
                </a>
                <a href="{{ route('rawat-inap', array_merge(request()->except(['tgl_awal', 'tgl_akhir']), ['tgl_awal' => $startMonth, 'tgl_akhir' => $endMonth])) }}"
                   class="btn-quick-date {{ ($tglAwal === $startMonth && $tglAkhir === $endMonth) ? 'active' : '' }}">
                    <i class="bi bi-calendar-month me-1"></i> Bulan Ini
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ═══════════════════════════════════════════
     STAT CARDS
═══════════════════════════════════════════ --}}
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
            <div class="mini-stat-icon green"><i class="bi bi-clipboard2-pulse-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['soap_hari_ini'] }}</div>
                <div class="mini-stat-label">SOAP Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon amber"><i class="bi bi-door-open-fill"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['total_selesai'] }}</div>
                <div class="mini-stat-label">Sudah Pulang</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat">
            <div class="mini-stat-icon purple"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="mini-stat-value">{{ $statsRanap['rata_lama_inap'] }}<span style="font-size:.9rem"> hr</span></div>
                <div class="mini-stat-label">Rata-rata LOS</div>
            </div>
        </div>
    </div>
</div>

{{-- Kelas mini stats ──────────────────── --}}
<div class="row g-2 mb-4">
    @php
        $kelasData = [
            ['label'=>'VVIP',   'val'=>$statsRanap['kelas_vvip'],  'cls'=>'amber'],
            ['label'=>'VIP',    'val'=>$statsRanap['kelas_vip'],   'cls'=>'rose'],
            ['label'=>'Kelas I','val'=>$statsRanap['kelas_i'],     'cls'=>'purple'],
            ['label'=>'Kelas II','val'=>$statsRanap['kelas_ii'],   'cls'=>'indigo'],
            ['label'=>'Kelas III','val'=>$statsRanap['kelas_iii'], 'cls'=>'teal'],
            ['label'=>'Lainnya','val'=>$statsRanap['tanpa_kelas'], 'cls'=>'cyan'],
        ];
    @endphp
    @foreach($kelasData as $k)
    <div class="col-4 col-md-2">
        <div class="mini-stat" style="padding:.8rem; gap:.7rem;">
            <div class="mini-stat-icon {{ $k['cls'] }}" style="width:36px;height:36px;border-radius:10px;font-size:1rem;">
                <i class="bi bi-bed-fill"></i>
            </div>
            <div>
                <div class="mini-stat-value" style="font-size:1.3rem;">{{ $k['val'] }}</div>
                <div class="mini-stat-label" style="font-size:.65rem;">{{ $k['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════
     TAB CARD — Aktif vs Selesai
═══════════════════════════════════════════ --}}
<div class="tab-card-container">
    <div class="tab-nav-header">
        <ul class="nav nav-pills" id="ranapMainTab">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabAktif">
                    <i class="bi bi-hospital-fill me-1"></i>
                    Sedang Dirawat
                    <span class="badge bg-primary ms-1 rounded-pill" style="font-size:.68rem;">{{ count($pasienRanap) }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabSelesai">
                    <i class="bi bi-door-open-fill me-1"></i>
                    Sudah Pulang
                    <span class="badge bg-secondary ms-1 rounded-pill" style="font-size:.68rem;">{{ count($pasienRanapSelesai) }}</span>
                </button>
            </li>
        </ul>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted" style="font-size:.78rem; font-weight:600;">
                <i class="bi bi-info-circle me-1"></i>
                Klik <strong>EMR</strong> untuk input rekam medis
            </span>
            <button class="btn-primary-ranap" onclick="location.reload()" style="padding:.45rem .9rem; border-radius:10px; font-size:.76rem;">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>

    <div class="tab-content">
        {{-- ── Tab Aktif ── --}}
        <div class="tab-pane fade show active" id="tabAktif">
            <div style="padding:1rem 1.2rem 1.4rem;">
                @if(count($pasienRanap) > 0)
                <div class="table-responsive">
                    <table class="table table-custom" id="tblRanapAktif" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:38px; text-align:center;">#</th>
                                <th style="min-width:220px;">Pasien</th>
                                <th style="min-width:145px;">Ruang & Kamar</th>
                                <th style="min-width:175px;">DPJP & Penjamin</th>
                                <th style="min-width:175px;">Diagnosa</th>
                                <th style="min-width:175px;">Penanggung Jawab</th>
                                <th style="width:115px; text-align:center;">Tgl Masuk & LOS</th>
                                <th style="width:110px; text-align:center;">Status SOAP</th>
                                <th style="width:105px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pasienRanap as $i => $p)
                            @php
                                $umur  = \Carbon\Carbon::parse($p->tgl_lahir)->diffInYears(now());
                                $lama  = $p->lama ?? \Carbon\Carbon::parse($p->tgl_masuk)->diffInDays(now());
                                $lamaClass = $lama <= 3 ? 'short' : ($lama <= 7 ? 'medium' : 'long');
                                $initials  = strtoupper(substr($p->nm_pasien ?? 'P', 0, 1)) . (strpos($p->nm_pasien, ' ') !== false ? strtoupper(substr(strstr($p->nm_pasien,' '), 1, 1)) : '');
                                $kelasRaw  = strtolower($p->kelas ?? '');
                                $kelasCls  = str_contains($kelasRaw,'vvip') ? 'vvip' : (str_contains($kelasRaw,'vip') ? 'vip' : (str_contains($kelasRaw,'icu') ? 'icu' : (str_contains($kelasRaw,'1') || str_contains($kelasRaw,'i ') ? 'k1' : (str_contains($kelasRaw,'2') || str_contains($kelasRaw,'ii') ? 'k2' : (str_contains($kelasRaw,'3') || str_contains($kelasRaw,'iii') ? 'k3' : 'other')))));
                                $noRawatB64 = base64_encode($p->no_rawat);
                                $fullAlamat = $p->alamat_lengkap ?? $p->alamat;
                            @endphp
                            <tr>
                                <td style="color:#94a3b8; font-size:.75rem; font-weight:700; text-align:center;">{{ $i+1 }}</td>

                                {{-- Col 2: Pasien --}}
                                <td>
                                    <div class="patient-cell">
                                        <div class="patient-avatar" style="width:38px;height:38px;font-size:.85rem;">{{ $initials }}</div>
                                        <div>
                                            <div class="patient-name" style="font-size:.875rem;">{{ $p->nm_pasien }}</div>
                                            <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                                <span class="badge-rm">RM: {{ $p->no_rkm_medis }}</span>
                                                <span class="badge-meta-sm">{{ $p->jk == 'L' ? 'L' : 'P' }} &bull; {{ $p->umur_daftar ?? $umur.' th' }}</span>
                                                @if($p->gol_darah && $p->gol_darah !== '-')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:.65rem; font-weight:700; padding:.18rem .4rem;">Gol: {{ $p->gol_darah }}</span>
                                                @endif
                                            </div>
                                            <div class="col-subtext font-monospace text-muted mt-1" style="font-size:.68rem;">
                                                <i class="bi bi-hash"></i>{{ $p->no_rawat }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Col 3: Ruang & Kamar --}}
                                <td>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge-ward"><i class="bi bi-door-closed me-1"></i>{{ $p->kd_kamar ?? '-' }}</span>
                                        @if($p->kelas)
                                        <span class="badge-kelas {{ $kelasCls }}">{{ $p->kelas }}</span>
                                        @endif
                                    </div>
                                    <div style="font-size:.73rem; font-weight:600; color:#475569; margin-top:3px;">
                                        <i class="bi bi-building me-1 text-muted"></i>{{ $p->nm_bangsal ?? '-' }}
                                    </div>
                                    @if($p->trf_kamar)
                                    <div style="font-size:.69rem; color:#059669; font-weight:700; margin-top:2px;">
                                        <i class="bi bi-cash me-1"></i>Rp {{ number_format($p->trf_kamar,0,',','.') }}<span style="font-weight:400; color:#64748b;">/hr</span>
                                    </div>
                                    @endif
                                </td>

                                {{-- Col 4: DPJP & Penjamin --}}
                                <td>
                                    <div style="font-size:.82rem; font-weight:700; color:#0f172a;">
                                        <i class="bi bi-person-badge text-primary me-1"></i>{{ Str::limit($p->nm_dokter ?? '-', 24) }}
                                    </div>
                                    @if($p->spesialis)
                                    <div style="font-size:.69rem; color:#7c3aed; font-weight:600; margin-left:1.1rem;">{{ $p->spesialis }}</div>
                                    @endif
                                    <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                        <span class="badge bg-light text-dark border" style="font-size:.68rem; font-weight:700;">
                                            <i class="bi bi-shield-check text-success me-1"></i>{{ $p->jenis_bayar ?? '-' }}
                                        </span>
                                        @if($p->status_bayar)
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size:.65rem;">{{ $p->status_bayar }}</span>
                                        @endif
                                    </div>
                                    @if($p->no_sep)
                                    <div class="mt-1" style="font-size:.68rem; font-family:monospace; color:#059669; font-weight:700;">
                                        <i class="bi bi-card-checklist me-1"></i>SEP: {{ $p->no_sep }}
                                    </div>
                                    @endif
                                </td>

                                {{-- Col 5: Diagnosa --}}
                                <td>
                                    @if($p->diagnosa_awal)
                                    <div style="font-size:.74rem; color:#1e3a8a; line-height:1.3; margin-bottom:4px;">
                                        <span class="diag-masuk-pill">Masuk</span> {{ Str::limit($p->diagnosa_awal, 42) }}
                                    </div>
                                    @endif
                                    @if($p->kd_penyakit)
                                    <div style="line-height:1.3;">
                                        <span class="badge bg-primary text-white" style="font-size:.65rem; font-weight:800;">{{ $p->kd_penyakit }}</span>
                                        <span style="font-size:.73rem; color:#334155; font-weight:600;">{{ Str::limit($p->nm_penyakit ?? '-', 35) }}</span>
                                    </div>
                                    @elseif($p->diagnosa_akhir)
                                    <div style="font-size:.73rem; color:#334155; line-height:1.3;">
                                        <span class="diag-akhir-pill">Akhir</span> {{ Str::limit($p->diagnosa_akhir, 42) }}
                                    </div>
                                    @elseif(!$p->diagnosa_awal)
                                    <span style="color:#94a3b8; font-size:.75rem; font-style:italic;">Belum ada diagnosa</span>
                                    @endif
                                </td>

                                {{-- Col 6: Penanggung Jawab & Alamat --}}
                                <td>
                                    @if($p->p_jawab)
                                    <div style="font-size:.78rem; font-weight:700; color:#0f172a;">
                                        <i class="bi bi-people-fill text-primary me-1"></i>{{ Str::limit($p->p_jawab, 20) }}
                                        @if($p->hubunganpj)
                                        <span class="badge bg-light text-muted border" style="font-size:.65rem; font-weight:600;">{{ $p->hubunganpj }}</span>
                                        @endif
                                    </div>
                                    @else
                                    <div style="font-size:.75rem; color:#94a3b8;"><i class="bi bi-people me-1"></i>—</div>
                                    @endif
                                    @if($fullAlamat)
                                    <div class="col-subtext mt-1" style="max-width:185px;" title="{{ $fullAlamat }}">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ Str::limit($fullAlamat, 40) }}
                                    </div>
                                    @endif
                                    @if($p->agama)
                                    <div style="font-size:.67rem; color:#94a3b8; margin-top:2px;">
                                        <i class="bi bi-dot"></i>{{ $p->agama }}
                                    </div>
                                    @endif
                                </td>

                                {{-- Col 7: Tgl Masuk & LOS --}}
                                <td style="text-align:center;">
                                    <div style="font-size:.78rem; font-weight:800; color:#0f172a;">
                                        {{ $p->tgl_masuk ? \Carbon\Carbon::parse($p->tgl_masuk)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div style="font-size:.69rem; color:#64748b; font-weight:600;">
                                        {{ $p->jam_masuk ? substr($p->jam_masuk,0,5).' WIB' : '' }}
                                    </div>
                                    <div class="lama-indicator {{ $lamaClass }} mt-1 justify-content-center" style="font-size:.72rem;">
                                        <i class="bi bi-clock-history"></i><span>{{ $lama }} Hari</span>
                                    </div>
                                </td>

                                {{-- Col 8: Status SOAP --}}
                                <td style="text-align:center;">
                                    @if($p->status_soap_hari_ini === 'terlayani')
                                    <span class="badge-status-ranap soap-ok d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-check2-circle"></i> Sudah SOAP
                                    </span>
                                    @else
                                    <span class="badge-status-ranap soap-no d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-hourglass-split"></i> Belum SOAP
                                    </span>
                                    @endif
                                </td>

                                {{-- Col 9: Aksi --}}
                                <td style="text-align:center;">
                                    <button type="button" class="btn-detail-ranap"
                                        data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvasRanap"
                                        data-norawat-b64="{{ $noRawatB64 }}"
                                        data-nama="{{ addslashes($p->nm_pasien) }}"
                                        onclick="bukaDetailRanap('{{ $noRawatB64 }}', '{{ addslashes($p->nm_pasien) }}')"
                                        title="Buka Rekam Medis Pasien">
                                        <i class="bi bi-folder2-open"></i> EMR
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="bi bi-hospital"></i>
                    <p>Tidak ada pasien rawat inap yang sedang dirawat</p>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Tab Selesai ── --}}
        <div class="tab-pane fade" id="tabSelesai">
            <div style="padding:1rem 1.2rem 1.4rem;">
                @if(count($pasienRanapSelesai) > 0)
                <div class="table-responsive">
                    <table class="table table-custom" id="tblRanapSelesai" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:38px; text-align:center;">#</th>
                                <th style="min-width:220px;">Pasien</th>
                                <th style="min-width:145px;">Ruang & Kamar</th>
                                <th style="min-width:175px;">DPJP & Penjamin</th>
                                <th style="min-width:175px;">Diagnosa</th>
                                <th style="min-width:175px;">Penanggung Jawab</th>
                                <th style="width:125px; text-align:center;">Periode Rawat & LOS</th>
                                <th style="width:120px; text-align:center;">Biaya & Status</th>
                                <th style="width:105px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pasienRanapSelesai as $i => $p)
                            @php
                                $umur  = \Carbon\Carbon::parse($p->tgl_lahir)->diffInYears(now());
                                $lama  = $p->lama ?? 0;
                                $lamaClass = $lama <= 3 ? 'short' : ($lama <= 7 ? 'medium' : 'long');
                                $initials  = strtoupper(substr($p->nm_pasien ?? 'P', 0, 1)) . (strpos($p->nm_pasien, ' ') !== false ? strtoupper(substr(strstr($p->nm_pasien,' '), 1, 1)) : '');
                                $noRawatB64 = base64_encode($p->no_rawat);
                                $fullAlamat = $p->alamat_lengkap ?? $p->alamat;
                            @endphp
                            <tr>
                                <td style="color:#94a3b8; font-size:.75rem; font-weight:700; text-align:center;">{{ $i+1 }}</td>

                                {{-- Col 2: Pasien --}}
                                <td>
                                    <div class="patient-cell">
                                        <div class="patient-avatar" style="width:38px;height:38px;font-size:.85rem;background:linear-gradient(135deg,#64748b,#94a3b8);">{{ $initials }}</div>
                                        <div>
                                            <div class="patient-name" style="font-size:.875rem;">{{ $p->nm_pasien }}</div>
                                            <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                                <span class="badge-rm">RM: {{ $p->no_rkm_medis }}</span>
                                                <span class="badge-meta-sm">{{ $p->jk == 'L' ? 'L' : 'P' }} &bull; {{ $p->umur_daftar ?? $umur.' th' }}</span>
                                                @if($p->gol_darah && $p->gol_darah !== '-')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:.65rem; font-weight:700; padding:.18rem .4rem;">Gol: {{ $p->gol_darah }}</span>
                                                @endif
                                            </div>
                                            <div class="col-subtext font-monospace text-muted mt-1" style="font-size:.68rem;">
                                                <i class="bi bi-hash"></i>{{ $p->no_rawat }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Col 3: Ruang & Kamar --}}
                                <td>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge-ward"><i class="bi bi-door-closed me-1"></i>{{ $p->kd_kamar ?? '-' }}</span>
                                        @if($p->kelas)
                                        <span class="badge-kelas other">{{ $p->kelas }}</span>
                                        @endif
                                    </div>
                                    <div style="font-size:.73rem; font-weight:600; color:#475569; margin-top:3px;">
                                        <i class="bi bi-building me-1 text-muted"></i>{{ $p->nm_bangsal ?? '-' }}
                                    </div>
                                </td>

                                {{-- Col 4: DPJP & Penjamin --}}
                                <td>
                                    <div style="font-size:.82rem; font-weight:700; color:#0f172a;">
                                        <i class="bi bi-person-badge text-primary me-1"></i>{{ Str::limit($p->nm_dokter ?? '-', 24) }}
                                    </div>
                                    @if($p->spesialis)
                                    <div style="font-size:.69rem; color:#7c3aed; font-weight:600; margin-left:1.1rem;">{{ $p->spesialis }}</div>
                                    @endif
                                    <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                        <span class="badge bg-light text-dark border" style="font-size:.68rem; font-weight:700;">
                                            <i class="bi bi-shield-check text-success me-1"></i>{{ $p->jenis_bayar ?? '-' }}
                                        </span>
                                        @if($p->status_bayar)
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size:.65rem;">{{ $p->status_bayar }}</span>
                                        @endif
                                    </div>
                                    @if($p->no_sep)
                                    <div class="mt-1" style="font-size:.68rem; font-family:monospace; color:#059669; font-weight:700;">
                                        <i class="bi bi-card-checklist me-1"></i>SEP: {{ $p->no_sep }}
                                    </div>
                                    @endif
                                </td>

                                {{-- Col 5: Diagnosa --}}
                                <td>
                                    @if($p->diagnosa_awal)
                                    <div style="font-size:.74rem; color:#1e3a8a; line-height:1.3; margin-bottom:4px;">
                                        <span class="diag-masuk-pill">Masuk</span> {{ Str::limit($p->diagnosa_awal, 42) }}
                                    </div>
                                    @endif
                                    @if($p->kd_penyakit)
                                    <div style="line-height:1.3;">
                                        <span class="badge bg-primary text-white" style="font-size:.65rem; font-weight:800;">{{ $p->kd_penyakit }}</span>
                                        <span style="font-size:.73rem; color:#334155; font-weight:600;">{{ Str::limit($p->nm_penyakit ?? '-', 35) }}</span>
                                    </div>
                                    @elseif($p->diagnosa_akhir)
                                    <div style="font-size:.73rem; color:#334155; line-height:1.3;">
                                        <span class="diag-akhir-pill">Akhir</span> {{ Str::limit($p->diagnosa_akhir, 42) }}
                                    </div>
                                    @elseif(!$p->diagnosa_awal)
                                    <span style="color:#94a3b8; font-size:.75rem; font-style:italic;">Belum ada diagnosa</span>
                                    @endif
                                </td>

                                {{-- Col 6: Penanggung Jawab & Alamat --}}
                                <td>
                                    @if($p->p_jawab)
                                    <div style="font-size:.78rem; font-weight:700; color:#0f172a;">
                                        <i class="bi bi-people-fill text-muted me-1"></i>{{ Str::limit($p->p_jawab, 20) }}
                                        @if($p->hubunganpj)
                                        <span class="badge bg-light text-muted border" style="font-size:.65rem; font-weight:600;">{{ $p->hubunganpj }}</span>
                                        @endif
                                    </div>
                                    @else
                                    <div style="font-size:.75rem; color:#94a3b8;"><i class="bi bi-people me-1"></i>—</div>
                                    @endif
                                    @if($fullAlamat)
                                    <div class="col-subtext mt-1" style="max-width:185px;" title="{{ $fullAlamat }}">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ Str::limit($fullAlamat, 40) }}
                                    </div>
                                    @endif
                                    @if($p->agama)
                                    <div style="font-size:.67rem; color:#94a3b8; margin-top:2px;">
                                        <i class="bi bi-dot"></i>{{ $p->agama }}
                                    </div>
                                    @endif
                                </td>

                                {{-- Col 7: Periode Rawat & LOS --}}
                                <td style="text-align:center;">
                                    <div style="font-size:.75rem; font-weight:700; color:#059669;">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>{{ $p->tgl_masuk ? \Carbon\Carbon::parse($p->tgl_masuk)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div style="font-size:.75rem; font-weight:700; color:#dc2626; margin-top:2px;">
                                        <i class="bi bi-box-arrow-right me-1"></i>{{ $p->tgl_keluar ? \Carbon\Carbon::parse($p->tgl_keluar)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="lama-indicator {{ $lamaClass }} mt-1 justify-content-center" style="font-size:.72rem;">
                                        <i class="bi bi-clock-history"></i><span>{{ $lama }} Hari</span>
                                    </div>
                                </td>

                                {{-- Col 8: Biaya & Status Pulang --}}
                                <td style="text-align:center;">
                                    @if($p->ttl_biaya)
                                    <div style="font-size:.82rem; font-weight:800; color:#059669;">
                                        Rp {{ number_format($p->ttl_biaya,0,',','.') }}
                                    </div>
                                    @else
                                    <span style="color:#94a3b8; font-size:.75rem;">—</span>
                                    @endif
                                    <span class="badge bg-light text-secondary border mt-1" style="font-size:.7rem; font-weight:700;">
                                        {{ $p->stts_pulang ?? 'Pulang' }}
                                    </span>
                                </td>

                                {{-- Col 9: Aksi --}}
                                <td style="text-align:center;">
                                    <button type="button" class="btn-detail-ranap" style="background:#475569;"
                                        data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvasRanap"
                                        data-norawat-b64="{{ $noRawatB64 }}"
                                        data-nama="{{ addslashes($p->nm_pasien) }}"
                                        onclick="bukaDetailRanap('{{ $noRawatB64 }}', '{{ addslashes($p->nm_pasien) }}')"
                                        title="Lihat Rekam Medis">
                                        <i class="bi bi-eye"></i> Rekam Medis
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="bi bi-door-open"></i>
                    <p>Belum ada pasien yang sudah pulang</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

</div>{{-- end ranap-page-container --}}

{{-- ═══════════════════════════════════════════
     OFFCANVAS — DETAIL REKAM MEDIS RANAP
═══════════════════════════════════════════ --}}
<div class="offcanvas offcanvas-end offcanvas-pelayanan" tabindex="-1" id="offcanvasRanap"
     data-bs-scroll="true" data-bs-backdrop="true">

    {{-- Header --}}
    <div class="offcanvas-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <h5 class="offcanvas-title mb-0">
                <i class="bi bi-hospital text-white me-1"></i> SIMRS EMR — Lembar Pelayanan Rawat Inap
            </h5>
            <span class="badge bg-white text-primary fw-bold ms-2 px-2.5 py-1" id="oc-no-rawat-title" style="font-size:.78rem;"></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    {{-- Loading state --}}
    <div id="oc-loading" class="d-none align-items-center justify-content-center flex-column" style="height: calc(100vh - 60px);">
        <div class="text-center text-muted">
            <div class="spinner-border text-primary mb-3" style="width:3rem; height:3rem;"></div>
            <div style="font-size:1rem; font-weight:800; color:#1e40af;">Memuat Data Pasien Rekam Medis...</div>
            <div style="font-size:.8rem; color:#64748b; margin-top:.3rem;">Mengambil data CPPT, diagnosa, tindakan, obat, dan riwayat</div>
        </div>
    </div>

    {{-- CONTAINER SPLIT 2 SECTION (KIRI: PASIEN SIDEBAR, KANAN: WORKSPACE TABS) --}}
    <div id="oc-content" class="emr-split-container d-none">
        
        {{-- SECTION KIRI: SIDEBAR DATA PASIEN COMPACT & ESTETIK (Width 310px) --}}
        <div class="emr-sidebar-left">
            {{-- Header: Avatar + Nama + Status --}}
            <div class="patient-card-header">
                <div class="d-flex align-items-start gap-2">
                    <div class="patient-avatar-lg me-2" id="oc-avatar">P</div>
                    <div style="min-width:0;flex:1;">
                        <div class="patient-name-text" id="oc-nm-pasien">Loading Pasien...</div>
                        <div class="patient-badge-status" id="oc-badge-status-pasien">
                            <i class="bi bi-hospital"></i> Sedang Dirawat
                        </div>
                    </div>
                </div>
            </div>

            {{-- ID Strip: No Rawat, No RM, Kamar & Kelas, Penjamin --}}
            <div class="patient-id-strip">
                <div class="patient-id-row">
                    <span class="patient-id-label">No. Rawat</span>
                    <span class="badge-rm" id="oc-no-rawat-val" style="font-size:.72rem;padding:.18rem .55rem;">—</span>
                </div>
                <div class="patient-id-row">
                    <span class="patient-id-label">No. RM</span>
                    <span class="badge-rm" id="oc-no-rkm-val" style="font-size:.72rem;padding:.18rem .55rem;">—</span>
                </div>
                <div class="patient-id-row">
                    <span class="patient-id-label">Kamar & Kelas</span>
                    <span class="badge-ward" id="oc-kamar-kelas-val" style="font-size:.72rem;padding:.18rem .55rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">—</span>
                </div>
                <div class="patient-id-row">
                    <span class="patient-id-label">Penjamin</span>
                    <span class="badge-status-ranap aktif" id="oc-bayar-val" style="font-size:.72rem;padding:.18rem .55rem;">—</span>
                </div>
            </div>

            {{-- Demografi & Info Detail Pasien Ranap (Scrollable) --}}
            <div class="patient-info-grid">
                <div class="patient-info-row">
                    <span class="patient-info-label">Kelamin</span>
                    <span id="oc-jk-val" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Lahir / Umur</span>
                    <span id="oc-umur-val" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Tgl Masuk</span>
                    <span id="oc-masuk-val" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Lama Rawat (LOS)</span>
                    <span id="oc-los-val" class="patient-info-val text-primary fw-bold">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Bangsal</span>
                    <span id="oc-bangsal-val" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Tarif Kamar</span>
                    <span id="oc-trf-kamar-val" class="patient-info-val text-success fw-bold">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Dokter DPJP</span>
                    <span id="oc-dpjp-val" class="patient-info-val text-primary fw-bold">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">No. Telepon</span>
                    <span id="oc-telp-val" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">PJ & Hubungan</span>
                    <span id="oc-pj-val" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row" id="oc-sep-row" style="display:none;">
                    <span class="patient-info-label">No. SEP BPJS</span>
                    <span id="oc-sep-val" class="patient-info-val text-success font-monospace fw-bold">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Gol. Darah</span>
                    <span id="oc-darah-val" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Agama & Kerja</span>
                    <span id="oc-agama-val" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Diagnosa Masuk</span>
                    <span id="oc-diag-awal-val" class="patient-info-val text-dark fw-bold">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Diagnosa Akhir</span>
                    <span id="oc-diag-akhir-val" class="patient-info-val text-indigo fw-bold">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Alamat Lengkap</span>
                    <span id="oc-alamat-val" class="patient-info-val" style="font-size:.75rem; line-height:1.35;">—</span>
                </div>
            </div>
        </div>

        {{-- SECTION KANAN: WORKSPACE TABS & INPUT --}}
        <div class="emr-main-right">
            {{-- Nav Tabs --}}
            <ul class="nav nav-tabs nav-tabs-pelayanan" id="ranapNavTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-soap-btn" type="button" onclick="switchTab('soap')">
                        <i class="bi bi-clipboard2-pulse"></i> 1. SOAP CPPT <span class="badge bg-primary ms-1" id="badge-soap">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-diagnosa-btn" type="button" onclick="switchTab('diagnosa')">
                        <i class="bi bi-search-heart"></i> 2. Diagnosa ICD-10 <span class="badge bg-secondary ms-1" id="badge-diagnosa">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-tindakan-btn" type="button" onclick="switchTab('tindakan')">
                        <i class="bi bi-activity"></i> 3. Tindakan Ranap <span class="badge bg-secondary ms-1" id="badge-tindakan">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-resep-btn" type="button" onclick="switchTab('resep')">
                        <i class="bi bi-capsule"></i> 4. Resep Obat <span class="badge bg-secondary ms-1" id="badge-resep">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-lab-btn" type="button" onclick="switchTab('lab')">
                        <i class="bi bi-eyedropper"></i> 5. Laboratorium <span class="badge bg-secondary ms-1" id="badge-lab">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-rad-btn" type="button" onclick="switchTab('radiologi')">
                        <i class="bi bi-radioactive"></i> 6. Radiologi <span class="badge bg-secondary ms-1" id="badge-rad">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-operasi-btn" type="button" onclick="switchTab('operasi')">
                        <i class="bi bi-calendar-event-fill"></i> 7. Operasi / Bedah <span class="badge bg-secondary ms-1" id="badge-operasi">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-riwayat-btn" type="button" onclick="switchTab('riwayat')">
                        <i class="bi bi-clock-history"></i> 8. Riwayat Lengkap
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-resume-btn" type="button" onclick="switchTab('resume')">
                        <i class="bi bi-file-earmark-medical"></i> 9. Resume Ranap <span class="badge bg-secondary ms-1" id="badge-resume">0</span>
                    </button>
                </li>
            </ul>

            {{-- Workspace Body --}}
            <div class="emr-workspace-body">
                
                {{-- ── Tab 1: SOAP CPPT ── --}}
                <div class="ranap-tab-panel active" id="panel-soap">
                    <div class="soap-form-card">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <h6 class="mb-0" style="font-size:.92rem; font-weight:800; color:#0f172a;">
                                <i class="bi bi-pencil-square me-2" style="color:var(--ranap-primary);"></i>
                                Input Catatan Perkembangan Pasien Terintegrasi (CPPT)
                            </h6>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="date" id="soap-tgl" class="form-control form-control-sm" style="width:145px; border-radius:10px; font-size:.82rem;" value="{{ date('Y-m-d') }}">
                                <input type="time" id="soap-jam" class="form-control form-control-sm" style="width:115px; border-radius:10px; font-size:.82rem;" value="{{ date('H:i') }}">
                            </div>
                        </div>

                        {{-- Tanda-tanda Vital --}}
                        <div style="font-size:.73rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:.6rem;">
                            <i class="bi bi-heart-pulse me-1"></i> Tanda-tanda Vital (TTV)
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="vital-input-group">
                                    <label>Tekanan Darah</label>
                                    <input type="text" id="sv-tensi" placeholder="120/80">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="vital-input-group">
                                    <label>Nadi (x/mnt)</label>
                                    <input type="text" id="sv-nadi" placeholder="80">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="vital-input-group">
                                    <label>Respirasi (x/mnt)</label>
                                    <input type="text" id="sv-respirasi" placeholder="18">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="vital-input-group">
                                    <label>Suhu (°C)</label>
                                    <input type="text" id="sv-suhu" placeholder="36.5">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="vital-input-group">
                                    <label>SpO₂ (%)</label>
                                    <input type="text" id="sv-spo2" placeholder="99">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="vital-input-group">
                                    <label>GCS</label>
                                    <input type="text" id="sv-gcs" placeholder="15">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="vital-input-group">
                                    <label>BB (kg)</label>
                                    <input type="text" id="sv-berat" placeholder="Contoh: 60">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="vital-input-group">
                                    <label>Kesadaran</label>
                                    <select id="sv-kesadaran">
                                        <option>Compos Mentis</option>
                                        <option>Somnolence</option>
                                        <option>Sopor</option>
                                        <option>Coma</option>
                                        <option>Apatis</option>
                                        <option>Delirium</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SOAP Textareas --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <div class="soap-section-label"><i class="bi bi-chat-left-text me-1" style="color:#0891b2;"></i>S — Subjektif (Keluhan)</div>
                                <textarea class="soap-textarea" id="soap-keluhan" placeholder="Keluhan subyektif pasien hari ini..."></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="soap-section-label"><i class="bi bi-stethoscope me-1" style="color:#7c3aed;"></i>O — Objektif (Pemeriksaan Fisik)</div>
                                <textarea class="soap-textarea" id="soap-pemeriksaan" placeholder="Hasil pemeriksaan fisik..."></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="soap-section-label"><i class="bi bi-lightbulb me-1" style="color:#d97706;"></i>A — Asesmen (Penilaian Klinis)</div>
                                <textarea class="soap-textarea" id="soap-penilaian" placeholder="Diagnosa kerja, DD, penilaian klinis..."></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="soap-section-label"><i class="bi bi-list-check me-1" style="color:#059669;"></i>P — Plan (Rencana Terapi / RTL)</div>
                                <textarea class="soap-textarea" id="soap-rtl" placeholder="Rencana tindak lanjut, terapi, monitoring..."></textarea>
                            </div>
                            <div class="col-12">
                                <div class="soap-section-label"><i class="bi bi-clipboard-check me-1" style="color:#475569;"></i>Instruksi / Evaluasi</div>
                                <textarea class="soap-textarea" id="soap-instruksi" placeholder="Instruksi perawatan, evaluasi, catatan tambahan..."></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn-outline-ranap" onclick="resetSoapForm()">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset Form
                            </button>
                            <button class="btn-success-ranap" id="btn-simpan-soap" onclick="simpanSoap()">
                                <i class="bi bi-check-lg"></i> Simpan Catatan CPPT
                            </button>
                        </div>
                    </div>

                    {{-- Riwayat CPPT --}}
                    <h6 class="section-title mb-3">
                        <i class="bi bi-clock-history"></i> Riwayat Catatan Perkembangan (CPPT)
                    </h6>
                    <div id="soap-list-container">
                        <div class="empty-state"><i class="bi bi-journal-text"></i><p>Belum ada catatan SOAP</p></div>
                    </div>
                </div>

                {{-- ── Tab 2: Diagnosa ICD-10 ── --}}
                <div class="ranap-tab-panel" id="panel-diagnosa">
                    <div class="soap-form-card mb-3">
                        <h6 class="mb-3" style="font-size:.92rem; font-weight:800; color:#0f172a;">
                            <i class="bi bi-search-heart me-2" style="color:var(--ranap-primary);"></i>Tambah Diagnosa ICD-10 Pasien
                        </h6>
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-7">
                                <label class="soap-section-label">Cari Diagnosa / Kode ICD-10</label>
                                <select id="diagnosa-select" style="width:100%;">
                                    <option value="">Ketik kode atau nama penyakit ICD-10...</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="soap-section-label">Prioritas Diagnosa</label>
                                <select id="diagnosa-prioritas" class="form-select" style="border-radius:10px; font-size:.84rem; height:38px;">
                                    <option value="1">1 — Utama (Primer)</option>
                                    <option value="2">2 — Sekunder</option>
                                    <option value="3">3 — Tambahan</option>
                                    <option value="4">4 — Komorbiditas</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-2">
                                <button class="btn-primary-ranap w-100" onclick="tambahDiagnosa()" style="height:38px; border-radius:10px;">
                                    <i class="bi bi-plus-lg"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    <h6 class="section-title mb-3"><i class="bi bi-list-ul"></i> Daftar Diagnosa Pasien</h6>
                    <div id="diagnosa-list-container">
                        <div class="empty-state"><i class="bi bi-search-heart"></i><p>Belum ada diagnosa ditambahkan</p></div>
                    </div>
                </div>

                {{-- ── Tab 3: Tindakan Ranap ── --}}
                <div class="ranap-tab-panel" id="panel-tindakan">
                    <div class="soap-form-card mb-3">
                        <h6 class="mb-3" style="font-size:.92rem; font-weight:800; color:#0f172a;">
                            <i class="bi bi-activity me-2" style="color:var(--ranap-primary);"></i>Input Tindakan & Prosedur Rawat Inap
                        </h6>
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-5">
                                <label class="soap-section-label">Jenis Tindakan & Tarif</label>
                                <select id="tindakan-select" style="width:100%;">
                                    <option value="">Cari tindakan rawat inap...</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="soap-section-label">Tanggal</label>
                                <input type="date" id="tindakan-tgl" value="{{ date('Y-m-d') }}" class="form-control" style="border-radius:10px; font-size:.83rem; height:38px;">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="soap-section-label">Jam</label>
                                <input type="time" id="tindakan-jam" value="{{ date('H:i') }}" class="form-control" style="border-radius:10px; font-size:.83rem; height:38px;">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="soap-section-label">Pelaksana</label>
                                <select id="tindakan-jenis" class="form-select" style="border-radius:10px; font-size:.83rem; height:38px;">
                                    <option value="Dokter">Dokter</option>
                                    <option value="Paramedis">Paramedis</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-1">
                                <button class="btn-primary-ranap w-100" onclick="tambahTindakan()" style="height:38px; border-radius:10px;" title="Simpan Tindakan">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <h6 class="section-title mb-3"><i class="bi bi-list-check"></i> Daftar Tindakan yang Diberikan</h6>
                    <div id="tindakan-list-container">
                        <div class="empty-state"><i class="bi bi-activity"></i><p>Belum ada tindakan dicatat</p></div>
                    </div>
                </div>

                {{-- ── Tab 4: Resep Obat ── --}}
                <div class="ranap-tab-panel" id="panel-resep">
                    <div class="soap-form-card mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <h6 class="mb-0" style="font-size:.92rem; font-weight:800; color:#0f172a;">
                                <i class="bi bi-capsule me-2" style="color:var(--ranap-primary);"></i>E-Resep Obat &amp; BHP Rawat Inap
                            </h6>
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:.78rem; font-weight:700; color:#64748b;">Tgl Resep:</span>
                                <input type="date" id="resep-tgl" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" style="border-radius:10px; font-size:.82rem; width:145px;">
                            </div>
                        </div>

                        {{-- Datalist master_aturan_pakai --}}
                        <datalist id="listAturanPakaiRanap"></datalist>

                        {{-- Sub-pills Mode Peresepan --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <ul class="nav nav-pills gap-1" id="pills-resep-mode-ranap" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="btn btn-sm btn-outline-success active font-bold text-xs py-1 px-3 rounded-pill" id="pills-obat-jadi-ranap-tab" data-bs-toggle="pill" data-bs-target="#pills-obat-jadi-ranap" type="button" role="tab">
                                        <i class="bi bi-capsule me-1"></i> 1. Obat Jadi (Non-Racikan)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="btn btn-sm btn-outline-primary font-bold text-xs py-1 px-3 rounded-pill" id="pills-obat-racik-ranap-tab" data-bs-toggle="pill" data-bs-target="#pills-obat-racik-ranap" type="button" role="tab">
                                        <i class="bi bi-mortarboard-fill me-1"></i> 2. Obat Racikan
                                    </button>
                                </li>
                            </ul>
                            <span class="text-xs text-muted"><i class="bi bi-info-circle me-1"></i>Standar E-Resep SIMRS Namira</span>
                        </div>

                        <div class="tab-content mb-3" id="pills-resep-mode-ranap-content">
                            {{-- SUB-TAB 1: OBAT JADI --}}
                            <div class="tab-pane fade show active" id="pills-obat-jadi-ranap" role="tabpanel">
                                <div class="row g-2 align-items-center mb-3">
                                    <div class="col-12 col-md-10">
                                        <select id="obat-select" style="width:100%;">
                                            <option value="">Cari obat / BHP farmasi...</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <button class="btn-primary-ranap w-100" onclick="tambahObatCart()" style="height:38px; border-radius:10px;">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                                {{-- Keranjang Obat Jadi --}}
                                <div id="obat-cart" style="min-height:60px;"></div>
                            </div>

                            {{-- SUB-TAB 2: OBAT RACIKAN --}}
                            <div class="tab-pane fade" id="pills-obat-racik-ranap" role="tabpanel">
                                <div class="card p-3 border mb-3 text-xs" style="border-radius:10px; background:#f8fafc;">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label font-bold text-xs text-slate-700 mb-1">Nama Racikan</label>
                                            <input type="text" id="racikNamaRanap" class="form-control form-control-sm" placeholder="Misal: Puyer Batuk, Sirup Racik...">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label font-bold text-xs text-slate-700 mb-1">Metode Racik</label>
                                            <select id="racikMetodeRanap" class="form-select form-select-sm">
                                                <option value="R01">Puyer</option>
                                                <option value="R02">Sirup</option>
                                                <option value="R03">Salep</option>
                                                <option value="R04">Kapsul</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label font-bold text-xs text-slate-700 mb-1">Jml Kemasan</label>
                                            <input type="number" id="racikJmlRanap" class="form-control form-control-sm" value="10" min="1">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label font-bold text-xs text-slate-700 mb-1">Aturan Pakai</label>
                                            <input type="text" id="racikAturanRanap" list="listAturanPakaiRanap" class="form-control form-control-sm" placeholder="3 X 1 Sehari...">
                                        </div>
                                        <div class="col-md-9">
                                            <label class="form-label font-bold text-xs text-slate-700 mb-1">Keterangan Racikan (Opsional)</label>
                                            <input type="text" id="racikKetRanap" class="form-control form-control-sm" placeholder="Misal: Sesudah makan...">
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-primary btn-sm w-100 font-bold" id="btnBuatGrupRacikanRanap" onclick="buatGrupRacikanRanap()">
                                                <i class="bi bi-plus-circle me-1"></i> Buat Racikan
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="daftarRacikanContainerRanap">
                                    <div class="text-center py-3 border rounded-3 bg-light text-muted fs-7">
                                        Belum ada obat racikan dibuat. Silakan isi form di atas dan klik <b>Buat Racikan</b>.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Summary & Actions --}}
                        <div class="d-flex justify-content-between align-items-center p-2.5 rounded-3 bg-light border mt-3 flex-wrap gap-2">
                            <span class="text-xs text-slate-600 font-semibold" id="resepSummaryTextRanap">
                                <i class="bi bi-cart3 me-1"></i> Total: <b>0</b> Obat Jadi, <b>0</b> Racikan
                            </span>
                            <div class="d-flex gap-2">
                                <button class="btn-outline-ranap" onclick="clearObatCart()">
                                    <i class="bi bi-trash me-1"></i> Bersihkan
                                </button>
                                <button class="btn-success-ranap" id="btn-simpan-resep" onclick="simpanResep()">
                                    <i class="bi bi-send me-1"></i> Kirim Resep ke Farmasi
                                </button>
                            </div>
                        </div>
                    </div>
                    <h6 class="section-title mb-3"><i class="bi bi-receipt"></i> Riwayat E-Resep Pasien</h6>
                    <div id="resep-list-container">
                        <div class="empty-state"><i class="bi bi-capsule"></i><p>Belum ada resep</p></div>
                    </div>
                </div>

                {{-- ── Tab 5: Laboratorium ── --}}
                <div class="ranap-tab-panel" id="panel-lab">
                    <div class="soap-form-card mb-3">
                        <h6 class="mb-3" style="font-size:.92rem; font-weight:800; color:#0f172a;">
                            <i class="bi bi-eyedropper me-2" style="color:var(--ranap-primary);"></i>Permintaan Pemeriksaan Laboratorium
                        </h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-7">
                                <label class="soap-section-label">Pilih Jenis Pemeriksaan Lab</label>
                                <select id="lab-select" style="width:100%;" multiple></select>
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="soap-section-label">Diagnosa / Indikasi Klinis</label>
                                <input type="text" id="lab-diagnosa" class="form-control" style="border-radius:10px; font-size:.84rem; height:38px;" placeholder="Indikasi klinis permintaan lab...">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn-primary-ranap" onclick="kirimLab()" style="border-radius:10px;">
                                <i class="bi bi-send me-1"></i> Kirim Order Laboratorium
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <h6 class="section-title mb-3"><i class="bi bi-card-list"></i> Order Lab Terkirim</h6>
                            <div id="lab-orders-container">
                                <div class="empty-state"><i class="bi bi-eyedropper"></i><p>Belum ada order lab</p></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <h6 class="section-title mb-3"><i class="bi bi-table"></i> Hasil Pemeriksaan Lab</h6>
                            <div id="lab-results-container">
                                <div class="empty-state"><i class="bi bi-graph-up"></i><p>Belum ada hasil lab</p></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Tab 6: Radiologi ── --}}
                <div class="ranap-tab-panel" id="panel-radiologi">
                    <div class="soap-form-card mb-3">
                        <h6 class="mb-3" style="font-size:.92rem; font-weight:800; color:#0f172a;">
                            <i class="bi bi-radioactive me-2" style="color:var(--ranap-primary);"></i>Permintaan Pemeriksaan Radiologi
                        </h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-7">
                                <label class="soap-section-label">Pilih Jenis Pemeriksaan Radiologi</label>
                                <select id="rad-select" style="width:100%;" multiple></select>
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="soap-section-label">Diagnosa / Indikasi Klinis</label>
                                <input type="text" id="rad-diagnosa" class="form-control" style="border-radius:10px; font-size:.84rem; height:38px;" placeholder="Indikasi klinis radiologi...">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn-primary-ranap" onclick="kirimRadiologi()" style="border-radius:10px;">
                                <i class="bi bi-send me-1"></i> Kirim Order Radiologi
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <h6 class="section-title mb-3"><i class="bi bi-card-list"></i> Order Terkirim</h6>
                            <div id="rad-orders-container">
                                <div class="empty-state"><i class="bi bi-radioactive"></i><p>Belum ada order radiologi</p></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <h6 class="section-title mb-3"><i class="bi bi-file-medical"></i> Hasil / Ekspertisi Radiologi</h6>
                            <div id="rad-results-container">
                                <div class="empty-state"><i class="bi bi-file-earmark-medical"></i><p>Belum ada hasil radiologi</p></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Tab 7: Operasi / Bedah (SIMRS-Namira Standar) ── --}}
                <div class="ranap-tab-panel" id="panel-operasi">
                    {{-- Sub-Navigation Tab Operasi Ranap --}}
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 flex-wrap gap-2">
                        <ul class="nav nav-pills gap-1.5" id="pills-ops-ranap-mode" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="btn btn-sm btn-outline-danger active font-bold text-xs py-1.5 px-3 rounded-pill" id="pills-ops-booking-ranap-tab" data-bs-toggle="pill" data-bs-target="#pills-ops-booking-ranap" type="button" role="tab">
                                    <i class="bi bi-calendar-event me-1"></i> 1. Booking Jadwal Operasi (<span id="countOpsRanapBookingText">0</span>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="btn btn-sm btn-outline-primary font-bold text-xs py-1.5 px-3 rounded-pill" id="pills-ops-laporan-ranap-tab" data-bs-toggle="pill" data-bs-target="#pills-ops-laporan-ranap" type="button" role="tab">
                                    <i class="bi bi-file-earmark-medical me-1"></i> 2. Laporan Operasi SIMRS (<span id="countOpsRanapLaporanText">0</span>)
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="pills-ops-ranap-content">
                        {{-- ── SUB-PANEL 1: BOOKING JADWAL OPERASI RANAP ── --}}
                        <div class="tab-pane fade show active" id="pills-ops-booking-ranap" role="tabpanel">
                            <div class="soap-form-card mb-3">
                                <form id="formOperasiRanap">
                                    <input type="hidden" name="no_rawat" id="opsRanapNoRawat">

                                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 border-bottom pb-2">
                                        <h6 class="mb-0" style="font-size:.92rem; font-weight:800; color:#0f172a;">
                                            <i class="bi bi-calendar-check-fill text-danger me-2"></i>
                                            Booking Jadwal Operasi / Kamar Bedah (OK) Rawat Inap
                                        </h6>
                                        <button type="button" class="btn btn-outline-danger btn-sm font-bold text-xs rounded-pill px-3 py-1.5" onclick="bukaModalCariPaketRanap()">
                                            <i class="bi bi-search me-1"></i> Cari &amp; Pilih Paket Operasi
                                        </button>
                                    </div>

                                    <div class="row g-2.5 mb-3">
                                        <div class="col-md-7">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Paket Operasi / Tindakan Bedah <span class="text-danger">*</span></label>
                                            <select name="kode_paket" id="opsRanapKodePaket" class="form-select form-select-sm select2-search">
                                                <option value="">-- Pilih Paket Operasi --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Dokter Operator (Bedah) <span class="text-danger">*</span></label>
                                            <select name="kd_dokter" id="opsRanapKdDokter" class="form-select form-select-sm select2-search">
                                                <option value="">-- Pilih Dokter Operator --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Ruang Operasi (OK)</label>
                                            <select name="kd_ruang_ok" id="opsRanapKdRuangOk" class="form-select form-select-sm select2-search">
                                                <option value="O1">KAMAR OPERASI 1</option>
                                                <option value="O2">KAMAR OPERASI 2</option>
                                                <option value="O3">KAMAR OPERASI 3</option>
                                                <option value="O4">KAMAR OPERASI 4</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal Operasi</label>
                                            <input type="date" name="tanggal" id="opsRanapTanggal" class="form-control form-control-sm" value="{{ now()->toDateString() }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Jam Mulai</label>
                                            <input type="time" name="jam_mulai" id="opsRanapJamMulai" class="form-control form-control-sm" value="08:00">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Jam Selesai</label>
                                            <input type="time" name="jam_selesai" id="opsRanapJamSelesai" class="form-control form-control-sm" value="09:30">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Status</label>
                                            <select name="status" id="opsRanapStatus" class="form-select form-select-sm">
                                                <option value="Menunggu">Menunggu</option>
                                                <option value="Proses Operasi">Proses Operasi</option>
                                                <option value="Selesai">Selesai</option>
                                                <option value="Batal">Batal</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-danger font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanOperasiRanap">
                                            <i class="bi bi-calendar-check-fill me-1"></i> Simpan Jadwal Operasi
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="soap-form-card">
                                <h6 class="section-title mb-3"><i class="bi bi-clock-history text-primary"></i> Riwayat Booking Jadwal Operasi Pasien</h6>
                                <div id="riwayatOpsRanapContainer">
                                    <div class="empty-state py-3"><i class="bi bi-calendar-event fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada booking operasi.</p></div>
                                </div>
                            </div>
                        </div>

                        {{-- ── SUB-PANEL 2: FORMULIR LAPORAN OPERASI SIMRS-NAMIRA RANAP ── --}}
                        <div class="tab-pane fade" id="pills-ops-laporan-ranap" role="tabpanel">
                            {{-- Alert Mode Edit Laporan Operasi Ranap --}}
                            <div id="alertEditLaporanOpsRanap" class="alert alert-warning d-none d-flex justify-content-between align-items-center py-2 px-3 mb-3" style="border-radius:10px; border-left: 5px solid #d97706;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-pencil-square fs-6 text-amber-800"></i>
                                    <div>
                                        <strong class="text-amber-900">Mode Edit Laporan Operasi:</strong> Mengubah laporan tanggal <span id="editLapOpsRanapTglText" class="font-bold text-dark"></span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-dark font-bold py-1 px-2.5 rounded-pill" onclick="batalEditLaporanOpsRanap()">
                                    <i class="bi bi-x-circle me-1"></i>Batal Edit (Input Baru)
                                </button>
                            </div>

                            <div class="soap-form-card mb-3">
                                <form id="formLaporanOperasiRanap">
                                    <input type="hidden" name="no_rawat" id="lapOpsRanapNoRawat">
                                    <input type="hidden" name="old_tanggal" id="lapOpsRanapOldTanggal" value="">

                                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 border-bottom pb-2">
                                        <h6 class="mb-0" style="font-size:.92rem; font-weight:800; color:#0f172a;">
                                            <i class="bi bi-file-earmark-medical-fill text-primary me-2"></i>
                                            Formulir Laporan Operasi (Standar SIMRS-Namira)
                                        </h6>
                                        <button type="button" class="btn btn-outline-primary btn-sm font-bold text-xs rounded-pill px-3 py-1.5 shadow-xs" onclick="bukaModalTemplateLaporanRanap()">
                                            <i class="bi bi-bookmarks-fill me-1 text-primary"></i> Pilih Template Laporan Operasi
                                        </button>
                                    </div>

                                    <div class="row g-2.5 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal Laporan Operasi</label>
                                            <input type="datetime-local" name="tanggal" id="lapOpsRanapTanggal" class="form-control form-control-sm" value="{{ now()->format('Y-m-d\TH:i') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal &amp; Jam Mulai Operasi</label>
                                            <input type="datetime-local" name="tgl_operasi" id="lapOpsRanapTglOperasi" class="form-control form-control-sm" value="{{ now()->format('Y-m-d\TH:i') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal &amp; Jam Selesai Operasi</label>
                                            <input type="datetime-local" name="selesaioperasi" id="lapOpsRanapSelesaiOperasi" class="form-control form-control-sm" value="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Diagnosa Pra Bedah (Pre-Op) <span class="text-danger">*</span></label>
                                            <input type="text" name="diagnosa_preop" id="lapOpsRanapPreop" class="form-control form-control-sm" placeholder="Contoh: G1 P0 A0 Hamil Aterm dg/ SC 1x...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Diagnosa Pasca Bedah (Post-Op) <span class="text-danger">*</span></label>
                                            <input type="text" name="diagnosa_postop" id="lapOpsRanapPostop" class="form-control form-control-sm" placeholder="Contoh: Post Sectio Caesarea...">
                                        </div>

                                        <div class="col-md-5">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Jaringan yang Dieksekusi / Dieksisi</label>
                                            <input type="text" name="jaringan_dieksekusi" id="lapOpsRanapJaringan" class="form-control form-control-sm" placeholder="Contoh: Kulit, Dinding Abdomen, -">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Permintaan PA</label>
                                            <select name="permintaan_pa" id="lapOpsRanapPa" class="form-select form-select-sm">
                                                <option value="Tidak">Tidak</option>
                                                <option value="Ya">Ya</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Jenis Pembiusan (Anasthesi)</label>
                                            <input type="text" name="jenis_anasthesi" id="lapOpsRanapAnasthesi" class="form-control form-control-sm" placeholder="Spinal, GA, Lokal, Sedasi..." value="SPINAL">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Kategori Operasi</label>
                                            <select name="kategori" id="lapOpsRanapKategori" class="form-select form-select-sm">
                                                <option value="-">-</option>
                                                <option value="Khusus">Khusus</option>
                                                <option value="Besar" selected>Besar</option>
                                                <option value="Sedang">Sedang</option>
                                                <option value="Kecil">Kecil</option>
                                                <option value="Elektive">Elektive</option>
                                                <option value="Emergency">Emergency</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Uraian Laporan Operasi / Jalannya Tindakan Pembedahan <span class="text-danger">*</span></label>
                                            <textarea name="laporan_operasi" id="lapOpsRanapLaporan" class="form-control form-control-sm" rows="8" placeholder="Tuliskan laporan operasi secara lengkap: desinfeksi, insisi, eksplorasi, hemostasis, penjahitan lapis demi lapis, jumlah perdarahan, urine, kondisi bayi/pasien..." style="font-family:monospace, inherit; line-height:1.6;"></textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-secondary btn-sm font-bold px-3 py-2" onclick="resetFormLaporanOpsRanap()">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Formulir
                                        </button>
                                        <button type="button" class="btn btn-success font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanLaporanOperasiRanap">
                                            <i class="bi bi-save-fill me-1"></i> Simpan Laporan Operasi
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="soap-form-card">
                                <h6 class="section-title mb-3"><i class="bi bi-file-earmark-text-fill text-danger"></i> Daftar Laporan Operasi Pasien Ini</h6>
                                <div id="laporanOpsRanapContainer">
                                    <div class="empty-state py-3"><i class="bi bi-file-earmark-medical fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada laporan operasi.</p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Tab 8: Riwayat Lengkap ── --}}
                <div class="ranap-tab-panel" id="panel-riwayat">
                    <h6 class="section-title mb-3"><i class="bi bi-clock-history"></i> Riwayat Perawatan Lengkap</h6>
                    <div id="riwayat-container">
                        <div class="empty-state"><i class="bi bi-clock-history"></i><p>Memuat riwayat...</p></div>
                    </div>
                </div>

                {{-- ── Tab 8: Resume Ranap ── --}}
                <div class="ranap-tab-panel" id="panel-resume">
                    <div class="soap-form-card mb-3">
                        <form id="formResumeRanap">
                            <input type="hidden" name="no_rawat" id="resumeRanapNoRawat">

                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2 border-bottom pb-2">
                                <h6 class="mb-0" style="font-size:.92rem; font-weight:800; color:#0f172a;">
                                    <i class="bi bi-file-earmark-medical me-2" style="color:var(--ranap-primary);"></i>
                                    Resume Medis Pasien Pulang Rawat Inap (Discharge Summary)
                                </h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-success btn-sm font-bold text-xs" id="btnPilihTemplateResumeRanap" onclick="bukaModalPilihTemplateRanap()">
                                        <i class="bi bi-file-earmark-text me-1"></i> Pilih Template Pengisian
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm font-bold text-xs" id="btnSimpanSebagaiTemplateRanap" onclick="bukaModalSimpanTemplateRanap()" title="Simpan data resume ranap saat ini sebagai template baru">
                                        <i class="bi bi-bookmark-plus me-1"></i> Simpan Sebagai Template
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm font-bold text-xs" id="btnAutoFillResumeRanap" onclick="autoFillResumeRanap()">
                                        <i class="bi bi-magic me-1"></i> Tarik Data Otomatis (CPPT, Diagnosa &amp; Resep)
                                    </button>
                                </div>
                            </div>

                            <div class="card p-3 mb-3 border text-xs" style="border-radius:10px; background:#f8fafc;">
                                {{-- 1. Anamnesa & Masuk RS --}}
                                <div class="fw-bold fs-7 text-primary mb-2"><i class="bi bi-box-arrow-in-right me-1"></i> 1. Anamnesa &amp; Kondisi Masuk Rumah Sakit</div>
                                <div class="row g-2.5 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Diagnosa Awal Masuk</label>
                                        <input type="text" name="diagnosa_awal" id="rr_diagnosa_awal" class="form-control form-control-sm" placeholder="Diagnosa saat pasien pertama kali masuk...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Alasan Masuk Rumah Sakit / Indikasi Rawat</label>
                                        <textarea name="alasan" id="rr_alasan" class="form-control form-control-sm" rows="1" placeholder="Alasan perlu dirawat inap..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Keluhan Utama</label>
                                        <textarea name="keluhan_utama" id="rr_keluhan_utama" class="form-control form-control-sm" rows="2" placeholder="Keluhan utama pasien..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Pemeriksaan Fisik Utama</label>
                                        <textarea name="pemeriksaan_fisik" id="rr_pemeriksaan_fisik" class="form-control form-control-sm" rows="2" placeholder="Tanda vital, kesadaran, keadaan umum..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Jalannya Penyakit / Kronologis Perkembangan Pasien</label>
                                        <textarea name="jalannya_penyakit" id="rr_jalannya_penyakit" class="form-control form-control-sm" rows="2" placeholder="Perjalanan penyakit selama dalam masa perawatan..."></textarea>
                                    </div>
                                </div>

                                <hr class="my-3 text-slate-300">

                                {{-- 2. Penunjang, Tindakan & Terapi --}}
                                <div class="fw-bold fs-7 text-primary mb-2"><i class="bi bi-eyedropper me-1"></i> 2. Pemeriksaan Penunjang, Terapi &amp; Tindakan Selama di RS</div>
                                <div class="row g-2.5 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Pemeriksaan Penunjang (Radiologi, EKG, USG, dll)</label>
                                        <textarea name="pemeriksaan_penunjang" id="rr_pemeriksaan_penunjang" class="form-control form-control-sm" rows="2" placeholder="Ringkasan hasil rontgen, ct-scan, usg, dll..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Hasil Laboratorium Penting / Terkait</label>
                                        <textarea name="hasil_laborat" id="rr_hasil_laborat" class="form-control form-control-sm" rows="2" placeholder="Ringkasan hasil laboratorium darah, urine, dll..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Tindakan / Prosedur / Operasi Selama Dirawat</label>
                                        <textarea name="tindakan_dan_operasi" id="rr_tindakan_dan_operasi" class="form-control form-control-sm" rows="2" placeholder="Tindakan medis atau prosedur operasi yang telah dilakukan..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Obat-obatan &amp; Terapi Selama Dirawat di Rumah Sakit</label>
                                        <textarea name="obat_di_rs" id="rr_obat_di_rs" class="form-control form-control-sm" rows="2" placeholder="Daftar injeksi, infus, obat oral selama dirawat..."></textarea>
                                    </div>
                                </div>

                                <hr class="my-3 text-slate-300">

                                {{-- 3. Diagnosa & Prosedur Akhir (ICD-10 & ICD-9) --}}
                                <div class="fw-bold fs-7 text-primary mb-2"><i class="bi bi-diagram-3-fill me-1"></i> 3. Diagnosa Akhir &amp; Prosedur (ICD-10 &amp; ICD-9-CM)</div>
                                <div class="row g-2.5 mb-2">
                                    <div class="col-md-8">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Diagnosa Utama (Utama Pulang)</label>
                                        <input type="text" name="diagnosa_utama" id="rr_diagnosa_utama" class="form-control form-control-sm" placeholder="Nama diagnosa utama...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-10 Utama</label>
                                        <input type="text" name="kd_diagnosa_utama" id="rr_kd_diagnosa_utama" class="form-control form-control-sm font-monospace" placeholder="Kode ICD-10...">
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Diagnosa Sekunder 1</label>
                                        <input type="text" name="diagnosa_sekunder" id="rr_diagnosa_sekunder" class="form-control form-control-sm" placeholder="Diagnosa sekunder 1...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-10 Sekunder 1</label>
                                        <input type="text" name="kd_diagnosa_sekunder" id="rr_kd_diagnosa_sekunder" class="form-control form-control-sm font-monospace" placeholder="Kode ICD-10...">
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Diagnosa Sekunder 2</label>
                                        <input type="text" name="diagnosa_sekunder2" id="rr_diagnosa_sekunder2" class="form-control form-control-sm" placeholder="Diagnosa sekunder 2...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-10 Sekunder 2</label>
                                        <input type="text" name="kd_diagnosa_sekunder2" id="rr_kd_diagnosa_sekunder2" class="form-control form-control-sm font-monospace" placeholder="Kode ICD-10...">
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Diagnosa Sekunder 3 (Opsional)</label>
                                        <input type="text" name="diagnosa_sekunder3" id="rr_diagnosa_sekunder3" class="form-control form-control-sm" placeholder="Diagnosa sekunder 3...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-10 Sekunder 3</label>
                                        <input type="text" name="kd_diagnosa_sekunder3" id="rr_kd_diagnosa_sekunder3" class="form-control form-control-sm font-monospace" placeholder="Kode ICD-10...">
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Prosedur / Tindakan Utama</label>
                                        <input type="text" name="prosedur_utama" id="rr_prosedur_utama" class="form-control form-control-sm" placeholder="Prosedur utama...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-9 Utama</label>
                                        <input type="text" name="kd_prosedur_utama" id="rr_kd_prosedur_utama" class="form-control form-control-sm font-monospace" placeholder="Kode ICD-9...">
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Prosedur Sekunder 1</label>
                                        <input type="text" name="prosedur_sekunder" id="rr_prosedur_sekunder" class="form-control form-control-sm" placeholder="Prosedur sekunder 1...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-9 Sekunder 1</label>
                                        <input type="text" name="kd_prosedur_sekunder" id="rr_kd_prosedur_sekunder" class="form-control form-control-sm font-monospace" placeholder="Kode ICD-9...">
                                    </div>
                                </div>

                                <hr class="my-3 text-slate-300">

                                {{-- 4. Alergi, Diet & Kondisi Pulang --}}
                                <div class="fw-bold fs-7 text-primary mb-2"><i class="bi bi-box-arrow-right me-1"></i> 4. Kondisi Pulang, Edukasi &amp; Rencana Tindak Lanjut</div>
                                <div class="row g-2.5 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Cara Keluar</label>
                                        <select name="cara_keluar" id="rr_cara_keluar" class="form-select form-select-sm">
                                            <option value="Atas Izin Dokter">Atas Izin Dokter</option>
                                            <option value="Atas Permintaan Sendiri">Atas Permintaan Sendiri (APS)</option>
                                            <option value="Pindah RS Lain">Pindah / Rujuk RS Lain</option>
                                            <option value="Meninggal">Meninggal</option>
                                            <option value="Lain-lain">Lain-lain</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Ket. Cara Keluar</label>
                                        <input type="text" name="ket_keluar" id="rr_ket_keluar" class="form-control form-control-sm" placeholder="Keterangan keluar...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Keadaan Keluar</label>
                                        <select name="keadaan" id="rr_keadaan" class="form-select form-select-sm">
                                            <option value="Sembuh">Sembuh</option>
                                            <option value="Membaik">Membaik</option>
                                            <option value="Belum Sembuh">Belum Sembuh</option>
                                            <option value="Meninggal">Meninggal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Ket. Keadaan</label>
                                        <input type="text" name="ket_keadaan" id="rr_ket_keadaan" class="form-control form-control-sm" placeholder="Keterangan keadaan...">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Pengobatan Dilanjutkan Di</label>
                                        <select name="dilanjutkan" id="rr_dilanjutkan" class="form-select form-select-sm">
                                            <option value="Kembali Ke RS">Kembali Ke RS (Poli Rawat Jalan)</option>
                                            <option value="Puskesmas">Puskesmas</option>
                                            <option value="Dokter Luar RS">Dokter Praktek Luar RS</option>
                                            <option value="Rumah Sakit Lain">Rumah Sakit Lain</option>
                                            <option value="Tempat Lain">Tempat Lain</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Ket. Dilanjutkan</label>
                                        <input type="text" name="ket_dilanjutkan" id="rr_ket_dilanjutkan" class="form-control form-control-sm" placeholder="Keterangan rujukan lanjutan...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Tanggal Kontrol Ulang</label>
                                        <input type="date" name="kontrol" id="rr_kontrol" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Diet Pasien</label>
                                        <input type="text" name="diet" id="rr_diet" class="form-control form-control-sm" placeholder="Misal: Rendah garam, bubur lunak...">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Riwayat Alergi</label>
                                        <input type="text" name="alergi" id="rr_alergi" class="form-control form-control-sm" placeholder="Alergi obat / makanan...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Hasil Lab yang Belum Selesai (Pending)</label>
                                        <input type="text" name="lab_belum" id="rr_lab_belum" class="form-control form-control-sm" placeholder="Pemeriksaan lab yang hasilnya menyusul...">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Obat Pulang / Terapi Rumah</label>
                                        <textarea name="obat_pulang" id="rr_obat_pulang" class="form-control form-control-sm" rows="3" placeholder="Daftar obat yang dibawa pulang oleh pasien beserta aturan pakai..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Instruksi / Edukasi &amp; Catatan Lanjutan</label>
                                        <textarea name="edukasi" id="rr_edukasi" class="form-control form-control-sm" rows="3" placeholder="Instruksi perawatan di rumah, tanda bahaya harus segera ke IGD..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span id="resumeRanapStatusBadge" class="text-xs text-muted"><i class="bi bi-info-circle me-1"></i>Belum disimpan</span>
                                <button type="button" class="btn btn-primary font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanResumeRanap" onclick="simpanResumeRanap()">
                                    <i class="bi bi-save-fill me-1"></i> Simpan Resume Medis Rawat Inap
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>{{-- end emr-workspace-body --}}
        </div>{{-- end emr-main-right --}}

    </div>{{-- end oc-content --}}
</div>{{-- end offcanvas --}}

{{-- Toast Container --}}
<div id="ranap-toast-container"></div>

{{-- MODAL PILIH TEMPLATE RESUME RANAP --}}
<div class="modal fade" id="modalPilihTemplateResumeRanap" tabindex="-1" aria-labelledby="modalPilihTemplateResumeRanapLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header py-3 px-4" style="background:linear-gradient(135deg, #042b1b 0%, #0d7044 100%); color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-file-earmark-medical-fill fs-5 text-white"></i>
                    </div>
                    <div>
                        <h6 class="modal-title font-bold mb-0 text-white" id="modalPilihTemplateResumeRanapLabel">Pilih Template Resume Medis Rawat Inap</h6>
                        <small class="text-white text-opacity-75" style="font-size:0.75rem;">Standar Pengisian Klinis Rawat Inap SIMRS Khanza Namira</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4" style="background:#f8fafc;">
                <!-- Search Box -->
                <div class="mb-3 position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"></i>
                    <input type="text" id="searchTemplateResumeRanap" class="form-control form-control-sm ps-5 py-2 shadow-xs" placeholder="Ketik kata kunci template, diagnosa, keluhan..." style="border-radius:10px; font-size:0.85rem;" onkeyup="filterTemplateRanapCards()">
                </div>

                <!-- Tabs between Resume Templates and Khanza Examination Templates -->
                <ul class="nav nav-pills mb-3 gap-2" id="pills-template-tab-ranap" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-1.5 px-3 font-bold text-xs rounded-pill" id="tab-tmpl-resume-ranap" data-bs-toggle="pill" data-bs-target="#content-tmpl-resume-ranap" type="button" role="tab">
                            <i class="bi bi-file-earmark-text me-1"></i> Template Resume Medis Ranap (<span id="countTmplResumeRanap">0</span>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1.5 px-3 font-bold text-xs rounded-pill" id="tab-tmpl-khanza-ranap" data-bs-toggle="pill" data-bs-target="#content-tmpl-khanza-ranap" type="button" role="tab">
                            <i class="bi bi-hospital me-1"></i> Template Pemeriksaan SIMRS (<span id="countTmplKhanzaRanap">0</span>)
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab 1: Resume Templates -->
                    <div class="tab-pane fade show active" id="content-tmpl-resume-ranap" role="tabpanel">
                        <div id="listTemplateResumeRanapContainer" class="d-flex flex-column gap-2.5">
                            <div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat template...</div>
                        </div>
                    </div>
                    <!-- Tab 2: Khanza Templates -->
                    <div class="tab-pane fade" id="content-tmpl-khanza-ranap" role="tabpanel">
                        <div id="listTemplateKhanzaRanapContainer" class="d-flex flex-column gap-2.5">
                            <div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat data...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2.5 px-4 bg-white border-top">
                <button type="button" class="btn btn-secondary btn-sm px-3 font-bold text-xs rounded-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL SIMPAN TEMPLATE RESUME RANAP --}}
<div class="modal fade" id="modalSimpanTemplateResumeRanap" tabindex="-1" aria-labelledby="modalSimpanTemplateResumeRanapLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header py-3 px-4" style="background:#0f172a; color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bookmark-plus-fill text-warning fs-5"></i>
                    <h6 class="modal-title font-bold mb-0 text-white" id="modalSimpanTemplateResumeRanapLabel">Simpan Sebagai Template Resume Ranap</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label font-bold text-xs text-slate-700 mb-1">Nama / Judul Template <span class="text-danger">*</span></label>
                    <input type="text" id="inputNamaTemplateRanap" class="form-control form-control-sm" placeholder="Contoh: Resume DHF Grade II, Resume Post SC..." style="border-radius:8px;">
                    <div class="text-muted text-xs mt-1">Gunakan nama yang jelas agar mudah dicari saat pelayanan pasien lain.</div>
                </div>
                <div class="card p-3 border bg-white text-xs" style="border-radius:10px;">
                    <div class="font-bold text-slate-800 mb-1"><i class="bi bi-info-circle text-primary me-1"></i> Data yang Akan Disimpan ke Template:</div>
                    <div class="text-slate-600" id="previewSaveTemplateRanap">
                        <!-- Filled by JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2.5 px-4 bg-white border-top">
                <button type="button" class="btn btn-secondary btn-sm px-3 font-bold text-xs rounded-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm px-4 font-bold text-xs rounded-2" id="btnSubmitSimpanTemplateRanap" onclick="submitSimpanTemplateRanap()">
                    <i class="bi bi-save me-1"></i> Simpan Template
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CARI & PILIH PAKET OPERASI SIMRS-NAMIRA (RAWAT INAP) --}}
<div class="modal fade" id="modalCariPaketRanap" tabindex="-1" aria-labelledby="modalCariPaketRanapLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header py-3 px-4" style="background:#0f172a; color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-search text-danger fs-5"></i>
                    <h6 class="modal-title font-bold mb-0 text-white" id="modalCariPaketRanapLabel">::[ Cari &amp; Pilih Paket Tindakan Operasi SIMRS-Namira (Ranap) ]::</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="card p-3 mb-3 border bg-white shadow-xs" style="border-radius:10px;">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Cari Nama Operasi / Kode Paket</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchPaketOpsRanapInput" class="form-control" placeholder="Ketik nama operasi (misal: SC, Sirkumsisi, Hernia, Kuret)..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Kategori</label>
                            <select id="filterKategoriOpsRanapSelect" class="form-select form-select-sm">
                                <option value="">-- Semua Kategori --</option>
                                <option value="Operasi">Operasi Bedah</option>
                                <option value="Kebidanan">Kebidanan / Kandungan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Kelas Perawatan</label>
                            <select id="filterKelasOpsRanapSelect" class="form-select form-select-sm">
                                <option value="">-- Semua Kelas --</option>
                                <option value="Kelas 1">Kelas 1</option>
                                <option value="Kelas 2">Kelas 2</option>
                                <option value="Kelas 3">Kelas 3</option>
                                <option value="Kelas VIP">Kelas VIP</option>
                                <option value="Kelas VVIP">Kelas VVIP</option>
                                <option value="Rawat Jalan">Rawat Jalan</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 font-bold" onclick="loadPaketOperasiModalRanap('')">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive bg-white rounded-3 border shadow-xs" style="max-height: 420px;">
                    <table class="table table-hover table-sm align-middle mb-0 text-xs" id="tableModalPaketOpsRanap">
                        <thead class="table-dark sticky-top" style="font-size:.78rem;">
                            <tr>
                                <th style="width:110px;">Kode</th>
                                <th>Nama Tindakan / Paket Operasi</th>
                                <th style="width:110px;">Kategori</th>
                                <th style="width:110px;">Kelas</th>
                                <th style="width:130px; text-align:right;">Tarif Operator</th>
                                <th style="width:140px; text-align:right;">Total Tarif (Rp)</th>
                                <th style="width:90px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyModalPaketOpsRanap">
                            <tr><td colspan="7" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat paket operasi...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2 px-4 bg-white border-top">
                <button type="button" class="btn btn-secondary btn-sm px-3 font-bold text-xs rounded-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PILIH TEMPLATE LAPORAN OPERASI SIMRS-NAMIRA (RAWAT INAP) --}}
<div class="modal fade" id="modalTemplateLaporanRanap" tabindex="-1" aria-labelledby="modalTemplateLaporanRanapLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header py-3 px-4" style="background:#0f172a; color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bookmarks-fill text-primary fs-5"></i>
                    <h6 class="modal-title font-bold mb-0 text-white" id="modalTemplateLaporanRanapLabel">::[ Master Template Laporan Operasi SIMRS-Namira ]::</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="row g-3">
                    {{-- Sisi Kiri: Daftar Template --}}
                    <div class="col-md-6">
                        <div class="card p-2.5 mb-2.5 border bg-white shadow-xs" style="border-radius:10px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchTemplateOpsRanapInput" class="form-control" placeholder="Cari nama template operasi / diagnosa..." autocomplete="off">
                            </div>
                        </div>

                        <div class="table-responsive bg-white rounded-3 border shadow-xs" style="max-height: 420px;">
                            <table class="table table-hover table-sm align-middle mb-0 text-xs" id="tableModalTemplateOpsRanap">
                                <thead class="table-dark sticky-top" style="font-size:.78rem;">
                                    <tr>
                                        <th style="width:70px;">Kode</th>
                                        <th>Nama Operasi</th>
                                        <th>Pre-Op / Post-Op</th>
                                        <th style="width:60px; text-align:center;">PA</th>
                                        <th style="width:70px; text-align:center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyModalTemplateOpsRanap">
                                    <tr><td colspan="5" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat template laporan operasi...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Preview Template Terpilih --}}
                    <div class="col-md-6">
                        <div class="card p-3 border bg-white shadow-xs h-100" style="border-radius:12px;">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <h6 class="font-bold text-xs text-slate-800 mb-0"><i class="bi bi-eye text-primary me-1"></i> Preview Template Operasi</h6>
                                <span class="badge bg-primary px-2 py-1" id="previewTemplateRanapNoText">-</span>
                            </div>
                            <div class="mb-2 text-xs">
                                <div><strong>Nama Operasi:</strong> <span id="previewTemplateRanapNamaText" class="text-slate-800 font-bold">-</span></div>
                                <div><strong>Diagnosa Pre-Op:</strong> <span id="previewTemplateRanapPreopText" class="text-slate-700">-</span></div>
                                <div><strong>Diagnosa Post-Op:</strong> <span id="previewTemplateRanapPostopText" class="text-slate-700">-</span></div>
                                <div><strong>Jaringan Dieksisi:</strong> <span id="previewTemplateRanapJaringanText" class="text-slate-700">-</span> | <strong>PA:</strong> <span id="previewTemplateRanapPaText" class="badge bg-light text-dark border">-</span></div>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Uraian Laporan Pembedahan:</label>
                                <textarea id="previewTemplateRanapLaporanText" class="form-control form-control-sm bg-light" rows="12" readonly style="font-family:monospace, inherit; font-size:.78rem; line-height:1.5;"></textarea>
                            </div>
                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-success btn-sm font-bold px-4 py-2" id="btnTerapkanTemplateOpsRanap" disabled style="border-radius:8px;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Terapkan Template Ini ke Formulir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 px-4 bg-white border-top">
                <button type="button" class="btn btn-secondary btn-sm px-3 font-bold text-xs rounded-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ================================================================
//  ePasien RS Namira — Rawat Inap JavaScript
// ================================================================
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const isDokterOrAdmin = {{ (session('auth_user.is_admin') || session('auth_user.is_dokter')) ? 'true' : 'false' }};

// ── Global state ──────────────────────────────────────────────
let currentNoRawat   = '';
let currentData      = {};
let obatCart         = [];  // [{kode_brng, nama_brng, jml, aturan_pakai, keterangan, kode_sat}]
let racikanCart      = [];  // [{no_racik, nama_racik, kd_racik, jml_dr, aturan_pakai, keterangan, detail: []}]

function loadMasterAturanPakaiRanap() {
    fetch('/rawat-inap/master-aturan-pakai')
        .then(r => r.json())
        .then(data => {
            let html = '';
            (data || []).forEach(item => {
                const val = typeof item === 'string' ? item : (item.aturan || item.aturan_pakai || '');
                if (val) html += `<option value="${val}">`;
            });
            const dl = document.getElementById('listAturanPakaiRanap');
            if (dl) dl.innerHTML = html;
        })
        .catch(e => console.warn('Failed to load master aturan pakai ranap', e));
}

// ── Live Clock ────────────────────────────────────────────────
function updateClock() {
    const now  = new Date();
    const hh   = String(now.getHours()).padStart(2,'0');
    const mm   = String(now.getMinutes()).padStart(2,'0');
    const ss   = String(now.getSeconds()).padStart(2,'0');
    const el   = document.getElementById('live-time');
    if (el) el.textContent = `${hh}:${mm}:${ss}`;
}
setInterval(updateClock, 1000);
updateClock();

// ── DataTables Init ───────────────────────────────────────────
$(document).ready(function() {
    loadMasterAturanPakaiRanap();

    const dtCfg = {
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ pasien',
            paginate: { previous: '‹', next: '›' },
            emptyTable: 'Tidak ada data',
            zeroRecords: 'Data tidak ditemukan',
        },
        pageLength: 25,
        responsive: true,
        columnDefs: [{ targets: [0, -1], orderable: false }],
    };
    if ($('#tblRanapAktif').length)   $('#tblRanapAktif').DataTable(dtCfg);
    if ($('#tblRanapSelesai').length) $('#tblRanapSelesai').DataTable(dtCfg);
});

// ── Toast Notification ────────────────────────────────────────
function showToast(msg, type = 'success', duration = 3500) {
    const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill' };
    const c = document.getElementById('ranap-toast-container');
    const t = document.createElement('div');
    t.className = `ranap-toast ${type}`;
    t.innerHTML = `<i class="bi ${icons[type] ?? icons.success}" style="font-size:1.1rem; flex-shrink:0;"></i><span>${msg}</span>`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(100%)'; t.style.transition='all .3s'; setTimeout(()=>t.remove(), 300); }, duration);
}

// ── Tab Switching ─────────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('#ranapNavTabs .nav-link').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.ranap-tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById(`tab-${tab}-btn`)?.classList.add('active');
    document.getElementById(`panel-${tab}`)?.classList.add('active');
}

// ── Open Detail Offcanvas ─────────────────────────────────────
function bukaDetailRanap(noRawatB64, nmPasien) {
    currentNoRawat = noRawatB64;
    obatCart = [];
    racikanCart = [];

    // Reset UI using Bootstrap utility classes (d-none, d-flex) to avoid CSS priority collision
    const ocLoading = document.getElementById('oc-loading');
    const ocContent = document.getElementById('oc-content');
    if (ocLoading) {
        ocLoading.classList.remove('d-none');
        ocLoading.classList.add('d-flex');
    }
    if (ocContent) {
        ocContent.classList.add('d-none');
        ocContent.classList.remove('d-flex');
    }
    try { resetAllPanels(); } catch(e) { console.warn('resetAllPanels error:', e); }

    const ocEl = document.getElementById('offcanvasRanap');
    if (ocEl && window.bootstrap && bootstrap.Offcanvas) {
        const oc = bootstrap.Offcanvas.getOrCreateInstance(ocEl);
        oc.show();
    } else if (window.jQuery) {
        $('#offcanvasRanap').offcanvas('show');
    }

    // Fetch data
    fetch("{{ route('rawat-inap.pasien-detail') }}?no_rawat=" + encodeURIComponent(noRawatB64), {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) throw new Error(d.message ?? 'Gagal memuat data.');
        currentData = d;
        renderOffcanvas(d);
        if (ocLoading) {
            ocLoading.classList.add('d-none');
            ocLoading.classList.remove('d-flex');
        }
        if (ocContent) {
            ocContent.classList.remove('d-none');
            ocContent.classList.add('d-flex');
        }
    })
    .catch(e => {
        if (ocLoading) {
            ocLoading.classList.add('d-none');
            ocLoading.classList.remove('d-flex');
        }
        showToast(e.message, 'error');
    });
}
window.bukaDetailRanap = bukaDetailRanap;

$(document).on('click', '.btn-detail-ranap', function(e) {
    const b64 = $(this).data('norawat-b64') || $(this).attr('data-norawat-b64');
    const nama = $(this).data('nama') || $(this).attr('data-nama');
    if (b64) {
        bukaDetailRanap(b64, nama);
    }
});

function resetAllPanels() {
    switchTab('soap');
    const safeSet = (id, html) => {
        const el = document.getElementById(id);
        if (el) el.innerHTML = html;
    };
    safeSet('soap-list-container', '<div class="empty-state"><i class="bi bi-journal-text"></i><p>Memuat...</p></div>');
    safeSet('diagnosa-list-container', '<div class="empty-state"><i class="bi bi-search-heart"></i><p>Memuat...</p></div>');
    safeSet('tindakan-list-container', '<div class="empty-state"><i class="bi bi-activity"></i><p>Memuat...</p></div>');
    safeSet('resep-list-container', '<div class="empty-state"><i class="bi bi-capsule"></i><p>Memuat...</p></div>');
    safeSet('lab-orders-container', '<div class="empty-state"><i class="bi bi-eyedropper"></i><p>Memuat...</p></div>');
    safeSet('lab-results-container', '<div class="empty-state"><i class="bi bi-graph-up"></i><p>Memuat...</p></div>');
    safeSet('rad-orders-container', '<div class="empty-state"><i class="bi bi-radioactive"></i><p>Memuat...</p></div>');
    safeSet('rad-results-container', '<div class="empty-state"><i class="bi bi-file-earmark-medical"></i><p>Memuat...</p></div>');
    safeSet('riwayat-container', '<div class="empty-state"><i class="bi bi-clock-history"></i><p>Memuat...</p></div>');
    safeSet('riwayatOpsRanapContainer', '<div class="empty-state py-3"><i class="bi bi-calendar-event fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Memuat...</p></div>');
    safeSet('laporanOpsRanapContainer', '<div class="empty-state py-3"><i class="bi bi-file-earmark-medical fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Memuat...</p></div>');
    safeSet('obat-cart', '');

    obatCart = [];
    racikanCart = [];
    renderRacikanCart();
    updateResepSummaryRanap();

    if (document.getElementById('formResumeRanap')) document.getElementById('formResumeRanap').reset();
    safeSet('resumeRanapStatusBadge', '<span class="text-xs text-muted"><i class="bi bi-info-circle me-1"></i>Belum disimpan</span>');

    if (document.getElementById('formOperasiRanap')) document.getElementById('formOperasiRanap').reset();
    if (document.getElementById('formLaporanOperasiRanap')) document.getElementById('formLaporanOperasiRanap').reset();
    if (typeof batalEditLaporanOpsRanap === 'function') batalEditLaporanOpsRanap();

    // reset tab badges
    ['soap','diagnosa','tindakan','resep','lab','rad','operasi','resume'].forEach(id => {
        const b = document.getElementById(`badge-${id}`); if(b) b.textContent='0';
    });
}

// ── Render Offcanvas content ──────────────────────────────────
function renderOffcanvas(d) {
    const p = d.pasien;
    if (!p) return;

    // Avatar & nama
    const initials = (p.nm_pasien ?? 'P').split(' ').slice(0,2).map(w=>w[0]?.toUpperCase()??'').join('');
    const ocAvatar = document.getElementById('oc-avatar');
    if (ocAvatar) ocAvatar.textContent = initials;

    const ocNm = document.getElementById('oc-nm-pasien');
    if (ocNm) ocNm.textContent = p.nm_pasien ?? '-';

    const ocTitle = document.getElementById('oc-no-rawat-title');
    if (ocTitle) ocTitle.textContent = p.no_rawat ? `No. Rawat: ${p.no_rawat}` : '';

    // Status Pasien Badge
    const badgeStatus = document.getElementById('oc-badge-status-pasien');
    if (badgeStatus) {
        if (p.stts_pulang && p.stts_pulang !== '-') {
            badgeStatus.innerHTML = `<i class="bi bi-door-open"></i> Sudah Pulang (${p.stts_pulang})`;
            badgeStatus.style.background = 'rgba(239, 68, 68, 0.2)';
            badgeStatus.style.color = '#fca5a5';
        } else {
            badgeStatus.innerHTML = `<i class="bi bi-hospital"></i> Sedang Dirawat`;
            badgeStatus.style.background = 'rgba(255, 255, 255, 0.18)';
            badgeStatus.style.color = '#93c5fd';
        }
    }

    // ID Strip
    const elRawatVal = document.getElementById('oc-no-rawat-val');
    if (elRawatVal) elRawatVal.textContent = p.no_rawat ?? '-';

    const elRmVal = document.getElementById('oc-no-rkm-val');
    if (elRmVal) elRmVal.textContent = p.no_rkm_medis ?? '-';

    const elKamarVal = document.getElementById('oc-kamar-kelas-val');
    if (elKamarVal) elKamarVal.textContent = `${p.kd_kamar ?? '-'} (${p.kelas ?? '-'})`;

    const elBayarVal = document.getElementById('oc-bayar-val');
    if (elBayarVal) elBayarVal.textContent = p.jenis_bayar ?? '-';

    // Umur string
    let umur = '';
    if (p.tgl_lahir) {
        const birth = new Date(p.tgl_lahir);
        const now2  = new Date();
        umur = Math.floor((now2 - birth) / (365.25 * 24 * 3600 * 1000)) + ' thn';
    }
    const umurTxt = p.umur_daftar || umur;

    // Info Grid
    const elJk = document.getElementById('oc-jk-val');
    if (elJk) elJk.textContent = (p.jk === 'L' ? 'Laki-laki' : (p.jk === 'P' ? 'Perempuan' : '-'));

    const elUmur = document.getElementById('oc-umur-val');
    if (elUmur) elUmur.textContent = `${fmtDate(p.tgl_lahir)} (${umurTxt})`;

    const masukFmt = p.tgl_masuk ? `${fmtDate(p.tgl_masuk)} ${p.jam_masuk ? p.jam_masuk.substring(0,5) : ''}` : '-';
    const elMasuk = document.getElementById('oc-masuk-val');
    if (elMasuk) elMasuk.textContent = masukFmt;

    const los = p.tgl_masuk ? Math.floor((new Date() - new Date(p.tgl_masuk)) / (24*3600*1000)) : (p.lama ?? 0);
    const elLos = document.getElementById('oc-los-val');
    if (elLos) elLos.textContent = `${los} Hari`;

    const elBangsal = document.getElementById('oc-bangsal-val');
    if (elBangsal) elBangsal.textContent = p.nm_bangsal ?? '-';

    const elTrf = document.getElementById('oc-trf-kamar-val');
    if (elTrf) elTrf.textContent = p.trf_kamar ? fmtRupiah(p.trf_kamar) : '-';

    const dpjpNama = p.nm_dpjp || ((d.dpjpList && d.dpjpList.length) ? d.dpjpList[0].nm_dokter : (p.nm_dokter ?? '-'));
    const elDpjp = document.getElementById('oc-dpjp-val');
    if (elDpjp) elDpjp.textContent = dpjpNama;

    const elTelp = document.getElementById('oc-telp-val');
    if (elTelp) elTelp.textContent = p.no_tlp || '-';

    const elPj = document.getElementById('oc-pj-val');
    if (elPj) elPj.textContent = p.p_jawab ? `${p.p_jawab}${p.hubunganpj ? ' ('+p.hubunganpj+')' : ''}` : '-';

    const sepRow = document.getElementById('oc-sep-row');
    const sepVal = document.getElementById('oc-sep-val');
    if (p.no_sep) {
        if (sepVal) sepVal.textContent = p.no_sep;
        if (sepRow) sepRow.style.display = 'flex';
    } else {
        if (sepRow) sepRow.style.display = 'none';
    }

    const elDarah = document.getElementById('oc-darah-val');
    if (elDarah) elDarah.textContent = p.gol_darah || '-';

    const elAgama = document.getElementById('oc-agama-val');
    if (elAgama) elAgama.textContent = [p.agama, p.pekerjaan].filter(Boolean).join(' · ') || '-';

    const elDiagAwal = document.getElementById('oc-diag-awal-val');
    if (elDiagAwal) elDiagAwal.textContent = p.diagnosa_awal || '-';

    const diagList = d.diagnosaList && d.diagnosaList.length ? d.diagnosaList.map(dg => `${dg.kd_penyakit} - ${dg.nm_penyakit}`).join(', ') : '-';
    const elDiagAkhir = document.getElementById('oc-diag-akhir-val');
    if (elDiagAkhir) elDiagAkhir.textContent = (p.diagnosa_akhir && p.diagnosa_akhir !== '-') ? p.diagnosa_akhir : diagList;

    const elAlamat = document.getElementById('oc-alamat-val');
    if (elAlamat) elAlamat.textContent = p.alamat_lengkap || p.alamat || '-';

    // Render each panel
    renderSoapPanel(d.soapList ?? []);
    renderDiagnosaPanel(d.diagnosaList ?? []);
    renderTindakanPanel(d.tindakanList ?? []);
    renderResepPanel(d.resepList ?? []);
    renderLabPanel(d.labOrders ?? [], d.labResults ?? []);
    renderRadPanel(d.radOrders ?? [], d.radResults ?? []);
    renderRiwayatPanel(d.soapList ?? [], d.riwayatKamar ?? [], d.awalMedisRanap ?? null);
    renderOperasiRanap(d.bookingOps ?? [], d.laporanOps ?? []);
    renderResumeRanap(d.resumeRanap, d);

    $('#opsRanapNoRawat, #lapOpsRanapNoRawat').val(p.no_rawat);
    loadMasterOperasiRanap();
    loadMasterDokterRanap();
    loadMasterRuangOkRanap();

    // Init Select2 for this offcanvas instance
    initSelect2();
}

// ── Render SOAP Panel ─────────────────────────────────────────
function renderSoapPanel(list) {
    const badge = document.getElementById('badge-soap');
    if (badge) badge.textContent = list.length;

    const c = document.getElementById('soap-list-container');
    if (!list.length) {
        c.innerHTML = '<div class="empty-state"><i class="bi bi-journal-text"></i><p>Belum ada catatan SOAP</p></div>';
        return;
    }
    c.innerHTML = list.map(s => `
        <div class="timeline-card mb-3">
            <div class="timeline-date">
                <i class="bi bi-calendar3 me-1"></i>${fmtDate(s.tgl_perawatan)} &nbsp;
                <i class="bi bi-clock me-1"></i>${(s.jam_rawat??'').substring(0,5)} &mdash;
                <span style="color:#059669;">${s.petugas ?? '-'}</span>
            </div>
            ${s.tensi||s.nadi||s.suhu_tubuh||s.respirasi||s.spo2 ? `
            <div class="d-flex flex-wrap gap-2 mb-2">
                ${vital('bi-heart-pulse','TD',s.tensi,'#dc2626')}
                ${vital('bi-activity','N',s.nadi,'#1e40af')}
                ${vital('bi-thermometer-half','S',s.suhu_tubuh,'#d97706')}
                ${vital('bi-lungs','RR',s.respirasi,'#059669')}
                ${vital('bi-droplet','SpO2',s.spo2,'#7c3aed')}
                ${vital('bi-person-arms-up','GCS',s.gcs,'#0891b2')}
            </div>` : ''}
            <div class="timeline-soap-row">
                ${soapField('S — Subjektif',s.keluhan)}
                ${soapField('O — Objektif',s.pemeriksaan)}
                ${soapField('A — Asesmen',s.penilaian)}
                ${soapField('P — Plan/RTL',s.rtl)}
            </div>
            ${s.instruksi ? soapField('Instruksi/Evaluasi',s.instruksi) : ''}
        </div>
    `).join('');
}

function vital(icon, label, val, color) {
    if (!val) return '';
    return `<span style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:20px; padding:.2rem .65rem; font-size:.73rem; font-weight:700; display:inline-flex; align-items:center; gap:.3rem;">
        <i class="bi ${icon}" style="color:${color};"></i>${label}: <strong>${val}</strong>
    </span>`;
}
function soapField(lbl, val) {
    if (!val) return '';
    return `<div class="soap-field"><label>${lbl}</label><p>${escapeHtml(val)}</p></div>`;
}

// ── Render Diagnosa Panel ─────────────────────────────────────
function renderDiagnosaPanel(list) {
    const badge = document.getElementById('badge-diagnosa');
    if (badge) badge.textContent = list.length;

    const c = document.getElementById('diagnosa-list-container');
    if (!list.length) {
        c.innerHTML = '<div class="empty-state"><i class="bi bi-search-heart"></i><p>Belum ada diagnosa ditambahkan</p></div>';
        return;
    }
    const prioritasLabel = ['','Utama','Sekunder','Tambahan','Komorbid'];
    c.innerHTML = list.map(d => `
        <div class="diagnosa-item">
            <span class="kd">${d.kd_penyakit}</span>
            <span class="nm">${d.nm_penyakit ?? ''}</span>
            <span class="prioritas">${prioritasLabel[d.prioritas] ?? `P${d.prioritas}`}</span>
            <button class="btn-hapus-diagnosa" onclick="hapusDiagnosa('${d.kd_penyakit}')" title="Hapus">
                <i class="bi bi-trash3-fill"></i>
            </button>
        </div>
    `).join('');
}

// ── Render Tindakan Panel ─────────────────────────────────────
function renderTindakanPanel(list) {
    const badge = document.getElementById('badge-tindakan');
    if (badge) badge.textContent = list.length;

    const c = document.getElementById('tindakan-list-container');
    if (!list.length) {
        c.innerHTML = '<div class="empty-state"><i class="bi bi-activity"></i><p>Belum ada tindakan dicatat</p></div>';
        return;
    }
    c.innerHTML = list.map(t => `
        <div class="tindakan-item">
            <span class="nm">${escapeHtml(t.nm_perawatan ?? t.kd_jenis_prw)}</span>
            <span class="jenis ${(t.jenis??'Dokter').toLowerCase()}">${t.jenis ?? 'Dokter'}</span>
            <span class="tarif">${fmtRupiah(t.total_byr)}</span>
            <span class="waktu"><i class="bi bi-calendar3 me-1"></i>${fmtDate(t.tgl_perawatan)} ${(t.jam_rawat??'').substring(0,5)}</span>
            <button type="button" class="btn-hapus-diagnosa ms-2" onclick="hapusTindakan('${t.kd_jenis_prw}', '${t.tgl_perawatan}', '${t.jam_rawat}', '${t.jenis ?? 'Dokter'}')" title="Hapus tindakan">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `).join('');
}

// ── Render Resep Panel ────────────────────────────────────────
function renderResepPanel(list) {
    const badge = document.getElementById('badge-resep');
    if (badge) badge.textContent = list.length;

    const c = document.getElementById('resep-list-container');
    if (!list.length) {
        c.innerHTML = '<div class="empty-state"><i class="bi bi-capsule"></i><p>Belum ada resep</p></div>';
        return;
    }
    c.innerHTML = list.map(r => {
        const statusBadge = r.can_delete
            ? `<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Menunggu Validasi Farmasi</span>
               <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 ms-2 font-bold" onclick="batalResepRanap('${r.no_resep}')">
                   <i class="bi bi-trash me-1"></i>Batal Resep
               </button>`
            : `<span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Diserahkan Farmasi (${r.tgl_penyerahan} ${r.jam_penyerahan || ''})</span>`;

        let obatBody = '';
        if (r.obat_list && r.obat_list.length > 0) {
            obatBody += `<div class="mb-2">
                <div class="fw-bold text-success fs-8 mb-1"><i class="bi bi-capsule me-1"></i>Obat Jadi / Paten:</div>
                <table class="table table-sm table-bordered mb-1 text-xs" style="background:#fafafa;">
                    <thead><tr class="table-light"><th>Nama Obat</th><th style="width:70px;text-align:center;">Jumlah</th><th>Aturan Pakai</th><th>Keterangan</th></tr></thead>
                    <tbody>`;
            r.obat_list.forEach(o => {
                obatBody += `<tr>
                    <td><strong>${o.nama_brng || o.kode_brng}</strong></td>
                    <td class="text-center">${o.jml} ${o.kode_sat || ''}</td>
                    <td><code>${o.aturan_pakai || '-'}</code></td>
                    <td>${o.keterangan || '-'}</td>
                </tr>`;
            });
            obatBody += `</tbody></table></div>`;
        }

        if (r.racik_list && r.racik_list.length > 0) {
            obatBody += `<div class="mb-2">
                <div class="fw-bold text-primary fs-8 mb-1"><i class="bi bi-mortarboard-fill me-1"></i>Obat Racikan:</div>`;
            r.racik_list.forEach(rc => {
                obatBody += `
                    <div class="border rounded-2 p-2 mb-1.5" style="background:#f0f9ff; border-color:#bae6fd !important;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-primary fs-8"><i class="bi bi-box-seam me-1"></i>${rc.nama_racik} (${rc.metode || 'Racikan'}) - Jml: ${rc.jml_dr}</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">${rc.aturan_pakai || '-'}</span>
                        </div>
                        ${rc.keterangan ? `<div class="text-xs text-muted mb-1"><em>Ket: ${rc.keterangan}</em></div>` : ''}`;
                if (rc.detail && rc.detail.length > 0) {
                    obatBody += `<table class="table table-sm table-bordered mb-0 text-xs bg-white">
                        <thead><tr class="table-light"><th>Bahan Obat</th><th>P1 / P2</th><th>Kandungan</th><th style="width:70px;text-align:center;">Jml</th></tr></thead><tbody>`;
                    rc.detail.forEach(d => {
                        obatBody += `<tr>
                            <td>${d.nama_brng || d.kode_brng}</td>
                            <td>${d.p1 || 1} / ${d.p2 || 1}</td>
                            <td>${d.kandungan || '-'}</td>
                            <td class="text-center font-bold">${d.jml}</td>
                        </tr>`;
                    });
                    obatBody += `</tbody></table>`;
                }
                obatBody += `</div>`;
            });
            obatBody += `</div>`;
        }

        if (!r.obat_list?.length && !r.racik_list?.length) {
            obatBody = `<div class="obat-list">${r.detail_obat ?? '-'}</div>`;
        }

        return `
            <div class="resep-item mb-2.5">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-1 mb-1 border-bottom pb-1">
                    <div>
                        <span class="no-resep"><i class="bi bi-receipt me-1"></i>${r.no_resep}</span>
                        <span class="waktu ms-2"><i class="bi bi-clock me-1"></i>${fmtDate(r.tgl_perawatan)} ${(r.jam||r.jam_perawatan||'').substring(0,5)}</span>
                    </div>
                    <div class="d-flex align-items-center">${statusBadge}</div>
                </div>
                <div class="mb-2" style="font-size:.73rem; color:#059669; font-weight:700;">DPJP: ${r.nm_dokter ?? '-'}</div>
                ${obatBody}
            </div>`;
    }).join('');
}

// ── Render Lab Panel ──────────────────────────────────────────
function renderLabPanel(orders, results) {
    const badge = document.getElementById('badge-lab');
    if (badge) badge.textContent = results.length + orders.length;

    // Orders
    const oc = document.getElementById('lab-orders-container');
    if (!orders.length) {
        oc.innerHTML = '<div class="empty-state"><i class="bi bi-eyedropper"></i><p>Belum ada order lab</p></div>';
    } else {
        oc.innerHTML = orders.map(o => `
            <div class="tindakan-item" style="background:#f0fdf4; border-color:#bbf7d0; flex-direction:column; align-items:flex-start; gap:.3rem;">
                <span style="font-size:.7rem; font-weight:800; color:#059669;"><i class="bi bi-hash me-1"></i>${o.noorder}</span>
                <span style="font-size:.8rem; font-weight:600; color:#0f172a;">${o.detail_pemeriksaan ?? '-'}</span>
                <span class="waktu"><i class="bi bi-clock me-1"></i>${fmtDate(o.tgl_permintaan)} ${(o.jam_permintaan??'').substring(0,5)}</span>
            </div>
        `).join('');
    }

    // Results
    const rc = document.getElementById('lab-results-container');
    if (!results.length) {
        rc.innerHTML = '<div class="empty-state"><i class="bi bi-graph-up"></i><p>Belum ada hasil lab</p></div>';
    } else {
        // Group by date
        const grouped = {};
        results.forEach(r => {
            const key = r.tgl_periksa + ' ' + (r.jam??'').substring(0,5);
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(r);
        });
        rc.innerHTML = Object.entries(grouped).map(([dt, rows]) => `
            <div style="margin-bottom:1rem;">
                <div style="font-size:.72rem; font-weight:800; color:#1e40af; margin-bottom:.4rem; text-transform:uppercase; letter-spacing:.4px;">
                    <i class="bi bi-calendar3 me-1"></i>${dt}
                </div>
                ${rows.map(r => `
                <div class="lab-result-row">
                    <span class="pemeriksaan">${r.nama_pemeriksaan ?? '-'}</span>
                    <span class="nilai ${(r.keterangan??'').toLowerCase().includes('abnormal')||(!r.keterangan&&r.nilai&&r.nilai_rujukan&&r.nilai!==r.nilai_rujukan?'abnormal':'')}">${r.nilai ?? '-'}</span>
                    <span class="rujukan">${r.nilai_rujukan ?? ''} ${r.satuan ?? ''}</span>
                    <span class="keterangan">${r.keterangan ?? ''}</span>
                </div>`).join('')}
            </div>
        `).join('');
    }
}

// ── Render Radiologi Panel ────────────────────────────────────
function renderRadPanel(orders, results) {
    const badge = document.getElementById('badge-rad');
    if (badge) badge.textContent = results.length + orders.length;

    const oc = document.getElementById('rad-orders-container');
    if (!orders.length) {
        oc.innerHTML = '<div class="empty-state"><i class="bi bi-radioactive"></i><p>Belum ada order radiologi</p></div>';
    } else {
        oc.innerHTML = orders.map(o => `
            <div class="tindakan-item" style="flex-direction:column; align-items:flex-start; gap:.3rem;">
                <span style="font-size:.7rem; font-weight:800; color:#7c3aed;"><i class="bi bi-hash me-1"></i>${o.noorder}</span>
                <span style="font-size:.8rem; font-weight:600;">${o.detail_pemeriksaan ?? '-'}</span>
                <span class="waktu"><i class="bi bi-clock me-1"></i>${fmtDate(o.tgl_permintaan)}</span>
            </div>
        `).join('');
    }

    const rc = document.getElementById('rad-results-container');
    if (!results.length) {
        rc.innerHTML = '<div class="empty-state"><i class="bi bi-file-earmark-medical"></i><p>Belum ada hasil radiologi</p></div>';
    } else {
        rc.innerHTML = results.map(r => `
            <div class="resep-item" style="background:#faf5ff; border-color:#e9d5ff;">
                <div style="font-size:.7rem; font-weight:800; color:#7c3aed;">${fmtDate(r.tgl_periksa)} ${(r.jam??'').substring(0,5)}</div>
                <div style="font-size:.82rem; color:#0f172a; margin-top:.4rem; line-height:1.6; white-space:pre-wrap;">${escapeHtml(r.hasil ?? '-')}</div>
            </div>
        `).join('');
    }
}

// ── Render Riwayat Panel ──────────────────────────────────────
function renderRiwayatPanel(soapList, kamarList, awalMedis = null) {
    const c = document.getElementById('riwayat-container');
    if (!soapList.length && !kamarList.length && !awalMedis) {
        c.innerHTML = '<div class="empty-state"><i class="bi bi-clock-history"></i><p>Belum ada riwayat</p></div>';
        return;
    }

    let html = '';

    // Asesmen Awal Medis Ranap (jika sudah diinput dokter)
    if (awalMedis) {
        html += `<div class="soap-form-card mb-4" style="border-left:4px solid var(--ranap-primary);">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <h6 style="font-size:.88rem; font-weight:800; color:#0f172a; margin:0;">
                    <i class="bi bi-file-earmark-medical-fill me-2" style="color:var(--ranap-primary);"></i>
                    Asesmen Awal Medis Rawat Inap
                </h6>
                <span class="badge" style="background:rgba(30,64,175,.1); color:#1e40af; font-size:.72rem; font-weight:700;">
                    ${fmtDate(awalMedis.tanggal)} · ${escapeHtml(awalMedis.nm_dokter ?? awalMedis.kd_dokter)}
                </span>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-12 col-md-6">
                    <div style="font-size:.72rem; font-weight:800; color:#64748b;">ANAMNESIS (${awalMedis.anamnesis ?? 'Autoanamnesis'})</div>
                    <div style="font-size:.82rem; color:#0f172a; margin-top:.2rem;"><strong>Keluhan Utama:</strong> ${escapeHtml(awalMedis.keluhan_utama ?? '-')}</div>
                    ${awalMedis.rps ? `<div style="font-size:.8rem; color:#475569; margin-top:.2rem;"><strong>RPS:</strong> ${escapeHtml(awalMedis.rps)}</div>` : ''}
                    ${awalMedis.rpd ? `<div style="font-size:.8rem; color:#475569; margin-top:.2rem;"><strong>RPD:</strong> ${escapeHtml(awalMedis.rpd)}</div>` : ''}
                    ${awalMedis.alergi ? `<div style="font-size:.8rem; color:#dc2626; margin-top:.2rem;"><strong>Alergi:</strong> ${escapeHtml(awalMedis.alergi)}</div>` : ''}
                </div>
                <div class="col-12 col-md-6">
                    <div style="font-size:.72rem; font-weight:800; color:#64748b;">TANDA VITAL</div>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        ${vital('bi-heart-pulse','TD',awalMedis.td,'#dc2626')}
                        ${vital('bi-activity','N',awalMedis.nadi,'#1e40af')}
                        ${vital('bi-thermometer-half','S',awalMedis.suhu,'#d97706')}
                        ${vital('bi-lungs','RR',awalMedis.rr,'#059669')}
                        ${vital('bi-droplet','SpO2',awalMedis.spo,'#7c3aed')}
                        ${vital('bi-person-bounding-box','GCS',awalMedis.gcs,'#0284c7')}
                    </div>
                </div>
            </div>
            ${awalMedis.diagnosis ? `
            <div class="p-2 mb-2 rounded" style="background:#f8fafc; border:1px solid #e2e8f0;">
                <div style="font-size:.72rem; font-weight:800; color:#1e40af;">DIAGNOSIS AWAL</div>
                <div style="font-size:.83rem; font-weight:600; color:#0f172a;">${escapeHtml(awalMedis.diagnosis)}</div>
            </div>` : ''}
            ${awalMedis.tata ? `
            <div class="p-2 rounded" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                <div style="font-size:.72rem; font-weight:800; color:#059669;">RENCANA TERAPI / TATA LAKSANA</div>
                <div style="font-size:.82rem; color:#0f172a;">${escapeHtml(awalMedis.tata)}</div>
            </div>` : ''}
        </div>`;
    }

    // Kamar history
    if (kamarList.length) {
        html += `<h6 style="font-size:.82rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:1rem;">
            <i class="bi bi-door-open me-1"></i> Riwayat Kamar
        </h6>`;
        html += '<div class="timeline-riwayat mb-4">';
        kamarList.forEach(k => {
            html += `
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <div class="timeline-date">
                        ${fmtDate(k.tgl_masuk)} ${(k.jam_masuk??'').substring(0,5)} →
                        ${k.tgl_keluar && k.tgl_keluar !== '0000-00-00' ? fmtDate(k.tgl_keluar) + ' ' + (k.jam_keluar??'').substring(0,5) : '<span style="color:#059669;font-weight:700;">Masih dirawat</span>'}
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge-ward">${k.kd_kamar ?? '-'}</span>
                        <span style="font-size:.75rem; color:#475569;">${k.nm_bangsal ?? ''} ${k.kelas ? '· '+k.kelas : ''}</span>
                        ${k.lama ? `<span style="font-size:.73rem; color:#d97706; font-weight:700;">${k.lama} hari</span>` : ''}
                    </div>
                </div>
            </div>`;
        });
        html += '</div>';
    }

    // SOAP history
    if (soapList.length) {
        html += `<h6 style="font-size:.82rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:1rem;">
            <i class="bi bi-clipboard2-pulse me-1"></i> Timeline Catatan SOAP
        </h6>`;
        html += '<div class="timeline-riwayat">';
        soapList.forEach(s => {
            html += `
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <div class="timeline-date">
                        <i class="bi bi-calendar3 me-1"></i>${fmtDate(s.tgl_perawatan)}
                        <i class="bi bi-clock ms-2 me-1"></i>${(s.jam_rawat??'').substring(0,5)}
                        <span style="color:#059669; margin-left:.5rem;">${s.petugas ?? ''}</span>
                    </div>
                    ${s.tensi||s.nadi||s.suhu_tubuh ? `
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        ${vital('bi-heart-pulse','TD',s.tensi,'#dc2626')}
                        ${vital('bi-activity','N',s.nadi,'#1e40af')}
                        ${vital('bi-thermometer-half','S',s.suhu_tubuh,'#d97706')}
                        ${vital('bi-lungs','RR',s.respirasi,'#059669')}
                        ${vital('bi-droplet','SpO2',s.spo2,'#7c3aed')}
                    </div>` : ''}
                    ${s.keluhan   ? `<div class="mb-1"><label style="font-size:.65rem; font-weight:800; color:#94a3b8;">S:</label> <span style="font-size:.8rem;">${escapeHtml(s.keluhan)}</span></div>` : ''}
                    ${s.penilaian ? `<div class="mb-1"><label style="font-size:.65rem; font-weight:800; color:#94a3b8;">A:</label> <span style="font-size:.8rem;">${escapeHtml(s.penilaian)}</span></div>` : ''}
                    ${s.rtl       ? `<div class="mb-1"><label style="font-size:.65rem; font-weight:800; color:#94a3b8;">P:</label> <span style="font-size:.8rem;">${escapeHtml(s.rtl)}</span></div>` : ''}
                </div>
            </div>`;
        });
        html += '</div>';
    }
    c.innerHTML = html;
}

// ── Init Select2 Instances ────────────────────────────────────
let select2Inited = false;
function initSelect2() {
    if (select2Inited) {
        ['#diagnosa-select','#tindakan-select','#obat-select','#lab-select','#rad-select'].forEach(id => {
            try { $(id).val(null).trigger('change'); } catch(e){}
        });
        return;
    }
    select2Inited = true;

    const getActiveNoRawat = () => {
        return currentData?.pasien?.no_rawat || (currentNoRawat ? atob(currentNoRawat) : '');
    };

    // Diagnosa ICD-10
    $('#diagnosa-select').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#offcanvasRanap'),
        placeholder: 'Ketik kode atau nama penyakit ICD-10...',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: '/rawat-inap/master-diagnosa',
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: (data || []).map(d => ({
                    id: d.kd_penyakit,
                    text: `${d.kd_penyakit} — ${d.nm_penyakit}`
                }))
            }),
            cache: true
        }
    });

    // Tindakan (disesuaikan penjamin pasien ranap)
    $('#tindakan-select').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#offcanvasRanap'),
        placeholder: 'Cari tindakan rawat inap...',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: '/rawat-inap/master-tindakan',
            dataType: 'json',
            delay: 300,
            data: params => ({
                q: params.term,
                no_rawat: getActiveNoRawat()
            }),
            processResults: data => ({
                results: (data || []).map(d => ({
                    id: d.kd_jenis_prw,
                    text: `${d.nm_perawatan} — Rp ${Number(d.total_byr||0).toLocaleString('id-ID')}${d.png_jawab ? ' ['+d.png_jawab+']' : ''}`,
                    total_byr: d.total_byr
                }))
            }),
            cache: true
        }
    });

    // Obat / BHP (disesuaikan tarif kelas kamar pasien & depo ranap)
    $('#obat-select').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#offcanvasRanap'),
        placeholder: 'Cari obat / BHP farmasi...',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: '/rawat-inap/master-obat',
            dataType: 'json',
            delay: 300,
            data: params => ({
                q: params.term,
                no_rawat: getActiveNoRawat()
            }),
            processResults: data => ({
                results: (data || []).map(d => ({
                    id: d.kode_brng,
                    text: d.nama_brng,
                    nama_brng: d.nama_brng,
                    kode_sat: d.kode_sat,
                    harga: d.harga,
                    stok_depo: d.stok_depo !== undefined ? d.stok_depo : (d.stok ?? 0),
                    stok_total: d.stok_total !== undefined ? d.stok_total : (d.stok ?? 0),
                    kategori: d.kategori || '-'
                }))
            }),
            cache: true
        },
        templateResult: function(item) {
            if (item.loading) return item.text;
            const stokDepo = parseFloat(item.stok_depo || 0);
            const stokTotal = parseFloat(item.stok_total || 0);
            const depoBadge = stokDepo > 0 
                ? `<span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5">Depo Ranap: ${stokDepo}</span>`
                : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5">Depo Ranap: Kosong</span>`;
            const totalBadge = `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-1.5 py-0.5">Total RS: ${stokTotal}</span>`;
            const hargaFmt = Number(item.harga || 0).toLocaleString('id-ID');
            return $(`
                <div class="d-flex justify-content-between align-items-center py-1">
                    <div>
                        <div class="font-bold text-slate-800 text-xs">${item.nama_brng || item.text} <span class="text-slate-500 fw-normal">(${item.kode_sat || '-'})</span></div>
                        <div class="text-slate-500 text-xs">Kode: <code>${item.id}</code> | Rp ${hargaFmt}</div>
                    </div>
                    <div class="text-end d-flex flex-column gap-1 align-items-end text-xs">
                        ${depoBadge}
                        ${totalBadge}
                    </div>
                </div>
            `);
        },
        templateSelection: function(item) {
            if (!item.id) return item.text;
            const stokDepo = parseFloat(item.stok_depo || 0);
            const stokFmt = stokDepo > 0 ? `[Depo: ${stokDepo}]` : `[Depo: Habis]`;
            return `${item.nama_brng || item.text} (${item.kode_sat || '-'}) — Rp ${Number(item.harga||0).toLocaleString('id-ID')} ${stokFmt}`;
        }
    });

    // Lab (disesuaikan penjamin pasien)
    $('#lab-select').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#offcanvasRanap'),
        placeholder: 'Cari jenis pemeriksaan lab...',
        minimumInputLength: 2,
        ajax: {
            url: '/rawat-inap/master-lab',
            dataType: 'json',
            delay: 300,
            data: params => ({
                q: params.term,
                no_rawat: getActiveNoRawat()
            }),
            processResults: data => ({
                results: (data || []).map(d => ({
                    id: d.kd_jenis_prw,
                    text: `${d.nm_perawatan} — Rp ${Number(d.total_byr||0).toLocaleString('id-ID')}${d.png_jawab ? ' ['+d.png_jawab+']' : ''}`
                }))
            }),
            cache: true
        }
    });

    // Radiologi (disesuaikan penjamin pasien)
    $('#rad-select').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#offcanvasRanap'),
        placeholder: 'Cari jenis pemeriksaan radiologi...',
        minimumInputLength: 2,
        ajax: {
            url: '/rawat-inap/master-radiologi',
            dataType: 'json',
            delay: 300,
            data: params => ({
                q: params.term,
                no_rawat: getActiveNoRawat()
            }),
            processResults: data => ({
                results: (data || []).map(d => ({
                    id: d.kd_jenis_prw,
                    text: `${d.nm_perawatan} — Rp ${Number(d.total_byr||0).toLocaleString('id-ID')}${d.png_jawab ? ' ['+d.png_jawab+']' : ''}`
                }))
            }),
            cache: true
        }
    });
}

// ── Focus & Typing Fix inside Bootstrap Offcanvas ─────────────
$(document).ready(function() {
    $('#offcanvasRanap').on('shown.bs.offcanvas', function () {
        $(this).removeAttr('tabindex');
    });

    $(document).on('select2:open', function () {
        setTimeout(function () {
            const searchInput = document.querySelector('.select2-container--open .select2-search__field');
            if (searchInput) {
                searchInput.focus();
            }
        }, 50);
    });
});

// ── SOAP Actions ──────────────────────────────────────────────
function simpanSoap() {
    const btn = document.getElementById('btn-simpan-soap');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

    const noRawatDecoded = atob(currentNoRawat);
    const payload = {
        no_rawat:       noRawatDecoded,
        tgl_perawatan:  document.getElementById('soap-tgl').value,
        jam_rawat:      document.getElementById('soap-jam').value,
        tensi:          document.getElementById('sv-tensi').value,
        nadi:           document.getElementById('sv-nadi').value,
        respirasi:      document.getElementById('sv-respirasi').value,
        suhu_tubuh:     document.getElementById('sv-suhu').value,
        spo2:           document.getElementById('sv-spo2').value,
        gcs:            document.getElementById('sv-gcs').value,
        berat:          document.getElementById('sv-berat').value,
        kesadaran:      document.getElementById('sv-kesadaran').value,
        keluhan:        document.getElementById('soap-keluhan').value,
        pemeriksaan:    document.getElementById('soap-pemeriksaan').value,
        penilaian:      document.getElementById('soap-penilaian').value,
        rtl:            document.getElementById('soap-rtl').value,
        instruksi:      document.getElementById('soap-instruksi').value,
    };

    fetch('/rawat-inap/simpan-soap', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            // Refresh SOAP list
            refreshDetailPanel(currentNoRawat);
        } else {
            showToast(d.message, 'error');
        }
    })
    .catch(() => showToast('Terjadi kesalahan jaringan.', 'error'))
    .finally(() => {
        btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan SOAP';
    });
}

function resetSoapForm() {
    ['soap-keluhan','soap-pemeriksaan','soap-penilaian','soap-rtl','soap-instruksi'].forEach(id => document.getElementById(id).value = '');
    ['sv-tensi','sv-nadi','sv-respirasi','sv-suhu','sv-spo2','sv-gcs','sv-berat'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('sv-kesadaran').value = 'Compos Mentis';
}

// ── Diagnosa Actions ──────────────────────────────────────────
function tambahDiagnosa() {
    const selected = $('#diagnosa-select').val();
    const prioritas = document.getElementById('diagnosa-prioritas').value;

    if (!selected) { showToast('Pilih diagnosa terlebih dahulu.', 'warning'); return; }

    const noRawatDecoded = atob(currentNoRawat);
    fetch('/rawat-inap/simpan-diagnosa', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ no_rawat: noRawatDecoded, kd_penyakit: selected, prioritas, status: 'Ranap' })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            refreshDetailPanel(currentNoRawat);
            $('#diagnosa-select').val(null).trigger('change');
        } else {
            showToast(d.message, 'error');
        }
    })
    .catch(() => showToast('Gagal menambahkan diagnosa.', 'error'));
}

function hapusDiagnosa(kdPenyakit) {
    Swal.fire({
        title: 'Hapus Diagnosa?',
        text: `Hapus diagnosa ${kdPenyakit}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const noRawatDecoded = atob(currentNoRawat);
            fetch('/rawat-inap/hapus-diagnosa', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ no_rawat: noRawatDecoded, kd_penyakit: kdPenyakit })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) { showToast(d.message, 'success'); refreshDetailPanel(currentNoRawat); }
                else showToast(d.message, 'error');
            })
            .catch(() => showToast('Gagal menghapus diagnosa.', 'error'));
        }
    });
}

// ── Tindakan Actions ──────────────────────────────────────────
function tambahTindakan() {
    const selected = $('#tindakan-select').val();
    const tgl = document.getElementById('tindakan-tgl').value;
    const jam = document.getElementById('tindakan-jam').value;
    const jenis = document.getElementById('tindakan-jenis').value;

    if (!selected) { showToast('Pilih jenis tindakan terlebih dahulu.', 'warning'); return; }

    const noRawatDecoded = atob(currentNoRawat);
    fetch('/rawat-inap/simpan-tindakan', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ no_rawat: noRawatDecoded, kd_jenis_prw: selected, tgl_perawatan: tgl, jam_rawat: jam + ':00', jenis })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { showToast(d.message, 'success'); refreshDetailPanel(currentNoRawat); $('#tindakan-select').val(null).trigger('change'); }
        else showToast(d.message, 'error');
    })
    .catch(() => showToast('Gagal menyimpan tindakan.', 'error'));
}

function hapusTindakan(kdJenisPrw, tgl, jam, jenis) {
    Swal.fire({
        title: 'Hapus Tindakan?',
        text: 'Apakah Anda yakin ingin menghapus tindakan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const noRawatDecoded = atob(currentNoRawat);
            fetch('/rawat-inap/hapus-tindakan', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({
                    no_rawat: noRawatDecoded,
                    kd_jenis_prw: kdJenisPrw,
                    tgl_perawatan: tgl,
                    jam_rawat: jam,
                    jenis: jenis
                })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    showToast(d.message, 'success');
                    refreshDetailPanel(currentNoRawat);
                } else {
                    showToast(d.message, 'error');
                }
            })
            .catch(() => showToast('Gagal menghapus tindakan.', 'error'));
        }
    });
}

// ── Obat Cart & Racikan (Resep Ranap SIMRS Parity) ───────────
function updateResepSummaryRanap() {
    const el = document.getElementById('resepSummaryTextRanap');
    if (el) {
        el.innerHTML = `<i class="bi bi-cart3 me-1"></i> Total: <b>${obatCart.length}</b> Obat Jadi, <b>${racikanCart.length}</b> Racikan`;
    }
}

function tambahObatCart() {
    const selectedOption = $('#obat-select').select2('data')[0];
    if (!selectedOption || !selectedOption.id) { showToast('Pilih obat terlebih dahulu.', 'warning'); return; }

    if (obatCart.find(i => i.kode_brng === selectedOption.id)) {
        showToast('Obat sudah ada dalam keranjang.', 'warning'); return;
    }

    obatCart.push({
        kode_brng:    selectedOption.id,
        nama_brng:    selectedOption.nama_brng || selectedOption.text,
        kode_sat:     selectedOption.kode_sat ?? '',
        jml:          1,
        aturan_pakai: '3 X 1 Sehari',
        keterangan:   ''
    });

    renderObatCart();
    updateResepSummaryRanap();
    $('#obat-select').val(null).trigger('change');
}

function renderObatCart() {
    const c = document.getElementById('obat-cart');
    if (!c) return;
    if (!obatCart.length) {
        c.innerHTML = '<div style="text-align:center; padding:1.5rem; color:#94a3b8; font-size:.82rem; font-weight:600;">Keranjang resep obat jadi kosong. Cari dan tambahkan obat di atas.</div>';
        return;
    }
    c.innerHTML = obatCart.map((item, idx) => `
        <div class="obat-cart-item mb-2 p-2 border rounded-2 bg-white d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div style="min-width:180px; flex:1;">
                <div class="obat-cart-name font-bold text-slate-800 text-xs">${item.nama_brng}</div>
                <div style="font-size:.7rem; color:#64748b;">Kode: ${item.kode_brng} ${item.kode_sat ? '('+item.kode_sat+')' : ''}</div>
            </div>
            <div style="width:85px;">
                <label style="font-size:.68rem; font-weight:700; color:#64748b; text-transform:uppercase;">Jumlah</label>
                <input type="number" class="form-control form-control-sm text-center" min="0.5" step="0.5" value="${item.jml}"
                    onchange="obatCart[${idx}].jml = parseFloat(this.value)||1">
            </div>
            <div style="width:200px;">
                <label style="font-size:.68rem; font-weight:700; color:#64748b; text-transform:uppercase;">Aturan Pakai</label>
                <input type="text" list="listAturanPakaiRanap" class="form-control form-control-sm" placeholder="3 X 1 Sehari..." value="${item.aturan_pakai}"
                    onchange="obatCart[${idx}].aturan_pakai = this.value">
            </div>
            <div style="width:150px;">
                <label style="font-size:.68rem; font-weight:700; color:#64748b; text-transform:uppercase;">Keterangan</label>
                <input type="text" class="form-control form-control-sm" placeholder="Sesudah makan..." value="${item.keterangan || ''}"
                    onchange="obatCart[${idx}].keterangan = this.value">
            </div>
            <button class="btn btn-sm btn-outline-danger py-1 px-2" onclick="hapusObatCart(${idx})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `).join('');
}

function hapusObatCart(idx) {
    obatCart.splice(idx, 1);
    renderObatCart();
    updateResepSummaryRanap();
}

function clearObatCart() {
    obatCart = [];
    racikanCart = [];
    renderObatCart();
    renderRacikanCart();
    updateResepSummaryRanap();
}

// ── Racikan Builder Ranap ──────────────────────────────────────
function buatGrupRacikanRanap() {
    const nama = document.getElementById('racikNamaRanap')?.value.trim();
    const metode = document.getElementById('racikMetodeRanap')?.value || 'R01';
    const metodeSel = document.getElementById('racikMetodeRanap');
    const metodeName = metodeSel ? metodeSel.options[metodeSel.selectedIndex].text : 'Puyer';
    const jml = parseInt(document.getElementById('racikJmlRanap')?.value) || 10;
    const aturan = document.getElementById('racikAturanRanap')?.value.trim() || '3 X 1 Sehari';
    const ket = document.getElementById('racikKetRanap')?.value.trim() || '';

    if (!nama) {
        showToast('Masukkan nama racikan terlebih dahulu.', 'warning');
        return;
    }

    racikanCart.push({
        no_racik: racikanCart.length + 1,
        nama_racik: nama,
        kd_racik: metode,
        metode_name: metodeName,
        jml_dr: jml,
        aturan_pakai: aturan,
        keterangan: ket,
        detail: []
    });

    if (document.getElementById('racikNamaRanap')) document.getElementById('racikNamaRanap').value = '';
    if (document.getElementById('racikKetRanap')) document.getElementById('racikKetRanap').value = '';

    renderRacikanCart();
    updateResepSummaryRanap();
    showToast(`Racikan "${nama}" berhasil dibuat. Silakan tambahkan bahan obat.`, 'success');
}

function renderRacikanCart() {
    const c = document.getElementById('daftarRacikanContainerRanap');
    if (!c) return;

    if (!racikanCart.length) {
        c.innerHTML = '<div class="text-center py-3 border rounded-3 bg-light text-muted fs-7">Belum ada obat racikan dibuat. Silakan isi form di atas dan klik <b>Buat Racikan</b>.</div>';
        return;
    }

    c.innerHTML = racikanCart.map((r, rIdx) => {
        let bahanRows = '';
        if (r.detail && r.detail.length > 0) {
            bahanRows = r.detail.map((d, dIdx) => `
                <tr>
                    <td><strong>${d.nama_brng}</strong><div class="text-muted text-xs">Kode: ${d.kode_brng}</div></td>
                    <td style="width:110px;">
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control form-control-sm text-center px-1" min="1" value="${d.p1 || 1}"
                                onchange="updateBahanP1Ranap(${rIdx}, ${dIdx}, this.value)">
                            <span class="input-group-text px-1">/</span>
                            <input type="number" class="form-control form-control-sm text-center px-1" min="1" value="${d.p2 || 1}"
                                onchange="updateBahanP2Ranap(${rIdx}, ${dIdx}, this.value)">
                        </div>
                    </td>
                    <td style="width:110px;">
                        <input type="text" class="form-control form-control-sm" value="${d.kandungan || ''}"
                            onchange="racikanCart[${rIdx}].detail[${dIdx}].kandungan = this.value">
                    </td>
                    <td style="width:100px; text-align:center;">
                        <input type="number" class="form-control form-control-sm text-center fw-bold text-primary" min="0.1" step="any" value="${d.jml}" id="jml_ranap_${rIdx}_${dIdx}"
                            onchange="racikanCart[${rIdx}].detail[${dIdx}].jml = parseFloat(this.value)||1">
                    </td>
                    <td style="width:40px; text-align:center;">
                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" onclick="hapusBahanRacikRanap(${rIdx}, ${dIdx})">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        } else {
            bahanRows = '<tr><td colspan="5" class="text-center text-muted py-2 fs-8">Belum ada bahan obat dalam racikan ini.</td></tr>';
        }

        return `
            <div class="card p-3 mb-3 border text-xs" style="border-radius:10px; background:#f0f9ff; border-color:#bae6fd !important;">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-1">
                    <div>
                        <span class="badge bg-primary me-1">${r.metode_name}</span>
                        <strong class="text-primary fs-7">${r.nama_racik}</strong>
                        <span class="text-slate-600 ms-2">Kemasan: <b>${r.jml_dr}</b> | Aturan: <b>${r.aturan_pakai}</b></span>
                        ${r.keterangan ? `<span class="text-muted ms-2">(${r.keterangan})</span>` : ''}
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 font-bold" onclick="hapusGrupRacikRanap(${rIdx})">
                        <i class="bi bi-trash me-1"></i> Hapus Racikan
                    </button>
                </div>

                {{-- Search & Tambah Bahan Inline --}}
                <div class="p-2 border rounded-2 bg-white mb-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label font-bold text-xs text-slate-600 mb-0">Cari Bahan Obat</label>
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm search-bahan-ranap-input" data-ridx="${rIdx}" placeholder="Ketik nama bahan obat...">
                                <div class="search-results-dropdown dropdown-bahan-ranap-${rIdx}" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:1050; background:#fff; border:1px solid #cbd5e1; max-height:200px; overflow-y:auto; border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,0.1);"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-bold text-xs text-slate-600 mb-0">P1 / P2</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control form-control-sm text-center" id="p1_ranap_${rIdx}" value="1" min="1" oninput="hitungJmlBahanRanap(${rIdx})">
                                <span class="input-group-text px-1">/</span>
                                <input type="number" class="form-control form-control-sm text-center" id="p2_ranap_${rIdx}" value="1" min="1" oninput="hitungJmlBahanRanap(${rIdx})">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-bold text-xs text-slate-600 mb-0">Kandungan (mg)</label>
                            <input type="text" class="form-control form-control-sm" id="kandungan_ranap_${rIdx}" placeholder="Misal: 500">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-0">Jml Butuh Obat</label>
                            <input type="number" class="form-control form-control-sm fw-bold text-primary" id="jmlBahan_ranap_${rIdx}" value="${r.jml_dr}" min="0.1" step="any">
                        </div>
                    </div>
                </div>

                {{-- Tabel Bahan Obat Racik --}}
                <div class="table-responsive border rounded-2 bg-white">
                    <table class="table table-sm table-bordered align-middle mb-0 text-xs">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Bahan Obat</th>
                                <th style="width:110px; text-align:center;">P1 / P2</th>
                                <th style="width:110px;">Kandungan</th>
                                <th style="width:100px; text-align:center;">Jml Butuh</th>
                                <th style="width:40px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${bahanRows}
                        </tbody>
                    </table>
                </div>
            </div>`;
    }).join('');
}

function hitungJmlBahanRanap(rIdx) {
    const p1 = parseFloat(document.getElementById(`p1_ranap_${rIdx}`)?.value) || 1;
    const p2 = parseFloat(document.getElementById(`p2_ranap_${rIdx}`)?.value) || 1;
    const kemasan = parseFloat(racikanCart[rIdx]?.jml_dr) || 1;
    const jml = Math.ceil((p1 / p2) * kemasan);
    const jmlInput = document.getElementById(`jmlBahan_ranap_${rIdx}`);
    if (jmlInput) jmlInput.value = jml;
}

function updateBahanP1Ranap(rIdx, dIdx, val) {
    const p1 = parseFloat(val) || 1;
    racikanCart[rIdx].detail[dIdx].p1 = p1;
    const p2 = parseFloat(racikanCart[rIdx].detail[dIdx].p2) || 1;
    const kemasan = parseFloat(racikanCart[rIdx]?.jml_dr) || 1;
    const newJml = Math.ceil((p1 / p2) * kemasan);
    racikanCart[rIdx].detail[dIdx].jml = newJml;
    const el = document.getElementById(`jml_ranap_${rIdx}_${dIdx}`);
    if (el) el.value = newJml;
}

function updateBahanP2Ranap(rIdx, dIdx, val) {
    const p2 = parseFloat(val) || 1;
    racikanCart[rIdx].detail[dIdx].p2 = p2;
    const p1 = parseFloat(racikanCart[rIdx].detail[dIdx].p1) || 1;
    const kemasan = parseFloat(racikanCart[rIdx]?.jml_dr) || 1;
    const newJml = Math.ceil((p1 / p2) * kemasan);
    racikanCart[rIdx].detail[dIdx].jml = newJml;
    const el = document.getElementById(`jml_ranap_${rIdx}_${dIdx}`);
    if (el) el.value = newJml;
}

// Search Bahan Racik Ranap
$(document).on('keyup', '.search-bahan-ranap-input', function () {
    const input = $(this);
    const q = input.val().trim();
    const rIdx = input.data('ridx');
    const dropdown = $(`.dropdown-bahan-ranap-${rIdx}`);

    if (q.length < 2) { dropdown.hide(); return; }

    const noRawat = getActiveNoRawat();
    $.ajax({
        url: '/rawat-inap/master-obat',
        type: 'GET',
        data: { q: q, no_rawat: noRawat },
        success: function (data) {
            let html = '';
            if (data && data.length > 0) {
                data.forEach(item => {
                    const satStr  = item.kode_sat ? ` (${item.kode_sat})` : '';
                    const stokDepo = parseFloat(item.stok_depo !== undefined ? item.stok_depo : (item.stok ?? 0));
                    const stokTotal = parseFloat(item.stok_total !== undefined ? item.stok_total : stokDepo);
                    const depoBadge = stokDepo > 0 
                        ? `<span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5">Depo Ranap: ${stokDepo}</span>`
                        : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5">Depo Ranap: Kosong</span>`;
                    const totalBadge = `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-1.5 py-0.5 ms-1">Total RS: ${stokTotal}</span>`;

                    html += `
                        <div class="dropdown-item select-bahan-ranap-item py-1.5 px-2.5 border-bottom cursor-pointer d-flex justify-content-between align-items-center"
                             data-ridx="${rIdx}"
                             data-kode="${item.id || item.kode_brng}"
                             data-nama="${item.nama_brng || item.text}"
                             data-satuan="${item.kode_sat || ''}"
                             data-kapasitas="${item.kapasitas || 0}">
                            <div>
                                <div class="font-bold text-slate-800 text-xs">${item.nama_brng || item.text}${satStr}</div>
                                <div class="text-slate-500 text-xs">Kode: <code>${item.id || item.kode_brng}</code> | Rp ${Number(item.harga||0).toLocaleString('id-ID')}</div>
                            </div>
                            <div class="text-end">
                                ${depoBadge}
                                ${totalBadge}
                            </div>
                        </div>`;
                });
            } else {
                html = `<div class="p-2.5 text-muted text-xs text-center">Bahan obat tidak ditemukan</div>`;
            }
            dropdown.html(html).show();
        }
    });
});

$(document).on('click', '.select-bahan-ranap-item', function () {
    const rIdx = $(this).data('ridx');
    const kode = $(this).data('kode');
    const nama = $(this).data('nama');
    const kapasitas = parseFloat($(this).data('kapasitas')) || 0;

    const p1 = parseFloat($(`#p1_ranap_${rIdx}`).val()) || 1;
    const p2 = parseFloat($(`#p2_ranap_${rIdx}`).val()) || 1;
    let kandungan = $(`#kandungan_ranap_${rIdx}`).val() || (kapasitas > 0 ? kapasitas : '');
    const kemasan = parseFloat(racikanCart[rIdx]?.jml_dr) || 1;
    let jml = parseFloat($(`#jmlBahan_ranap_${rIdx}`).val()) || Math.ceil((p1 / p2) * kemasan);

    racikanCart[rIdx].detail.push({
        kode_brng: kode,
        nama_brng: nama,
        p1: p1,
        p2: p2,
        kandungan: kandungan,
        jml: jml
    });

    $(`.search-bahan-ranap-input[data-ridx="${rIdx}"]`).val('');
    $(`.dropdown-bahan-ranap-${rIdx}`).hide();
    renderRacikanCart();
});

function hapusGrupRacikRanap(rIdx) {
    racikanCart.splice(rIdx, 1);
    renderRacikanCart();
    updateResepSummaryRanap();
}

function hapusBahanRacikRanap(rIdx, dIdx) {
}

// ── Simpan E-Resep Ranap (Obat Jadi & Racikan) ───────────────────
function simpanResep() {
    if (!obatCart.length && !racikanCart.length) {
        showToast('Pilih minimal 1 obat jadi atau 1 racikan untuk resep.', 'warning');
        return;
    }

    for (let i = 0; i < racikanCart.length; i++) {
        if (!racikanCart[i].detail || !racikanCart[i].detail.length) {
            showToast(`Racikan "${racikanCart[i].nama_racik}" belum memiliki bahan obat.`, 'warning');
            return;
        }
    }

    const btn = document.getElementById('btn-simpan-resep');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...';

    const noRawatDecoded = atob(currentNoRawat);
    fetch('/rawat-inap/simpan-resep', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            no_rawat:      noRawatDecoded,
            tgl_perawatan: document.getElementById('resep-tgl').value,
            items:         obatCart.map(i => ({
                kode_brng:    i.kode_brng,
                jml:          i.jml,
                aturan_pakai: i.aturan_pakai,
                keterangan:   i.keterangan || ''
            })),
            racikan:       racikanCart.map((r, idx) => ({
                no_racik:     idx + 1,
                nama_racik:   r.nama_racik,
                kd_racik:     r.kd_racik,
                jml_dr:       r.jml_dr,
                aturan_pakai: r.aturan_pakai,
                keterangan:   r.keterangan || '',
                detail:       r.detail.map(d => ({
                    kode_brng: d.kode_brng,
                    p1:        d.p1 || 1,
                    p2:        d.p2 || 1,
                    kandungan: d.kandungan || 0,
                    jml:       d.jml || 1
                }))
            }))
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            clearObatCart();
            refreshDetailPanel(currentNoRawat);
        } else {
            showToast(d.message, 'error');
        }
    })
    .catch(() => showToast('Gagal mengirim resep.', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-1"></i> Kirim Resep ke Farmasi';
    });
}

// ── Batal E-Resep Ranap ──────────────────────────────────────────
function batalResepRanap(noResep) {
    Swal.fire({
        title: 'Batalkan E-Resep?',
        html: `Apakah Anda yakin ingin membatalkan resep <b>${noResep}</b>?<br><small class="text-danger">Resep hanya dapat dibatalkan jika belum diserahkan oleh Farmasi.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Batalkan',
        cancelButtonText: 'Tutup'
    }).then((result) => {
        if (result.isConfirmed) {
            const noRawatDecoded = atob(currentNoRawat);
            fetch('/rawat-inap/hapus-resep', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({
                    no_resep: noResep,
                    no_rawat: noRawatDecoded
                })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast(res.message, 'success');
                    refreshDetailPanel(currentNoRawat);
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Gagal membatalkan resep.', 'error'));
        }
    });
}
window.batalResepRanap = batalResepRanap;

// ================================================================
// ── Resume Medis Ranap (resume_pasien_ranap) ─────────────────────
// ================================================================
function renderResumeRanap(resm, d) {
    const nr = d.pasien ? d.pasien.no_rawat : (currentNoRawat ? atob(currentNoRawat) : '');
    const nrInput = document.getElementById('resumeRanapNoRawat');
    if (nrInput) nrInput.value = nr;

    const badge = document.getElementById('badge-resume');
    const statusBadge = document.getElementById('resumeRanapStatusBadge');

    if (resm) {
        if (badge) badge.textContent = '1';
        if (statusBadge) {
            statusBadge.innerHTML = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 font-bold"><i class="bi bi-check-circle me-1"></i>Tersimpan di SIMRS</span>';
        }

        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.value = (val !== null && val !== undefined && val !== '-') ? val : '';
        };

        setVal('rr_diagnosa_awal', resm.diagnosa_awal);
        setVal('rr_alasan', resm.alasan);
        setVal('rr_keluhan_utama', resm.keluhan_utama);
        setVal('rr_pemeriksaan_fisik', resm.pemeriksaan_fisik);
        setVal('rr_jalannya_penyakit', resm.jalannya_penyakit);
        setVal('rr_pemeriksaan_penunjang', resm.pemeriksaan_penunjang);
        setVal('rr_hasil_laborat', resm.hasil_laborat);
        setVal('rr_tindakan_dan_operasi', resm.tindakan_dan_operasi);
        setVal('rr_obat_di_rs', resm.obat_di_rs);
        setVal('rr_diagnosa_utama', resm.diagnosa_utama);
        setVal('rr_kd_diagnosa_utama', resm.kd_diagnosa_utama);
        setVal('rr_diagnosa_sekunder', resm.diagnosa_sekunder);
        setVal('rr_kd_diagnosa_sekunder', resm.kd_diagnosa_sekunder);
        setVal('rr_diagnosa_sekunder2', resm.diagnosa_sekunder2);
        setVal('rr_kd_diagnosa_sekunder2', resm.kd_diagnosa_sekunder2);
        setVal('rr_diagnosa_sekunder3', resm.diagnosa_sekunder3);
        setVal('rr_kd_diagnosa_sekunder3', resm.kd_diagnosa_sekunder3);
        setVal('rr_prosedur_utama', resm.prosedur_utama);
        setVal('rr_kd_prosedur_utama', resm.kd_prosedur_utama);
        setVal('rr_prosedur_sekunder', resm.prosedur_sekunder);
        setVal('rr_kd_prosedur_sekunder', resm.kd_prosedur_sekunder);
        setVal('rr_cara_keluar', resm.cara_keluar || 'Atas Izin Dokter');
        setVal('rr_ket_keluar', resm.ket_keluar);
        setVal('rr_keadaan', resm.keadaan || 'Membaik');
        setVal('rr_ket_keadaan', resm.ket_keadaan);
        setVal('rr_dilanjutkan', resm.dilanjutkan || 'Kembali Ke RS');
        setVal('rr_ket_dilanjutkan', resm.ket_dilanjutkan);
        setVal('rr_kontrol', resm.kontrol);
        setVal('rr_diet', resm.diet);
        setVal('rr_alergi', resm.alergi);
        setVal('rr_lab_belum', resm.lab_belum);
        setVal('rr_obat_pulang', resm.obat_pulang);
        setVal('rr_edukasi', resm.edukasi);
    } else {
        if (badge) badge.textContent = '0';
        if (statusBadge) {
            statusBadge.innerHTML = '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1"><i class="bi bi-info-circle me-1"></i>Belum dibuat / disimpan</span>';
        }
        const form = document.getElementById('formResumeRanap');
        if (form) form.reset();
        if (nrInput) nrInput.value = nr;
    }
}

function autoFillResumeRanap() {
    if (!currentData || !currentData.pasien) {
        showToast('Data pasien belum dimuat sepenuhnya.', 'warning');
        return;
    }
    const d = currentData;
    const p = d.pasien;

    // Diagnosa Awal & Alasan
    const setIfEmpty = (id, val) => {
        const el = document.getElementById(id);
        if (el && (!el.value || el.value === '-') && val) el.value = val;
    };

    setIfEmpty('rr_diagnosa_awal', p.diagnosa_awal || (d.awalMedisRanap ? d.awalMedisRanap.keluhan_utama : ''));
    setIfEmpty('rr_alasan', d.awalMedisRanap ? d.awalMedisRanap.riwayat_penyakit : (p.alasan_masuk || 'Indikasi Perawatan Rawat Inap'));

    // Keluhan & Fisik dari CPPT pertama / Awal Medis
    if (d.soapList && d.soapList.length > 0) {
        const sFirst = d.soapList[d.soapList.length - 1]; // CPPT terawal
        setIfEmpty('rr_keluhan_utama', sFirst.keluhan || (d.awalMedisRanap ? d.awalMedisRanap.keluhan_utama : ''));
        setIfEmpty('rr_pemeriksaan_fisik', sFirst.pemeriksaan || '');

        // Jalannya penyakit: ringkasan CPPT
        const kronologi = d.soapList.map(s => `[${fmtDate(s.tgl_perawatan)}] Assessment: ${s.penilaian || '-'}; Instruksi: ${s.instruksi || '-'}`).join('\n');
        setIfEmpty('rr_jalannya_penyakit', kronologi);
    } else if (d.awalMedisRanap) {
        setIfEmpty('rr_keluhan_utama', d.awalMedisRanap.keluhan_utama);
        setIfEmpty('rr_pemeriksaan_fisik', d.awalMedisRanap.pemeriksaan_fisik);
    }

    // Pemeriksaan Penunjang (Radiologi)
    if (d.radOrders && d.radOrders.length > 0) {
        const radStr = d.radOrders.map(ro => `${ro.tgl_permintaan}: ${ro.detail_pemeriksaan}`).join('; ');
        setIfEmpty('rr_pemeriksaan_penunjang', radStr);
    }

    // Hasil Lab
    if (d.labOrders && d.labOrders.length > 0) {
        const labStr = d.labOrders.map(lo => `${lo.tgl_permintaan}: ${lo.detail_pemeriksaan}`).join('; ');
        setIfEmpty('rr_hasil_laborat', labStr);
    }

    // Tindakan & Operasi
    if (d.tindakanList && d.tindakanList.length > 0) {
        const tdkStr = d.tindakanList.map(t => `${fmtDate(t.tgl_perawatan)}: ${t.nm_perawatan}`).join('; ');
        setIfEmpty('rr_tindakan_dan_operasi', tdkStr);

        // Map ke Prosedur Utama & Sekunder
        setIfEmpty('rr_prosedur_utama', d.tindakanList[0].nm_perawatan);
        setIfEmpty('rr_kd_prosedur_utama', d.tindakanList[0].kd_jenis_prw);
        if (d.tindakanList[1]) {
            setIfEmpty('rr_prosedur_sekunder', d.tindakanList[1].nm_perawatan);
            setIfEmpty('rr_kd_prosedur_sekunder', d.tindakanList[1].kd_jenis_prw);
        }
    }

    // Obat di RS & Obat Pulang
    if (d.resepList && d.resepList.length > 0) {
        let obatRS = [];
        let obatPlg = [];
        d.resepList.forEach(r => {
            if (r.obat_list && r.obat_list.length > 0) {
                r.obat_list.forEach(o => {
                    obatRS.push(`${o.nama_brng} (${o.jml} ${o.kode_sat}) - ${o.aturan_pakai || '-'}`);
                });
            }
            if (r.racik_list && r.racik_list.length > 0) {
                r.racik_list.forEach(rc => {
                    obatRS.push(`Racikan: ${rc.nama_racik} (${rc.jml_dr} kemasan) - ${rc.aturan_pakai || '-'}`);
                });
            }
        });
        setIfEmpty('rr_obat_di_rs', obatRS.join('\n'));

        // Resep terakhir sebagai obat pulang awal
        const lastResep = d.resepList[0];
        if (lastResep) {
            if (lastResep.obat_list && lastResep.obat_list.length > 0) {
                lastResep.obat_list.forEach(o => obatPlg.push(`${o.nama_brng} No. ${o.jml} (${o.aturan_pakai || '-'})`));
            }
            if (lastResep.racik_list && lastResep.racik_list.length > 0) {
                lastResep.racik_list.forEach(rc => obatPlg.push(`Racikan: ${rc.nama_racik} No. ${rc.jml_dr} (${rc.aturan_pakai || '-'})`));
            }
            setIfEmpty('rr_obat_pulang', obatPlg.join('\n'));
        }
    }

    // Diagnosa ICD-10
    if (d.diagnosaList && d.diagnosaList.length > 0) {
        const d1 = d.diagnosaList[0];
        if (d1) {
            setIfEmpty('rr_diagnosa_utama', d1.nm_penyakit);
            setIfEmpty('rr_kd_diagnosa_utama', d1.kd_penyakit);
        }
        const d2 = d.diagnosaList[1];
        if (d2) {
            setIfEmpty('rr_diagnosa_sekunder', d2.nm_penyakit);
            setIfEmpty('rr_kd_diagnosa_sekunder', d2.kd_penyakit);
        }
        const d3 = d.diagnosaList[2];
        if (d3) {
            setIfEmpty('rr_diagnosa_sekunder2', d3.nm_penyakit);
            setIfEmpty('rr_kd_diagnosa_sekunder2', d3.kd_penyakit);
        }
        const d4 = d.diagnosaList[3];
        if (d4) {
            setIfEmpty('rr_diagnosa_sekunder3', d4.nm_penyakit);
            setIfEmpty('rr_kd_diagnosa_sekunder3', d4.kd_penyakit);
        }
    }

    setIfEmpty('rr_cara_keluar', 'Atas Izin Dokter');
    setIfEmpty('rr_keadaan', 'Membaik');
    setIfEmpty('rr_dilanjutkan', 'Kembali Ke RS');
    setIfEmpty('rr_edukasi', 'Istirahat cukup, minum obat teratur sesuai petunjuk, dan kontrol ulang ke poliklinik.');

    showToast('Data resume ranap berhasil ditarik otomatis dari CPPT, Diagnosa & Resep.', 'success');
}

function simpanResumeRanap() {
    const noRawat = document.getElementById('resumeRanapNoRawat')?.value;
    if (!noRawat) {
        showToast('Nomor rawat pasien belum dipilih.', 'warning');
        return;
    }

    const btn = document.getElementById('btnSimpanResumeRanap');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
    }

    const form = document.getElementById('formResumeRanap');
    const formData = new FormData(form);
    const dataObj = {};
    formData.forEach((value, key) => dataObj[key] = value);

    fetch('/rawat-inap/simpan-resume', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: JSON.stringify(dataObj)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showToast(res.message, 'success');
            const statusBadge = document.getElementById('resumeRanapStatusBadge');
            if (statusBadge) {
                statusBadge.innerHTML = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 font-bold"><i class="bi bi-check-circle me-1"></i>Tersimpan di SIMRS</span>';
            }
            const badge = document.getElementById('badge-resume');
            if (badge) badge.textContent = '1';
        } else {
            showToast(res.message || 'Gagal menyimpan resume.', 'error');
        }
    })
    .catch(() => showToast('Gagal menyimpan resume ranap.', 'error'))
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save-fill me-1"></i> Simpan Resume Medis Rawat Inap';
        }
    });
}

// ================================================================
//  FITUR TEMPLATE RESUME RANAP (SIMRS NAMIRA PARITY)
// ================================================================
let cachedResumeRanapTemplates = [];
let cachedKhanzaRanapTemplates = [];

function bukaModalPilihTemplateRanap() {
    loadTemplateResumeRanapList();
    const modalEl = document.getElementById('modalPilihTemplateResumeRanap');
    if (modalEl && window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if (window.jQuery) {
        $('#modalPilihTemplateResumeRanap').modal('show');
    }
}
window.bukaModalPilihTemplateRanap = bukaModalPilihTemplateRanap;

function loadTemplateResumeRanapList(searchQuery = '') {
    const c = document.getElementById('listTemplateResumeRanapContainer');
    if (c) c.innerHTML = '<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat template...</div>';

    fetch('/rawat-inap/template-resume?q=' + encodeURIComponent(searchQuery), {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(res => {
        cachedResumeRanapTemplates = res.templates || [];
        cachedKhanzaRanapTemplates = res.khanzaTemplates || [];
        const cntResume = document.getElementById('countTmplResumeRanap');
        const cntKhanza = document.getElementById('countTmplKhanzaRanap');
        if (cntResume) cntResume.textContent = cachedResumeRanapTemplates.length;
        if (cntKhanza) cntKhanza.textContent = cachedKhanzaRanapTemplates.length;
        renderTemplateResumeRanapCards(cachedResumeRanapTemplates);
        renderTemplateKhanzaRanapCards(cachedKhanzaRanapTemplates);
    })
    .catch(() => {
        if (c) c.innerHTML = '<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat template resume ranap.</div>';
    });
}

function renderTemplateResumeRanapCards(list) {
    const c = document.getElementById('listTemplateResumeRanapContainer');
    if (!c) return;
    if (!list || list.length === 0) {
        c.innerHTML = `
            <div class="text-center py-4 px-3 border rounded-3 bg-white text-muted">
                <i class="bi bi-folder-x fs-2 opacity-50 d-block mb-1"></i>
                <div class="fw-bold fs-7 text-slate-700">Belum Ada Template Resume Ranap</div>
                <div class="text-xs text-slate-500 mt-1">Gunakan tombol <b>"Simpan Sebagai Template"</b> pada form resume untuk membuat template baru.</div>
            </div>
        `;
        return;
    }

    let html = '';
    list.forEach((t, idx) => {
        const diagStr = t.diagnosa_utama ? `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 me-1">${escapeHtml(t.kd_diagnosa_utama ? t.kd_diagnosa_utama + ' — ' : '')}${escapeHtml(t.diagnosa_utama)}</span>` : '';
        const ownerBadge = t.kd_dokter ? '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Dokter Pribadi</span>' : '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Umum / Rumah Sakit</span>';

        html += `
            <div class="card p-3 border shadow-xs bg-white" style="border-radius:12px; transition:transform .15s, box-shadow .15s;">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap">
                    <div>
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <h6 class="fw-bold text-slate-800 mb-0 fs-7">${escapeHtml(t.nama_template)}</h6>
                            ${ownerBadge}
                        </div>
                        <div class="mt-1">${diagStr}</div>
                    </div>
                    <div class="d-flex gap-1.5">
                        <button type="button" class="btn btn-sm btn-success font-bold text-xs py-1 px-2.5 rounded-2" onclick="terapkanTemplateResumeRanap(${idx})" title="Gunakan template ini">
                            <i class="bi bi-check2-circle me-1"></i> Gunakan Template
                        </button>
                        ${t.kd_dokter ? `
                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-2" onclick="hapusTemplateResumeRanap(${t.id}, '${escapeHtml(t.nama_template).replace(/'/g, "\\'")}')" title="Hapus template ini">
                            <i class="bi bi-trash"></i>
                        </button>` : ''}
                    </div>
                </div>
                <div class="row g-2 text-xs text-slate-600 border-top pt-2 mt-1">
                    ${t.diagnosa_awal ? `<div class="col-md-6"><b>Diag Awal:</b> <span class="text-slate-700">${escapeHtml(t.diagnosa_awal)}</span></div>` : ''}
                    ${t.keluhan_utama ? `<div class="col-md-6"><b>Keluhan:</b> <span class="text-slate-700">${escapeHtml(t.keluhan_utama.substring(0, 80))}${t.keluhan_utama.length > 80 ? '...' : ''}</span></div>` : ''}
                    ${t.jalannya_penyakit ? `<div class="col-md-6"><b>Perjalanan:</b> <span class="text-slate-700">${escapeHtml(t.jalannya_penyakit.substring(0, 80))}${t.jalannya_penyakit.length > 80 ? '...' : ''}</span></div>` : ''}
                    ${t.obat_di_rs ? `<div class="col-md-6 text-truncate"><b>Terapi RS:</b> <span class="text-slate-700">${escapeHtml(t.obat_di_rs.replace(/\n/g, ', '))}</span></div>` : ''}
                    ${t.obat_pulang ? `<div class="col-12 text-truncate"><b>Terapi Pulang:</b> <span class="text-slate-700">${escapeHtml(t.obat_pulang.replace(/\n/g, ', '))}</span></div>` : ''}
                </div>
            </div>
        `;
    });
    c.innerHTML = html;
}

function renderTemplateKhanzaRanapCards(list) {
    const c = document.getElementById('listTemplateKhanzaRanapContainer');
    if (!c) return;
    if (!list || list.length === 0) {
        c.innerHTML = `
            <div class="text-center py-4 px-3 border rounded-3 bg-white text-muted">
                <i class="bi bi-inbox fs-2 opacity-50 d-block mb-1"></i>
                <div class="fw-bold fs-7 text-slate-700">Belum Ada Template Pemeriksaan Dokter di SIMRS Khanza</div>
                <div class="text-xs text-slate-500 mt-1">Master template pemeriksaan dikelola melalui menu Khanza desktop.</div>
            </div>
        `;
        return;
    }

    let html = '';
    list.forEach((t, idx) => {
        html += `
            <div class="card p-3 border shadow-xs bg-white" style="border-radius:12px;">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap">
                    <div>
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="badge bg-secondary font-monospace">${escapeHtml(t.no_template)}</span>
                            <h6 class="fw-bold text-slate-800 mb-0 fs-7">${escapeHtml(t.penilaian || t.keluhan || ('Template ' + t.no_template))}</h6>
                        </div>
                        <small class="text-muted text-xs">Dokter: ${escapeHtml(t.nm_dokter || t.kd_dokter || '-')}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary font-bold text-xs py-1 px-2.5 rounded-2" onclick="terapkanTemplateKhanzaRanap(${idx})" title="Terapkan template pemeriksaan ke resume">
                        <i class="bi bi-box-arrow-in-down-right me-1"></i> Gunakan
                    </button>
                </div>
                <div class="row g-2 text-xs text-slate-600 border-top pt-2 mt-1">
                    ${t.keluhan ? `<div class="col-md-6"><b>Subjek / Keluhan:</b> ${escapeHtml(t.keluhan)}</div>` : ''}
                    ${t.pemeriksaan ? `<div class="col-md-6"><b>Objek / Fisik:</b> ${escapeHtml(t.pemeriksaan)}</div>` : ''}
                    ${t.penilaian ? `<div class="col-md-6"><b>Asesmen / Diagnosa:</b> ${escapeHtml(t.penilaian)}</div>` : ''}
                    ${t.rencana ? `<div class="col-md-6"><b>Plan / Terapi:</b> ${escapeHtml(t.rencana)}</div>` : ''}
                </div>
            </div>
        `;
    });
    c.innerHTML = html;
}

function filterTemplateRanapCards() {
    const q = (document.getElementById('searchTemplateResumeRanap')?.value || '').toLowerCase();
    const filtered = cachedResumeRanapTemplates.filter(t => {
        return (t.nama_template || '').toLowerCase().includes(q) ||
               (t.diagnosa_utama || '').toLowerCase().includes(q) ||
               (t.diagnosa_awal || '').toLowerCase().includes(q) ||
               (t.keluhan_utama || '').toLowerCase().includes(q) ||
               (t.jalannya_penyakit || '').toLowerCase().includes(q);
    });
    renderTemplateResumeRanapCards(filtered);

    const filteredKhanza = cachedKhanzaRanapTemplates.filter(t => {
        return (t.no_template || '').toLowerCase().includes(q) ||
               (t.keluhan || '').toLowerCase().includes(q) ||
               (t.penilaian || '').toLowerCase().includes(q);
    });
    renderTemplateKhanzaRanapCards(filteredKhanza);
}
window.filterTemplateRanapCards = filterTemplateRanapCards;

window.terapkanTemplateResumeRanap = function (idx) {
    const tmpl = cachedResumeRanapTemplates[idx];
    if (!tmpl) return;

    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el && val !== undefined && val !== null && val !== '') el.value = val;
    };

    setVal('rr_diagnosa_awal', tmpl.diagnosa_awal);
    setVal('rr_alasan', tmpl.alasan);
    setVal('rr_keluhan_utama', tmpl.keluhan_utama);
    setVal('rr_pemeriksaan_fisik', tmpl.pemeriksaan_fisik);
    setVal('rr_jalannya_penyakit', tmpl.jalannya_penyakit);
    setVal('rr_pemeriksaan_penunjang', tmpl.pemeriksaan_penunjang);
    setVal('rr_hasil_laborat', tmpl.hasil_laborat);
    setVal('rr_tindakan_dan_operasi', tmpl.tindakan_dan_operasi);
    setVal('rr_obat_di_rs', tmpl.obat_di_rs);
    setVal('rr_diagnosa_utama', tmpl.diagnosa_utama);
    setVal('rr_kd_diagnosa_utama', tmpl.kd_diagnosa_utama);
    setVal('rr_diagnosa_sekunder', tmpl.diagnosa_sekunder);
    setVal('rr_kd_diagnosa_sekunder', tmpl.kd_diagnosa_sekunder);
    setVal('rr_diagnosa_sekunder2', tmpl.diagnosa_sekunder2);
    setVal('rr_kd_diagnosa_sekunder2', tmpl.kd_diagnosa_sekunder2);
    setVal('rr_diagnosa_sekunder3', tmpl.diagnosa_sekunder3);
    setVal('rr_kd_diagnosa_sekunder3', tmpl.kd_diagnosa_sekunder3);
    setVal('rr_prosedur_utama', tmpl.prosedur_utama);
    setVal('rr_kd_prosedur_utama', tmpl.kd_prosedur_utama);
    setVal('rr_prosedur_sekunder', tmpl.prosedur_sekunder);
    setVal('rr_kd_prosedur_sekunder', tmpl.kd_prosedur_sekunder);
    setVal('rr_alergi', tmpl.alergi);
    setVal('rr_diet', tmpl.diet);
    setVal('rr_lab_belum', tmpl.lab_belum);
    setVal('rr_edukasi', tmpl.edukasi);
    setVal('rr_cara_keluar', tmpl.cara_keluar);
    setVal('rr_ket_keluar', tmpl.ket_keluar);
    setVal('rr_keadaan', tmpl.keadaan);
    setVal('rr_ket_keadaan', tmpl.ket_keadaan);
    setVal('rr_dilanjutkan', tmpl.dilanjutkan);
    setVal('rr_ket_dilanjutkan', tmpl.ket_dilanjutkan);
    setVal('rr_kontrol', tmpl.kontrol);
    setVal('rr_obat_pulang', tmpl.obat_pulang);

    const modalEl = document.getElementById('modalPilihTemplateResumeRanap');
    if (modalEl && window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    } else if (window.jQuery) {
        $('#modalPilihTemplateResumeRanap').modal('hide');
    }

    showToast(`Template "${tmpl.nama_template}" berhasil diterapkan ke form resume ranap.`, 'success');
};

window.terapkanTemplateKhanzaRanap = function (idx) {
    const tmpl = cachedKhanzaRanapTemplates[idx];
    if (!tmpl) return;

    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el && val) el.value = val;
    };

    setVal('rr_keluhan_utama', tmpl.keluhan);
    setVal('rr_pemeriksaan_fisik', tmpl.pemeriksaan);
    setVal('rr_diagnosa_utama', tmpl.penilaian);
    setVal('rr_obat_pulang', tmpl.rencana);

    const modalEl = document.getElementById('modalPilihTemplateResumeRanap');
    if (modalEl && window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    } else if (window.jQuery) {
        $('#modalPilihTemplateResumeRanap').modal('hide');
    }

    showToast(`Template pemeriksaan "${tmpl.no_template}" berhasil diterapkan.`, 'success');
};

window.hapusTemplateResumeRanap = function (id, nama) {
    Swal.fire({
        title: 'Hapus Template Resume Ranap?',
        html: `Apakah Anda yakin ingin menghapus template <b>${escapeHtml(nama)}</b>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/rawat-inap/hapus-template-resume', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: id })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast(res.message || 'Template berhasil dihapus.', 'success');
                    loadTemplateResumeRanapList(document.getElementById('searchTemplateResumeRanap')?.value || '');
                } else {
                    showToast(res.message || 'Gagal menghapus template.', 'error');
                }
            })
            .catch(() => showToast('Terjadi kesalahan saat menghapus template.', 'error'));
        }
    });
};

function bukaModalSimpanTemplateRanap() {
    const diag = document.getElementById('rr_diagnosa_utama')?.value || '';
    const diagAwal = document.getElementById('rr_diagnosa_awal')?.value || '';
    const keluhan = document.getElementById('rr_keluhan_utama')?.value || '';

    if (!diag && !diagAwal && !keluhan) {
        showToast('Isi minimal Diagnosa Utama atau Keluhan Utama pada form resume sebelum menyimpannya sebagai template.', 'warning');
        return;
    }

    const suggestedName = diag ? `Resume Ranap ${diag}` : (diagAwal ? `Resume Ranap ${diagAwal}` : 'Resume Ranap Template Baru');
    const inputName = document.getElementById('inputNamaTemplateRanap');
    if (inputName) inputName.value = suggestedName;

    const previewEl = document.getElementById('previewSaveTemplateRanap');
    if (previewEl) {
        previewEl.innerHTML = `
            <div><b>Diagnosa Utama:</b> ${escapeHtml(diag || '-')} ${document.getElementById('rr_kd_diagnosa_utama')?.value ? '(' + escapeHtml(document.getElementById('rr_kd_diagnosa_utama').value) + ')' : ''}</div>
            <div><b>Diagnosa Awal:</b> ${escapeHtml(diagAwal || '-')}</div>
            <div><b>Keluhan Utama:</b> ${escapeHtml((keluhan || '-').substring(0, 80))}</div>
            <div><b>Terapi Pulang:</b> ${escapeHtml((document.getElementById('rr_obat_pulang')?.value || '-').substring(0, 80))}</div>
        `;
    }

    const modalEl = document.getElementById('modalSimpanTemplateResumeRanap');
    if (modalEl && window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if (window.jQuery) {
        $('#modalSimpanTemplateResumeRanap').modal('show');
    }
}
window.bukaModalSimpanTemplateRanap = bukaModalSimpanTemplateRanap;

function submitSimpanTemplateRanap() {
    const nama = (document.getElementById('inputNamaTemplateRanap')?.value || '').trim();
    if (!nama) {
        showToast('Nama template wajib diisi.', 'warning');
        return;
    }

    const btn = document.getElementById('btnSubmitSimpanTemplateRanap');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
    }

    const getVal = (id) => document.getElementById(id)?.value || '';

    const payload = {
        nama_template:          nama,
        diagnosa_awal:          getVal('rr_diagnosa_awal'),
        alasan:                 getVal('rr_alasan'),
        keluhan_utama:          getVal('rr_keluhan_utama'),
        pemeriksaan_fisik:      getVal('rr_pemeriksaan_fisik'),
        jalannya_penyakit:      getVal('rr_jalannya_penyakit'),
        pemeriksaan_penunjang:  getVal('rr_pemeriksaan_penunjang'),
        hasil_laborat:          getVal('rr_hasil_laborat'),
        tindakan_dan_operasi:   getVal('rr_tindakan_dan_operasi'),
        obat_di_rs:             getVal('rr_obat_di_rs'),
        diagnosa_utama:         getVal('rr_diagnosa_utama'),
        kd_diagnosa_utama:      getVal('rr_kd_diagnosa_utama'),
        diagnosa_sekunder:      getVal('rr_diagnosa_sekunder'),
        kd_diagnosa_sekunder:   getVal('rr_kd_diagnosa_sekunder'),
        diagnosa_sekunder2:     getVal('rr_diagnosa_sekunder2'),
        kd_diagnosa_sekunder2:  getVal('rr_kd_diagnosa_sekunder2'),
        diagnosa_sekunder3:     getVal('rr_diagnosa_sekunder3'),
        kd_diagnosa_sekunder3:  getVal('rr_kd_diagnosa_sekunder3'),
        prosedur_utama:         getVal('rr_prosedur_utama'),
        kd_prosedur_utama:      getVal('rr_kd_prosedur_utama'),
        prosedur_sekunder:      getVal('rr_prosedur_sekunder'),
        kd_prosedur_sekunder:   getVal('rr_kd_prosedur_sekunder'),
        alergi:                 getVal('rr_alergi'),
        diet:                   getVal('rr_diet'),
        lab_belum:              getVal('rr_lab_belum'),
        edukasi:                getVal('rr_edukasi'),
        cara_keluar:            getVal('rr_cara_keluar'),
        ket_keluar:             getVal('rr_ket_keluar'),
        keadaan:                getVal('rr_keadaan'),
        ket_keadaan:            getVal('rr_ket_keadaan'),
        dilanjutkan:            getVal('rr_dilanjutkan'),
        ket_dilanjutkan:        getVal('rr_ket_dilanjutkan'),
        kontrol:                getVal('rr_kontrol'),
        obat_pulang:            getVal('rr_obat_pulang')
    };

    fetch('/rawat-inap/simpan-template-resume', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showToast(res.message || 'Template resume ranap berhasil disimpan.', 'success');
            const modalEl = document.getElementById('modalSimpanTemplateResumeRanap');
            if (modalEl && window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).hide();
            } else if (window.jQuery) {
                $('#modalSimpanTemplateResumeRanap').modal('hide');
            }
        } else {
            showToast(res.message || 'Gagal menyimpan template.', 'error');
        }
    })
    .catch(() => showToast('Terjadi kesalahan saat menyimpan template.', 'error'))
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Template';
        }
    });
}
window.submitSimpanTemplateRanap = submitSimpanTemplateRanap;

// ── Lab Actions ───────────────────────────────────────────────
function kirimLab() {
    const selected = $('#lab-select').val();
    if (!selected || !selected.length) { showToast('Pilih minimal 1 jenis pemeriksaan lab.', 'warning'); return; }

    const noRawatDecoded = atob(currentNoRawat);
    fetch('/rawat-inap/simpan-lab', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ no_rawat: noRawatDecoded, items: selected, diagnosa_klinis: document.getElementById('lab-diagnosa').value })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            $('#lab-select').val(null).trigger('change');
            document.getElementById('lab-diagnosa').value = '';
            refreshDetailPanel(currentNoRawat);
        } else {
            showToast(d.message, 'error');
        }
    })
    .catch(() => showToast('Gagal mengirim order lab.', 'error'));
}

// ── Radiologi Actions ─────────────────────────────────────────
function kirimRadiologi() {
    const selected = $('#rad-select').val();
    if (!selected || !selected.length) { showToast('Pilih minimal 1 jenis pemeriksaan radiologi.', 'warning'); return; }

    const noRawatDecoded = atob(currentNoRawat);
    fetch('/rawat-inap/simpan-radiologi', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ no_rawat: noRawatDecoded, items: selected, diagnosa_klinis: document.getElementById('rad-diagnosa').value })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            $('#rad-select').val(null).trigger('change');
            document.getElementById('rad-diagnosa').value = '';
            refreshDetailPanel(currentNoRawat);
        } else {
            showToast(d.message, 'error');
        }
    })
    .catch(() => showToast('Gagal mengirim order radiologi.', 'error'));
}

// ── Refresh Detail Panel ──────────────────────────────────────
function refreshDetailPanel(noRawatB64) {
    fetch("{{ route('rawat-inap.pasien-detail') }}?no_rawat=" + encodeURIComponent(noRawatB64), {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) return;
        currentData = d;
        renderSoapPanel(d.soapList ?? []);
        renderDiagnosaPanel(d.diagnosaList ?? []);
        renderTindakanPanel(d.tindakanList ?? []);
        renderResepPanel(d.resepList ?? []);
        renderLabPanel(d.labOrders ?? [], d.labResults ?? []);
        renderRadPanel(d.radOrders ?? [], d.radResults ?? []);
        renderRiwayatPanel(d.soapList ?? [], d.riwayatKamar ?? []);
        renderOperasiRanap(d.bookingOps ?? [], d.laporanOps ?? []);
    })
    .catch(() => {});
}

// ================================================================
//  OPERASI / BEDAH RAWAT INAP (SIMRS-NAMIRA PARITY)
// ================================================================

function toDateTimeLocalValueRanap(dateStr) {
    if (!dateStr) return '';
    try {
        const s = dateStr.replace(' ', 'T');
        return s.substring(0, 16);
    } catch (e) {
        return '';
    }
}

function loadMasterOperasiRanap() {
    $.ajax({
        url: '{{ route("rawat-inap.master-operasi") }}',
        type: 'GET',
        success: function (data) {
            let html = '<option value="">-- Pilih Paket Operasi --</option>';
            (data || []).forEach(item => {
                const kelasStr = item.kelas ? ` [Kelas: ${item.kelas}]` : '';
                const pjStr = item.png_jawab ? ` — ${item.png_jawab}` : '';
                html += `<option value="${item.kode_paket}">${item.nm_perawatan}${kelasStr}${pjStr} (${item.kode_paket})</option>`;
            });
            $('#opsRanapKodePaket').html(html).trigger('change');
        }
    });
}

function loadMasterDokterRanap() {
    $.ajax({
        url: '{{ route("rawat-inap.master-dokter") }}',
        type: 'GET',
        success: function (data) {
            let html = '<option value="">-- Pilih Dokter Operator --</option>';
            (data || []).forEach(item => {
                html += `<option value="${item.kd_dokter}">${item.nm_dokter}</option>`;
            });
            $('#opsRanapKdDokter').html(html).trigger('change');
        }
    });
}

function loadMasterRuangOkRanap() {
    $.ajax({
        url: '{{ route("rawat-inap.master-ruang-ok") }}',
        type: 'GET',
        success: function (data) {
            let html = '';
            (data || []).forEach(item => {
                html += `<option value="${item.kd_ruang_ok}">${item.nm_ruang_ok} (${item.kd_ruang_ok})</option>`;
            });
            if (html) {
                $('#opsRanapKdRuangOk').html(html);
            }
        }
    });
}

function renderOperasiRanap(bookingList, laporanList) {
    const bCount = (bookingList || []).length;
    const lCount = (laporanList || []).length;

    const bBadge = document.getElementById('badge-operasi');
    if (bBadge) bBadge.textContent = bCount + lCount;

    $('#countOpsRanapBookingText').text(bCount);
    $('#countOpsRanapLaporanText').text(lCount);

    // 1. Render Riwayat Booking Operasi
    const bc = $('#riwayatOpsRanapContainer');
    if (!bookingList || bookingList.length === 0) {
        bc.html('<div class="empty-state py-3"><i class="bi bi-calendar-event fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada booking operasi untuk pasien ini.</p></div>');
    } else {
        let htmlBook = '';
        bookingList.forEach(b => {
            const sttsCls = b.status === 'Selesai' ? 'bg-success' : (b.status === 'Batal' ? 'bg-danger' : (b.status === 'Proses Operasi' ? 'bg-info text-white' : 'bg-warning text-dark'));
            const kelasBadge = b.kelas ? `<span class="badge bg-primary bg-opacity-10 text-primary ms-1">Kelas ${escapeHtml(b.kelas)}</span>` : '';
            const pjBadge = b.png_jawab ? `<span class="badge bg-info bg-opacity-10 text-info ms-1">${escapeHtml(b.png_jawab)}</span>` : '';
            const ruangBadge = b.nm_ruang_ok ? `<span class="badge bg-purple-700 bg-opacity-10 text-purple-700 border border-purple-200 ms-1"><i class="bi bi-door-closed me-1"></i>${escapeHtml(b.nm_ruang_ok)}</span>` : '';

            const printBtn = `<button type="button" class="btn btn-xs btn-outline-secondary font-bold px-2 py-1 rounded-2" onclick="cetakBookingOpsRanap('${escapeHtml(b.no_rawat)}', '${escapeHtml(b.kode_paket)}', '${escapeHtml(b.tanggal)}')"><i class="bi bi-printer me-1"></i>Cetak</button>`;

            const delBtn = isDokterOrAdmin ? `
                <button type="button" class="btn btn-xs btn-outline-danger font-bold px-2 py-1 rounded-2" onclick="hapusBookingOpsRanap('${escapeHtml(b.no_rawat)}', '${escapeHtml(b.kode_paket)}', '${escapeHtml(b.tanggal)}')">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            ` : '';

            htmlBook += `
            <div class="card p-3 mb-2.5 border text-xs shadow-xs" style="border-radius:12px; background:#fff;">
                <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                        <span class="text-danger fs-7"><i class="bi bi-calendar-event me-1"></i>Tgl Operasi: ${fmtDate(b.tanggal)} (${(b.jam_mulai||'').substring(0,5)} - ${(b.jam_selesai||'').substring(0,5)})</span>
                        ${ruangBadge}
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="badge ${sttsCls} px-2.5 py-1">${escapeHtml(b.status || 'Menunggu')}</span>
                        <div class="btn-group btn-group-sm">
                            ${printBtn}
                            ${delBtn}
                        </div>
                    </div>
                </div>
                <div><strong>Paket Operasi:</strong> <span class="text-slate-900 font-bold">${escapeHtml(b.nama_paket || b.kode_paket)}</span> <code>(${escapeHtml(b.kode_paket)})</code> ${kelasBadge} ${pjBadge}</div>
                <div class="text-slate-600 mt-1"><strong>Dokter Operator:</strong> ${escapeHtml(b.dokter_operator || b.kd_dokter)}</div>
            </div>`;
        });
        bc.html(htmlBook);
    }

    // 2. Render Riwayat Laporan Operasi
    const lc = $('#laporanOpsRanapContainer');
    if (!laporanList || laporanList.length === 0) {
        lc.html('<div class="empty-state py-3"><i class="bi bi-file-earmark-medical fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada laporan operasi untuk pasien ini.</p></div>');
    } else {
        let htmlLap = '';
        laporanList.forEach(lo => {
            const isCur = lo.is_current ? '<span class="badge bg-success ms-1">Rawat Inap Ini</span>' : `<span class="badge bg-light text-slate-600 border ms-1">${escapeHtml(lo.no_rawat)}</span>`;
            const paBadge = lo.permintaan_pa === 'Ya' 
                ? `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5"><i class="bi bi-check-circle-fill me-1"></i>Dikirim PA</span>`
                : `<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-0.5">Tanpa PA</span>`;
            
            const katBadge = lo.kategori && lo.kategori !== '-'
                ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5">Kategori: ${escapeHtml(lo.kategori)}</span>`
                : '';

            const printLapBtn = `<button type="button" class="btn btn-xs btn-outline-secondary font-bold px-2 py-1" onclick="cetakLaporanOpsRanap('${escapeHtml(lo.no_rawat)}', '${escapeHtml(lo.tanggal)}')"><i class="bi bi-printer me-1"></i>Cetak</button>`;

            const actionBtns = `
                <div class="btn-group btn-group-sm">
                    ${printLapBtn}
                    ${isDokterOrAdmin ? `
                    <button type="button" class="btn btn-xs btn-outline-primary font-bold px-2 py-1" onclick="editLaporanOpsRanap('${escapeHtml(lo.no_rawat)}', '${escapeHtml(lo.tanggal)}', '${escapeHtml(lo.tgl_operasi||'')}', '${escapeHtml(lo.selesaioperasi||'')}', '${escapeHtml(lo.diagnosa_preop||'')}', '${escapeHtml(lo.diagnosa_postop||'')}', '${escapeHtml(lo.jaringan_dieksekusi||'')}', '${escapeHtml(lo.permintaan_pa||'Tidak')}', '${escapeHtml(lo.jenis_anasthesi||'')}', '${escapeHtml(lo.kategori||'-')}', ${JSON.stringify(lo.laporan_operasi||'').replace(/"/g, '&quot;')})">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-danger font-bold px-2 py-1" onclick="hapusLaporanOpsRanap('${escapeHtml(lo.no_rawat)}', '${escapeHtml(lo.tanggal)}')">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                    ` : ''}
                </div>
            `;

            htmlLap += `
            <div class="card p-3 mb-3 border text-xs shadow-xs" style="border-radius:12px; background:#fff;">
                <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                    <div class="d-flex align-items-center flex-wrap gap-1">
                        <span class="text-danger fs-7"><i class="bi bi-file-earmark-medical-fill me-1"></i>Laporan Operasi: ${fmtDate(lo.tanggal)} ${(lo.tanggal||'').substring(11,16)} WIB</span>
                        ${isCur}
                    </div>
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        ${paBadge}
                        ${katBadge}
                        <span class="badge bg-danger px-2.5 py-1">Anestesi: ${escapeHtml(lo.jenis_anasthesi || '-')}</span>
                        ${actionBtns}
                    </div>
                </div>
                <div class="row g-2 mb-2 text-slate-700">
                    <div class="col-md-6"><strong>Diagnosa Pre-Op:</strong> <span class="text-slate-900 fw-semibold">${escapeHtml(lo.diagnosa_preop || '-')}</span></div>
                    <div class="col-md-6"><strong>Diagnosa Post-Op:</strong> <span class="text-slate-900 fw-semibold">${escapeHtml(lo.diagnosa_postop || '-')}</span></div>
                    <div class="col-md-6"><strong>Jaringan Dieksisi:</strong> <span>${escapeHtml(lo.jaringan_dieksekusi || '-')}</span></div>
                    <div class="col-md-6"><strong>Waktu Operasi:</strong> <span>Mulai: ${(lo.tgl_operasi||'').substring(11,16)} — Selesai: ${(lo.selesaioperasi||'').substring(11,16)}</span></div>
                </div>
                <div class="fw-bold text-slate-800 mt-2 mb-1"><i class="bi bi-body-text me-1"></i>Uraian Jalannya Operasi / Prosedur Bedah:</div>
                <div class="text-slate-800 p-3 bg-light rounded-2 border" style="white-space:pre-wrap; font-family:monospace, inherit; font-size:.8rem; line-height:1.6;">${escapeHtml(lo.laporan_operasi || '-')}</div>
            </div>`;
        });
        lc.html(htmlLap);
    }
}

// ── SIMPAN BOOKING JADWAL OPERASI RANAP ──
$('#btnSimpanOperasiRanap').on('click', function () {
    if (!isDokterOrAdmin) {
        showToast('Hanya Dokter dan Admin Utama yang berwenang menjadwalkan operasi.', 'warning');
        return;
    }

    const noRawat = $('#opsRanapNoRawat').val();
    if (!noRawat) {
        showToast('Pilih pasien rawat inap terlebih dahulu.', 'warning');
        return;
    }

    if (!$('#opsRanapKodePaket').val()) {
        showToast('Pilih paket operasi terlebih dahulu.', 'warning');
        return;
    }

    if (!$('#opsRanapKdDokter').val()) {
        showToast('Pilih dokter operator terlebih dahulu.', 'warning');
        return;
    }

    const btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    $.ajax({
        url: '{{ route("rawat-inap.simpan-booking-operasi") }}',
        type: 'POST',
        data: $('#formOperasiRanap').serialize() + '&_token=' + CSRF,
        success: function (res) {
            showToast(res.message || 'Jadwal operasi berhasil disimpan.', 'success');
            if (currentNoRawat) refreshDetailPanel(currentNoRawat);
        },
        error: function (err) {
            showToast('Gagal menyimpan jadwal operasi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'), 'error');
        },
        complete: function () {
            btn.prop('disabled', false).html('<i class="bi bi-calendar-check-fill me-1"></i> Simpan Jadwal Operasi');
        }
    });
});

// ── HAPUS BOOKING OPERASI RANAP ──
window.hapusBookingOpsRanap = function (noRawat, kodePaket, tanggal) {
    if (!isDokterOrAdmin) {
        showToast('Hanya Dokter dan Admin Utama yang berwenang menghapus booking operasi.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Hapus Jadwal Operasi?',
        html: `Apakah Anda yakin ingin membatalkan/menghapus booking operasi <b>${kodePaket}</b> pada tanggal <b>${tanggal}</b>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("rawat-inap.hapus-booking-operasi") }}',
                type: 'DELETE',
                data: {
                    no_rawat: noRawat,
                    kode_paket: kodePaket,
                    tanggal: tanggal,
                    _token: CSRF
                },
                success: function (res) {
                    showToast(res.message || 'Jadwal operasi berhasil dihapus.', 'success');
                    if (currentNoRawat) refreshDetailPanel(currentNoRawat);
                },
                error: function (err) {
                    showToast('Gagal menghapus jadwal operasi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'), 'error');
                }
            });
        }
    });
};

// CETAK JADWAL BOOKING OPERASI RANAP (SIMRS-NAMIRA STANDAR)
window.cetakBookingOpsRanap = function (noRawat, kodePaket, tanggal) {
    if (!noRawat) return;
    const baseUrl = "{{ route('rawat-inap.cetak-booking-operasi') }}";
    const url = `${baseUrl}?no_rawat=${encodeURIComponent(noRawat)}&kode_paket=${encodeURIComponent(kodePaket||'')}&tanggal=${encodeURIComponent(tanggal||'')}`;
    window.open(url, '_blank');
};

// CETAK LAPORAN OPERASI RANAP (SIMRS-NAMIRA STANDAR)
window.cetakLaporanOpsRanap = function (noRawat, tanggal) {
    if (!noRawat) return;
    const baseUrl = "{{ route('rawat-inap.cetak-laporan-operasi') }}";
    const url = `${baseUrl}?no_rawat=${encodeURIComponent(noRawat)}${tanggal ? '&tanggal=' + encodeURIComponent(tanggal) : ''}`;
    window.open(url, '_blank');
};

// ── MODAL CARI & PILIH PAKET OPERASI RANAP ──
let timerCariPaketOpsRanap = null;

window.bukaModalCariPaketRanap = function () {
    $('#modalCariPaketRanap').modal('show');
    loadPaketOperasiModalRanap($('#searchPaketOpsRanapInput').val() || '');
};

window.loadPaketOperasiModalRanap = function (q) {
    const query = (typeof q === 'string' ? q : $('#searchPaketOpsRanapInput').val()) || '';
    const kategori = $('#filterKategoriOpsRanapSelect').val() || '';
    const kelas = $('#filterKelasOpsRanapSelect').val() || '';

    $('#tbodyModalPaketOpsRanap').html(`
        <tr><td colspan="7" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat paket operasi...</td></tr>
    `);

    $.ajax({
        url: '{{ route("rawat-inap.master-operasi") }}',
        type: 'GET',
        data: { q: query, kategori: kategori, kelas: kelas },
        success: function (data) {
            if (!data || data.length === 0) {
                $('#tbodyModalPaketOpsRanap').html(`
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> Tidak ada paket operasi yang sesuai pencarian.</td></tr>
                `);
                return;
            }

            let html = '';
            data.forEach(item => {
                const tarifOp = parseFloat(item.operator1 || 0);
                const totalTarif = parseFloat(item.total_tarif || item.biaya || 0);
                const safeName = (item.nm_perawatan || '').replace(/'/g, "\\'");

                html += `
                <tr>
                    <td class="font-bold text-slate-800"><code>${item.kode_paket}</code></td>
                    <td>
                        <div class="fw-bold text-slate-900">${item.nm_perawatan}</div>
                        ${item.png_jawab ? `<small class="text-muted"><i class="bi bi-shield-check me-1"></i>${item.png_jawab}</small>` : ''}
                    </td>
                    <td><span class="badge bg-light text-dark border">${item.kategori || '-'}</span></td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">${item.kelas || 'Semua'}</span></td>
                    <td class="text-end font-mono">Rp ${tarifOp.toLocaleString('id-ID')}</td>
                    <td class="text-end font-mono font-bold text-success">Rp ${totalTarif.toLocaleString('id-ID')}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-primary font-bold px-2.5 py-1 rounded-2" onclick="pilihPaketOperasiRanap('${item.kode_paket}', '${safeName}', ${totalTarif})">
                            <i class="bi bi-check2-circle me-1"></i>Pilih
                        </button>
                    </td>
                </tr>`;
            });
            $('#tbodyModalPaketOpsRanap').html(html);
        },
        error: function () {
            $('#tbodyModalPaketOpsRanap').html(`
                <tr><td colspan="7" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat data paket operasi.</td></tr>
            `);
        }
    });
};

$('#searchPaketOpsRanapInput').on('input', function () {
    clearTimeout(timerCariPaketOpsRanap);
    const val = $(this).val();
    timerCariPaketOpsRanap = setTimeout(() => {
        loadPaketOperasiModalRanap(val);
    }, 300);
});

$('#filterKategoriOpsRanapSelect, #filterKelasOpsRanapSelect').on('change', function () {
    loadPaketOperasiModalRanap($('#searchPaketOpsRanapInput').val());
});

window.pilihPaketOperasiRanap = function (kode, nama, tarif) {
    let exists = false;
    $('#opsRanapKodePaket option').each(function () {
        if ($(this).val() === kode) {
            exists = true;
            return false;
        }
    });

    if (!exists) {
        $('#opsRanapKodePaket').append(new Option(`${nama} (${kode})`, kode, true, true));
    } else {
        $('#opsRanapKodePaket').val(kode);
    }
    $('#opsRanapKodePaket').trigger('change');

    $('#modalCariPaketRanap').modal('hide');
    showToast(`Paket operasi ${nama} berhasil dipilih.`, 'success');
};

// ── MODAL PILIH TEMPLATE LAPORAN OPERASI RANAP ──
let timerTemplateOpsRanap = null;
let selectedTemplateOpsRanapData = null;

window.bukaModalTemplateLaporanRanap = function () {
    $('#modalTemplateLaporanRanap').modal('show');
    selectedTemplateOpsRanapData = null;
    $('#btnTerapkanTemplateOpsRanap').prop('disabled', true);
    $('#previewTemplateRanapNoText').text('-');
    $('#previewTemplateRanapNamaText').text('-');
    $('#previewTemplateRanapPreopText').text('-');
    $('#previewTemplateRanapPostopText').text('-');
    $('#previewTemplateRanapJaringanText').text('-');
    $('#previewTemplateRanapPaText').text('-');
    $('#previewTemplateRanapLaporanText').val('');

    loadTemplateOperasiModalRanap($('#searchTemplateOpsRanapInput').val() || '');
};

window.loadTemplateOperasiModalRanap = function (q) {
    const query = (typeof q === 'string' ? q : $('#searchTemplateOpsRanapInput').val()) || '';

    $('#tbodyModalTemplateOpsRanap').html(`
        <tr><td colspan="5" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat template laporan operasi...</td></tr>
    `);

    $.ajax({
        url: '{{ route("rawat-inap.master-template-laporan-operasi") }}',
        type: 'GET',
        data: { q: query },
        success: function (data) {
            if (!data || data.length === 0) {
                $('#tbodyModalTemplateOpsRanap').html(`
                    <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> Tidak ada template yang cocok.</td></tr>
                `);
                return;
            }

            window.__cachedTemplatesOpsRanap = data;
            let html = '';
            data.forEach((t, idx) => {
                const paBadge = t.permintaan_pa === 'Ya'
                    ? `<span class="badge bg-danger">Ya</span>`
                    : `<span class="badge bg-secondary">Tidak</span>`;

                html += `
                <tr style="cursor:pointer;" onclick="previewTemplateLaporanOpsRanapItem(${idx})">
                    <td class="font-bold text-slate-800"><code>${t.no_template}</code></td>
                    <td>
                        <div class="fw-bold text-slate-900">${t.nama_operasi}</div>
                    </td>
                    <td>
                        <div class="text-xs text-muted">Pre: ${t.diagnosa_preop || '-'}</div>
                        <div class="text-xs text-muted">Post: ${t.diagnosa_postop || '-'}</div>
                    </td>
                    <td class="text-center">${paBadge}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-outline-primary font-bold px-2 py-1 rounded-2" onclick="event.stopPropagation(); previewTemplateLaporanOpsRanapItem(${idx});">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>`;
            });
            $('#tbodyModalTemplateOpsRanap').html(html);
        },
        error: function () {
            $('#tbodyModalTemplateOpsRanap').html(`
                <tr><td colspan="5" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat template laporan operasi.</td></tr>
            `);
        }
    });
};

$('#searchTemplateOpsRanapInput').on('input', function () {
    clearTimeout(timerTemplateOpsRanap);
    const val = $(this).val();
    timerTemplateOpsRanap = setTimeout(() => {
        loadTemplateOperasiModalRanap(val);
    }, 300);
});

window.previewTemplateLaporanOpsRanapItem = function (idx) {
    if (!window.__cachedTemplatesOpsRanap || !window.__cachedTemplatesOpsRanap[idx]) return;
    const t = window.__cachedTemplatesOpsRanap[idx];
    selectedTemplateOpsRanapData = t;

    $('#previewTemplateRanapNoText').text(t.no_template);
    $('#previewTemplateRanapNamaText').text(t.nama_operasi);
    $('#previewTemplateRanapPreopText').text(t.diagnosa_preop || '-');
    $('#previewTemplateRanapPostopText').text(t.diagnosa_postop || '-');
    $('#previewTemplateRanapJaringanText').text(t.jaringan_dieksisi || '-');
    $('#previewTemplateRanapPaText').text(t.permintaan_pa || 'Tidak');
    $('#previewTemplateRanapLaporanText').val(t.laporan_operasi || '');

    $('#btnTerapkanTemplateOpsRanap').prop('disabled', false);

    $('#tbodyModalTemplateOpsRanap tr').removeClass('table-primary');
    $(`#tbodyModalTemplateOpsRanap tr:eq(${idx})`).addClass('table-primary');
};

$('#btnTerapkanTemplateOpsRanap').on('click', function () {
    if (!selectedTemplateOpsRanapData) {
        showToast('Pilih salah satu template laporan operasi terlebih dahulu.', 'warning');
        return;
    }

    const t = selectedTemplateOpsRanapData;
    if (t.diagnosa_preop) $('#lapOpsRanapPreop').val(t.diagnosa_preop);
    if (t.diagnosa_postop) $('#lapOpsRanapPostop').val(t.diagnosa_postop);
    if (t.jaringan_dieksisi) $('#lapOpsRanapJaringan').val(t.jaringan_dieksisi);
    if (t.permintaan_pa) $('#lapOpsRanapPa').val(t.permintaan_pa);
    if (t.laporan_operasi) $('#lapOpsRanapLaporan').val(t.laporan_operasi);

    $('#modalTemplateLaporanRanap').modal('hide');
    showToast(`Template "${t.nama_operasi}" berhasil diterapkan ke formulir laporan operasi.`, 'success');
});

// ── SIMPAN / EDIT LAPORAN OPERASI RANAP ──
$('#btnSimpanLaporanOperasiRanap').on('click', function () {
    if (!isDokterOrAdmin) {
        showToast('Hanya Dokter dan Admin Utama yang berwenang menyimpan Laporan Operasi.', 'warning');
        return;
    }

    const noRawat = $('#lapOpsRanapNoRawat').val();
    if (!noRawat) {
        showToast('Pilih pasien rawat inap terlebih dahulu.', 'warning');
        return;
    }

    const preop = $('#lapOpsRanapPreop').val().trim();
    const postop = $('#lapOpsRanapPostop').val().trim();
    const laporan = $('#lapOpsRanapLaporan').val().trim();

    if (!preop) {
        showToast('Diagnosa Pra Bedah (Pre-Op) wajib diisi.', 'warning');
        $('#lapOpsRanapPreop').focus();
        return;
    }

    if (!postop) {
        showToast('Diagnosa Pasca Bedah (Post-Op) wajib diisi.', 'warning');
        $('#lapOpsRanapPostop').focus();
        return;
    }

    if (!laporan) {
        showToast('Uraian laporan operasi / jalannya tindakan pembedahan wajib diisi.', 'warning');
        $('#lapOpsRanapLaporan').focus();
        return;
    }

    const btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    $.ajax({
        url: '{{ route("rawat-inap.simpan-laporan-operasi") }}',
        type: 'POST',
        data: $('#formLaporanOperasiRanap').serialize() + '&_token=' + CSRF,
        success: function (res) {
            showToast(res.message || 'Laporan operasi berhasil disimpan.', 'success');
            batalEditLaporanOpsRanap();
            if (currentNoRawat) refreshDetailPanel(currentNoRawat);
        },
        error: function (err) {
            showToast('Gagal menyimpan laporan operasi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'), 'error');
        },
        complete: function () {
            btn.prop('disabled', false).html('<i class="bi bi-save-fill me-1"></i> Simpan Laporan Operasi');
        }
    });
});

// ── EDIT LAPORAN OPERASI RANAP ──
window.editLaporanOpsRanap = function (noRawat, tanggal, tglOperasi, selesaiOperasi, preop, postop, jaringan, pa, anesthesi, kategori, laporan) {
    if (!isDokterOrAdmin) {
        showToast('Hanya Dokter dan Admin Utama yang berwenang mengedit Laporan Operasi.', 'warning');
        return;
    }

    $('#lapOpsRanapNoRawat').val(noRawat);
    $('#lapOpsRanapOldTanggal').val(tanggal);

    $('#lapOpsRanapTanggal').val(toDateTimeLocalValueRanap(tanggal));
    if (tglOperasi) $('#lapOpsRanapTglOperasi').val(toDateTimeLocalValueRanap(tglOperasi));
    if (selesaiOperasi) $('#lapOpsRanapSelesaiOperasi').val(toDateTimeLocalValueRanap(selesaiOperasi));

    $('#lapOpsRanapPreop').val(preop || '');
    $('#lapOpsRanapPostop').val(postop || '');
    $('#lapOpsRanapJaringan').val(jaringan || '');
    $('#lapOpsRanapPa').val(pa || 'Tidak');
    $('#lapOpsRanapAnasthesi').val(anesthesi || 'SPINAL');
    $('#lapOpsRanapKategori').val(kategori || 'Besar');
    $('#lapOpsRanapLaporan').val(laporan || '');

    $('#editLapOpsRanapTglText').text(tanggal);
    $('#alertEditLaporanOpsRanap').removeClass('d-none');
    $('#btnSimpanLaporanOperasiRanap').html('<i class="bi bi-pencil-square me-1"></i> Simpan Perubahan Laporan Operasi');

    // Buka sub-tab 2 Laporan Operasi Ranap
    const triggerEl = document.querySelector('#pills-ops-laporan-ranap-tab');
    if (triggerEl) {
        const tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
        tab.show();
    }

    // Switch ke tab operasi ranap jika belum
    switchTab('operasi');
    document.getElementById('formLaporanOperasiRanap')?.scrollIntoView({ behavior: 'smooth' });
    showToast(`Laporan operasi tanggal ${tanggal} dimuat ke formulir.`, 'info');
};

window.batalEditLaporanOpsRanap = function () {
    $('#lapOpsRanapOldTanggal').val('');
    $('#alertEditLaporanOpsRanap').addClass('d-none');
    $('#btnSimpanLaporanOperasiRanap').html('<i class="bi bi-save-fill me-1"></i> Simpan Laporan Operasi');
    resetFormLaporanOpsRanap();
};

window.resetFormLaporanOpsRanap = function () {
    const noRawat = $('#lapOpsRanapNoRawat').val();
    if ($('#formLaporanOperasiRanap').length && $('#formLaporanOperasiRanap')[0]) {
        $('#formLaporanOperasiRanap')[0].reset();
    }
    $('#lapOpsRanapNoRawat').val(noRawat);
    $('#lapOpsRanapOldTanggal').val('');

    const now = new Date();
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, '0');
    const d = String(now.getDate()).padStart(2, '0');
    const h = String(now.getHours()).padStart(2, '0');
    const min = String(now.getMinutes()).padStart(2, '0');
    const nowIso = `${y}-${m}-${d}T${h}:${min}`;

    const endObj = new Date(now.getTime() + 60 * 60 * 1000);
    const eh = String(endObj.getHours()).padStart(2, '0');
    const emin = String(endObj.getMinutes()).padStart(2, '0');
    const endIso = `${y}-${m}-${d}T${eh}:${emin}`;

    $('#lapOpsRanapTanggal').val(nowIso);
    $('#lapOpsRanapTglOperasi').val(nowIso);
    $('#lapOpsRanapSelesaiOperasi').val(endIso);
    $('#lapOpsRanapPa').val('Tidak');
    $('#lapOpsRanapAnasthesi').val('SPINAL');
    $('#lapOpsRanapKategori').val('Besar');
};

// ── HAPUS LAPORAN OPERASI RANAP ──
window.hapusLaporanOpsRanap = function (noRawat, tanggal) {
    if (!isDokterOrAdmin) {
        showToast('Hanya Dokter dan Admin Utama yang berwenang menghapus Laporan Operasi.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Hapus Laporan Operasi?',
        html: `Apakah Anda yakin ingin menghapus Laporan Operasi tanggal <b>${tanggal}</b>? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("rawat-inap.hapus-laporan-operasi") }}',
                type: 'DELETE',
                data: {
                    no_rawat: noRawat,
                    tanggal: tanggal,
                    _token: CSRF
                },
                success: function (res) {
                    showToast(res.message || 'Laporan operasi berhasil dihapus.', 'success');
                    if (currentNoRawat) refreshDetailPanel(currentNoRawat);
                },
                error: function (err) {
                    showToast('Gagal menghapus laporan operasi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'), 'error');
                }
            });
        }
    });
};

// ── Utility ───────────────────────────────────────────────────
function fmtDate(val) {
    if (!val) return '-';
    try {
        return new Date(val).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
    } catch(e) { return val; }
}
function fmtRupiah(val) {
    if (!val) return '-';
    return 'Rp ' + Number(val).toLocaleString('id-ID');
}
function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
