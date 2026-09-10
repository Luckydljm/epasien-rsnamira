@extends('layouts.app')

@section('title', 'Rawat Jalan')
@section('page-title', 'Rawat Jalan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

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

    /* === Filter Card === */
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

    /* === 4 Stat Mini Cards Grid === */
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

    /* === Table Card Container === */
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

    /* Action Button Pelayanan Medis Dokter */
    .btn-pelayanan {
        background: #0d7044;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 800;
        padding: .4rem .85rem;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        transition: all .2s ease;
        cursor: pointer;
    }
    .btn-pelayanan:hover {
        background: #085633;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(13, 112, 68, 0.3);
        transform: translateY(-1px);
    }

    /* === OFFCANVAS PELAYANAN DOKTER (FULL 1 LAYAR 2-SECTION SPLIT LAYOUT) === */
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
        background: linear-gradient(135deg, #042b1b, #0d7044);
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
        height: calc(100vh - 52px);
        overflow: hidden;
    }

    /* Section Kiri: Sidebar Pasien — Full Height, Flex Column */
    .emr-sidebar-left {
        width: 290px;
        flex: 0 0 290px;
        background: #f1f5f9;
        border-right: 1.5px solid #cbd5e1;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        padding: 0;
    }

    /* Patient Card Styles */
    .patient-card-header {
        background: linear-gradient(160deg, #0d7044 0%, #064e31 100%);
        padding: 1rem 1rem .85rem;
        color: #fff;
        flex-shrink: 0;
    }
    .patient-avatar-lg {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: rgba(255,255,255,0.2);
        color: #fff;
        font-size: 1.05rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 2px solid rgba(255,255,255,0.28);
        letter-spacing: -1px;
    }
    .patient-name-text {
        font-size: .9rem;
        font-weight: 800;
        color: #fff;
        line-height: 1.25;
        word-break: break-word;
    }
    .patient-badge-status {
        display: inline-flex;
        align-items: center;
        gap: .2rem;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.28);
        color: #bbf7d0;
        border-radius: 20px;
        padding: .12rem .5rem;
        font-size: .65rem;
        font-weight: 700;
        margin-top: .28rem;
    }
    .patient-id-strip {
        background: #f8fafc;
        padding: .5rem 1rem;
        display: flex;
        flex-direction: column;
        gap: .32rem;
        border-bottom: 1.5px solid #e2e8f0;
        border-top: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .patient-id-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 20px;
    }
    .patient-id-label {
        font-size: .65rem;
        color: #94a3b8;
        font-weight: 600;
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
        padding: .42rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        gap: .5rem;
    }
    .patient-info-row:last-child { border-bottom: none; }
    .patient-info-label {
        font-size: .67rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .25px;
        white-space: nowrap;
        min-width: 76px;
        padding-top: .08rem;
    }
    .patient-info-val {
        font-size: .8rem;
        font-weight: 700;
        color: #1e293b;
        word-break: break-word;
        flex: 1;
        line-height: 1.3;
    }

    /* Section Kanan: Main Canvas Form & Riwayat */
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
        font-size: .88rem;
        font-weight: 800;
        padding: 0.85rem 1.1rem;
        border-bottom: 3.5px solid transparent;
        transition: all .2s ease;
        display: flex;
        align-items: center;
        gap: .45rem;
    }
    .nav-tabs-pelayanan .nav-link i {
        font-size: 1.05rem;
    }
    .nav-tabs-pelayanan .nav-link:hover {
        color: #0d7044;
        background: rgba(13,112,68,0.04);
    }
    .nav-tabs-pelayanan .nav-link.active {
        color: #0d7044;
        border-bottom-color: #0d7044;
        background: transparent;
    }

    .offcanvas-pelayanan .offcanvas-body {
        flex: 1 1 auto !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 1.5rem 2rem 5rem 2rem !important;
        height: 100% !important;
        min-height: 0 !important;
    }

    .tab-content-pelayanan {
        padding: 0 !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }

    .tab-content-pelayanan .tab-pane {
        padding-bottom: 3.5rem !important;
    }

    .form-section-title {
        font-size: .88rem;
        font-weight: 800;
        color: #0f172a;
        border-bottom: 1.5px solid #e2e8f0;
        padding-bottom: .4rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .45rem;
    }

    .search-results-dropdown {
        position: absolute;
        top: 100%; left: 0; right: 0;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        max-height: 220px;
        overflow-y: auto;
        z-index: 1050;
        display: none;
    }
    .search-results-dropdown .dropdown-item {
        font-size: .82rem;
        font-weight: 600;
        padding: .5rem .85rem;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
    }
    .search-results-dropdown .dropdown-item:hover {
        background: #f0fdf4;
        color: #0d7044;
    }

    .selected-items-table {
        width: 100%;
        font-size: .81rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
    .selected-items-table th {
        background: #f8fafc;
        font-weight: 800;
        font-size: .72rem;
        text-transform: uppercase;
        padding: .6rem .8rem;
    }
    .selected-items-table td {
        padding: .6rem .8rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Select2 custom adjustments for Bootstrap 5 inside offcanvas */
    .select2-container--bootstrap-5 .select2-selection {
        font-size: .83rem;
        border-radius: 8px;
        min-height: 34px;
        padding-top: .15rem;
        border-color: #cbd5e1;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: var(--rajal-primary);
        box-shadow: 0 0 0 3px rgba(13, 112, 68, 0.12);
    }

    .select2-container {
        z-index: 99999 !important;
    }
    .select2-dropdown {
        z-index: 99999 !important;
    }

    /* SweetAlert2 container on top of offcanvas */
    .swal2-container {
        z-index: 100000 !important;
    }

    .table-emr-detail th {
        background-color: #f8fafc;
        font-weight: 700;
        font-size: .78rem;
        color: #334155;
        border-bottom: 1.5px solid #e2e8f0;
        padding: .55rem .75rem;
    }
    .table-emr-detail td {
        font-size: .8rem;
        padding: .55rem .75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-emr-detail tbody tr:hover {
        background-color: #f8fafc;
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
        <div class="col">
            <div class="mini-stat">
                <div class="mini-stat-icon red"><i class="bi bi-lightning-charge-fill"></i></div>
                <div>
                    <div class="mini-stat-value">{{ $statsRajal['igd'] }}</div>
                    <div class="mini-stat-label">Pasien IGD</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="mini-stat">
                <div class="mini-stat-icon purple"><i class="bi bi-building"></i></div>
                <div>
                    <div class="mini-stat-value">{{ $statsRajal['poli'] }}</div>
                    <div class="mini-stat-label">Pasien Poli</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="mini-stat">
                <div class="mini-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="mini-stat-value">{{ $statsRajal['terlayani'] }}</div>
                    <div class="mini-stat-label">Sudah Terlayani</div>
                </div>
            </div>
        </div>
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
                            <th>No. Rawat / RM</th>
                            <th>Pasien</th>
                            <th>Waktu Registrasi</th>
                            <th>Dokter Dituju</th>
                            <th>Poliklinik</th>
                            <th>Penanggung Jawab</th>
                            <th>No. HP Pasien</th>
                            <th>Stts Poli</th>
                            <th>Jenis Bayar</th>
                            <th>Status Pasien</th>
                            <th>Status Bayar</th>
                            <th>SEP</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pasienList as $i => $p)
                        @php
                            $namaPasien = ucwords(strtolower($p->nm_pasien ?? '-'));
                            $namaDokter = $formatDokterName($p->nm_dokter ?? '');

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

                            $statusBayar = strtolower(trim($p->status_bayar ?? ''));
                            $bayarSudah  = in_array($statusBayar, ['sudah', 'lunas', 'paid', '1', 'true', 'sudah bayar']);

                            $noTlp = $p->no_tlp ?? null;
                            $noSep = $p->no_sep ?? null;

                            $nmPj      = ucwords(strtolower($p->nm_penjamin ?? ''));
                            $alamatPj  = $p->alamat_pj ?? '';
                            $hubPj     = $p->hub_keluarga ?? '';
                            $jenisBayar = $p->jenis_bayar ?? '';

                            $jkText = ($p->jk ?? '') === 'L' ? 'Laki-laki (♂)' : (($p->jk ?? '') === 'P' ? 'Perempuan (♀)' : '-');
                            $umurText = (!empty($p->tgl_lahir) && $p->tgl_lahir !== '0000-00-00') ? \Carbon\Carbon::parse($p->tgl_lahir)->age . ' tahun (' . \Carbon\Carbon::parse($p->tgl_lahir)->format('d-m-Y') . ')' : '-';

                            $nmPoli = ucwords(strtolower($p->nm_poli ?? '-'));
                            $isIgd  = ($p->jenis_kunjungan ?? '') === 'IGD';
                            $noRawatB64 = base64_encode($p->no_rawat);
                        @endphp
                        <tr>
                            <td style="color:#94a3b8;font-size:.78rem;text-align:center;font-weight:600;">{{ $i + 1 }}</td>

                            <td>
                                <div><span class="no-rawat-badge">{{ $p->no_rawat ?? '-' }}</span></div>
                                <div class="mt-1"><span class="no-rm-chip">RM: {{ $p->no_rkm_medis ?? '-' }}</span></div>
                            </td>

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

                            <td>
                                <div style="font-size:.83rem;font-weight:700;color:#0f172a;">
                                    <i class="bi bi-person-fill me-1" style="color:#0d7044;font-size:.8rem;"></i>{{ $namaDokter }}
                                </div>
                                @if(!empty($p->kd_dokter))
                                    <div class="mt-1"><span class="code-chip">{{ $p->kd_dokter }}</span></div>
                                @endif
                            </td>

                            <td>
                                @if($isIgd)
                                    <span class="badge-poli-igd"><i class="bi bi-lightning-fill"></i> {{ $nmPoli }} (IGD)</span>
                                @else
                                    <span class="badge-poli-main"><i class="bi bi-building"></i> {{ $nmPoli }}</span>
                                @endif
                            </td>

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

                            <td>
                                @if($noTlp && $noTlp !== '000000000000000' && $noTlp !== '0')
                                    <a href="tel:{{ $noTlp }}" style="font-size:.78rem;color:#0284c7;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;background:#f0f9ff;border:1px solid #bae6fd;padding:.2rem .6rem;border-radius:8px;">
                                        <i class="bi bi-telephone-fill" style="font-size:.7rem;"></i>{{ $noTlp }}
                                    </a>
                                @else
                                    <span style="color:#94a3b8;font-size:.78rem;">—</span>
                                @endif
                            </td>

                            <td>
                                @if(($p->stts_poli ?? '') !== '')
                                    <span class="badge-stts-poli-ded">{{ ucfirst($p->stts_poli) }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:.78rem;">—</span>
                                @endif
                            </td>

                            <td>
                                @if($jenisBayar)
                                    <span class="badge-penjamin"><i class="bi bi-credit-card-2-front-fill"></i> {{ $jenisBayar }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:.78rem;">—</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge-stts {{ $sttsCls }}">
                                    @if($sttsCls === 'sudah') <span class="status-dot dot-green"></span>
                                    @elseif($sttsCls === 'belum') <span class="status-dot dot-amber"></span>
                                    @endif
                                    {{ ucfirst($p->status_pasien ?? '—') }}
                                </span>
                            </td>

                            <td>
                                @if(($p->status_bayar ?? '') !== '')
                                    @if($bayarSudah)
                                        <span class="badge-sudah-bayar"><i class="bi bi-check-circle-fill"></i> Sudah Bayar</span>
                                    @else
                                        <span class="badge-belum-bayar"><i class="bi bi-exclamation-circle-fill"></i> Belum Bayar</span>
                                    @endif
                                @else
                                    <span style="color:#94a3b8;font-size:.78rem;">—</span>
                                @endif
                            </td>

                            <td>
                                @if($noSep)
                                    <span class="badge-sep"><i class="bi bi-shield-fill-check" style="color:#0284c7;"></i> {{ $noSep }}</span>
                                @else
                                    <span class="badge-sep empty">Belum Ada SEP</span>
                                @endif
                            </td>

                            <td style="text-align:center;">
                                <button type="button" class="btn-pelayanan open-pelayanan-btn"
                                        data-norawat-b64="{{ $noRawatB64 }}"
                                        data-norawat="{{ $p->no_rawat }}"
                                        data-norm="{{ $p->no_rkm_medis }}"
                                        data-nama="{{ $namaPasien }}"
                                        data-jk="{{ $jkText }}"
                                        data-umur="{{ $umurText }}"
                                        data-tlp="{{ $noTlp ?: '-' }}"
                                        data-poli="{{ $nmPoli }}"
                                        data-dokter="{{ $namaDokter }}"
                                        data-bayar="{{ $jenisBayar }}"
                                        data-pj="{{ $nmPj ?: '-' }}"
                                        data-hubpj="{{ $hubPj ?: '-' }}"
                                        data-alamatpj="{{ $alamatPj ?: '-' }}">
                                    <i class="bi bi-journal-medical me-1"></i> Pelayanan Medis
                                </button>
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

{{-- ============================================================
     OFFCANVAS DRAWER PELAYANAN DOKTER (FULL SCREEN EMR 2-SECTION SPLIT)
============================================================ --}}
<div class="offcanvas offcanvas-end offcanvas-pelayanan" tabindex="-1" id="offcanvasPelayanan" aria-labelledby="offcanvasPelayananLabel">
    <div class="offcanvas-header">
        <div class="d-flex align-items-center gap-3">
            <h5 class="offcanvas-title" id="offcanvasPelayananLabel">
                <i class="bi bi-stethoscope text-emerald-300 me-1"></i> SIMRS EMR — Lembar Pelayanan Medis Dokter
            </h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    {{-- CONTAINER SPLIT 2 SECTION (KIRI: PASIEN MINIMALIS, KANAN: FORM & RIWAYAT) --}}
    <div class="emr-split-container">
        
        {{-- SECTION KIRI: SIDEBAR DATA PASIEN COMPACT & ESTETIK --}}
        <div class="emr-sidebar-left">

            {{-- Gradient Header: Avatar + Nama + Status --}}
            <div class="patient-card-header">
                <div class="d-flex align-items-start gap-2.5">
                    <div class="patient-avatar-lg me-2" id="ofcAvatar">P</div>
                    <div style="min-width:0;flex:1;">
                        <div class="patient-name-text" id="ofcNamaPasien">Loading Pasien...</div>
                        <div class="patient-badge-status">
                            <i class="bi bi-check2-circle"></i> Pasien Aktif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ID Strip: No Rawat, No RM, Poli, Penjamin --}}
            <div class="patient-id-strip">
                <div class="patient-id-row">
                    <span class="patient-id-label">No. Rawat</span>
                    <span class="no-rawat-badge" id="ofcNoRawat" style="font-size:.72rem;padding:.18rem .55rem;">—</span>
                </div>
                <div class="patient-id-row">
                    <span class="patient-id-label">No. RM</span>
                    <span class="no-rm-chip" id="ofcNoRm" style="font-size:.72rem;padding:.18rem .55rem;">—</span>
                </div>
                <div class="patient-id-row">
                    <span class="patient-id-label">Poliklinik</span>
                    <span class="badge-poli-main" id="ofcPoli" style="font-size:.72rem;padding:.18rem .55rem;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">—</span>
                </div>
                <div class="patient-id-row">
                    <span class="patient-id-label">Penjamin</span>
                    <span class="badge-penjamin" id="ofcBayar" style="font-size:.72rem;padding:.18rem .55rem;">—</span>
                </div>
            </div>

            {{-- Demografi Detail: Inline Label-Value Rows --}}
            <div class="patient-info-grid">
                <div class="patient-info-row">
                    <span class="patient-info-label">Kelamin</span>
                    <span id="ofcJk" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Lahir / Umur</span>
                    <span id="ofcUmur" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">No. Telepon</span>
                    <span id="ofcTlp" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Dokter DPJP</span>
                    <span id="ofcDokter" class="patient-info-val">—</span>
                </div>
                <div class="patient-info-row">
                    <span class="patient-info-label">Alamat</span>
                    <span id="ofcAlamatPasien" class="patient-info-val">Memuat...</span>
                </div>
            </div>

        </div>

        {{-- SECTION KANAN: MAIN WORKING EMR CANVAS (TABS + FORM + RIWAYAT) --}}
        <div class="emr-main-right">
            
            {{-- Nav Tab Modul Dokter (6 MODUL LENGKAP SIMRS KHANZA) --}}
            <ul class="nav nav-tabs nav-tabs-pelayanan" id="pelayananTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-soap-btn" data-bs-toggle="tab" data-bs-target="#tab-soap" type="button" role="tab">
                        <i class="bi bi-journal-medical"></i> 1. SOAP
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-awalmedis-btn" data-bs-toggle="tab" data-bs-target="#tab-awalmedis" type="button" role="tab">
                        <i class="bi bi-clipboard2-user-fill"></i> 2. Awal Medis
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-lab-btn" data-bs-toggle="tab" data-bs-target="#tab-lab" type="button" role="tab">
                        <i class="bi bi-eyedropper"></i> 3. Laboratorium
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-rad-btn" data-bs-toggle="tab" data-bs-target="#tab-rad" type="button" role="tab">
                        <i class="bi bi-intersect"></i> 4. Radiologi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-resep-btn" data-bs-toggle="tab" data-bs-target="#tab-resep" type="button" role="tab">
                        <i class="bi bi-capsule"></i> 5. Resep Dokter
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-operasi-btn" data-bs-toggle="tab" data-bs-target="#tab-operasi" type="button" role="tab">
                        <i class="bi bi-calendar-event-fill"></i> 6. Jadwal Operasi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-diagnosa-btn" data-bs-toggle="tab" data-bs-target="#tab-diagnosa" type="button" role="tab">
                        <i class="bi bi-search-heart"></i> 7. Diagnosa
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-tindakan-btn" data-bs-toggle="tab" data-bs-target="#tab-tindakan" type="button" role="tab">
                        <i class="bi bi-activity"></i> 8. Tindakan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-resume-btn" data-bs-toggle="tab" data-bs-target="#tab-resume" type="button" role="tab">
                        <i class="bi bi-file-earmark-medical"></i> 9. Resume Ralan
                    </button>
                </li>
            </ul>

            {{-- Isi Konten 6 Tab Modul --}}
            <div class="offcanvas-body">
                <div class="tab-content tab-content-pelayanan" id="pelayananTabContent">

            {{-- ===================================
                 1. MODUL SOAP LENGKAP (pemeriksaan_ralan)
            =================================== --}}
            <div class="tab-pane fade show active" id="tab-soap" role="tabpanel">
                <form id="formSoap">
                    <input type="hidden" name="no_rawat" id="soapNoRawat">

                    <div class="form-section-title"><i class="bi bi-heart-pulse-fill text-danger"></i> Tanda Vital (TTV) &amp; Fisik Utama</div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Suhu Tubuh (°C)</label>
                            <input type="text" name="suhu_tubuh" id="soapSuhu" class="form-control form-control-sm" placeholder="36.5">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Tensi (mmHg)</label>
                            <input type="text" name="tensi" id="soapTensi" class="form-control form-control-sm" placeholder="120/80">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Nadi (x/mnt)</label>
                            <input type="text" name="nadi" id="soapNadi" class="form-control form-control-sm" placeholder="80">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Respirasi (x/mnt)</label>
                            <input type="text" name="respirasi" id="soapRespirasi" class="form-control form-control-sm" placeholder="20">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Tinggi Badan (cm)</label>
                            <input type="text" name="tinggi" id="soapTinggi" class="form-control form-control-sm" placeholder="165">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Berat Badan (kg)</label>
                            <input type="text" name="berat" id="soapBerat" class="form-control form-control-sm" placeholder="60">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">SpO2 (%)</label>
                            <input type="text" name="spo2" id="soapSpo2" class="form-control form-control-sm" placeholder="98">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">GCS (E,V,M)</label>
                            <input type="text" name="gcs" id="soapGcs" class="form-control form-control-sm" placeholder="15">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Kesadaran</label>
                            <select name="kesadaran" id="soapKesadaran" class="form-select form-select-sm select2-search">
                                <option value="Compos Mentis">Compos Mentis</option>
                                <option value="Apatis">Apatis</option>
                                <option value="Somnolen">Somnolen</option>
                                <option value="Sopor">Sopor</option>
                                <option value="Koma">Koma</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Lingkar Perut (cm)</label>
                            <input type="text" name="lingkar_perut" id="soapLingkarPerut" class="form-control form-control-sm" placeholder="80">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Alergi Obat/Makanan</label>
                            <input type="text" name="alergi" id="soapAlergi" class="form-control form-control-sm" placeholder="Alergi...">
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bi bi-file-earmark-medical-fill text-success"></i> Form SOAP &amp; Evaluasi (Lengkap Khanza SIMRS)</div>
                    <div class="mb-2">
                        <label class="form-label font-bold text-xs text-slate-600 mb-1"><strong>[S] Subjek / Keluhan Utama Pasien:</strong></label>
                        <textarea name="keluhan" id="soapKeluhan" class="form-control form-control-sm" rows="2" placeholder="Keluhan utama pasien..."></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label font-bold text-xs text-slate-600 mb-1"><strong>[O] Objek / Hasil Pemeriksaan Fisik &amp; Penunjang:</strong></label>
                        <textarea name="pemeriksaan" id="soapPemeriksaan" class="form-control form-control-sm" rows="2" placeholder="Hasil pemeriksaan fisik / penunjang..."></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label font-bold text-xs text-slate-600 mb-1"><strong>[A] Asesmen / Diagnosis Kerja / ICD-10:</strong></label>
                        <textarea name="penilaian" id="soapPenilaian" class="form-control form-control-sm" rows="2" placeholder="Asesmen klinis / diagnosis pasien..."></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label font-bold text-xs text-slate-600 mb-1"><strong>[P] Plan / Rencana Tindak Lanjut:</strong></label>
                        <textarea name="rtl" id="soapRtl" class="form-control form-control-sm" rows="2" placeholder="Rencana terapi / penatalaksanaan..."></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Instruksi / Implikasi Klinik</label>
                            <textarea name="instruksi" id="soapInstruksi" class="form-control form-control-sm" rows="2" placeholder="Instruksi perawat / farmasi..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Evaluasi Keperawatan / Medis</label>
                            <textarea name="evaluasi" id="soapEvaluasi" class="form-control form-control-sm" rows="2" placeholder="Evaluasi kondisi pasien..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanSoap">
                            <i class="bi bi-save-fill me-1"></i> Simpan Data SOAP
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-clock-history text-primary"></i> Riwayat SOAP Pasien Ini</div>
                <div id="riwayatSoapContainer">
                    <span class="text-muted text-xs">Memuat riwayat SOAP...</span>
                </div>
            </div>

            {{-- ===================================
                 2. MODUL AWAL MEDIS LENGKAP (penilaian_medis_ralan)
            =================================== --}}
            <div class="tab-pane fade" id="tab-awalmedis" role="tabpanel">
                <form id="formAwalMedis">
                    <input type="hidden" name="no_rawat" id="awalNoRawat">

                    {{-- SECTION 1: ANAMNESIS MEDIS --}}
                    <div class="form-section-title"><i class="bi bi-person-lines-fill text-primary"></i> 1. Anamnesis Medis</div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Jenis Anamnesis</label>
                            <select name="anamnesis" id="awalAnamnesis" class="form-select form-select-sm select2-search">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Hubungan Alloanamnesis</label>
                            <input type="text" name="hubungan" id="awalHubungan" class="form-control form-control-sm" placeholder="Hubungan jika Alloanamnesis...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Alergi Obat / Makanan</label>
                            <input type="text" name="alergi" id="awalAlergi" class="form-control form-control-sm" placeholder="Riwayat alergi...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Keluhan Utama</label>
                            <textarea name="keluhan_utama" id="awalKeluhanUtama" class="form-control form-control-sm" rows="2" placeholder="Keluhan utama pasien..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Riwayat Penyakit Sekarang (RPS)</label>
                            <textarea name="rps" id="awalRps" class="form-control form-control-sm" rows="2" placeholder="Perjalanan penyakit saat ini..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Riwayat Penyakit Dahulu (RPD)</label>
                            <textarea name="rpd" id="awalRpd" class="form-control form-control-sm" rows="2" placeholder="Riwayat penyakit dahulu..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Riwayat Penyakit Keluarga (RPK)</label>
                            <textarea name="rpk" id="awalRpk" class="form-control form-control-sm" rows="2" placeholder="Riwayat penyakit keluarga..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Riwayat Penggunaan Obat (RPO)</label>
                            <textarea name="rpo" id="awalRpo" class="form-control form-control-sm" rows="2" placeholder="Obat yang sedang dikonsumsi..."></textarea>
                        </div>
                    </div>

                    {{-- SECTION 2: KEADAAN UMUM & VITAL SIGNS --}}
                    <div class="form-section-title"><i class="bi bi-heart-pulse text-danger"></i> 2. Keadaan Umum &amp; Tanda Vital</div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6 col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Keadaan Umum</label>
                            <select name="keadaan" id="awalKeadaan" class="form-select form-select-sm select2-search">
                                <option value="Sehat">Sehat</option>
                                <option value="Sakit Ringan">Sakit Ringan</option>
                                <option value="Sakit Sedang">Sakit Sedang</option>
                                <option value="Sakit Berat">Sakit Berat</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Kesadaran</label>
                            <select name="kesadaran" id="awalKesadaran" class="form-select form-select-sm select2-search">
                                <option value="Compos Mentis">Compos Mentis</option>
                                <option value="Apatis">Apatis</option>
                                <option value="Somnolen">Somnolen</option>
                                <option value="Sopor">Sopor</option>
                                <option value="Koma">Koma</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">GCS (E,V,M)</label>
                            <input type="text" name="gcs" id="awalGcs" class="form-control form-control-sm" value="15">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">TD (mmHg)</label>
                            <input type="text" name="td" id="awalTd" class="form-control form-control-sm" placeholder="120/80">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Nadi (x/m)</label>
                            <input type="text" name="nadi" id="awalNadi" class="form-control form-control-sm" placeholder="80">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">RR (x/m)</label>
                            <input type="text" name="rr" id="awalRr" class="form-control form-control-sm" placeholder="20">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Suhu (°C)</label>
                            <input type="text" name="suhu" id="awalSuhu" class="form-control form-control-sm" placeholder="36.5">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">SpO2 (%)</label>
                            <input type="text" name="spo" id="awalSpo" class="form-control form-control-sm" placeholder="98">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">BB (kg) / TB (cm)</label>
                            <div class="d-flex gap-1">
                                <input type="text" name="bb" id="awalBb" class="form-control form-control-sm" placeholder="BB">
                                <input type="text" name="tb" id="awalTb" class="form-control form-control-sm" placeholder="TB">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: PEMERIKSAAN FISIK GENERALIS (8 SYSTEM ORGANS) --}}
                    <div class="form-section-title"><i class="bi bi-body-text text-primary"></i> 3. Pemeriksaan Fisik Generalis</div>
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Kepala</label>
                            <select name="kepala" id="awalKepala" class="form-select form-select-sm select2-search">
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Gigi &amp; Mulut</label>
                            <select name="gigi" id="awalGigi" class="form-select form-select-sm select2-search">
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">THT</label>
                            <select name="tht" id="awalTht" class="form-select form-select-sm select2-search">
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Thoraks</label>
                            <select name="thoraks" id="awalThoraks" class="form-select form-select-sm select2-search">
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Abdomen</label>
                            <select name="abdomen" id="awalAbdomen" class="form-select form-select-sm select2-search">
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Genital</label>
                            <select name="genital" id="awalGenital" class="form-select form-select-sm select2-search">
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Ekstremitas</label>
                            <select name="ekstremitas" id="awalEkstremitas" class="form-select form-select-sm select2-search">
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Kulit</label>
                            <select name="kulit" id="awalKulit" class="form-select form-select-sm select2-search">
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </select>
                        </div>
                    </div>

                    {{-- SECTION 4: STATUS LOKALIS & PENUNJANG --}}
                    <div class="form-section-title"><i class="bi bi-card-checklist text-purple-700"></i> 4. Keterangan Fisik, Status Lokalis &amp; Penunjang</div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Keterangan Pemeriksaan Fisik</label>
                            <textarea name="ket_fisik" id="awalKetFisik" class="form-control form-control-sm" rows="2" placeholder="Penjelasan temuan fisik..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Status Lokalis</label>
                            <textarea name="ket_lokalis" id="awalKetLokalis" class="form-control form-control-sm" rows="2" placeholder="Temuan lokasi/organ khusus..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Pemeriksaan Penunjang (Lab/Rad/EKG)</label>
                            <textarea name="penunjang" id="awalPenunjang" class="form-control form-control-sm" rows="2" placeholder="Hasil EKG, USG, Laboratorium..."></textarea>
                        </div>
                    </div>

                    {{-- SECTION 5: DIAGNOSIS & TATA LAKSANA --}}
                    <div class="form-section-title"><i class="bi bi-journal-check text-success"></i> 5. Diagnosis, Tata Laksana &amp; Konsul Rujukan</div>
                    <div class="mb-2">
                        <label class="form-label font-bold text-xs text-slate-600 mb-1"><strong>Diagnosis Utama &amp; Sekunder:</strong></label>
                        <textarea name="diagnosis" id="awalDiagnosis" class="form-control form-control-sm" rows="2" placeholder="Diagnosis kerja / ICD-10..."></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label font-bold text-xs text-slate-600 mb-1"><strong>Tata Laksana / Rencana Pengobatan:</strong></label>
                        <textarea name="tata" id="awalTata" class="form-control form-control-sm" rows="2" placeholder="Terapi, resep obat, tindakan medis..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold text-xs text-slate-600 mb-1">Edukasi / Konsul / Rujukan Interdisiplin</label>
                        <textarea name="konsulrujuk" id="awalKonsulrujuk" class="form-control form-control-sm" rows="2" placeholder="Konsul spesialistik, rujukan..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanAwalMedis">
                            <i class="bi bi-save-fill me-1"></i> Simpan Penilaian Awal Medis
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-clock-history text-primary"></i> Riwayat Penilaian Awal Medis Pasien Ini</div>
                <div id="riwayatAwalMedisContainer">
                    <span class="text-muted text-xs">Memuat riwayat Penilaian Awal Medis...</span>
                </div>
            </div>

            {{-- ===================================
                 3. MODUL LABORATORIUM (permintaan_lab)
            =================================== --}}
            <div class="tab-pane fade" id="tab-lab" role="tabpanel">
                <form id="formLab">
                    <input type="hidden" name="no_rawat" id="labNoRawat">

                    <div class="form-section-title"><i class="bi bi-search text-success"></i> Cari Pemeriksaan Laboratorium</div>
                    <div class="position-relative mb-3">
                        <input type="text" id="searchLabInput" class="form-control form-control-sm" placeholder="Ketik nama pemeriksaan Lab (misal: Hematologi, Urin, SGOT, Gula Darah)..." autocomplete="off">
                        <div id="searchLabDropdown" class="search-results-dropdown"></div>
                    </div>

                    <div class="form-section-title"><i class="bi bi-check-square-fill text-success"></i> Daftar Pemeriksaan Lab Dipilih</div>
                    <table class="selected-items-table mb-3">
                        <thead>
                            <tr>
                                <th style="width:90px;">Kode</th>
                                <th>Nama Pemeriksaan Lab</th>
                                <th style="width:130px;">Tarif (Rp)</th>
                                <th style="width:130px;">Penjamin</th>
                                <th style="width:50px;text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="selectedLabTbody">
                            <tr><td colspan="5" class="text-center text-muted py-3 fs-7">Belum ada pemeriksaan Lab yang dipilih.</td></tr>
                        </tbody>
                    </table>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Diagnosa Klinis</label>
                            <input type="text" name="diagnosa_klinis" id="labDiagnosa" class="form-control form-control-sm" placeholder="Indikasi/diagnosa klinis...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Informasi Tambahan</label>
                            <input type="text" name="informasi_tambahan" id="labInfo" class="form-control form-control-sm" placeholder="Catatan untuk petugas Lab...">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanLab">
                            <i class="bi bi-send-fill me-1"></i> Kirim Permintaan LAB
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-file-earmark-check-fill text-success"></i> Hasil Pemeriksaan Laboratorium Pasien</div>
                <div id="hasilLabContainer"><span class="text-muted text-xs">Belum ada hasil pemeriksaan Laboratorium.</span></div>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-clock-history text-primary"></i> Riwayat Permintaan Lab</div>
                <div id="riwayatLabContainer"><span class="text-muted text-xs">Belum ada riwayat permintaan Lab.</span></div>
            </div>

            {{-- ===================================
                 4. MODUL RADIOLOGI (permintaan_radiologi)
            =================================== --}}
            <div class="tab-pane fade" id="tab-rad" role="tabpanel">
                <form id="formRad">
                    <input type="hidden" name="no_rawat" id="radNoRawat">

                    <div class="form-section-title"><i class="bi bi-search text-success"></i> Cari Pemeriksaan Radiologi</div>
                    <div class="position-relative mb-3">
                        <input type="text" id="searchRadInput" class="form-control form-control-sm" placeholder="Ketik jenis pemeriksaan Radiologi (misal: Thorax AP, USG Abdomen, CT Scan)..." autocomplete="off">
                        <div id="searchRadDropdown" class="search-results-dropdown"></div>
                    </div>

                    <div class="form-section-title"><i class="bi bi-check-square-fill text-success"></i> Daftar Pemeriksaan Radiologi Dipilih</div>
                    <table class="selected-items-table mb-3">
                        <thead>
                            <tr>
                                <th style="width:90px;">Kode</th>
                                <th>Nama Pemeriksaan Radiologi</th>
                                <th style="width:130px;">Tarif (Rp)</th>
                                <th style="width:130px;">Penjamin</th>
                                <th style="width:50px;text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="selectedRadTbody">
                            <tr><td colspan="5" class="text-center text-muted py-3 fs-7">Belum ada pemeriksaan Radiologi yang dipilih.</td></tr>
                        </tbody>
                    </table>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Diagnosa Klinis</label>
                            <input type="text" name="diagnosa_klinis" id="radDiagnosa" class="form-control form-control-sm" placeholder="Indikasi/diagnosa klinis...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Informasi Tambahan</label>
                            <input type="text" name="informasi_tambahan" id="radInfo" class="form-control form-control-sm" placeholder="Catatan untuk radiografer...">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanRad">
                            <i class="bi bi-send-fill me-1"></i> Kirim Permintaan Radiologi
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-file-earmark-medical-fill text-primary"></i> Hasil Ekspertisi Radiologi Pasien</div>
                <div id="hasilRadContainer"><span class="text-muted text-xs">Belum ada hasil ekspertisi Radiologi.</span></div>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-clock-history text-primary"></i> Riwayat Permintaan Radiologi</div>
                <div id="riwayatRadContainer"><span class="text-muted text-xs">Belum ada riwayat permintaan Radiologi.</span></div>
            </div>

            {{-- ===================================
                 5. MODUL RESEP DOKTER (resep_obat)
            =================================== --}}
            <div class="tab-pane fade" id="tab-resep" role="tabpanel">
                <form id="formResep">
                    <input type="hidden" name="no_rawat" id="resepNoRawat">

                    {{-- Datalist master_aturan_pakai --}}
                    <datalist id="listAturanPakai"></datalist>

                    {{-- Sub-tab Mode Peresepan --}}
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <ul class="nav nav-pills gap-1" id="pills-resep-mode" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="btn btn-sm btn-outline-success active font-bold text-xs py-1 px-3 rounded-pill" id="pills-obat-jadi-tab" data-bs-toggle="pill" data-bs-target="#pills-obat-jadi" type="button" role="tab">
                                    <i class="bi bi-capsule me-1"></i> 1. Obat Jadi (Non-Racikan)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="btn btn-sm btn-outline-primary font-bold text-xs py-1 px-3 rounded-pill" id="pills-obat-racik-tab" data-bs-toggle="pill" data-bs-target="#pills-obat-racik" type="button" role="tab">
                                    <i class="bi bi-mortarboard-fill me-1"></i> 2. Obat Racikan
                                </button>
                            </li>
                        </ul>
                        <span class="text-xs text-muted"><i class="bi bi-info-circle me-1"></i>Standar SIMRS Namira</span>
                    </div>

                    <div class="tab-content mb-3" id="pills-resep-mode-content">
                        {{-- SUB-TAB 1: OBAT JADI --}}
                        <div class="tab-pane fade show active" id="pills-obat-jadi" role="tabpanel">
                            <div class="card p-3 border text-xs mb-3" style="border-radius:10px;background:#f8fafc;">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Cari Obat Jadi / Farmasi</label>
                                <div class="position-relative">
                                    <input type="text" id="searchObatInput" class="form-control form-control-sm" placeholder="Ketik nama / kode obat (misal: Paracetamol, Amoxicillin)..." autocomplete="off">
                                    <div id="searchObatDropdown" class="search-results-dropdown"></div>
                                </div>
                            </div>

                            <div class="form-section-title"><i class="bi bi-check2-circle text-success"></i> Daftar Obat Jadi yang Dipilih</div>
                            <div class="table-responsive border rounded-3 overflow-hidden shadow-xs bg-white mb-2">
                                <table class="table table-hover table-emr-detail align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nama Obat &amp; Satuan</th>
                                            <th style="width:90px; text-align:center;">Jumlah</th>
                                            <th style="width:230px;">Aturan Pakai</th>
                                            <th style="width:140px;">Keterangan</th>
                                            <th style="width:50px; text-align:center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedObatTbody">
                                        <tr><td colspan="5" class="text-center text-muted py-3 fs-7">Belum ada obat jadi yang dipilih.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- SUB-TAB 2: OBAT RACIKAN --}}
                        <div class="tab-pane fade" id="pills-obat-racik" role="tabpanel">
                            <div class="card p-3 border text-xs mb-3" style="border-radius:10px;background:#f8fafc;">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Nama Racikan</label>
                                        <input type="text" id="racikNamaInput" class="form-control form-control-sm" placeholder="Misal: Puyer Batuk, Salep Campur...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Metode Racik</label>
                                        <select id="racikMetodeSelect" class="form-select form-select-sm">
                                            <option value="R01">Puyer</option>
                                            <option value="R02">Sirup</option>
                                            <option value="R03">Salep</option>
                                            <option value="R04">Kapsul</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Jml Kemasan</label>
                                        <input type="number" id="racikJmlInput" class="form-control form-control-sm" value="10" min="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Aturan Pakai Racik</label>
                                        <input type="text" id="racikAturanInput" list="listAturanPakai" class="form-control form-control-sm" placeholder="Misal: 3 X 1 Sehari...">
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Keterangan Racik (Opsional)</label>
                                        <input type="text" id="racikKetInput" class="form-control form-control-sm" placeholder="Misal: Diminum sesudah makan, bila demam...">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary btn-sm w-100 font-bold" id="btnBuatGrupRacikan">
                                            <i class="bi bi-plus-circle me-1"></i> Buat Racikan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section-title"><i class="bi bi-boxes text-primary"></i> Daftar Racikan Dokter</div>
                            <div id="daftarRacikanContainer">
                                <div class="text-center py-3 border rounded-3 bg-light text-muted fs-7">
                                    Belum ada obat racikan dibuat. Silakan isi form di atas dan klik <b>Buat Racikan</b>.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Ringkasan & Tombol Simpan Resep --}}
                    <div class="d-flex justify-content-between align-items-center p-2.5 rounded-3 bg-light border mb-3">
                        <span class="text-xs text-slate-600 font-semibold" id="resepSummaryText">
                            <i class="bi bi-cart3 me-1"></i> Total: <b>0</b> Obat Jadi, <b>0</b> Racikan
                        </span>
                        <button type="button" class="btn btn-success font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanResep">
                            <i class="bi bi-send-check me-1"></i> Simpan &amp; Kirim E-Resep ke Farmasi
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-clock-history text-primary"></i> Riwayat Resep Pasien Ini</div>
                <div id="riwayatResepContainer"><span class="text-muted text-xs">Belum ada riwayat resep obat.</span></div>
            </div>

            {{-- ===================================
                 6. MODUL JADWAL OPERASI (booking_operasi - DlgBookingOperasi)
            =================================== --}}
            <div class="tab-pane fade" id="tab-operasi" role="tabpanel">
                <form id="formOperasi">
                    <input type="hidden" name="no_rawat" id="opsNoRawat">

                    <div class="form-section-title"><i class="bi bi-calendar-event text-danger"></i> Booking Jadwal Operasi / Kamar Bedah (OK)</div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Paket Operasi / Tindakan Bedah</label>
                            <select name="kode_paket" id="opsKodePaket" class="form-select form-select-sm select2-search">
                                <option value="">-- Pilih Paket Operasi --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Dokter Operator (Bedah)</label>
                            <select name="kd_dokter" id="opsKdDokter" class="form-select form-select-sm select2-search">
                                <option value="">-- Pilih Dokter Operator --</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal Operasi</label>
                            <input type="date" name="tanggal" id="opsTanggal" class="form-control form-control-sm" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="opsJamMulai" class="form-control form-control-sm" value="08:00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Jam Selesai</label>
                            <input type="time" name="jam_selesai" id="opsJamSelesai" class="form-control form-control-sm" value="09:30">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Status Operasi</label>
                            <select name="status" id="opsStatus" class="form-select form-select-sm select2-search">
                                <option value="Menunggu">Menunggu</option>
                                <option value="Proses Operasi">Proses Operasi</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Batal">Batal</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanOperasi">
                            <i class="bi bi-calendar-check-fill me-1"></i> Simpan Jadwal Operasi
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-file-earmark-text-fill text-danger"></i> Laporan Operasi Pasien</div>
                <div id="laporanOpsContainer"><span class="text-muted text-xs">Belum ada laporan operasi.</span></div>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-clock-history text-primary"></i> Riwayat Booking Operasi Pasien</div>
                <div id="riwayatOpsContainer"><span class="text-muted text-xs">Belum ada booking operasi.</span></div>
            </div>

            {{-- ===================================
                 7. MODUL DIAGNOSA ICD-10 (diagnosa_pasien)
            =================================== --}}
            <div class="tab-pane fade" id="tab-diagnosa" role="tabpanel">
                <div class="form-section-title"><i class="bi bi-search-heart text-primary"></i> Input Diagnosa Pasien (ICD-10)</div>
                <div class="card p-3 mb-3 border text-xs" style="border-radius:10px;background:#f8fafc;">
                    <div class="row g-2">
                        <div class="col-md-7">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Cari Kode / Nama Penyakit</label>
                            <select id="rajalDiagnosaSelect" class="form-select form-select-sm" style="width:100%"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Prioritas</label>
                            <select id="rajalDiagnosaPrioritas" class="form-select form-select-sm">
                                <option value="1">1 — Diagnosa Utama</option>
                                <option value="2">2 — Diagnosa Sekunder</option>
                                <option value="3">3 — Diagnosa Sekunder 2</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-sm w-100 font-bold" id="btnTambahDiagnosaRajal" style="border-radius:8px;">
                                <i class="bi bi-plus-lg me-1"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-section-title"><i class="bi bi-clipboard2-check-fill text-success"></i> Daftar Diagnosa Pasien Ini</div>
                <div id="rajalDiagnosaContainer"><span class="text-muted text-xs">Belum ada diagnosa dicatat.</span></div>
            </div>

            {{-- ===================================
                 8. MODUL TINDAKAN MEDIS (rawat_jl_dr)
            =================================== --}}
            <div class="tab-pane fade" id="tab-tindakan" role="tabpanel">
                <div class="form-section-title"><i class="bi bi-activity text-primary"></i> Input Tindakan Rawat Jalan</div>
                <div class="card p-3 mb-3 border text-xs" style="border-radius:10px;background:#f8fafc;">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Pilih Tindakan / Pemeriksaan</label>
                            <select id="rajalTindakanSelect" class="form-select form-select-sm" style="width:100%"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Pelaksana</label>
                            <select id="rajalTindakanJenis" class="form-select form-select-sm">
                                <option value="Dokter">Dokter</option>
                                <option value="Paramedis">Dokter &amp; Paramedis</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal</label>
                            <input type="date" id="rajalTindakanTgl" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-success btn-sm w-100 font-bold" id="btnTambahTindakanRajal" style="border-radius:8px;">
                                <i class="bi bi-plus-lg me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-section-title"><i class="bi bi-list-check text-success"></i> Riwayat Tindakan Pasien Ini</div>
                <div id="rajalTindakanContainer"><span class="text-muted text-xs">Belum ada tindakan dicatat.</span></div>
            </div>

            {{-- ===================================
                 9. MODUL RESUME MEDIS RAWAT JALAN (resume_pasien)
            =================================== --}}
            <div class="tab-pane fade" id="tab-resume" role="tabpanel">
                <form id="formResumeRajal">
                    <input type="hidden" name="no_rawat" id="resumeNoRawat">

                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div class="form-section-title mb-0"><i class="bi bi-file-earmark-medical text-primary"></i> Ringkasan / Resume Pasien Rawat Jalan</div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-success btn-sm font-bold text-xs" id="btnPilihTemplateResumeRajal">
                                <i class="bi bi-file-earmark-text me-1"></i> Pilih Template Pengisian
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm font-bold text-xs" id="btnSimpanSebagaiTemplateRajal" title="Simpan data resume saat ini sebagai template baru">
                                <i class="bi bi-bookmark-plus me-1"></i> Simpan Sebagai Template
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm font-bold text-xs" id="btnAutoFillResumeRajal">
                                <i class="bi bi-magic me-1"></i> Tarik Data dari CPPT &amp; Diagnosa
                            </button>
                        </div>
                    </div>

                    <div class="card p-3 mb-3 border text-xs" style="border-radius:10px;background:#f8fafc;">
                        <div class="row g-2.5">
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Keluhan Utama / Anamnesis</label>
                                <textarea name="keluhan_utama" id="resKeluhanUtama" class="form-control form-control-sm" rows="3" placeholder="Keluhan utama saat pasien datang..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Jalannya Penyakit / Riwayat Pengobatan</label>
                                <textarea name="jalannya_penyakit" id="resJalannyaPenyakit" class="form-control form-control-sm" rows="3" placeholder="Perkembangan / kronologi penyakit..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Pemeriksaan Penunjang (Radiologi, EKG, dll)</label>
                                <textarea name="pemeriksaan_penunjang" id="resPemeriksaanPenunjang" class="form-control form-control-sm" rows="2" placeholder="Hasil rontgen, usg, ekg, dll..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Hasil Laboratorium Terkait</label>
                                <textarea name="hasil_laborat" id="resHasilLaborat" class="form-control form-control-sm" rows="2" placeholder="Ringkasan hasil lab darah, urine, dll..."></textarea>
                            </div>
                        </div>

                        <hr class="my-3 text-slate-300">
                        <div class="fw-bold fs-7 text-primary mb-2"><i class="bi bi-diagram-3-fill me-1"></i> Diagnosa &amp; Prosedur Pasien</div>

                        <div class="row g-2.5 mb-2">
                            <div class="col-md-8">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Diagnosa Utama</label>
                                <input type="text" name="diagnosa_utama" id="resDiagnosaUtama" class="form-control form-control-sm" placeholder="Nama diagnosa utama...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-10 Utama</label>
                                <input type="text" name="kd_diagnosa_utama" id="resKdDiagnosaUtama" class="form-control form-control-sm font-monospace" placeholder="Misal: A09.0, I10...">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Diagnosa Sekunder 1</label>
                                <input type="text" name="diagnosa_sekunder" id="resDiagnosaSekunder" class="form-control form-control-sm" placeholder="Diagnosa sekunder / penyerta...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-10 Sekunder 1</label>
                                <input type="text" name="kd_diagnosa_sekunder" id="resKdDiagnosaSekunder" class="form-control form-control-sm font-monospace" placeholder="Kode ICD-10...">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Diagnosa Sekunder 2</label>
                                <input type="text" name="diagnosa_sekunder2" id="resDiagnosaSekunder2" class="form-control form-control-sm" placeholder="Diagnosa sekunder tambahan...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-10 Sekunder 2</label>
                                <input type="text" name="kd_diagnosa_sekunder2" id="resKdDiagnosaSekunder2" class="form-control form-control-sm font-monospace" placeholder="Kode ICD-10...">
                            </div>
                        </div>

                        <div class="row g-2.5 mb-2">
                            <div class="col-md-8">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Prosedur / Tindakan Utama</label>
                                <input type="text" name="prosedur_utama" id="resProsedurUtama" class="form-control form-control-sm" placeholder="Tindakan / operasi utama...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-9-CM Utama</label>
                                <input type="text" name="kd_prosedur_utama" id="resKdProsedurUtama" class="form-control form-control-sm font-monospace" placeholder="Misal: 89.03, 93.94...">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Prosedur Sekunder</label>
                                <input type="text" name="prosedur_sekunder" id="resProsedurSekunder" class="form-control form-control-sm" placeholder="Prosedur sekunder...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Kode ICD-9-CM Sekunder</label>
                                <input type="text" name="kd_prosedur_sekunder" id="resKdProsedurSekunder" class="form-control form-control-sm font-monospace" placeholder="Kode ICD-9-CM...">
                            </div>
                        </div>

                        <hr class="my-3 text-slate-300">
                        <div class="fw-bold fs-7 text-primary mb-2"><i class="bi bi-box-arrow-right me-1"></i> Terapi &amp; Kondisi Pulang</div>

                        <div class="row g-2.5">
                            <div class="col-md-4">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Kondisi Pulang</label>
                                <select name="kondisi_pulang" id="resKondisiPulang" class="form-select form-select-sm">
                                    <option value="Hidup">Hidup</option>
                                    <option value="Meninggal">Meninggal</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Obat Pulang / Terapi Pulang</label>
                                <textarea name="obat_pulang" id="resObatPulang" class="form-control form-control-sm" rows="3" placeholder="Daftar obat yang dibawa pulang oleh pasien..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Pemeriksaan Lain / Catatan Tambahan</label>
                                <textarea name="pemeriksaan_lain" id="resPemeriksaanLain" class="form-control form-control-sm" rows="2" placeholder="Catatan kontrol, anjuran dokter, pemeriksaan lanjutan..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span id="resumeStatusBadge" class="text-xs text-muted"><i class="bi bi-info-circle me-1"></i>Belum disimpan</span>
                        <button type="button" class="btn btn-primary font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanResumeRajal">
                            <i class="bi bi-save-fill me-1"></i> Simpan Resume Medis Pasien
                        </button>
                    </div>
                </form>
            </div>

{{-- MODAL PILIH TEMPLATE RESUME RAJAL --}}
<div class="modal fade" id="modalPilihTemplateResumeRajal" tabindex="-1" aria-labelledby="modalPilihTemplateResumeRajalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header py-3 px-4" style="background:linear-gradient(135deg, #042b1b 0%, #0d7044 100%); color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-file-earmark-medical-fill fs-5 text-white"></i>
                    </div>
                    <div>
                        <h6 class="modal-title font-bold mb-0 text-white" id="modalPilihTemplateResumeRajalLabel">Pilih Template Resume Medis Rawat Jalan</h6>
                        <small class="text-white text-opacity-75" style="font-size:0.75rem;">Standar Pengisian Klinis SIMRS Khanza Namira</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4" style="background:#f8fafc;">
                <!-- Search Box -->
                <div class="mb-3 position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"></i>
                    <input type="text" id="searchTemplateResumeRajal" class="form-control form-control-sm ps-5 py-2 shadow-xs" placeholder="Ketik kata kunci template, diagnosa, keluhan..." style="border-radius:10px; font-size:0.85rem;">
                </div>

                <!-- Tabs between Resume Templates and Khanza Examination Templates -->
                <ul class="nav nav-pills mb-3 gap-2" id="pills-template-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-1.5 px-3 font-bold text-xs rounded-pill" id="tab-tmpl-resume-rajal" data-bs-toggle="pill" data-bs-target="#content-tmpl-resume-rajal" type="button" role="tab">
                            <i class="bi bi-file-earmark-text me-1"></i> Template Resume Medis (<span id="countTmplResumeRajal">0</span>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1.5 px-3 font-bold text-xs rounded-pill" id="tab-tmpl-khanza-rajal" data-bs-toggle="pill" data-bs-target="#content-tmpl-khanza-rajal" type="button" role="tab">
                            <i class="bi bi-hospital me-1"></i> Template Pemeriksaan SIMRS (<span id="countTmplKhanzaRajal">0</span>)
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab 1: Resume Templates -->
                    <div class="tab-pane fade show active" id="content-tmpl-resume-rajal" role="tabpanel">
                        <div id="listTemplateResumeRajalContainer" class="d-flex flex-column gap-2.5">
                            <div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat template...</div>
                        </div>
                    </div>
                    <!-- Tab 2: Khanza Templates -->
                    <div class="tab-pane fade" id="content-tmpl-khanza-rajal" role="tabpanel">
                        <div id="listTemplateKhanzaRajalContainer" class="d-flex flex-column gap-2.5">
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

{{-- MODAL SIMPAN TEMPLATE RESUME RAJAL --}}
<div class="modal fade" id="modalSimpanTemplateResumeRajal" tabindex="-1" aria-labelledby="modalSimpanTemplateResumeRajalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header py-3 px-4" style="background:#0f172a; color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bookmark-plus-fill text-warning fs-5"></i>
                    <h6 class="modal-title font-bold mb-0 text-white" id="modalSimpanTemplateResumeRajalLabel">Simpan Sebagai Template Resume</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label font-bold text-xs text-slate-700 mb-1">Nama / Judul Template <span class="text-danger">*</span></label>
                    <input type="text" id="inputNamaTemplateRajal" class="form-control form-control-sm" placeholder="Contoh: Resume Faringitis Akut, Resume Dispepsia..." style="border-radius:8px;">
                    <div class="text-muted text-xs mt-1">Gunakan nama yang jelas agar mudah dicari saat pelayanan pasien lain.</div>
                </div>
                <div class="card p-3 border bg-white text-xs" style="border-radius:10px;">
                    <div class="font-bold text-slate-800 mb-1"><i class="bi bi-info-circle text-primary me-1"></i> Data yang Akan Disimpan ke Template:</div>
                    <div class="text-slate-600" id="previewSaveTemplateRajal">
                        <!-- Filled by JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2.5 px-4 bg-white border-top">
                <button type="button" class="btn btn-secondary btn-sm px-3 font-bold text-xs rounded-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm px-4 font-bold text-xs rounded-2" id="btnSubmitSimpanTemplateRajal">
                    <i class="bi bi-save me-1"></i> Simpan Template
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

    // === DataTables ===
    if ($('#tabelRajal').length > 0) {
        $('#tabelRajal').DataTable({
            order: [[1, 'desc']],
            columnDefs: [
                { orderable: false, targets: [0, 13] }
            ],
            language: {
                search:         'Cari Pasien:',
                lengthMenu:     'Tampilkan _MENU_ data',
                info:           'Menampilkan _START_–_END_ dari _TOTAL_ pasien',
                infoEmpty:      'Tidak ada data',
                infoFiltered:   '(difilter dari _MAX_ total)',
                zeroRecords:    'Tidak ditemukan data pasien yang sesuai',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' }
            }
        });
    }

    // ============================================================
    // OFFCANVAS DOKTER & DYNAMIC MODULE LOGIC
    // ============================================================
    let activeNoRawat = '';
    let currentRajalData = null;
    let selectedLabItems = [];
    let selectedRadItems = [];
    let selectedObatItems = [];
    let selectedRacikanItems = [];

    const offcanvasEl = document.getElementById('offcanvasPelayanan');
    const bsOffcanvas = offcanvasEl ? new bootstrap.Offcanvas(offcanvasEl) : null;

    // Initialize Select2 inside Offcanvas
    if (offcanvasEl) {
        offcanvasEl.addEventListener('shown.bs.offcanvas', function () {
            $('.select2-search').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#offcanvasPelayanan'),
                width: '100%'
            });
        });
    }

    // Open Offcanvas Dokter when clicking 'Pelayanan Medis' button
    $(document).on('click', '.open-pelayanan-btn', function () {
        const noRawatB64 = $(this).data('norawat-b64');
        const noRawat    = $(this).data('norawat');
        const noRm       = $(this).data('norm');
        const nama       = $(this).data('nama');
        const jk         = $(this).data('jk');
        const umur       = $(this).data('umur');
        const tlp        = $(this).data('tlp');
        const poli       = $(this).data('poli');
        const dokter     = $(this).data('dokter');
        const bayar      = $(this).data('bayar');
        const pj         = $(this).data('pj');
        const hubpj      = $(this).data('hubpj');
        const alamatpj   = $(this).data('alamatpj');

        activeNoRawat = noRawat;

        // Header Pasien Rinci
        $('#ofcAvatar').text(nama.substr(0, 2).toUpperCase());
        $('#ofcNamaPasien').text(nama);
        $('#ofcNoRawat').text(noRawat);
        $('#ofcNoRm').text('RM: ' + noRm);
        $('#ofcPoli').text(poli);
        $('#ofcBayar').text(bayar);
        
        const jkHtml = (jk === 'L' || jk === 'Laki-laki')
            ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1" style="font-weight:700;"><i class="bi bi-gender-male me-1"></i> Laki-laki (L)</span>`
            : `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1" style="font-weight:700;"><i class="bi bi-gender-female me-1"></i> Perempuan (P)</span>`;
        $('#ofcJk').html(jkHtml);
        
        $('#ofcUmur').text(umur);
        $('#ofcTlp').text(tlp);
        $('#ofcDokter').text(dokter);
        $('#ofcAlamatPasien').text(alamatpj || '-');

        // Reset forms
        $('#formSoap')[0].reset();
        $('#formAwalMedis')[0].reset();
        $('#formLab')[0].reset();
        $('#formRad')[0].reset();
        $('#formResep')[0].reset();
        $('#formOperasi')[0].reset();
        if ($('#formResumeRajal').length) $('#formResumeRajal')[0].reset();
        $('.select2-search').val(null).trigger('change.select2');

        // Set hidden input no_rawat across all forms
        $('#soapNoRawat, #awalNoRawat, #labNoRawat, #radNoRawat, #resepNoRawat, #opsNoRawat, #resumeNoRawat').val(noRawat);

        // Reset temporary selections
        selectedLabItems = [];
        selectedRadItems = [];
        selectedObatItems = [];
        selectedRacikanItems = [];
        renderSelectedLab();
        renderSelectedRad();
        renderSelectedObat();
        renderDaftarRacikan();
        updateResepSummary();

        // Load detail data via AJAX
        loadPasienDetail(noRawatB64);
        loadMasterOperasi();

        if (bsOffcanvas) bsOffcanvas.show();
    });

    function loadPasienDetail(noRawatB64) {
        $.ajax({
            url: `/rawat-jalan/pasien-detail/${noRawatB64}`,
            type: 'GET',
            success: function (res) {
                if (res.success) {
                    // Update header if backend returns additional demography
                    if (res.pasien && res.pasien.alamat) {
                        $('#ofcAlamatPasien').text(res.pasien.alamat);
                    }

                    // 1. Render History SOAP (Seluruh Kunjungan)
                    if (res.soapList && res.soapList.length > 0) {
                        let htmlSoap = '';
                        res.soapList.forEach(s => {
                            htmlSoap += `
                                <div class="card p-3 mb-2.5 border text-xs" style="border-radius:10px;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
                                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                                        <div>
                                            <span class="text-success"><i class="bi bi-calendar-event me-1"></i>${s.tgl_perawatan} ${s.jam_rawat}</span>
                                            <span class="ms-2 text-slate-500">| No. Rawat: <code>${s.no_rawat}</code></span>
                                            ${s.nm_dokter ? `<span class="ms-2 text-primary font-semibold">| ${s.nm_dokter}</span>` : ''}
                                        </div>
                                        <div>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Tensi: ${s.tensi || '-'}</span>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 ms-1">Nadi: ${s.nadi || '-'}</span>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 ms-1">Suhu: ${s.suhu_tubuh || '-'} °C</span>
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-6"><strong>[S] Subjek:</strong> ${s.keluhan || '-'}</div>
                                        <div class="col-md-6"><strong>[O] Objek:</strong> ${s.pemeriksaan || '-'}</div>
                                        <div class="col-md-6"><strong>[A] Asesmen:</strong> ${s.penilaian || '-'}</div>
                                        <div class="col-md-6"><strong>[P] Plan:</strong> ${s.rtl || '-'}</div>
                                        ${s.instruksi ? `<div class="col-md-6 text-slate-600"><strong>Instruksi:</strong> ${s.instruksi}</div>` : ''}
                                        ${s.evaluasi ? `<div class="col-md-6 text-slate-600"><strong>Evaluasi:</strong> ${s.evaluasi}</div>` : ''}
                                    </div>
                                </div>`;
                        });
                        $('#riwayatSoapContainer').html(htmlSoap);
                    } else {
                        $('#riwayatSoapContainer').html('<span class="text-muted text-xs">Belum ada riwayat SOAP.</span>');
                    }

                    // 2. Render History Awal Medis (Seluruh Kunjungan & Departemen)
                    if (res.awalMedisList && res.awalMedisList.length > 0) {
                        let htmlAwal = '';
                        res.awalMedisList.forEach(a => {
                            htmlAwal += `
                                <div class="card p-3 mb-2.5 border text-xs" style="border-radius:10px;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
                                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                                        <div>
                                            <span class="text-primary"><i class="bi bi-calendar-event me-1"></i>${a.tanggal}</span>
                                            <span class="ms-2 text-slate-500">| No. Rawat: <code>${a.no_rawat}</code></span>
                                            ${a.nm_dokter ? `<span class="ms-2 text-success font-semibold">| ${a.nm_dokter}</span>` : ''}
                                        </div>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">${a.departemen || 'Medis'}</span>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-6"><strong>Keluhan Utama:</strong> ${a.keluhan_utama || '-'}</div>
                                        <div class="col-md-6"><strong>RPS:</strong> ${a.rps || '-'}</div>
                                        <div class="col-md-6"><strong>Diagnosis:</strong> ${a.diagnosis || '-'}</div>
                                        <div class="col-md-6"><strong>Tata Laksana:</strong> ${a.tata || '-'}</div>
                                    </div>
                                </div>`;
                        });
                        $('#riwayatAwalMedisContainer').html(htmlAwal);
                    } else {
                        $('#riwayatAwalMedisContainer').html('<span class="text-muted text-xs">Belum ada riwayat Penilaian Awal Medis.</span>');
                    }

                    // 3. Render Permintaan & HASIL Laboratorium
                    if (res.labResults && res.labResults.length > 0) {
                        let htmlLabRes = `<div class="table-responsive"><table class="table table-sm table-bordered text-xs mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tgl &amp; Jam</th>
                                    <th>Pemeriksaan / Parameter</th>
                                    <th>Hasil</th>
                                    <th>Nilai Rujukan</th>
                                    <th>Ket</th>
                                </tr>
                            </thead><tbody>`;
                        res.labResults.forEach(lr => {
                            const isAbnormal = lr.keterangan && lr.keterangan.trim() !== '';
                            const valClass = isAbnormal ? 'text-danger font-bold' : 'text-slate-800 font-semibold';
                            htmlLabRes += `<tr>
                                <td>${lr.tgl_periksa} ${lr.jam}</td>
                                <td><strong>${lr.nm_perawatan || '-'}</strong> — ${lr.nama_pemeriksaan}</td>
                                <td class="${valClass}">${lr.nilai}</td>
                                <td class="text-muted">${lr.nilai_rujukan}</td>
                                <td>${isAbnormal ? `<span class="badge bg-danger">${lr.keterangan}</span>` : '-'}</td>
                            </tr>`;
                        });
                        htmlLabRes += `</tbody></table></div>`;
                        $('#hasilLabContainer').html(htmlLabRes);
                    } else {
                        $('#hasilLabContainer').html('<span class="text-muted text-xs">Belum ada hasil pemeriksaan Laboratorium.</span>');
                    }

                    if (res.labOrders && res.labOrders.length > 0) {
                        let htmlLab = '';
                        res.labOrders.forEach(l => {
                            htmlLab += `
                                <div class="card p-3 mb-2.5 border text-xs" style="border-radius:10px;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
                                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                                        <span class="text-success fs-7"><i class="bi bi-eyedropper me-1"></i>No. Order: <code>${l.noorder}</code> (${l.tgl_permintaan} ${l.jam_permintaan})</span>
                                        <span class="text-slate-500">No. Rawat: <code>${l.no_rawat}</code></span>
                                    </div>
                                    <div><strong>Detail Permintaan Lab:</strong> <span class="text-slate-800 font-semibold">${l.detail_pemeriksaan || '-'}</span></div>
                                    ${l.diagnosa_klinis ? `<div class="mt-1 text-slate-600"><strong>Diagnosa Klinis:</strong> ${l.diagnosa_klinis}</div>` : ''}
                                </div>`;
                        });
                        $('#riwayatLabContainer').html(htmlLab);
                    } else {
                        $('#riwayatLabContainer').html('<span class="text-muted text-xs">Belum ada riwayat permintaan Lab.</span>');
                    }

                    // 4. Render Permintaan & HASIL Ekspertisi Radiologi
                    if (res.radResults && res.radResults.length > 0) {
                        let htmlRadRes = '';
                        res.radResults.forEach(rr => {
                            htmlRadRes += `
                                <div class="card p-3 mb-2.5 border text-xs" style="border-radius:10px;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
                                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom">
                                        <span class="text-primary fs-7"><i class="bi bi-file-earmark-medical-fill me-1"></i>Hasil Ekspertisi Radiologi (${rr.tgl_periksa} ${rr.jam})</span>
                                        <span class="text-slate-500">No. Rawat: <code>${rr.no_rawat}</code></span>
                                    </div>
                                    <div class="text-slate-800 mt-1 whitespace-pre-line" style="white-space:pre-line;line-height:1.6;font-family:inherit;">${rr.hasil || '-'}</div>
                                </div>`;
                        });
                        $('#hasilRadContainer').html(htmlRadRes);
                    } else {
                        $('#hasilRadContainer').html('<span class="text-muted text-xs">Belum ada hasil ekspertisi Radiologi.</span>');
                    }

                    if (res.radOrders && res.radOrders.length > 0) {
                        let htmlRad = '';
                        res.radOrders.forEach(r => {
                            htmlRad += `
                                <div class="card p-3 mb-2.5 border text-xs" style="border-radius:10px;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
                                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                                        <span class="text-primary fs-7"><i class="bi bi-intersect me-1"></i>No. Order: <code>${r.noorder}</code> (${r.tgl_permintaan} ${r.jam_permintaan})</span>
                                        <span class="text-slate-500">No. Rawat: <code>${r.no_rawat}</code></span>
                                    </div>
                                    <div><strong>Detail Permintaan Radiologi:</strong> <span class="text-slate-800 font-semibold">${r.detail_pemeriksaan || '-'}</span></div>
                                    ${r.diagnosa_klinis ? `<div class="mt-1 text-slate-600"><strong>Diagnosa Klinis:</strong> ${r.diagnosa_klinis}</div>` : ''}
                                </div>`;
                        });
                        $('#riwayatRadContainer').html(htmlRad);
                    } else {
                        $('#riwayatRadContainer').html('<span class="text-muted text-xs">Belum ada riwayat permintaan Radiologi.</span>');
                    }

                    // 5. Render History Resep Dokter (SIMRS Namira Parity)
                    if (res.resepList && res.resepList.length > 0) {
                        let htmlResep = '';
                        res.resepList.forEach(r => {
                            const jamStr = r.jam || '00:00:00';
                            const drStr  = r.nm_dokter || 'Dokter DPJP';
                            const statusBadge = r.can_delete
                                ? `<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Menunggu Validasi Farmasi</span>
                                   <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 ms-2 font-bold text-xs" onclick="batalResepRajal('${r.no_resep}')">
                                       <i class="bi bi-trash me-1"></i>Batal Resep
                                   </button>`
                                : `<span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Diserahkan Farmasi (${r.tgl_penyerahan} ${r.jam_penyerahan || ''})</span>`;

                            let obatBody = '';
                            if (r.obat_non_racik && r.obat_non_racik.length > 0) {
                                obatBody += `<div class="mb-2">
                                    <div class="fw-bold text-success fs-8 mb-1"><i class="bi bi-capsule me-1"></i>Obat Jadi / Paten:</div>
                                    <table class="table table-sm table-bordered mb-1 text-xs" style="background:#fafafa;">
                                        <thead><tr class="table-light"><th>Nama Obat</th><th style="width:70px;text-align:center;">Jumlah</th><th>Aturan Pakai</th><th>Keterangan</th></tr></thead>
                                        <tbody>`;
                                r.obat_non_racik.forEach(o => {
                                    obatBody += `<tr>
                                        <td><strong>${o.nama_brng || o.kode_brng}</strong></td>
                                        <td class="text-center">${o.jml} ${o.kode_sat || ''}</td>
                                        <td><code>${o.aturan_pakai || '-'}</code></td>
                                        <td>${o.keterangan || '-'}</td>
                                    </tr>`;
                                });
                                obatBody += `</tbody></table></div>`;
                            }

                            if (r.obat_racikan && r.obat_racikan.length > 0) {
                                obatBody += `<div class="mb-2">
                                    <div class="fw-bold text-primary fs-8 mb-1"><i class="bi bi-mortarboard-fill me-1"></i>Obat Racikan:</div>`;
                                r.obat_racikan.forEach(rc => {
                                    obatBody += `
                                        <div class="border rounded-2 p-2 mb-1.5" style="background:#f0f9ff; border-color:#bae6fd !important;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-primary fs-8"><i class="bi bi-box-seam me-1"></i>${rc.nama_racik} (${rc.metode || 'Racikan'}) - Jml: ${rc.jml_dr}</span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">${rc.aturan_pakai || '-'}</span>
                                            </div>
                                            ${rc.keterangan ? `<div class="text-xs text-muted mb-1"><em>Ket: ${rc.keterangan}</em></div>` : ''}`;
                                    if (rc.detail_racik && rc.detail_racik.length > 0) {
                                        obatBody += `<table class="table table-sm table-bordered mb-0 text-xs bg-white">
                                            <thead><tr class="table-light"><th>Bahan Obat</th><th>P1 / P2</th><th>Kandungan</th><th style="width:70px;text-align:center;">Jml</th></tr></thead><tbody>`;
                                        rc.detail_racik.forEach(d => {
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

                            if (!r.obat_non_racik?.length && !r.obat_racikan?.length) {
                                obatBody = `<div class="text-slate-800 mt-1 ps-2" style="border-left:3px solid #7c3aed;line-height:1.6;">${r.detail_obat || '-'}</div>`;
                            }

                            htmlResep += `
                                <div class="card p-3 mb-2.5 border text-xs" style="border-radius:10px;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
                                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                                        <span class="text-purple-700 fs-7"><i class="bi bi-capsule me-1"></i>No. Resep: <code>${r.no_resep}</code> (${r.tgl_perawatan} ${jamStr})</span>
                                        <div class="d-flex align-items-center">${statusBadge}</div>
                                    </div>
                                    <div class="text-slate-500 mb-2">${drStr} | No. Rawat: <code>${r.no_rawat}</code></div>
                                    ${obatBody}
                                </div>`;
                        });
                        $('#riwayatResepContainer').html(htmlResep);
                    } else {
                        $('#riwayatResepContainer').html('<span class="text-muted text-xs">Belum ada riwayat resep obat.</span>');
                    }

                    // 6. Render Booking Operasi & LAPORAN OPERASI
                    if (res.laporanOps && res.laporanOps.length > 0) {
                        let htmlLapOps = '';
                        res.laporanOps.forEach(lo => {
                            htmlLapOps += `
                                <div class="card p-3 mb-2.5 border text-xs" style="border-radius:10px;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
                                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom">
                                        <span class="text-danger fs-7"><i class="bi bi-file-earmark-text-fill me-1"></i>Laporan Operasi (${lo.tanggal})</span>
                                        <span class="badge bg-danger px-2.5 py-1">Anestesi: ${lo.jenis_anasthesi || '-'}</span>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6"><strong>Diagnosa Pre-Op:</strong> ${lo.diagnosa_preop || '-'}</div>
                                        <div class="col-md-6"><strong>Diagnosa Post-Op:</strong> ${lo.diagnosa_postop || '-'}</div>
                                        <div class="col-md-6"><strong>Jaringan Dieksekusi:</strong> ${lo.jaringan_dieksekusi || '-'}</div>
                                        <div class="col-md-6"><strong>Kategori Operasi:</strong> ${lo.kategori || '-'}</div>
                                    </div>
                                    <div class="fw-bold text-slate-800 mt-2 mb-1">Catatan Laporan Operasi:</div>
                                    <div class="text-slate-700 p-2 bg-light rounded" style="white-space:pre-line;line-height:1.5;">${lo.laporan_operasi || '-'}</div>
                                </div>`;
                        });
                        $('#laporanOpsContainer').html(htmlLapOps);
                    } else {
                        $('#laporanOpsContainer').html('<span class="text-muted text-xs">Belum ada laporan operasi.</span>');
                    }

                    if (res.bookingOps && res.bookingOps.length > 0) {
                        let htmlOps = '';
                        res.bookingOps.forEach(b => {
                            const sttsCls = b.status === 'Selesai' ? 'bg-success' : (b.status === 'Batal' ? 'bg-danger' : 'bg-warning text-dark');
                            const kelasBadge = b.kelas ? `<span class="badge bg-primary bg-opacity-10 text-primary ms-1">Kelas ${b.kelas}</span>` : '';
                            const pjBadge = b.png_jawab ? `<span class="badge bg-info bg-opacity-10 text-info ms-1">${b.png_jawab}</span>` : '';
                            htmlOps += `
                                <div class="card p-3 mb-2.5 border text-xs" style="border-radius:10px;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
                                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                                        <span class="text-danger fs-7"><i class="bi bi-calendar-event me-1"></i>Tgl Operasi: ${b.tanggal} (${b.jam_mulai} - ${b.jam_selesai})</span>
                                        <span class="badge ${sttsCls} px-2.5 py-1">${b.status || 'Menunggu'}</span>
                                    </div>
                                    <div><strong>Paket Operasi:</strong> <span class="text-slate-800 font-semibold">${b.nama_paket || b.kode_paket}</span> ${kelasBadge} ${pjBadge}</div>
                                    ${b.dokter_operator ? `<div class="mt-1 text-slate-600"><strong>Dokter Operator:</strong> ${b.dokter_operator}</div>` : ''}
                                    <div class="text-slate-500 mt-1">No. Rawat: <code>${b.no_rawat}</code></div>
                                </div>`;
                        });
                        $('#riwayatOpsContainer').html(htmlOps);
                    } else {
                        $('#riwayatOpsContainer').html('<span class="text-muted text-xs">Belum ada booking operasi.</span>');
                    }

                    // 7. Render Diagnosa ICD-10
                    renderDiagnosaRajal(res.diagnosaList || []);

                    // 8. Render Tindakan Medis
                    renderTindakanRajal(res.tindakanList || []);

                    // 9. Render Resume Medis Rajal
                    currentRajalData = res;
                    renderResumeRajal(res.resumePasien, res);
                }
            }
        });
    }

    // 1. Simpan SOAP
    $('#btnSimpanSoap').on('click', function () {
        $.ajax({
            url: '{{ route("rawat-jalan.simpan-soap") }}',
            type: 'POST',
            data: $('#formSoap').serialize() + '&_token={{ csrf_token() }}',
            success: function (res) {
                notifySuccess(res.message || 'SOAP berhasil disimpan.');
            },
            error: function (err) {
                notifyError('Gagal menyimpan SOAP: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
            }
        });
    });

    // 2. Simpan Awal Medis
    $('#btnSimpanAwalMedis').on('click', function () {
        $.ajax({
            url: '{{ route("rawat-jalan.simpan-awal-medis") }}',
            type: 'POST',
            data: $('#formAwalMedis').serialize() + '&_token={{ csrf_token() }}',
            success: function (res) {
                notifySuccess(res.message || 'Pemeriksaan awal medis berhasil disimpan.');
            },
            error: function (err) {
                notifyError('Gagal menyimpan Awal Medis: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
            }
        });
    });

    // 3. Search & Select LAB
    $('#searchLabInput').on('keyup', function () {
        const q = $(this).val();
        if (q.length < 2) { $('#searchLabDropdown').hide(); return; }
        $.ajax({
            url: '{{ route("rawat-jalan.master-lab") }}',
            type: 'GET',
            data: { q: q, no_rawat: $('#soapNoRawat').val() },
            success: function (data) {
                let html = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const tarif = Number(item.total_byr || 0).toLocaleString('id-ID');
                        const pj = item.png_jawab ? `<span class="badge bg-light text-dark border ms-1" style="font-size:0.7rem;">${item.png_jawab}</span>` : '';
                        html += `<div class="dropdown-item select-lab-item py-2 border-bottom" data-kode="${item.kd_jenis_prw}" data-nama="${item.nm_perawatan}" data-tarif="${item.total_byr || 0}" data-penjamin="${item.png_jawab || '-'}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>${item.nm_perawatan}</strong> <span class="text-muted fs-8">(${item.kd_jenis_prw})</span>
                                            ${pj}
                                        </div>
                                        <div class="text-success font-bold fs-7 ms-2">Rp ${tarif}</div>
                                    </div>
                                 </div>`;
                    });
                } else {
                    html = `<div class="p-2 text-muted text-xs">Pemeriksaan Lab tidak ditemukan</div>`;
                }
                $('#searchLabDropdown').html(html).show();
            }
        });
    });

    $(document).on('click', '.select-lab-item', function () {
        const kode = $(this).data('kode');
        const nama = $(this).data('nama');
        const tarif = $(this).data('tarif');
        const penjamin = $(this).data('penjamin');

        if (!selectedLabItems.some(i => i.kode === kode)) {
            selectedLabItems.push({ kode, nama, tarif, penjamin });
            renderSelectedLab();
        }
        $('#searchLabInput').val('');
        $('#searchLabDropdown').hide();
    });

    function renderSelectedLab() {
        if (selectedLabItems.length === 0) {
            $('#selectedLabTbody').html('<tr><td colspan="5" class="text-center text-muted py-3 fs-7">Belum ada pemeriksaan Lab yang dipilih.</td></tr>');
            return;
        }
        let html = '';
        selectedLabItems.forEach((item, idx) => {
            const tarifFmt = Number(item.tarif || 0).toLocaleString('id-ID');
            html += `<tr>
                        <td><code>${item.kode}</code></td>
                        <td>${item.nama}</td>
                        <td class="text-success font-bold">Rp ${tarifFmt}</td>
                        <td><span class="badge bg-light text-dark border text-xs">${item.penjamin || '-'}</span></td>
                        <td style="text-align:center;"><button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 remove-lab" data-idx="${idx}"><i class="bi bi-trash"></i></button></td>
                     </tr>`;
        });
        $('#selectedLabTbody').html(html);
    }

    $(document).on('click', '.remove-lab', function () {
        const idx = $(this).data('idx');
        selectedLabItems.splice(idx, 1);
        renderSelectedLab();
    });

    $('#btnSimpanLab').on('click', function () {
        const items = selectedLabItems.map(i => i.kode);
        if (items.length === 0) {
            notifyWarning('Pilih minimal 1 pemeriksaan Lab terlebih dahulu.');
            return;
        }
        $.ajax({
            url: '{{ route("rawat-jalan.simpan-lab") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                no_rawat: $('#labNoRawat').val(),
                items: items,
                diagnosa_klinis: $('#labDiagnosa').val(),
                informasi_tambahan: $('#labInfo').val()
            },
            success: function (res) {
                notifySuccess(res.message || 'Permintaan pemeriksaan Lab berhasil dikirim.');
                selectedLabItems = [];
                renderSelectedLab();
            },
            error: function (err) {
                notifyError('Gagal mengirim permintaan Lab: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
            }
        });
    });

    // 4. Search & Select RADIOLOGI
    $('#searchRadInput').on('keyup', function () {
        const q = $(this).val();
        if (q.length < 2) { $('#searchRadDropdown').hide(); return; }
        $.ajax({
            url: '{{ route("rawat-jalan.master-radiologi") }}',
            type: 'GET',
            data: { q: q, no_rawat: $('#soapNoRawat').val() },
            success: function (data) {
                let html = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const tarif = Number(item.total_byr || 0).toLocaleString('id-ID');
                        const pj = item.png_jawab ? `<span class="badge bg-light text-dark border ms-1" style="font-size:0.7rem;">${item.png_jawab}</span>` : '';
                        html += `<div class="dropdown-item select-rad-item py-2 border-bottom" data-kode="${item.kd_jenis_prw}" data-nama="${item.nm_perawatan}" data-tarif="${item.total_byr || 0}" data-penjamin="${item.png_jawab || '-'}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>${item.nm_perawatan}</strong> <span class="text-muted fs-8">(${item.kd_jenis_prw})</span>
                                            ${pj}
                                        </div>
                                        <div class="text-success font-bold fs-7 ms-2">Rp ${tarif}</div>
                                    </div>
                                 </div>`;
                    });
                } else {
                    html = `<div class="p-2 text-muted text-xs">Pemeriksaan Radiologi tidak ditemukan</div>`;
                }
                $('#searchRadDropdown').html(html).show();
            }
        });
    });

    $(document).on('click', '.select-rad-item', function () {
        const kode = $(this).data('kode');
        const nama = $(this).data('nama');
        const tarif = $(this).data('tarif');
        const penjamin = $(this).data('penjamin');

        if (!selectedRadItems.some(i => i.kode === kode)) {
            selectedRadItems.push({ kode, nama, tarif, penjamin });
            renderSelectedRad();
        }
        $('#searchRadInput').val('');
        $('#searchRadDropdown').hide();
    });

    function renderSelectedRad() {
        if (selectedRadItems.length === 0) {
            $('#selectedRadTbody').html('<tr><td colspan="5" class="text-center text-muted py-3 fs-7">Belum ada pemeriksaan Radiologi yang dipilih.</td></tr>');
            return;
        }
        let html = '';
        selectedRadItems.forEach((item, idx) => {
            const tarifFmt = Number(item.tarif || 0).toLocaleString('id-ID');
            html += `<tr>
                        <td><code>${item.kode}</code></td>
                        <td>${item.nama}</td>
                        <td class="text-success font-bold">Rp ${tarifFmt}</td>
                        <td><span class="badge bg-light text-dark border text-xs">${item.penjamin || '-'}</span></td>
                        <td style="text-align:center;"><button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 remove-rad" data-idx="${idx}"><i class="bi bi-trash"></i></button></td>
                     </tr>`;
        });
        $('#selectedRadTbody').html(html);
    }

    $(document).on('click', '.remove-rad', function () {
        const idx = $(this).data('idx');
        selectedRadItems.splice(idx, 1);
        renderSelectedRad();
    });

    $('#btnSimpanRad').on('click', function () {
        const items = selectedRadItems.map(i => i.kode);
        if (items.length === 0) {
            notifyWarning('Pilih minimal 1 pemeriksaan Radiologi terlebih dahulu.');
            return;
        }
        $.ajax({
            url: '{{ route("rawat-jalan.simpan-radiologi") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                no_rawat: $('#radNoRawat').val(),
                items: items,
                diagnosa_klinis: $('#radDiagnosa').val(),
                informasi_tambahan: $('#radInfo').val()
            },
            success: function (res) {
                notifySuccess(res.message || 'Permintaan pemeriksaan Radiologi berhasil dikirim.');
                selectedRadItems = [];
                renderSelectedRad();
            },
            error: function (err) {
                notifyError('Gagal mengirim permintaan Radiologi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
            }
        });
    });

    // ============================================================
    // 5. MASTER ATURAN PAKAI & RESEP DOKTER (SIMRS NAMIRA PARITY)
    // ============================================================
    function loadMasterAturanPakai() {
        $.ajax({
            url: '{{ route("rawat-jalan.master-aturan-pakai") }}',
            type: 'GET',
            success: function (data) {
                let html = '';
                (data || []).forEach(item => {
                    const val = typeof item === 'string' ? item : (item.aturan || item.aturan_pakai || '');
                    if (val) {
                        html += `<option value="${val}">`;
                    }
                });
                $('#listAturanPakai').html(html);
            }
        });
    }
    loadMasterAturanPakai();

    function updateResepSummary() {
        const obatCount = selectedObatItems.length;
        const racikCount = selectedRacikanItems.length;
        $('#resepSummaryText').html(`<i class="bi bi-cart3 me-1"></i> Total: <b>${obatCount}</b> Obat Jadi, <b>${racikCount}</b> Racikan`);
    }

    // --- SUB-TAB 1: OBAT JADI (NON-RACIKAN) ---
    $('#searchObatInput').on('keyup', function () {
        const q = $(this).val().trim();
        if (q.length < 2) { $('#searchObatDropdown').hide(); return; }
        $.ajax({
            url: '{{ route("rawat-jalan.master-obat") }}',
            type: 'GET',
            data: { q: q },
            success: function (data) {
                let html = '';
                if (data && data.length > 0) {
                    data.forEach(item => {
                        const stokStr = item.stok !== undefined ? ` | Stok: ${item.stok}` : '';
                        const satStr  = item.kode_sat ? ` (${item.kode_sat})` : '';
                        html += `
                            <div class="dropdown-item select-obat-item py-1.5 px-2.5 border-bottom cursor-pointer"
                                 data-kode="${item.kode_brng}"
                                 data-nama="${item.nama_brng}"
                                 data-satuan="${item.kode_sat || ''}">
                                <div class="font-bold text-slate-800 text-xs">${item.nama_brng}${satStr}</div>
                                <div class="text-slate-500 text-xs">Kode: <code>${item.kode_brng}</code>${stokStr}</div>
                            </div>`;
                    });
                } else {
                    html = `<div class="p-2.5 text-muted text-xs text-center">Obat tidak ditemukan</div>`;
                }
                $('#searchObatDropdown').html(html).show();
            }
        });
    });

    $(document).on('click', '.select-obat-item', function () {
        const kode   = $(this).data('kode');
        const nama   = $(this).data('nama');
        const satuan = $(this).data('satuan');

        if (!selectedObatItems.some(i => i.kode === kode)) {
            selectedObatItems.push({
                kode: kode,
                nama: nama,
                kode_sat: satuan,
                jml: 10,
                aturan_pakai: '3 X 1 Sehari',
                keterangan: ''
            });
            renderSelectedObat();
            updateResepSummary();
        }
        $('#searchObatInput').val('');
        $('#searchObatDropdown').hide();
    });

    function renderSelectedObat() {
        if (selectedObatItems.length === 0) {
            $('#selectedObatTbody').html('<tr><td colspan="5" class="text-center text-muted py-3 fs-7">Belum ada obat jadi yang dipilih.</td></tr>');
            return;
        }
        let html = '';
        selectedObatItems.forEach((item, idx) => {
            html += `<tr>
                        <td>
                            <strong>${item.nama}</strong>
                            <div class="text-muted text-xs">Kode: ${item.kode} ${item.kode_sat ? `(${item.kode_sat})` : ''}</div>
                        </td>
                        <td style="width:90px; text-align:center;">
                            <input type="number" class="form-control form-control-sm obat-jml text-center" data-idx="${idx}" value="${item.jml}" min="1">
                        </td>
                        <td style="width:230px;">
                            <input type="text" list="listAturanPakai" class="form-control form-control-sm obat-signa" data-idx="${idx}" value="${item.aturan_pakai}" placeholder="3 X 1 Sehari...">
                        </td>
                        <td style="width:140px;">
                            <input type="text" class="form-control form-control-sm obat-ket" data-idx="${idx}" value="${item.keterangan || ''}" placeholder="Sesudah makan...">
                        </td>
                        <td style="width:50px; text-align:center;">
                            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 remove-obat" data-idx="${idx}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                     </tr>`;
        });
        $('#selectedObatTbody').html(html);
    }

    $(document).on('change', '.obat-jml', function () {
        const idx = $(this).data('idx');
        selectedObatItems[idx].jml = parseFloat($(this).val()) || 1;
    });

    $(document).on('change', '.obat-signa', function () {
        const idx = $(this).data('idx');
        selectedObatItems[idx].aturan_pakai = $(this).val();
    });

    $(document).on('change', '.obat-ket', function () {
        const idx = $(this).data('idx');
        selectedObatItems[idx].keterangan = $(this).val();
    });

    $(document).on('click', '.remove-obat', function () {
        const idx = $(this).data('idx');
        selectedObatItems.splice(idx, 1);
        renderSelectedObat();
        updateResepSummary();
    });

    // --- SUB-TAB 2: OBAT RACIKAN ---
    $('#btnBuatGrupRacikan').on('click', function () {
        const nama = $('#racikNamaInput').val().trim();
        const metode = $('#racikMetodeSelect').val();
        const metodeName = $('#racikMetodeSelect option:selected').text();
        const jml = parseInt($('#racikJmlInput').val()) || 10;
        const aturan = $('#racikAturanInput').val().trim() || '3 X 1 Sehari';
        const ket = $('#racikKetInput').val().trim();

        if (!nama) {
            notifyWarning('Masukkan nama racikan terlebih dahulu.');
            return;
        }

        selectedRacikanItems.push({
            no_racik: selectedRacikanItems.length + 1,
            nama_racik: nama,
            kd_racik: metode,
            metode_name: metodeName,
            jml_dr: jml,
            aturan_pakai: aturan,
            keterangan: ket,
            detail: []
        });

        $('#racikNamaInput').val('');
        $('#racikKetInput').val('');
        renderDaftarRacikan();
        updateResepSummary();
        notifySuccess(`Racikan "${nama}" berhasil dibuat. Silakan tambahkan bahan obat.`);
    });

    function renderDaftarRacikan() {
        const container = $('#daftarRacikanContainer');
        if (!selectedRacikanItems || selectedRacikanItems.length === 0) {
            container.html(`
                <div class="text-center py-3 border rounded-3 bg-light text-muted fs-7">
                    Belum ada obat racikan dibuat. Silakan isi form di atas dan klik <b>Buat Racikan</b>.
                </div>`);
            return;
        }

        let html = '';
        selectedRacikanItems.forEach((r, rIdx) => {
            let bahanRows = '';
            if (r.detail && r.detail.length > 0) {
                r.detail.forEach((d, dIdx) => {
                    bahanRows += `
                        <tr>
                            <td><strong>${d.nama_brng}</strong><div class="text-muted text-xs">Kode: ${d.kode_brng}</div></td>
                            <td class="text-center">${d.p1 || 1} / ${d.p2 || 1}</td>
                            <td>${d.kandungan || '-'}</td>
                            <td style="width:100px; text-align:center;">
                                <input type="number" class="form-control form-control-sm text-center racik-bahan-jml" data-ridx="${rIdx}" data-didx="${dIdx}" value="${d.jml}" min="0.1" step="any">
                            </td>
                            <td style="width:40px; text-align:center;">
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" onclick="hapusBahanRacik(${rIdx}, ${dIdx})">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                bahanRows = `<tr><td colspan="5" class="text-center text-muted py-2 fs-8">Belum ada bahan obat dalam racikan ini.</td></tr>`;
            }

            html += `
                <div class="card p-3 mb-3 border text-xs" style="border-radius:10px; background:#f0f9ff; border-color:#bae6fd !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-1">
                        <div>
                            <span class="badge bg-primary me-1">${r.metode_name}</span>
                            <strong class="text-primary fs-7">${r.nama_racik}</strong>
                            <span class="text-slate-600 ms-2">Kemasan: <b>${r.jml_dr}</b> | Aturan: <b>${r.aturan_pakai}</b></span>
                            ${r.keterangan ? `<span class="text-muted ms-2">(${r.keterangan})</span>` : ''}
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 font-bold" onclick="hapusGrupRacik(${rIdx})">
                            <i class="bi bi-trash me-1"></i> Hapus Racikan
                        </button>
                    </div>

                    {{-- Search & Tambah Bahan Inline --}}
                    <div class="p-2 border rounded-2 bg-white mb-2">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label font-bold text-xs text-slate-600 mb-0">Cari Bahan Obat</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-sm search-bahan-input" data-ridx="${rIdx}" placeholder="Ketik nama bahan...">
                                    <div class="search-results-dropdown bahan-dropdown-${rIdx}" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label font-bold text-xs text-slate-600 mb-0">P1 / P2</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm text-center" id="p1_${rIdx}" value="1" min="1">
                                    <span class="input-group-text px-1">/</span>
                                    <input type="number" class="form-control form-control-sm text-center" id="p2_${rIdx}" value="1" min="1">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label font-bold text-xs text-slate-600 mb-0">Kandungan (mg)</label>
                                <input type="text" class="form-control form-control-sm" id="kandungan_${rIdx}" placeholder="Misal: 500">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-bold text-xs text-slate-600 mb-0">Jml Butuh Obat</label>
                                <input type="number" class="form-control form-control-sm" id="jmlBahan_${rIdx}" value="${r.jml_dr}" min="0.1" step="any">
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Bahan Obat Racik --}}
                    <div class="table-responsive border rounded-2 bg-white">
                        <table class="table table-sm table-bordered align-middle mb-0 text-xs">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Bahan Obat</th>
                                    <th style="width:90px; text-align:center;">P1 / P2</th>
                                    <th style="width:120px;">Kandungan</th>
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
        });
        container.html(html);
    }

    // Search bahan racik
    $(document).on('keyup', '.search-bahan-input', function () {
        const input = $(this);
        const q = input.val().trim();
        const rIdx = input.data('ridx');
        const dropdown = $(`.bahan-dropdown-${rIdx}`);

        if (q.length < 2) { dropdown.hide(); return; }

        $.ajax({
            url: '{{ route("rawat-jalan.master-obat") }}',
            type: 'GET',
            data: { q: q },
            success: function (data) {
                let html = '';
                if (data && data.length > 0) {
                    data.forEach(item => {
                        html += `
                            <div class="dropdown-item select-bahan-item py-1.5 px-2.5 border-bottom cursor-pointer"
                                 data-ridx="${rIdx}"
                                 data-kode="${item.kode_brng}"
                                 data-nama="${item.nama_brng}"
                                 data-satuan="${item.kode_sat || ''}"
                                 data-kapasitas="${item.kapasitas || 0}">
                                <div class="font-bold text-slate-800 text-xs">${item.nama_brng}</div>
                                <div class="text-slate-500 text-xs">Kode: <code>${item.kode_brng}</code> | Satuan: ${item.kode_sat || '-'}</div>
                            </div>`;
                    });
                } else {
                    html = `<div class="p-2.5 text-muted text-xs text-center">Bahan obat tidak ditemukan</div>`;
                }
                dropdown.html(html).show();
            }
        });
    });

    $(document).on('click', '.select-bahan-item', function () {
        const rIdx = $(this).data('ridx');
        const kode = $(this).data('kode');
        const nama = $(this).data('nama');
        const kapasitas = parseFloat($(this).data('kapasitas')) || 0;

        const p1 = parseFloat($(`#p1_${rIdx}`).val()) || 1;
        const p2 = parseFloat($(`#p2_${rIdx}`).val()) || 1;
        let kandungan = $(`#kandungan_${rIdx}`).val() || (kapasitas > 0 ? kapasitas : '');
        let jml = parseFloat($(`#jmlBahan_${rIdx}`).val()) || selectedRacikanItems[rIdx].jml_dr || 10;

        selectedRacikanItems[rIdx].detail.push({
            kode_brng: kode,
            nama_brng: nama,
            p1: p1,
            p2: p2,
            kandungan: kandungan,
            jml: jml
        });

        $(`.search-bahan-input[data-ridx="${rIdx}"]`).val('');
        $(`.bahan-dropdown-${rIdx}`).hide();
        renderDaftarRacikan();
    });

    $(document).on('change', '.racik-bahan-jml', function () {
        const rIdx = $(this).data('ridx');
        const dIdx = $(this).data('didx');
        selectedRacikanItems[rIdx].detail[dIdx].jml = parseFloat($(this).val()) || 1;
    });

    window.hapusGrupRacik = function (rIdx) {
        selectedRacikanItems.splice(rIdx, 1);
        renderDaftarRacikan();
        updateResepSummary();
    };

    window.hapusBahanRacik = function (rIdx, dIdx) {
        selectedRacikanItems[rIdx].detail.splice(dIdx, 1);
        renderDaftarRacikan();
    };

    // SIMPAN E-RESEP (OBAT JADI & OBAT RACIKAN)
    $('#btnSimpanResep').on('click', function () {
        const noRawat = $('#resepNoRawat').val();
        if (selectedObatItems.length === 0 && selectedRacikanItems.length === 0) {
            notifyWarning('Pilih minimal 1 obat jadi atau 1 obat racikan terlebih dahulu.');
            return;
        }

        // Cek validasi tiap racikan
        for (let i = 0; i < selectedRacikanItems.length; i++) {
            const r = selectedRacikanItems[i];
            if (!r.detail || r.detail.length === 0) {
                notifyWarning(`Racikan "${r.nama_racik}" belum memiliki bahan obat. Tambahkan bahan terlebih dahulu.`);
                return;
            }
        }

        const payload = {
            _token: '{{ csrf_token() }}',
            no_rawat: noRawat,
            items: selectedObatItems.map(i => ({
                kode_brng: i.kode,
                jml: i.jml,
                aturan_pakai: i.aturan_pakai,
                keterangan: i.keterangan || ''
            })),
            racikan: selectedRacikanItems.map((r, idx) => ({
                no_racik: idx + 1,
                nama_racik: r.nama_racik,
                kd_racik: r.kd_racik,
                jml_dr: r.jml_dr,
                aturan_pakai: r.aturan_pakai,
                keterangan: r.keterangan || '',
                detail: r.detail.map(d => ({
                    kode_brng: d.kode_brng,
                    p1: d.p1 || 1,
                    p2: d.p2 || 1,
                    kandungan: d.kandungan || 0,
                    jml: d.jml || 1
                }))
            }))
        };

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

        $.ajax({
            url: '{{ route("rawat-jalan.simpan-resep") }}',
            type: 'POST',
            data: payload,
            success: function (res) {
                notifySuccess(res.message || 'Resep dokter berhasil dikirim ke Farmasi.');
                selectedObatItems = [];
                selectedRacikanItems = [];
                renderSelectedObat();
                renderDaftarRacikan();
                updateResepSummary();
                if (noRawat) loadPasienDetail(btoa(noRawat));
            },
            error: function (err) {
                notifyError('Gagal menyimpan resep: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="bi bi-send-check me-1"></i> Simpan &amp; Kirim E-Resep ke Farmasi');
            }
        });
    });

    // BATAL RESEP (HAPUS RESEP SEBELUM DISERAHKAN)
    window.batalResepRajal = function (noResep) {
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
                $.ajax({
                    url: '{{ route("rawat-jalan.hapus-resep") }}',
                    type: 'DELETE',
                    data: {
                        no_resep: noResep,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        notifySuccess(res.message || 'Resep berhasil dibatalkan.');
                        const noRawat = $('#resepNoRawat').val();
                        if (noRawat) loadPasienDetail(btoa(noRawat));
                    },
                    error: function (err) {
                        notifyError('Gagal membatalkan resep: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
                    }
                });
            }
        });
    };

    // ============================================================
    // 9. MODUL RESUME PASIEN RAWAT JALAN (resume_pasien)
    // ============================================================
    function renderResumeRajal(resm, allData) {
        const nr = (allData && allData.pasien) ? allData.pasien.no_rawat : $('#soapNoRawat').val();
        $('#resumeNoRawat').val(nr);

        if (resm) {
            $('#resKeluhanUtama').val(resm.keluhan_utama || '');
            $('#resJalannyaPenyakit').val(resm.jalannya_penyakit || '');
            $('#resPemeriksaanPenunjang').val(resm.pemeriksaan_penunjang || '');
            $('#resHasilLaborat').val(resm.hasil_laborat || '');
            $('#resDiagnosaUtama').val(resm.diagnosa_utama || '');
            $('#resKdDiagnosaUtama').val(resm.kd_diagnosa_utama || '');
            $('#resDiagnosaSekunder').val(resm.diagnosa_sekunder || '');
            $('#resKdDiagnosaSekunder').val(resm.kd_diagnosa_sekunder || '');
            $('#resDiagnosaSekunder2').val(resm.diagnosa_sekunder2 || '');
            $('#resKdDiagnosaSekunder2').val(resm.kd_diagnosa_sekunder2 || '');
            $('#resProsedurUtama').val(resm.prosedur_utama || '');
            $('#resKdProsedurUtama').val(resm.kd_prosedur_utama || '');
            $('#resProsedurSekunder').val(resm.prosedur_sekunder || '');
            $('#resKdProsedurSekunder').val(resm.kd_prosedur_sekunder || '');
            $('#resKondisiPulang').val(resm.kondisi_pulang || 'Hidup');
            $('#resObatPulang').val(resm.obat_pulang || '');
            $('#resPemeriksaanLain').val(resm.pemeriksaan_lain || '');
            $('#resumeStatusBadge').html('<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 font-bold"><i class="bi bi-check-circle me-1"></i>Tersimpan di SIMRS</span>');
        } else {
            $('#formResumeRajal')[0].reset();
            $('#resumeNoRawat').val(nr);
            $('#resumeStatusBadge').html('<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1"><i class="bi bi-info-circle me-1"></i>Belum dibuat / disimpan</span>');
        }
    }

    // Auto-Fill Resume Rajal dari CPPT, Diagnosa & Resep
    $('#btnAutoFillResumeRajal').on('click', function () {
        if (!currentRajalData) {
            notifyWarning('Data pasien belum dimuat sepenuhnya.');
            return;
        }

        const d = currentRajalData;
        // Keluhan Utama
        const keluhan = (d.soap && d.soap.keluhan) ? d.soap.keluhan : ((d.awalMedis && d.awalMedis.keluhan_utama) ? d.awalMedis.keluhan_utama : '');
        if (keluhan && !$('#resKeluhanUtama').val()) $('#resKeluhanUtama').val(keluhan);

        // Jalannya Penyakit
        const rwt = (d.soap && d.soap.pemeriksaan) ? d.soap.pemeriksaan : ((d.awalMedis && d.awalMedis.riwayat_sekarang) ? d.awalMedis.riwayat_sekarang : '');
        if (rwt && !$('#resJalannyaPenyakit').val()) $('#resJalannyaPenyakit').val(rwt);

        // Pemeriksaan Penunjang (Radiologi)
        if (d.radOrders && d.radOrders.length > 0 && !$('#resPemeriksaanPenunjang').val()) {
            const radStr = d.radOrders.map(ro => `${ro.tgl_permintaan}: ${ro.detail_pemeriksaan}`).join('; ');
            $('#resPemeriksaanPenunjang').val(radStr);
        }

        // Hasil Laboratorium
        if (d.labOrders && d.labOrders.length > 0 && !$('#resHasilLaborat').val()) {
            const labStr = d.labOrders.map(lo => `${lo.tgl_permintaan}: ${lo.detail_pemeriksaan}`).join('; ');
            $('#resHasilLaborat').val(labStr);
        }

        // Diagnosa ICD-10
        if (d.diagnosaList && d.diagnosaList.length > 0) {
            const d1 = d.diagnosaList[0];
            if (d1) {
                $('#resDiagnosaUtama').val(d1.nm_penyakit || '');
                $('#resKdDiagnosaUtama').val(d1.kd_penyakit || '');
            }
            const d2 = d.diagnosaList[1];
            if (d2) {
                $('#resDiagnosaSekunder').val(d2.nm_penyakit || '');
                $('#resKdDiagnosaSekunder').val(d2.kd_penyakit || '');
            }
            const d3 = d.diagnosaList[2];
            if (d3) {
                $('#resDiagnosaSekunder2').val(d3.nm_penyakit || '');
                $('#resKdDiagnosaSekunder2').val(d3.kd_penyakit || '');
            }
        }

        // Prosedur / Tindakan
        if (d.tindakanList && d.tindakanList.length > 0) {
            const t1 = d.tindakanList[0];
            if (t1) {
                $('#resProsedurUtama').val(t1.nm_perawatan || '');
                $('#resKdProsedurUtama').val(t1.kd_jenis_prw || '');
            }
            const t2 = d.tindakanList[1];
            if (t2) {
                $('#resProsedurSekunder').val(t2.nm_perawatan || '');
                $('#resKdProsedurSekunder').val(t2.kd_jenis_prw || '');
            }
        }

        // Obat Pulang
        if (d.resepList && d.resepList.length > 0 && !$('#resObatPulang').val()) {
            let obatArr = [];
            d.resepList.forEach(r => {
                if (r.obat_non_racik && r.obat_non_racik.length > 0) {
                    r.obat_non_racik.forEach(o => {
                        obatArr.push(`${o.nama_brng} No. ${o.jml} (${o.aturan_pakai || '-'})`);
                    });
                }
                if (r.obat_racikan && r.obat_racikan.length > 0) {
                    r.obat_racikan.forEach(rc => {
                        obatArr.push(`Racikan ${rc.nama_racik} No. ${rc.jml_dr} (${rc.aturan_pakai || '-'})`);
                    });
                }
            });
            if (obatArr.length > 0) {
                $('#resObatPulang').val(obatArr.join('\n'));
            }
        }

        $('#resKondisiPulang').val('Hidup');
        notifySuccess('Data resume berhasil ditarik otomatis dari CPPT, Diagnosa, dan Resep.');
    });

    // Simpan Resume Medis Rajal
    $('#btnSimpanResumeRajal').on('click', function () {
        const noRawat = $('#resumeNoRawat').val();
        if (!noRawat) {
            notifyWarning('Nomor rawat pasien belum dipilih.');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

        $.ajax({
            url: '{{ route("rawat-jalan.simpan-resume") }}',
            type: 'POST',
            data: $('#formResumeRajal').serialize() + '&_token={{ csrf_token() }}',
            success: function (res) {
                notifySuccess(res.message || 'Resume medis rawat jalan berhasil disimpan.');
                $('#resumeStatusBadge').html('<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 font-bold"><i class="bi bi-check-circle me-1"></i>Tersimpan di SIMRS</span>');
                loadPasienDetail(btoa(noRawat));
            },
            error: function (err) {
                notifyError('Gagal menyimpan resume: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="bi bi-save-fill me-1"></i> Simpan Resume Medis Pasien');
            }
        });
    });

    // ============================================================
    // 9.B. FITUR TEMPLATE RESUME RAJAL (SIMRS NAMIRA PARITY)
    // ============================================================
    let cachedResumeRajalTemplates = [];
    let cachedKhanzaRajalTemplates = [];

    function loadTemplateResumeRajalList(searchQuery = '') {
        const c = $('#listTemplateResumeRajalContainer');
        c.html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat template...</div>');

        $.ajax({
            url: '{{ route("rawat-jalan.template-resume") }}',
            type: 'GET',
            data: { q: searchQuery },
            success: function (res) {
                cachedResumeRajalTemplates = res.templates || [];
                cachedKhanzaRajalTemplates = res.khanzaTemplates || [];
                $('#countTmplResumeRajal').text(cachedResumeRajalTemplates.length);
                $('#countTmplKhanzaRajal').text(cachedKhanzaRajalTemplates.length);
                renderTemplateResumeRajalCards(cachedResumeRajalTemplates);
                renderTemplateKhanzaRajalCards(cachedKhanzaRajalTemplates);
            },
            error: function () {
                c.html('<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat template resume.</div>');
            }
        });
    }

    function renderTemplateResumeRajalCards(list) {
        const c = $('#listTemplateResumeRajalContainer');
        if (!list || list.length === 0) {
            c.html(`
                <div class="text-center py-4 px-3 border rounded-3 bg-white text-muted">
                    <i class="bi bi-folder-x fs-2 opacity-50 d-block mb-1"></i>
                    <div class="fw-bold fs-7 text-slate-700">Belum Ada Template Resume</div>
                    <div class="text-xs text-slate-500 mt-1">Gunakan tombol <b>"Simpan Sebagai Template"</b> pada form resume untuk membuat template baru.</div>
                </div>
            `);
            return;
        }

        let html = '';
        list.forEach((t, idx) => {
            const diagStr = t.diagnosa_utama ? `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 me-1">${t.kd_diagnosa_utama ? t.kd_diagnosa_utama + ' — ' : ''}${t.diagnosa_utama}</span>` : '';
            const ownerBadge = t.kd_dokter ? '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Dokter Pribadi</span>' : '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Umum / Rumah Sakit</span>';

            html += `
                <div class="card p-3 border shadow-xs bg-white" style="border-radius:12px; transition:transform .15s, box-shadow .15s;">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap">
                        <div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <h6 class="fw-bold text-slate-800 mb-0 fs-7">${t.nama_template}</h6>
                                ${ownerBadge}
                            </div>
                            <div class="mt-1">${diagStr}</div>
                        </div>
                        <div class="d-flex gap-1.5">
                            <button type="button" class="btn btn-sm btn-success font-bold text-xs py-1 px-2.5 rounded-2" onclick="terapkanTemplateResumeRajal(${idx})" title="Gunakan template ini">
                                <i class="bi bi-check2-circle me-1"></i> Gunakan Template
                            </button>
                            ${t.kd_dokter ? `
                            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-2" onclick="hapusTemplateResumeRajal(${t.id}, '${(t.nama_template||'').replace(/'/g, "\\'")}')" title="Hapus template ini">
                                <i class="bi bi-trash"></i>
                            </button>` : ''}
                        </div>
                    </div>
                    <div class="row g-2 text-xs text-slate-600 border-top pt-2 mt-1">
                        ${t.keluhan_utama ? `<div class="col-md-6"><b>Keluhan:</b> <span class="text-slate-700">${t.keluhan_utama.substring(0, 85)}${t.keluhan_utama.length > 85 ? '...' : ''}</span></div>` : ''}
                        ${t.jalannya_penyakit ? `<div class="col-md-6"><b>Kronologis:</b> <span class="text-slate-700">${t.jalannya_penyakit.substring(0, 85)}${t.jalannya_penyakit.length > 85 ? '...' : ''}</span></div>` : ''}
                        ${t.obat_pulang ? `<div class="col-12 text-truncate"><b>Terapi:</b> <span class="text-slate-700">${t.obat_pulang.replace(/\n/g, ', ')}</span></div>` : ''}
                    </div>
                </div>
            `;
        });
        c.html(html);
    }

    function renderTemplateKhanzaRajalCards(list) {
        const c = $('#listTemplateKhanzaRajalContainer');
        if (!list || list.length === 0) {
            c.html(`
                <div class="text-center py-4 px-3 border rounded-3 bg-white text-muted">
                    <i class="bi bi-inbox fs-2 opacity-50 d-block mb-1"></i>
                    <div class="fw-bold fs-7 text-slate-700">Belum Ada Template Pemeriksaan Dokter di SIMRS Khanza</div>
                    <div class="text-xs text-slate-500 mt-1">Master template pemeriksaan Khanza dikelola melalui menu Master Template Pemeriksaan Dokter.</div>
                </div>
            `);
            return;
        }

        let html = '';
        list.forEach((t, idx) => {
            html += `
                <div class="card p-3 border shadow-xs bg-white" style="border-radius:12px;">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap">
                        <div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge bg-secondary font-monospace">${t.no_template}</span>
                                <h6 class="fw-bold text-slate-800 mb-0 fs-7">${t.penilaian || t.keluhan || ('Template ' + t.no_template)}</h6>
                            </div>
                            <small class="text-muted text-xs">Dokter: ${t.nm_dokter || t.kd_dokter || '-'}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary font-bold text-xs py-1 px-2.5 rounded-2" onclick="terapkanTemplateKhanzaRajal(${idx})" title="Terapkan template pemeriksaan ke resume">
                            <i class="bi bi-box-arrow-in-down-right me-1"></i> Gunakan
                        </button>
                    </div>
                    <div class="row g-2 text-xs text-slate-600 border-top pt-2 mt-1">
                        ${t.keluhan ? `<div class="col-md-6"><b>Subjek / Keluhan:</b> ${t.keluhan}</div>` : ''}
                        ${t.pemeriksaan ? `<div class="col-md-6"><b>Objek / Pemeriksaan:</b> ${t.pemeriksaan}</div>` : ''}
                        ${t.penilaian ? `<div class="col-md-6"><b>Asesmen:</b> ${t.penilaian}</div>` : ''}
                        ${t.rencana ? `<div class="col-md-6"><b>Plan:</b> ${t.rencana}</div>` : ''}
                    </div>
                </div>
            `;
        });
        c.html(html);
    }

    // Modal Pilih Template Show
    $('#btnPilihTemplateResumeRajal').on('click', function () {
        loadTemplateResumeRajalList();
        $('#modalPilihTemplateResumeRajal').modal('show');
    });

    // Search Filter
    $('#searchTemplateResumeRajal').on('keyup', function () {
        const q = $(this).val().toLowerCase();
        const filtered = cachedResumeRajalTemplates.filter(t => {
            return (t.nama_template || '').toLowerCase().includes(q) ||
                   (t.diagnosa_utama || '').toLowerCase().includes(q) ||
                   (t.keluhan_utama || '').toLowerCase().includes(q) ||
                   (t.jalannya_penyakit || '').toLowerCase().includes(q);
        });
        renderTemplateResumeRajalCards(filtered);

        const filteredKhanza = cachedKhanzaRajalTemplates.filter(t => {
            return (t.no_template || '').toLowerCase().includes(q) ||
                   (t.keluhan || '').toLowerCase().includes(q) ||
                   (t.penilaian || '').toLowerCase().includes(q);
        });
        renderTemplateKhanzaRajalCards(filteredKhanza);
    });

    // Terapkan Template Resume
    window.terapkanTemplateResumeRajal = function (idx) {
        const tmpl = cachedResumeRajalTemplates[idx];
        if (!tmpl) return;

        if (tmpl.keluhan_utama) $('#resKeluhanUtama').val(tmpl.keluhan_utama);
        if (tmpl.jalannya_penyakit) $('#resJalannyaPenyakit').val(tmpl.jalannya_penyakit);
        if (tmpl.pemeriksaan_penunjang) $('#resPemeriksaanPenunjang').val(tmpl.pemeriksaan_penunjang);
        if (tmpl.hasil_laborat) $('#resHasilLaborat').val(tmpl.hasil_laborat);
        if (tmpl.diagnosa_utama) $('#resDiagnosaUtama').val(tmpl.diagnosa_utama);
        if (tmpl.kd_diagnosa_utama) $('#resKdDiagnosaUtama').val(tmpl.kd_diagnosa_utama);
        if (tmpl.diagnosa_sekunder) $('#resDiagnosaSekunder').val(tmpl.diagnosa_sekunder);
        if (tmpl.kd_diagnosa_sekunder) $('#resKdDiagnosaSekunder').val(tmpl.kd_diagnosa_sekunder);
        if (tmpl.diagnosa_sekunder2) $('#resDiagnosaSekunder2').val(tmpl.diagnosa_sekunder2);
        if (tmpl.kd_diagnosa_sekunder2) $('#resKdDiagnosaSekunder2').val(tmpl.kd_diagnosa_sekunder2);
        if (tmpl.prosedur_utama) $('#resProsedurUtama').val(tmpl.prosedur_utama);
        if (tmpl.kd_prosedur_utama) $('#resKdProsedurUtama').val(tmpl.kd_prosedur_utama);
        if (tmpl.kondisi_pulang) $('#resKondisiPulang').val(tmpl.kondisi_pulang);
        if (tmpl.obat_pulang) $('#resObatPulang').val(tmpl.obat_pulang);

        $('#modalPilihTemplateResumeRajal').modal('hide');
        notifySuccess(`Template "${tmpl.nama_template}" berhasil diterapkan ke form resume.`);
    };

    // Terapkan Template Khanza
    window.terapkanTemplateKhanzaRajal = function (idx) {
        const tmpl = cachedKhanzaRajalTemplates[idx];
        if (!tmpl) return;

        if (tmpl.keluhan) $('#resKeluhanUtama').val(tmpl.keluhan);
        if (tmpl.pemeriksaan) $('#resJalannyaPenyakit').val(tmpl.pemeriksaan);
        if (tmpl.penilaian) $('#resDiagnosaUtama').val(tmpl.penilaian);
        if (tmpl.rencana) $('#resObatPulang').val(tmpl.rencana);

        $('#modalPilihTemplateResumeRajal').modal('hide');
        notifySuccess(`Template pemeriksaan "${tmpl.no_template}" berhasil diterapkan ke form resume.`);
    };

    // Hapus Template Resume
    window.hapusTemplateResumeRajal = function (id, nama) {
        Swal.fire({
            title: 'Hapus Template Resume?',
            html: `Apakah Anda yakin ingin menghapus template <b>${nama}</b>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("rawat-jalan.hapus-template-resume") }}',
                    type: 'DELETE',
                    data: { id: id, _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        notifySuccess(res.message || 'Template berhasil dihapus.');
                        loadTemplateResumeRajalList($('#searchTemplateResumeRajal').val());
                    },
                    error: function (err) {
                        notifyError('Gagal menghapus template: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
                    }
                });
            }
        });
    };

    // Buka Modal Simpan Sebagai Template
    $('#btnSimpanSebagaiTemplateRajal').on('click', function () {
        const diag = $('#resDiagnosaUtama').val();
        const keluhan = $('#resKeluhanUtama').val();

        if (!diag && !keluhan) {
            notifyWarning('Isi minimal Diagnosa Utama atau Keluhan Utama pada form resume sebelum menyimpannya sebagai template.');
            return;
        }

        const suggestedName = diag ? `Resume ${diag}` : `Resume Template Baru`;
        $('#inputNamaTemplateRajal').val(suggestedName);

        let previewHtml = `
            <div><b>Diagnosa Utama:</b> ${diag || '-'} ${$('#resKdDiagnosaUtama').val() ? '(' + $('#resKdDiagnosaUtama').val() + ')' : ''}</div>
            <div><b>Keluhan:</b> ${(keluhan || '-').substring(0, 80)}</div>
            <div><b>Terapi Obat:</b> ${($('#resObatPulang').val() || '-').substring(0, 80)}</div>
        `;
        $('#previewSaveTemplateRajal').html(previewHtml);
        $('#modalSimpanTemplateResumeRajal').modal('show');
    });

    // Submit Simpan Template
    $('#btnSubmitSimpanTemplateRajal').on('click', function () {
        const nama = $('#inputNamaTemplateRajal').val().trim();
        if (!nama) {
            notifyWarning('Nama template wajib diisi.');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

        const payload = {
            nama_template: nama,
            keluhan_utama: $('#resKeluhanUtama').val(),
            jalannya_penyakit: $('#resJalannyaPenyakit').val(),
            pemeriksaan_penunjang: $('#resPemeriksaanPenunjang').val(),
            hasil_laborat: $('#resHasilLaborat').val(),
            diagnosa_utama: $('#resDiagnosaUtama').val(),
            kd_diagnosa_utama: $('#resKdDiagnosaUtama').val(),
            diagnosa_sekunder: $('#resDiagnosaSekunder').val(),
            kd_diagnosa_sekunder: $('#resKdDiagnosaSekunder').val(),
            diagnosa_sekunder2: $('#resDiagnosaSekunder2').val(),
            kd_diagnosa_sekunder2: $('#resKdDiagnosaSekunder2').val(),
            prosedur_utama: $('#resProsedurUtama').val(),
            kd_prosedur_utama: $('#resKdProsedurUtama').val(),
            kondisi_pulang: $('#resKondisiPulang').val(),
            obat_pulang: $('#resObatPulang').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route("rawat-jalan.simpan-template-resume") }}',
            type: 'POST',
            data: payload,
            success: function (res) {
                $('#modalSimpanTemplateResumeRajal').modal('hide');
                notifySuccess(res.message || 'Template berhasil disimpan.');
            },
            error: function (err) {
                notifyError('Gagal menyimpan template: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Template');
            }
        });
    });

    // 6. Master & Simpan OPERASI
    function loadMasterOperasi() {
        $.ajax({
            url: '{{ route("rawat-jalan.master-operasi") }}',
            type: 'GET',
            success: function (data) {
                let html = '<option value="">-- Pilih Paket Operasi --</option>';
                data.forEach(item => {
                    const kelasStr = item.kelas ? ` [Kelas: ${item.kelas}]` : '';
                    const pjStr = item.png_jawab ? ` — ${item.png_jawab}` : '';
                    html += `<option value="${item.kode_paket}">${item.nm_perawatan}${kelasStr}${pjStr} (${item.kode_paket})</option>`;
                });
                $('#opsKodePaket').html(html).trigger('change');
            }
        });
        loadMasterDokter();
    }

    function loadMasterDokter() {
        $.ajax({
            url: '{{ route("rawat-jalan.master-dokter") }}',
            type: 'GET',
            success: function (data) {
                let html = '<option value="">-- Pilih Dokter Operator --</option>';
                data.forEach(item => {
                    html += `<option value="${item.kd_dokter}">${item.nm_dokter}</option>`;
                });
                $('#opsKdDokter').html(html).trigger('change');
            }
        });
    }

    $('#btnSimpanOperasi').on('click', function () {
        const noRawat = $('#opsNoRawat').val();
        if (!$('#opsKodePaket').val()) {
            notifyWarning('Pilih paket operasi terlebih dahulu.');
            return;
        }
        $.ajax({
            url: '{{ route("rawat-jalan.simpan-booking-operasi") }}',
            type: 'POST',
            data: $('#formOperasi').serialize() + '&_token={{ csrf_token() }}',
            success: function (res) {
                notifySuccess(res.message || 'Jadwal operasi berhasil disimpan.');
                if (noRawat) loadPasienDetail(btoa(noRawat));
            },
            error: function (err) {
                notifyError('Gagal menyimpan jadwal operasi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
            }
        });
    });

    // Close dropdowns when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#searchLabInput, #searchLabDropdown').length) { $('#searchLabDropdown').hide(); }
        if (!$(e.target).closest('#searchRadInput, #searchRadDropdown').length) { $('#searchRadDropdown').hide(); }
        if (!$(e.target).closest('#searchObatInput, #searchObatDropdown').length) { $('#searchObatDropdown').hide(); }
    });

    // ===================================================================
    // DIAGNOSA & TINDAKAN RAWAT JALAN JAVASCRIPT
    // ===================================================================
    function renderDiagnosaRajal(list) {
        if (!list || list.length === 0) {
            $('#rajalDiagnosaContainer').html(`
                <div class="text-center py-4 px-3 border rounded-3 bg-light text-muted">
                    <i class="bi bi-search-heart text-primary fs-2 opacity-50 d-block mb-1"></i>
                    <div class="fw-bold fs-7 text-slate-700">Belum Ada Diagnosa Dicatat</div>
                    <div class="text-xs text-slate-500 mt-1">Pilih kode ICD-10 pada form di atas dan klik Tambah.</div>
                </div>
            `);
            return;
        }
        let html = `
            <div class="table-responsive border rounded-3 overflow-hidden shadow-xs bg-white mb-2">
                <table class="table table-hover table-emr-detail align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px; text-align:center;">#</th>
                            <th style="width:115px;">Kode ICD-10</th>
                            <th>Nama Diagnosa / Penyakit</th>
                            <th style="width:145px; text-align:center;">Prioritas</th>
                            <th style="width:110px; text-align:center;">Status Kasus</th>
                            <th style="width:65px; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        list.forEach((d, idx) => {
            let prioBadge = '';
            if (d.prioritas == 1) {
                prioBadge = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 font-bold"><i class="bi bi-star-fill me-1 text-danger"></i>Utama (1)</span>';
            } else if (d.prioritas == 2) {
                prioBadge = `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 font-semibold">Sekunder (2)</span>`;
            } else {
                prioBadge = `<span class="badge bg-secondary bg-opacity-10 text-slate-700 border border-secondary border-opacity-25 px-2.5 py-1">Tambahan (${d.prioritas})</span>`;
            }

            const statusKasus = d.status_penyakit 
                ? `<span class="badge bg-light text-dark border text-xs">${d.status_penyakit}</span>`
                : `<span class="text-muted text-xs">-</span>`;

            html += `
                <tr>
                    <td style="text-align:center; color:#94a3b8; font-weight:600;">${idx + 1}</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-monospace px-2 py-1 fs-7">${d.kd_penyakit}</span></td>
                    <td>
                        <div class="font-bold text-slate-800 fs-7">${d.nm_penyakit || '-'}</div>
                    </td>
                    <td style="text-align:center;">${prioBadge}</td>
                    <td style="text-align:center;">${statusKasus}</td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 rounded-2" onclick="hapusDiagnosaRajal('${d.kd_penyakit}', '${(d.nm_penyakit||'').replace(/'/g, "\\'")}')" title="Hapus diagnosa">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });

        html += `   </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center px-1 text-slate-500 text-xs">
                <span><i class="bi bi-info-circle me-1"></i>Total <strong>${list.length}</strong> diagnosa terdaftar untuk pasien ini</span>
            </div>`;

        $('#rajalDiagnosaContainer').html(html);
    }
    window.renderDiagnosaRajal = renderDiagnosaRajal;

    function renderTindakanRajal(list) {
        if (!list || list.length === 0) {
            $('#rajalTindakanContainer').html(`
                <div class="text-center py-4 px-3 border rounded-3 bg-light text-muted">
                    <i class="bi bi-activity text-success fs-2 opacity-50 d-block mb-1"></i>
                    <div class="fw-bold fs-7 text-slate-700">Belum Ada Tindakan Dicatat</div>
                    <div class="text-xs text-slate-500 mt-1">Pilih tindakan dan tarif pada form di atas dan klik Simpan.</div>
                </div>
            `);
            return;
        }
        let totalBiaya = 0;
        let html = `
            <div class="table-responsive border rounded-3 overflow-hidden shadow-xs bg-white mb-2">
                <table class="table table-hover table-emr-detail align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px; text-align:center;">#</th>
                            <th>Tindakan / Prosedur Medis</th>
                            <th style="width:145px;">Pelaksana</th>
                            <th style="width:130px;">Penjamin</th>
                            <th style="width:140px;">Waktu Rawat</th>
                            <th style="width:130px; text-align:right;">Tarif (Rp)</th>
                            <th style="width:65px; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>`;

        list.forEach((t, idx) => {
            const biaya = Number(t.total_byr || 0);
            totalBiaya += biaya;

            const pjBadge = t.png_jawab 
                ? `<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-0.5" style="font-size:0.72rem;">${t.png_jawab}</span>` 
                : `<span class="badge bg-light text-muted border text-xs">-</span>`;

            const jenisBadge = (t.jenis && t.jenis.toLowerCase().includes('paramedis'))
                ? `<span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-people-fill me-1"></i>Dr &amp; Paramedis</span>`
                : `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-person-badge-fill me-1"></i>Dokter</span>`;

            const jamStr = (t.jam_rawat || '').substring(0, 5);

            html += `
                <tr>
                    <td style="text-align:center; color:#94a3b8; font-weight:600;">${idx + 1}</td>
                    <td>
                        <div class="font-bold text-slate-800 fs-7">${t.nm_perawatan || t.kd_jenis_prw}</div>
                        <div class="text-slate-400 font-monospace" style="font-size:0.7rem;">${t.kd_jenis_prw} ${t.petugas ? '· ' + t.petugas : ''}</div>
                    </td>
                    <td>${jenisBadge}</td>
                    <td>${pjBadge}</td>
                    <td class="text-slate-600 fs-8">
                        <div><i class="bi bi-calendar3 me-1 text-slate-400"></i>${t.tgl_perawatan}</div>
                        ${jamStr ? `<div class="text-slate-400"><i class="bi bi-clock me-1"></i>${jamStr} WIB</div>` : ''}
                    </td>
                    <td style="text-align:right;">
                        <span class="font-bold text-success fs-7">Rp ${biaya.toLocaleString('id-ID')}</span>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 rounded-2" onclick="hapusTindakanRajal('${t.kd_jenis_prw}', '${t.tgl_perawatan}', '${t.jam_rawat}', '${t.jenis || 'Dokter'}', '${(t.nm_perawatan||'').replace(/'/g, "\\'")}')" title="Hapus tindakan">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });

        html += `   </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center p-2.5 rounded-3 text-xs" style="background:#ecfdf5; border:1px solid #a7f3d0;">
                <span class="font-bold" style="color:#065f46;">
                    <i class="bi bi-check2-circle me-1"></i> Total: <strong>${list.length}</strong> Prosedur Tindakan
                </span>
                <span class="fs-7 font-bold" style="color:#064e3b;">
                    Total Biaya: <span style="font-size:0.95rem; color:#0d7044;">Rp ${totalBiaya.toLocaleString('id-ID')}</span>
                </span>
            </div>`;

        $('#rajalTindakanContainer').html(html);
    }
    window.renderTindakanRajal = renderTindakanRajal;

    // Fix Focus & Typing in Select2 inside Bootstrap Offcanvas
    $('#offcanvasPelayanan').on('shown.bs.offcanvas', function () {
        $(this).removeAttr('tabindex');
    });

    $(document).on('select2:open', () => {
        const searchInput = document.querySelector('.select2-container--open .select2-search__field');
        if (searchInput) searchInput.focus();
    });

    // Select2 Diagnosa Rajal
    $('#rajalDiagnosaSelect').select2({
        theme: 'bootstrap-5',
        placeholder: 'Ketik kode / nama diagnosa ICD-10...',
        minimumInputLength: 2,
        dropdownParent: $('#offcanvasPelayanan'),
        ajax: {
            url: '{{ route("rawat-jalan.master-diagnosa") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: (data || []).map(i => ({ id: i.kd_penyakit, text: `${i.kd_penyakit} — ${i.nm_penyakit}` }))
            })
        }
    });

    // Select2 Tindakan Rajal
    $('#rajalTindakanSelect').select2({
        theme: 'bootstrap-5',
        placeholder: 'Ketik nama tindakan / perawatan...',
        minimumInputLength: 2,
        dropdownParent: $('#offcanvasPelayanan'),
        ajax: {
            url: '{{ route("rawat-jalan.master-tindakan") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term, no_rawat: $('#soapNoRawat').val() }),
            processResults: data => ({
                results: (data || []).map(i => ({
                    id: i.kd_jenis_prw,
                    text: `${i.nm_perawatan} - Rp ${Number(i.total_byr||0).toLocaleString('id-ID')}${i.png_jawab ? ' ['+i.png_jawab+']' : ''}`
                }))
            })
        }
    });

    // Tambah Diagnosa Rajal
    $('#btnTambahDiagnosaRajal').on('click', function () {
        const noRawat = $('#soapNoRawat').val();
        const kdPenyakit = $('#rajalDiagnosaSelect').val();
        const prioritas = $('#rajalDiagnosaPrioritas').val();
        if (!kdPenyakit) {
            notifyWarning('Pilih diagnosa ICD-10 terlebih dahulu.');
            return;
        }

        $.ajax({
            url: '{{ route("rawat-jalan.simpan-diagnosa") }}',
            type: 'POST',
            data: {
                no_rawat: noRawat,
                kd_penyakit: kdPenyakit,
                prioritas: prioritas,
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                notifySuccess(res.message || 'Diagnosa berhasil ditambahkan.');
                loadPasienDetail(btoa(noRawat));
                $('#rajalDiagnosaSelect').val(null).trigger('change');
            },
            error: function (err) {
                notifyError('Gagal menambah diagnosa: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
            }
        });
    });

    window.hapusDiagnosaRajal = function (kdPenyakit, nmPenyakit) {
        Swal.fire({
            title: 'Hapus Diagnosa?',
            html: `Apakah Anda yakin ingin menghapus diagnosa <b>${kdPenyakit}</b> ${nmPenyakit ? '— ' + nmPenyakit : ''}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const noRawat = $('#soapNoRawat').val();
                $.ajax({
                    url: '{{ route("rawat-jalan.hapus-diagnosa") }}',
                    type: 'DELETE',
                    data: {
                        no_rawat: noRawat,
                        kd_penyakit: kdPenyakit,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        notifySuccess(res.message || 'Diagnosa berhasil dihapus.');
                        loadPasienDetail(btoa(noRawat));
                    },
                    error: function (err) {
                        notifyError('Gagal menghapus diagnosa: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
                    }
                });
            }
        });
    };

    // Tambah Tindakan Rajal
    $('#btnTambahTindakanRajal').on('click', function () {
        const noRawat = $('#soapNoRawat').val();
        const kdJenisPrw = $('#rajalTindakanSelect').val();
        const jenis = $('#rajalTindakanJenis').val();
        const tgl = $('#rajalTindakanTgl').val();
        if (!kdJenisPrw) {
            notifyWarning('Pilih tindakan terlebih dahulu.');
            return;
        }

        $.ajax({
            url: '{{ route("rawat-jalan.simpan-tindakan") }}',
            type: 'POST',
            data: {
                no_rawat: noRawat,
                kd_jenis_prw: kdJenisPrw,
                jenis: jenis,
                tgl_perawatan: tgl,
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                notifySuccess(res.message || 'Tindakan berhasil disimpan.');
                loadPasienDetail(btoa(noRawat));
                $('#rajalTindakanSelect').val(null).trigger('change');
            },
            error: function (err) {
                notifyError('Gagal menyimpan tindakan: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
            }
        });
    });

    window.hapusTindakanRajal = function (kdJenisPrw, tgl, jam, jenis, nmPerawatan) {
        Swal.fire({
            title: 'Hapus Tindakan?',
            html: `Apakah Anda yakin ingin menghapus tindakan <b>${nmPerawatan || kdJenisPrw}</b> (${jenis})?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const noRawat = $('#soapNoRawat').val();
                $.ajax({
                    url: '{{ route("rawat-jalan.hapus-tindakan") }}',
                    type: 'DELETE',
                    data: {
                        no_rawat: noRawat,
                        kd_jenis_prw: kdJenisPrw,
                        tgl_perawatan: tgl,
                        jam_rawat: jam,
                        jenis: jenis,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        notifySuccess(res.message || 'Tindakan berhasil dihapus.');
                        loadPasienDetail(btoa(noRawat));
                    },
                    error: function (err) {
                        notifyError('Gagal menghapus tindakan: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
                    }
                });
            }
        });
    };
});
</script>
@endpush
