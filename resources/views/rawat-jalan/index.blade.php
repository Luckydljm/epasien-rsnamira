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

    /* =============================================
       TIMELINE & RIWAYAT REKAM MEDIS (RALAN & RANAP)
    ============================================= */
    .timeline-riwayat { position: relative; padding-left: 1.5rem; }
    .timeline-riwayat::before {
        content: ''; position: absolute; left: .35rem; top: 0; bottom: 0;
        width: 2px; background: linear-gradient(180deg, #0d7044, #a7f3d0);
    }
    .timeline-item { position: relative; margin-bottom: 1.1rem; }
    .timeline-dot {
        position: absolute; left: -1.25rem; top: .3rem;
        width: 12px; height: 12px; border-radius: 50%;
        background: #0d7044; border: 2px solid #fff;
        box-shadow: 0 0 0 2px #a7f3d0;
    }
    .timeline-card {
        background: #fff; border: 1px solid var(--rajal-border);
        border-radius: 14px; padding: .9rem 1.1rem;
        box-shadow: 0 2px 6px rgba(0,0,0,.04);
    }
    .timeline-card.current-visit {
        border-color: #86efac;
        background: #f0fdf4;
    }
    .timeline-date {
        font-size: .74rem; font-weight: 800; color: #0d7044;
        text-transform: uppercase; letter-spacing: .4px;
        margin-bottom: .5rem;
    }
    .timeline-soap-row {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: .6rem; margin-bottom: .5rem;
    }
    @media (max-width:576px) { .timeline-soap-row { grid-template-columns: 1fr; } }
    .soap-field label { font-size: .65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
    .soap-field p { font-size: .8rem; color: #0f172a; margin: 0; line-height: 1.5; }

    /* Lab / Radiologi rows */
    .lab-result-row {
        display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
        align-items: center; gap: .5rem;
        padding: .55rem .75rem; border-radius: 8px;
        margin-bottom: .4rem; font-size: .8rem;
    }
    .lab-result-row:nth-child(even) { background: #f8fafc; }
    .lab-result-row .pemeriksaan { font-weight: 600; color: #0f172a; }
    .lab-result-row .nilai { font-weight: 800; color: #0d7044; }
    .lab-result-row .rujukan { color: #64748b; font-size: .75rem; }
    .lab-result-row .keterangan { font-size: .72rem; }
    .abnormal { color: #dc2626 !important; }

    /* Vital Badges */
    .vital-badge-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: .2rem .65rem;
        font-size: .73rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }

    /* Drug Catalog & Stock Badges */
    .stok-depo-badge {
        font-size: .75rem;
        font-weight: 700;
        padding: .25rem .6rem;
        border-radius: 8px;
    }
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
                                        data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvasPelayanan"
                                        onclick="bukaPelayananMedis(this)"
                                        data-norawat-b64="{{ $noRawatB64 }}"
                                        data-norawat="{{ $p->no_rawat }}"
                                        data-norm="{{ $p->no_rkm_medis }}"
                                        data-nama="{{ htmlspecialchars($namaPasien, ENT_QUOTES) }}"
                                        data-jk="{{ $jkText }}"
                                        data-umur="{{ htmlspecialchars($umurText, ENT_QUOTES) }}"
                                        data-tlp="{{ htmlspecialchars($noTlp ?: '-', ENT_QUOTES) }}"
                                        data-poli="{{ htmlspecialchars($nmPoli, ENT_QUOTES) }}"
                                        data-dokter="{{ htmlspecialchars($namaDokter, ENT_QUOTES) }}"
                                        data-bayar="{{ htmlspecialchars($jenisBayar, ENT_QUOTES) }}"
                                        data-pj="{{ htmlspecialchars($nmPj ?: '-', ENT_QUOTES) }}"
                                        data-hubpj="{{ htmlspecialchars($hubPj ?: '-', ENT_QUOTES) }}"
                                        data-alamatpj="{{ htmlspecialchars($alamatPj ?: '-', ENT_QUOTES) }}">
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
                        <i class="bi bi-calendar-event-fill"></i> 6. Operasi / Bedah
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
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-riwayat-btn" data-bs-toggle="tab" data-bs-target="#tab-riwayat" type="button" role="tab">
                        <i class="bi bi-clock-history"></i> 10. Riwayat Lengkap
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
                {{-- Alert Mode Edit SOAP --}}
                <div id="alertEditSoap" class="alert alert-warning d-none d-flex justify-content-between align-items-center py-2 px-3 mb-3" style="border-radius:10px; border-left: 5px solid #d97706;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square fs-6 text-amber-800"></i>
                        <div>
                            <strong class="text-amber-900">Mode Edit SOAP:</strong> Mengubah pemeriksaan tanggal <span id="editSoapTglText" class="font-bold text-dark"></span> jam <span id="editSoapJamText" class="font-bold text-dark"></span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-dark font-bold py-1 px-2.5 rounded-pill" onclick="batalEditSoapRajal()">
                        <i class="bi bi-x-circle me-1"></i>Batal Edit (Input Baru)
                    </button>
                </div>

                <form id="formSoap">
                    <input type="hidden" name="no_rawat" id="soapNoRawat">
                    <input type="hidden" name="tgl_perawatan" id="soapTglPerawatan">
                    <input type="hidden" name="jam_rawat" id="soapJamRawat">

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
                {{-- Alert Mode Edit Resep --}}
                <div id="alertEditResep" class="alert alert-warning d-none d-flex justify-content-between align-items-center py-2.5 px-3 mb-3" style="border-radius:10px; border-left: 5px solid #d97706;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square fs-5 text-amber-800"></i>
                        <div>
                            <strong class="text-amber-900">Mode Edit E-Resep:</strong> Mengubah Resep No. <code id="editNoResepText" class="fs-7 text-danger font-bold"></code>
                            <div class="text-xs text-amber-800">Silakan sesuaikan item obat jadi atau racikan di bawah lalu klik Simpan Perubahan.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-dark font-bold py-1.5 px-3 rounded-pill" onclick="batalEditResepRajal()">
                        <i class="bi bi-x-circle me-1"></i>Batal Edit (Resep Baru)
                    </button>
                </div>

                <form id="formResep">
                    <input type="hidden" name="no_rawat" id="resepNoRawat">
                    <input type="hidden" name="no_resep" id="resepEditNoResep" value="">

                    {{-- Datalist master_aturan_pakai --}}
                    <datalist id="listAturanPakai"></datalist>

                    {{-- Navigasi Sub-Tab Mode Peresepan --}}
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 flex-wrap gap-2">
                        <ul class="nav nav-pills gap-1.5" id="pills-resep-mode" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="btn btn-sm btn-outline-success active font-bold text-xs py-1.5 px-3 rounded-pill" id="pills-obat-jadi-tab" data-bs-toggle="pill" data-bs-target="#pills-obat-jadi" type="button" role="tab">
                                    <i class="bi bi-capsule me-1"></i> 1. Obat Jadi / Non-Racikan (<span id="countObatJadiText">0</span>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="btn btn-sm btn-outline-primary font-bold text-xs py-1.5 px-3 rounded-pill" id="pills-obat-racik-tab" data-bs-toggle="pill" data-bs-target="#pills-obat-racik" type="button" role="tab">
                                    <i class="bi bi-mortarboard-fill me-1"></i> 2. Obat Racikan (<span id="countRacikanText">0</span>)
                                </button>
                            </li>
                        </ul>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 text-xs font-bold">
                                <i class="bi bi-building-check me-1"></i>Depo Farmasi: RALAN (G002)
                            </span>
                        </div>
                    </div>

                    <div class="tab-content" id="pills-resep-mode-content">
                        {{-- ========================================================= --}}
                        {{-- SUB-TAB 1: OBAT JADI (NON-RACIKAN)                        --}}
                        {{-- ========================================================= --}}
                        <div class="tab-pane fade show active" id="pills-obat-jadi" role="tabpanel">
                            {{-- 1.A. DRAFT RESEP OBAT JADI YANG DIPILIH --}}
                            <div class="card border rounded-3 p-3 mb-3 bg-white shadow-xs">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-bold text-xs text-slate-800 mb-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-card-checklist text-success fs-6"></i>
                                        Daftar Obat Jadi Diresepkan
                                    </h6>
                                    <span class="text-xs text-muted" id="badgeCountJadiSummary">0 item obat dipilih</span>
                                </div>
                                <div class="table-responsive border rounded-3 overflow-hidden shadow-2xs bg-white mb-1">
                                    <table class="table table-hover table-sm align-middle mb-0 text-xs">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 35px; text-align: center;">No</th>
                                                <th style="width: 80px;">Kode</th>
                                                <th>Nama Obat &amp; Satuan</th>
                                                <th style="width: 105px; text-align: center;">Stok Ralan</th>
                                                <th style="width: 90px; text-align: center;">Jumlah</th>
                                                <th style="width: 220px;">Aturan Pakai</th>
                                                <th style="width: 150px;">Keterangan</th>
                                                <th style="width: 45px; text-align: center;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedObatTbody">
                                            <tr><td colspan="8" class="text-center text-muted py-3 fs-7">Belum ada obat jadi dipilih. Silakan pilih dari Katalog Obat di bawah.</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- 1.B. PENCARIAN & KATALOG SELURUH OBAT DEPO FARMASI RALAN (G002) --}}
                            <div class="card border rounded-3 p-3 mb-3 bg-white shadow-xs">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <div>
                                        <h6 class="font-bold text-xs text-slate-800 mb-0 d-flex align-items-center gap-1.5">
                                            <i class="bi bi-search text-success fs-6"></i>
                                            Katalog &amp; Live Search Obat — Depo Farmasi Ralan (G002)
                                        </h6>
                                        <small class="text-muted" style="font-size:0.75rem;">Obat dengan stok kosong tidak dapat diresepkan.</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-xs btn-outline-success py-1 px-2.5 font-bold" id="btnRefreshLiveObat" title="Muat ulang data obat">
                                            <i class="bi bi-arrow-clockwise me-1"></i>Muat Ulang Data
                                        </button>
                                    </div>
                                </div>

                                <!-- Filter & Live Search Toolbar -->
                                <div class="row g-2 mb-2.5 align-items-center">
                                    <div class="col-md-5">
                                        <div class="position-relative">
                                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2.5 text-muted"></i>
                                            <input type="text" id="liveSearchObatInput" class="form-control form-control-sm ps-5" placeholder="Ketik nama obat, zat aktif, kode barang... (Live search)" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="liveKategoriFilter" class="form-select form-select-sm">
                                            <option value="">Semua Kategori Obat &amp; BHP</option>
                                            <option value="OBAT">Kategori: Obat / Farmasi</option>
                                            <option value="ALKES">Kategori: Alkes / BHP</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center justify-content-between">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input cursor-pointer" type="checkbox" id="liveStokFilter" checked>
                                            <label class="form-check-label text-xs font-semibold text-slate-700 cursor-pointer" for="liveStokFilter">
                                                Hanya Stok Ada (&gt; 0)
                                            </label>
                                        </div>
                                        <span class="text-xs text-muted" id="textInfoLiveObat">Memuat data obat...</span>
                                    </div>
                                </div>

                                <!-- Tabel Katalog Obat -->
                                <div class="table-responsive border rounded-3 overflow-hidden shadow-2xs" style="max-height: 440px; overflow-y: auto;">
                                    <table class="table table-hover table-sm align-middle mb-0 text-xs" id="tableLiveMasterObat">
                                        <thead class="table-light sticky-top" style="z-index: 2;">
                                            <tr>
                                                <th style="width: 80px;">Kode</th>
                                                <th>Nama Obat &amp; Satuan</th>
                                                <th style="width: 90px;">Kategori</th>
                                                <th style="width: 105px; text-align: center;">Stok Ralan</th>
                                                <th style="width: 95px; text-align: right;">Harga (Rp)</th>
                                                <th style="width: 75px; text-align: center;">Jml</th>
                                                <th style="width: 160px;">Aturan Pakai</th>
                                                <th style="width: 130px;">Keterangan</th>
                                                <th style="width: 95px; text-align: center;">+ Tambah</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyLiveMasterObat">
                                            <tr>
                                                <td colspan="9" class="text-center py-4 text-muted">
                                                    <span class="spinner-border spinner-border-sm text-success me-1"></span> Mengambil seluruh obat dari Depo Farmasi Ralan...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================= --}}
                        {{-- SUB-TAB 2: OBAT RACIKAN (FORMAT SIMRS-NAMIRA)             --}}
                        {{-- ========================================================= --}}
                        <div class="tab-pane fade" id="pills-obat-racik" role="tabpanel">
                            {{-- Form Pembuatan Racikan Baru --}}
                            <div class="card p-3 border text-xs mb-3 shadow-xs bg-white" style="border-radius:10px;">
                                <div class="font-bold text-xs text-slate-800 mb-2 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-mortarboard text-primary fs-6"></i>
                                    Buat Resep Racikan Baru
                                </div>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Nama Racikan <span class="text-danger">*</span></label>
                                        <input type="text" id="racikNamaInput" class="form-control form-control-sm" placeholder="Misal: Puyer Batuk Pilek, Salep Campur...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Metode Racik <span class="text-danger">*</span></label>
                                        <select id="racikMetodeSelect" class="form-select form-select-sm">
                                            <option value="R01">Puyer</option>
                                            <option value="R02">Sirup</option>
                                            <option value="R03">Salep</option>
                                            <option value="R04">Kapsul</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Jml Kemasan <span class="text-danger">*</span></label>
                                        <input type="number" id="racikJmlInput" class="form-control form-control-sm text-center font-bold" value="10" min="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Aturan Pakai <span class="text-danger">*</span></label>
                                        <input type="text" id="racikAturanInput" list="listAturanPakai" class="form-control form-control-sm" placeholder="Misal: 3 X 1 Bungkus...">
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label font-bold text-xs text-slate-700 mb-1">Keterangan Racik (Opsional)</label>
                                        <input type="text" id="racikKetInput" class="form-control form-control-sm" placeholder="Misal: Diminum sesudah makan, bila demam panas...">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary btn-sm w-100 font-bold" id="btnBuatGrupRacikan">
                                            <i class="bi bi-plus-circle me-1"></i> Buat Racikan Baru
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section-title"><i class="bi bi-boxes text-primary"></i> Daftar Racikan Dokter (Standar SIMRS-Namira)</div>
                            <div id="daftarRacikanContainer">
                                <div class="text-center py-4 border rounded-3 bg-light text-muted fs-7">
                                    <i class="bi bi-mortarboard fs-2 opacity-50 d-block mb-1"></i>
                                    Belum ada obat racikan dibuat. Silakan isi form di atas dan klik <b>Buat Racikan Baru</b>.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Ringkasan & Tombol Simpan Resep (Persistent di Bagian Bawah) --}}
                    <div class="card border rounded-3 p-3 bg-white shadow-xs mt-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="text-xs text-slate-700 font-semibold" id="resepSummaryText">
                                <i class="bi bi-cart3 me-1"></i> Total: <b>0</b> Obat Jadi, <b>0</b> Racikan (0 Bahan)
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm font-bold text-xs px-3 d-none" id="btnBatalEditResep" onclick="batalEditResepRajal()">
                                    <i class="bi bi-x-circle me-1"></i> Batal Edit
                                </button>
                                <button type="button" class="btn btn-success font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanResep">
                                    <i class="bi bi-send-check me-1"></i> Simpan &amp; Kirim E-Resep ke Farmasi
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <hr class="my-4">
                <div class="form-section-title"><i class="bi bi-clock-history text-primary"></i> Riwayat Resep Pasien Ini</div>
                <div id="riwayatResepContainer"><span class="text-muted text-xs">Belum ada riwayat resep obat.</span></div>
            </div>

            {{-- ===================================
                 6. MODUL OPERASI (JADWAL BOOKING & LAPORAN OPERASI STANDAR SIMRS-NAMIRA)
            =================================== --}}
            <div class="tab-pane fade" id="tab-operasi" role="tabpanel">
                {{-- Sub-Navigation Tab Operasi --}}
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 flex-wrap gap-2">
                    <ul class="nav nav-pills gap-1.5" id="pills-ops-mode" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="btn btn-sm btn-outline-danger active font-bold text-xs py-1.5 px-3 rounded-pill" id="pills-ops-booking-tab" data-bs-toggle="pill" data-bs-target="#pills-ops-booking" type="button" role="tab">
                                <i class="bi bi-calendar-event me-1"></i> 1. Booking Jadwal Operasi (<span id="countOpsBookingText">0</span>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="btn btn-sm btn-outline-primary font-bold text-xs py-1.5 px-3 rounded-pill" id="pills-ops-laporan-tab" data-bs-toggle="pill" data-bs-target="#pills-ops-laporan" type="button" role="tab">
                                <i class="bi bi-file-earmark-medical me-1"></i> 2. Laporan Operasi SIMRS (<span id="countOpsLaporanText">0</span>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="btn btn-sm btn-outline-success font-bold text-xs py-1.5 px-3 rounded-pill" id="pills-ops-selesai-tab" data-bs-toggle="pill" data-bs-target="#pills-ops-selesai" type="button" role="tab">
                                <i class="bi bi-check2-circle me-1"></i> 3. Tindakan Operasi Selesai (<span id="countOpsSelesaiText">0</span>)
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="pills-ops-content">
                    {{-- ── SUB-PANEL 1: BOOKING JADWAL OPERASI ── --}}
                    <div class="tab-pane fade show active" id="pills-ops-booking" role="tabpanel">
                        {{-- Alert Mode Edit Booking Operasi --}}
                        <div id="alertEditBookingOps" class="alert alert-warning d-none d-flex justify-content-between align-items-center py-2 px-3 mb-3" style="border-radius:10px; border-left: 5px solid #d97706;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-pencil-square fs-6 text-amber-800"></i>
                                <div>
                                    <strong class="text-amber-900">Mode Ubah Jadwal Booking Operasi:</strong> Mengubah <span id="editBookingOpsText" class="font-bold text-dark"></span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-xs btn-outline-dark font-bold py-1 px-2.5 rounded-pill" onclick="batalEditBookingOpsRajal()">
                                <i class="bi bi-x-circle me-1"></i>Batal Edit (Booking Baru)
                            </button>
                        </div>

                        <form id="formOperasi">
                            <input type="hidden" name="no_rawat" id="opsNoRawat">
                            <input type="hidden" name="old_kode_paket" id="opsOldKodePaket" value="">
                            <input type="hidden" name="old_tanggal" id="opsOldTanggal" value="">
                            <input type="hidden" name="old_jam_mulai" id="opsOldJamMulai" value="">

                            <div class="form-section-title d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-calendar-check-fill text-danger"></i> Booking Jadwal Operasi / Kamar Bedah (OK)</span>
                                <button type="button" class="btn btn-xs btn-outline-danger font-bold px-2.5 py-1 rounded-pill" onclick="bukaModalCariPaketRajal()">
                                    <i class="bi bi-search me-1"></i> Cari &amp; Pilih Paket Operasi
                                </button>
                            </div>

                            <div class="row g-2.5 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Paket Operasi / Tindakan Bedah <span class="text-danger">*</span></label>
                                    <select name="kode_paket" id="opsKodePaket" class="form-select form-select-sm select2-search">
                                        <option value="">-- Pilih Paket Operasi --</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Dokter Operator (Bedah) <span class="text-danger">*</span></label>
                                    <select name="kd_dokter" id="opsKdDokter" class="form-select form-select-sm select2-search">
                                        <option value="">-- Pilih Dokter Operator --</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Ruang Operasi (OK)</label>
                                    <select name="kd_ruang_ok" id="opsKdRuangOk" class="form-select form-select-sm select2-search">
                                        <option value="O1">KAMAR OPERASI 1</option>
                                        <option value="O2">KAMAR OPERASI 2</option>
                                        <option value="O3">KAMAR OPERASI 3</option>
                                        <option value="O4">KAMAR OPERASI 4</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal Operasi</label>
                                    <input type="date" name="tanggal" id="opsTanggal" class="form-control form-control-sm" value="{{ now()->toDateString() }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Jam Mulai</label>
                                    <input type="time" name="jam_mulai" id="opsJamMulai" class="form-control form-control-sm" value="08:00">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Jam Selesai</label>
                                    <input type="time" name="jam_selesai" id="opsJamSelesai" class="form-control form-control-sm" value="09:30">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Status</label>
                                    <select name="status" id="opsStatus" class="form-select form-select-sm select2-search">
                                        <option value="Menunggu">Menunggu</option>
                                        <option value="Proses Operasi">Proses Operasi</option>
                                        <option value="Selesai">Selesai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary font-bold text-xs px-3 py-2" style="border-radius:8px;" onclick="batalEditBookingOpsRajal()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                </button>
                                <button type="button" class="btn btn-danger font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanOperasi">
                                    <i class="bi bi-calendar-check-fill me-1"></i> Simpan Jadwal Operasi
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">
                        <div class="form-section-title"><i class="bi bi-clock-history text-primary"></i> Riwayat Booking Operasi Pasien</div>
                        <div id="riwayatOpsContainer"><span class="text-muted text-xs">Belum ada booking operasi.</span></div>
                    </div>

                    {{-- ── SUB-PANEL 2: PENGINPUTAN LAPORAN OPERASI (STANDAR SIMRS-NAMIRA) ── --}}
                    <div class="tab-pane fade" id="pills-ops-laporan" role="tabpanel">
                        {{-- Alert Mode Edit Laporan Operasi --}}
                        <div id="alertEditLaporanOps" class="alert alert-warning d-none d-flex justify-content-between align-items-center py-2 px-3 mb-3" style="border-radius:10px; border-left: 5px solid #d97706;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-pencil-square fs-6 text-amber-800"></i>
                                <div>
                                    <strong class="text-amber-900">Mode Edit Laporan Operasi:</strong> Mengubah laporan tanggal <span id="editLapOpsTglText" class="font-bold text-dark"></span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-xs btn-outline-dark font-bold py-1 px-2.5 rounded-pill" onclick="batalEditLaporanOpsRajal()">
                                <i class="bi bi-x-circle me-1"></i>Batal Edit (Input Baru)
                            </button>
                        </div>

                        <form id="formLaporanOperasi">
                            <input type="hidden" name="no_rawat" id="lapOpsNoRawat">
                            <input type="hidden" name="old_tanggal" id="lapOpsOldTanggal" value="">

                            <div class="form-section-title d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-file-earmark-medical-fill text-primary"></i> Formulir Laporan Operasi (SIMRS-Namira)</span>
                                <button type="button" class="btn btn-xs btn-outline-primary font-bold px-3 py-1.5 rounded-pill shadow-xs" onclick="bukaModalTemplateLaporanRajal()">
                                    <i class="bi bi-bookmarks-fill me-1 text-primary"></i> Pilih Template Laporan Operasi
                                </button>
                            </div>

                            <div class="row g-2.5 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal Laporan Operasi</label>
                                    <input type="datetime-local" name="tanggal" id="lapOpsTanggal" class="form-control form-control-sm" value="{{ now()->format('Y-m-d\TH:i') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal &amp; Jam Mulai Operasi</label>
                                    <input type="datetime-local" name="tgl_operasi" id="lapOpsTglOperasi" class="form-control form-control-sm" value="{{ now()->format('Y-m-d\TH:i') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Tanggal &amp; Jam Selesai Operasi</label>
                                    <input type="datetime-local" name="selesaioperasi" id="lapOpsSelesaiOperasi" class="form-control form-control-sm" value="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Diagnosa Pra Bedah (Pre-Op) <span class="text-danger">*</span></label>
                                    <input type="text" name="diagnosa_preop" id="lapOpsPreop" class="form-control form-control-sm" placeholder="Contoh: G1 P0 A0 Hamil Aterm dg/ SC 1x...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Diagnosa Pasca Bedah (Post-Op) <span class="text-danger">*</span></label>
                                    <input type="text" name="diagnosa_postop" id="lapOpsPostop" class="form-control form-control-sm" placeholder="Contoh: Post Sectio Caesarea...">
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Jaringan yang Dieksekusi / Dieksisi</label>
                                    <input type="text" name="jaringan_dieksekusi" id="lapOpsJaringan" class="form-control form-control-sm" placeholder="Contoh: Kulit, Dinding Abdomen, -">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Permintaan PA</label>
                                    <select name="permintaan_pa" id="lapOpsPa" class="form-select form-select-sm">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Jenis Pembiusan (Anasthesi)</label>
                                    <input type="text" name="jenis_anasthesi" id="lapOpsAnasthesi" class="form-control form-control-sm" placeholder="Spinal, GA, Lokal, Sedasi..." value="SPINAL">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label font-bold text-xs text-slate-600 mb-1">Kategori Operasi</label>
                                    <select name="kategori" id="lapOpsKategori" class="form-select form-select-sm">
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
                                    <textarea name="laporan_operasi" id="lapOpsLaporan" class="form-control form-control-sm" rows="8" placeholder="Tuliskan laporan operasi secara lengkap: desinfeksi, insisi, eksplorasi, hemostasis, penjahitan lapis demi lapis, jumlah perdarahan, urine, kondisi bayi/pasien..." style="font-family:monospace, inherit; line-height:1.6;"></textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-secondary btn-sm font-bold px-3 py-2" onclick="resetFormLaporanOpsRajal()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Formulir
                                </button>
                                <button type="button" class="btn btn-success font-bold text-xs px-4 py-2" style="border-radius:8px;" id="btnSimpanLaporanOperasi">
                                    <i class="bi bi-save-fill me-1"></i> Simpan Laporan Operasi
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">
                        <div class="form-section-title"><i class="bi bi-file-earmark-text-fill text-danger"></i> Daftar Laporan Operasi Pasien Ini</div>
                        <div id="laporanOpsContainer"><span class="text-muted text-xs">Belum ada laporan operasi.</span></div>
                    </div>

                    {{-- ── SUB-PANEL 3: RIWAYAT TINDAKAN OPERASI SELESAI (TABEL OPERASI) ── --}}
                    <div class="tab-pane fade" id="pills-ops-selesai" role="tabpanel">
                        <div class="form-section-title"><i class="bi bi-shield-check text-success"></i> Riwayat Tindakan Operasi Selesai &amp; Rincian Billing</div>
                        <div id="selesaiOpsContainer"><span class="text-muted text-xs">Belum ada data operasi selesai.</span></div>
                    </div>
                </div>
            </div>

            {{-- ===================================
                 7. MODUL DIAGNOSA (ICD-10) & PROSEDUR (ICD-9-CM)
            =================================== --}}
            <div class="tab-pane fade" id="tab-diagnosa" role="tabpanel">
                {{-- Bagian 1: Diagnosa ICD-10 Kunjungan Ini --}}
                <div class="form-section-title d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-search-heart text-primary"></i> 1. Input Diagnosa Pasien (ICD-10)</span>
                </div>
                <div class="card p-3 mb-3 border text-xs" style="border-radius:10px;background:#f8fafc;">
                    <div class="row g-2">
                        <div class="col-md-7">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Cari Kode / Nama Penyakit (ICD-10)</label>
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

                <div class="fw-bold fs-7 text-slate-800 mb-2"><i class="bi bi-clipboard2-check-fill text-success me-1"></i> Diagnosa Pasien Kunjungan Ini (ICD-10)</div>
                <div id="rajalDiagnosaContainer" class="mb-4"><span class="text-muted text-xs">Belum ada diagnosa dicatat.</span></div>

                {{-- Bagian 2: Prosedur Tindakan ICD-9-CM --}}
                <hr class="my-4 text-slate-300">
                <div class="form-section-title d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-diagram-3-fill text-indigo-600"></i> 2. Input Prosedur / Tindakan Klinis (ICD-9-CM)</span>
                </div>
                <div class="card p-3 mb-3 border text-xs" style="border-radius:10px;background:#fdf4ff; border-color:#f0abfc !important;">
                    <div class="row g-2">
                        <div class="col-md-7">
                            <label class="form-label font-bold text-xs text-slate-700 mb-1">Cari Kode / Deskripsi Prosedur (ICD-9-CM)</label>
                            <select id="rajalProsedurSelect" class="form-select form-select-sm" style="width:100%"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-700 mb-1">Prioritas</label>
                            <select id="rajalProsedurPrioritas" class="form-select form-select-sm">
                                <option value="1">1 — Prosedur Utama</option>
                                <option value="2">2 — Prosedur Sekunder</option>
                                <option value="3">3 — Prosedur Sekunder 2</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-purple-700 btn-sm w-100 font-bold text-white" id="btnTambahProsedurRajal" style="background:#7c3aed; border-radius:8px;">
                                <i class="bi bi-plus-lg me-1"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="fw-bold fs-7 text-slate-800 mb-2"><i class="bi bi-check2-square text-purple-700 me-1"></i> Prosedur Pasien Kunjungan Ini (ICD-9-CM)</div>
                <div id="rajalProsedurContainer" class="mb-4"><span class="text-muted text-xs">Belum ada prosedur ICD-9 dicatat.</span></div>

                {{-- Bagian 3: Riwayat Diagnosa & Prosedur Lampau Seluruh Kunjungan --}}
                <hr class="my-4 text-slate-300">
                <div class="form-section-title"><i class="bi bi-clock-history text-secondary"></i> 3. Riwayat Diagnosa &amp; Prosedur Lampau (Seluruh Kunjungan Pasien)</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="fw-semibold text-slate-700 text-xs mb-1.5"><i class="bi bi-journal-medical text-primary me-1"></i>Riwayat Diagnosa ICD-10:</div>
                        <div id="rajalRiwayatDiagnosaContainer"><span class="text-muted text-xs">Belum ada riwayat diagnosa lampau.</span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold text-slate-700 text-xs mb-1.5"><i class="bi bi-diagram-2 text-purple-700 me-1"></i>Riwayat Prosedur ICD-9-CM:</div>
                        <div id="rajalRiwayatProsedurContainer"><span class="text-muted text-xs">Belum ada riwayat prosedur lampau.</span></div>
                    </div>
                </div>
            </div>

            {{-- ===================================
                 8. MODUL TINDAKAN MEDIS (rawat_jl_dr / rawat_jl_pr / rawat_jl_drpr)
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
                                <option value="Paramedis">Paramedis</option>
                                <option value="Dokter & Paramedis">Dokter &amp; Paramedis</option>
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

                <div class="fw-bold fs-7 text-slate-800 mb-2"><i class="bi bi-list-check text-success me-1"></i> Daftar Tindakan Pasien Kunjungan Ini</div>
                <div id="rajalTindakanContainer" class="mb-4"><span class="text-muted text-xs">Belum ada tindakan dicatat.</span></div>

                <hr class="my-4 text-slate-300">
                <div class="form-section-title"><i class="bi bi-clock-history text-secondary"></i> Riwayat Tindakan Medis Lampau (Ralan &amp; Ranap)</div>
                <div id="rajalRiwayatTindakanContainer"><span class="text-muted text-xs">Belum ada riwayat tindakan lampau.</span></div>
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

            {{-- ===================================
                 10. MODUL RIWAYAT LENGKAP REKAM MEDIS (RALAN & RANAP)
            =================================== --}}
            <div class="tab-pane fade" id="tab-riwayat" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-slate-800 mb-0 fs-7">
                            <i class="bi bi-clock-history text-success me-1"></i> Riwayat Rekam Medis Komprehensif (Rawat Jalan &amp; Rawat Inap)
                        </h6>
                        <small class="text-muted text-xs">Seluruh catatan klinis, anamnesis awal, SOAP, lab, radiologi, resep, dan operasi pasien</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary font-bold text-xs" id="btnRefreshRiwayatLengkap">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Riwayat
                        </button>
                    </div>
                </div>

                {{-- Filter Cepat Kategori Riwayat --}}
                <div class="d-flex gap-1.5 mb-3 flex-wrap" id="filterRiwayatLengkapButtons">
                    <button type="button" class="btn btn-xs btn-success font-bold filter-riwayat-btn active" data-filter="all">Semua Riwayat</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary font-bold filter-riwayat-btn" data-filter="soap">SOAP &amp; TTV</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary font-bold filter-riwayat-btn" data-filter="awalmedis">Asesmen Medis &amp; Kep</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary font-bold filter-riwayat-btn" data-filter="diagnosa">Diagnosa &amp; Prosedur</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary font-bold filter-riwayat-btn" data-filter="tindakan">Tindakan Medis</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary font-bold filter-riwayat-btn" data-filter="resep">Resep Dokter</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary font-bold filter-riwayat-btn" data-filter="lab">Laboratorium</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary font-bold filter-riwayat-btn" data-filter="rad">Radiologi</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary font-bold filter-riwayat-btn" data-filter="operasi">Operasi Bedah</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary font-bold filter-riwayat-btn" data-filter="resume">Resume Medis</button>
                </div>

                <div id="riwayatLengkapTimelineContainer">
                    <div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat riwayat rekam medis...</div>
                </div>
            </div>

                </div> {{-- End of #pelayananTabContent --}}
            </div> {{-- End of .offcanvas-body --}}
        </div> {{-- End of .emr-main-right --}}
    </div> {{-- End of .offcanvas-pelayanan --}}
</div> {{-- End of container balance --}}

{{-- MODAL KATALOG OBAT FARMASI & CEK STOK REAL-TIME (SIMRS NAMIRA) --}}
<div class="modal fade" id="modalKatalogObatRajal" tabindex="-1" aria-labelledby="modalKatalogObatRajalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header py-3 px-4" style="background:linear-gradient(135deg, #042b1b 0%, #0d7044 100%); color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-capsule fs-5 text-white"></i>
                    </div>
                    <div>
                        <h6 class="modal-title font-bold mb-0 text-white" id="modalKatalogObatRajalLabel">Katalog Obat Farmasi &amp; Cek Stok Real-Time</h6>
                        <small class="text-white text-opacity-75" style="font-size:0.75rem;">Depo Farmasi Ralan (G002) &amp; Total Depo RS Namira</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4" style="background:#f8fafc;">
                <!-- Filter Row -->
                <div class="row g-2 mb-3">
                    <div class="col-md-7">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"></i>
                            <input type="text" id="katalogSearchInput" class="form-control form-control-sm ps-5 py-2 shadow-xs" placeholder="Ketik nama obat / zat aktif / kode barang..." style="border-radius:10px; font-size:0.85rem;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="katalogKategoriFilter" class="form-select form-select-sm py-2" style="border-radius:10px; font-size:0.85rem;">
                            <option value="">Semua Kategori &amp; Jenis</option>
                            <option value="OBAT">Obat / Farmasi</option>
                            <option value="ALKES">Alat Kesehatan / BHP</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success btn-sm w-100 py-2 font-bold" id="btnCariKatalogObat">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                    </div>
                </div>

                <!-- Hasil Pencarian Obat Table -->
                <div class="table-responsive border rounded-3 bg-white shadow-xs" style="max-height: 480px; overflow-y:auto;">
                    <table class="table table-hover table-sm align-middle mb-0 text-xs">
                        <thead class="table-light sticky-top" style="z-index:2;">
                            <tr>
                                <th style="width:110px;">Kode</th>
                                <th>Nama Obat &amp; Satuan</th>
                                <th style="width:110px;">Kategori</th>
                                <th style="width:130px; text-align:center;">Stok Depo Ralan</th>
                                <th style="width:110px; text-align:center;">Stok Total RS</th>
                                <th style="width:110px; text-align:right;">Harga Ralan</th>
                                <th style="width:190px; text-align:center;">Pilih Obat</th>
                            </tr>
                        </thead>
                        <tbody id="katalogObatTbody">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-search fs-4 d-block mb-1 opacity-50"></i>
                                    Ketik nama obat di atas untuk mencari atau klik Cari.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2.5 px-4 bg-white border-top d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Stok Depo Ralan merupakan stok fisik tersedia di Depo Farmasi Rawat Jalan (G002).</small>
                <button type="button" class="btn btn-secondary btn-sm px-4 font-bold text-xs rounded-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
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

{{-- MODAL CARI & PILIH PAKET OPERASI SIMRS-NAMIRA --}}
<div class="modal fade" id="modalCariPaketOperasi" tabindex="-1" aria-labelledby="modalCariPaketOperasiLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header py-3 px-4" style="background:#0f172a; color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-search text-danger fs-5"></i>
                    <h6 class="modal-title font-bold mb-0 text-white" id="modalCariPaketOperasiLabel">::[ Cari &amp; Pilih Paket Tindakan Operasi SIMRS-Namira ]::</h6>
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
                                <input type="text" id="searchPaketOpsInput" class="form-control" placeholder="Ketik nama operasi (misal: SC, Sirkumsisi, Hernia, Kuret)..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Kategori</label>
                            <select id="filterKategoriOpsSelect" class="form-select form-select-sm">
                                <option value="">-- Semua Kategori --</option>
                                <option value="Operasi">Operasi Bedah</option>
                                <option value="Kebidanan">Kebidanan / Kandungan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs text-slate-600 mb-1">Kelas Perawatan</label>
                            <select id="filterKelasOpsSelect" class="form-select form-select-sm">
                                <option value="">-- Semua Kelas --</option>
                                <option value="Rawat Jalan">Rawat Jalan</option>
                                <option value="Kelas 1">Kelas 1</option>
                                <option value="Kelas 2">Kelas 2</option>
                                <option value="Kelas 3">Kelas 3</option>
                                <option value="Kelas VIP">Kelas VIP</option>
                                <option value="Kelas VVIP">Kelas VVIP</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 font-bold" onclick="loadPaketOperasiModal('')">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive bg-white rounded-3 border shadow-xs" style="max-height: 420px;">
                    <table class="table table-hover table-sm align-middle mb-0 text-xs" id="tableModalPaketOps">
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
                        <tbody id="tbodyModalPaketOps">
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

{{-- MODAL PILIH TEMPLATE LAPORAN OPERASI SIMRS-NAMIRA --}}
<div class="modal fade" id="modalTemplateLaporanOperasi" tabindex="-1" aria-labelledby="modalTemplateLaporanOperasiLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header py-3 px-4" style="background:#0f172a; color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bookmarks-fill text-primary fs-5"></i>
                    <h6 class="modal-title font-bold mb-0 text-white" id="modalTemplateLaporanOperasiLabel">::[ Master Template Laporan Operasi SIMRS-Namira ]::</h6>
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
                                <input type="text" id="searchTemplateOpsInput" class="form-control" placeholder="Cari nama template operasi / diagnosa..." autocomplete="off">
                            </div>
                        </div>

                        <div class="table-responsive bg-white rounded-3 border shadow-xs" style="max-height: 420px;">
                            <table class="table table-hover table-sm align-middle mb-0 text-xs" id="tableModalTemplateOps">
                                <thead class="table-dark sticky-top" style="font-size:.78rem;">
                                    <tr>
                                        <th style="width:70px;">Kode</th>
                                        <th>Nama Operasi</th>
                                        <th>Pre-Op / Post-Op</th>
                                        <th style="width:60px; text-align:center;">PA</th>
                                        <th style="width:70px; text-align:center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyModalTemplateOps">
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
                                <span class="badge bg-primary px-2 py-1" id="previewTemplateNoText">-</span>
                            </div>
                            <div class="mb-2 text-xs">
                                <div><strong>Nama Operasi:</strong> <span id="previewTemplateNamaText" class="text-slate-800 font-bold">-</span></div>
                                <div><strong>Diagnosa Pre-Op:</strong> <span id="previewTemplatePreopText" class="text-slate-700">-</span></div>
                                <div><strong>Diagnosa Post-Op:</strong> <span id="previewTemplatePostopText" class="text-slate-700">-</span></div>
                                <div><strong>Jaringan Dieksisi:</strong> <span id="previewTemplateJaringanText" class="text-slate-700">-</span> | <strong>PA:</strong> <span id="previewTemplatePaText" class="badge bg-light text-dark border">-</span></div>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Uraian Laporan Pembedahan:</label>
                                <textarea id="previewTemplateLaporanText" class="form-control form-control-sm bg-light" rows="12" readonly style="font-family:monospace, inherit; font-size:.78rem; line-height:1.5;"></textarea>
                            </div>
                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-success btn-sm font-bold px-4 py-2" id="btnTerapkanTemplateOps" disabled style="border-radius:8px;">
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
    const isUserDokter = {{ $isDokter ? 'true' : 'false' }};
    const isUserAdmin  = {{ $isAdmin ? 'true' : 'false' }};
    const isDokterOrAdmin = isUserDokter || isUserAdmin;

    let activeNoRawat = '';
    let currentRajalData = null;
    let selectedLabItems = [];
    let selectedRadItems = [];
    let selectedObatItems = [];
    let selectedRacikanItems = [];

    // Master Obat Cache untuk Live Search Depo Farmasi Ralan (G002)
    let allMasterObatRajal = [];
    let isMasterObatLoaded = false;

    const offcanvasEl = document.getElementById('offcanvasPelayanan');
    const bsOffcanvas = (offcanvasEl && window.bootstrap && bootstrap.Offcanvas)
        ? bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl)
        : null;

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

    // Global Function untuk Buka Offcanvas Pelayanan Medis
    window.bukaPelayananMedis = function (btn) {
        // 1. Buka offcanvas seketika tanpa blocking
        try {
            const ocEl = document.getElementById('offcanvasPelayanan');
            if (ocEl && window.bootstrap && bootstrap.Offcanvas) {
                bootstrap.Offcanvas.getOrCreateInstance(ocEl).show();
            } else if (window.jQuery) {
                $('#offcanvasPelayanan').offcanvas('show');
            }
        } catch (e) {
            console.warn('Error saat membuka offcanvas:', e);
        }

        // 2. Baca data dari atribut tombol secara defensif
        try {
            const $btn       = $(btn).closest('.open-pelayanan-btn').length ? $(btn).closest('.open-pelayanan-btn') : $(btn);
            const noRawat    = String($btn.attr('data-norawat') || $btn.data('norawat') || '').trim();
            const noRawatB64 = $btn.attr('data-norawat-b64') || $btn.data('norawat-b64') || $btn.data('norawatB64') || (noRawat ? btoa(noRawat) : '');
            const noRm       = String($btn.attr('data-norm') || $btn.data('norm') || '');
            const nama       = String($btn.attr('data-nama') || $btn.data('nama') || '').trim();
            const jk         = String($btn.attr('data-jk') || $btn.data('jk') || '');
            const umur       = String($btn.attr('data-umur') || $btn.data('umur') || '');
            const tlp        = String($btn.attr('data-tlp') || $btn.data('tlp') || '-');
            const poli       = String($btn.attr('data-poli') || $btn.data('poli') || '');
            const dokter     = String($btn.attr('data-dokter') || $btn.data('dokter') || '');
            const bayar      = String($btn.attr('data-bayar') || $btn.data('bayar') || '');
            const pj         = String($btn.attr('data-pj') || $btn.data('pj') || '-');
            const hubpj      = String($btn.attr('data-hubpj') || $btn.data('hubpj') || '-');
            const alamatpj   = String($btn.attr('data-alamatpj') || $btn.data('alamatpj') || '-');

            activeNoRawat = noRawat;

            // Header Pasien Rinci
            const initials = (nama.length >= 2 ? nama.substring(0, 2) : (nama || 'PS')).toUpperCase();
            $('#ofcAvatar').text(initials);
            $('#ofcNamaPasien').text(nama || 'Pasien');
            $('#ofcNoRawat').text(noRawat || '—');
            $('#ofcNoRm').text('RM: ' + (noRm || '—'));
            $('#ofcPoli').text(poli || '—');
            $('#ofcBayar').text(bayar || '—');
            
            const jkHtml = (jk === 'L' || jk === 'Laki-laki')
                ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1" style="font-weight:700;"><i class="bi bi-gender-male me-1"></i> Laki-laki (L)</span>`
                : `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1" style="font-weight:700;"><i class="bi bi-gender-female me-1"></i> Perempuan (P)</span>`;
            $('#ofcJk').html(jkHtml);
            
            $('#ofcUmur').text(umur || '—');
            $('#ofcTlp').text(tlp || '—');
            $('#ofcDokter').text(dokter || '—');
            $('#ofcAlamatPasien').text(alamatpj || '—');

            // Reset forms safely
            if ($('#formSoap').length && $('#formSoap')[0]) $('#formSoap')[0].reset();
            if ($('#formAwalMedis').length && $('#formAwalMedis')[0]) $('#formAwalMedis')[0].reset();
            if ($('#formLab').length && $('#formLab')[0]) $('#formLab')[0].reset();
            if ($('#formRad').length && $('#formRad')[0]) $('#formRad')[0].reset();
            if ($('#formResep').length && $('#formResep')[0]) $('#formResep')[0].reset();
            if ($('#formOperasi').length && $('#formOperasi')[0]) $('#formOperasi')[0].reset();
            if ($('#formLaporanOperasi').length && $('#formLaporanOperasi')[0]) $('#formLaporanOperasi')[0].reset();
            if ($('#formResumeRajal').length && $('#formResumeRajal')[0]) $('#formResumeRajal')[0].reset();
            
            try { $('.select2-search').val(null).trigger('change.select2'); } catch(e) {}

            // Set hidden input no_rawat across all forms
            $('#soapNoRawat, #awalNoRawat, #labNoRawat, #radNoRawat, #resepNoRawat, #opsNoRawat, #lapOpsNoRawat, #resumeNoRawat').val(noRawat);

            // Reset temporary selections
            selectedLabItems = [];
            selectedRadItems = [];
            selectedObatItems = [];
            selectedRacikanItems = [];
            if (typeof renderSelectedLab === 'function') renderSelectedLab();
            if (typeof renderSelectedRad === 'function') renderSelectedRad();
            if (typeof renderSelectedObat === 'function') renderSelectedObat();
            if (typeof renderDaftarRacikan === 'function') renderDaftarRacikan();
            if (typeof updateResepSummary === 'function') updateResepSummary();

            // Load detail data via AJAX
            loadPasienDetail(noRawat || noRawatB64);
            if (typeof loadMasterOperasi === 'function') loadMasterOperasi();
            if (typeof loadMasterRuangOk === 'function') loadMasterRuangOk();
        } catch (err) {
            console.error('Error in bukaPelayananMedis:', err);
        }
    };

    // Fallback Delegated Click Listener
    $(document).on('click', '.open-pelayanan-btn', function () {
        window.bukaPelayananMedis(this);
    });

    // --- Helper Functions for Formatting & Display ---
    function fmtDate(val) {
        if (!val || val === '0000-00-00') return '-';
        try {
            return new Date(val).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
        } catch(e) { return val; }
    }
    function fmtRupiah(val) {
        if (!val) return 'Rp 0';
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    }
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function vital(icon, label, val, color) {
        if (!val || val === '-' || val === '0' || val === '0.0') return '';
        return `<span class="vital-badge-item">
            <i class="bi ${icon}" style="color:${color};"></i>
            <span class="vital-label">${label}:</span>
            <span class="vital-val">${escapeHtml(val)}</span>
        </span>`;
    }
    function soapField(lbl, val) {
        if (!val || val === '-') return '';
        return `<div class="soap-field"><label>${lbl}</label><p>${escapeHtml(val)}</p></div>`;
    }

    function updateTabBadges(res) {
        const soapCount = (res.soapList || []).length;
        const awalCount = ((res.awalMedisList || []).length) + ((res.keperawatanList || []).length);
        const labCount  = (res.labOrders || []).length;
        const radCount  = (res.radOrders || []).length;
        const resepCount = (res.resepList || []).length;
        const opsCount  = ((res.bookingOps || []).length) + ((res.laporanOps || []).length) + ((res.tagihanOperasi || []).length);
        const diagCount = ((res.diagnosaList || []).length) + ((res.prosedurList || []).length);
        const tndkCount = (res.tindakanList || []).length;
        const resumeCount = res.resumePasien ? 1 : 0;
        const totalCount = soapCount + awalCount + labCount + radCount + resepCount + opsCount + diagCount + tndkCount + resumeCount;

        $('#tab-soap-btn').html(`<i class="bi bi-journal-medical"></i> 1. SOAP ${soapCount > 0 ? `<span class="badge bg-success bg-opacity-20 text-success rounded-pill ms-1">${soapCount}</span>` : ''}`);
        $('#tab-awalmedis-btn').html(`<i class="bi bi-clipboard2-user-fill"></i> 2. Awal Medis ${awalCount > 0 ? `<span class="badge bg-primary bg-opacity-20 text-primary rounded-pill ms-1">${awalCount}</span>` : ''}`);
        $('#tab-lab-btn').html(`<i class="bi bi-eyedropper"></i> 3. Laboratorium ${labCount > 0 ? `<span class="badge bg-info bg-opacity-20 text-info rounded-pill ms-1">${labCount}</span>` : ''}`);
        $('#tab-rad-btn').html(`<i class="bi bi-intersect"></i> 4. Radiologi ${radCount > 0 ? `<span class="badge bg-secondary bg-opacity-20 text-secondary rounded-pill ms-1">${radCount}</span>` : ''}`);
        $('#tab-resep-btn').html(`<i class="bi bi-capsule"></i> 5. Resep Dokter ${resepCount > 0 ? `<span class="badge bg-warning bg-opacity-30 text-warning-emphasis rounded-pill ms-1">${resepCount}</span>` : ''}`);
        $('#tab-operasi-btn').html(`<i class="bi bi-calendar-event-fill"></i> 6. Operasi / Bedah ${opsCount > 0 ? `<span class="badge bg-danger bg-opacity-20 text-danger rounded-pill ms-1">${opsCount}</span>` : ''}`);
        $('#tab-diagnosa-btn').html(`<i class="bi bi-search-heart"></i> 7. Diagnosa ${diagCount > 0 ? `<span class="badge bg-indigo-100 text-indigo-700 rounded-pill ms-1">${diagCount}</span>` : ''}`);
        $('#tab-tindakan-btn').html(`<i class="bi bi-activity"></i> 8. Tindakan ${tndkCount > 0 ? `<span class="badge bg-success bg-opacity-20 text-success rounded-pill ms-1">${tndkCount}</span>` : ''}`);
        $('#tab-resume-btn').html(`<i class="bi bi-file-earmark-medical"></i> 9. Resume Ralan ${resumeCount > 0 ? `<span class="badge bg-primary bg-opacity-20 text-primary rounded-pill ms-1">${resumeCount}</span>` : ''}`);
        $('#tab-riwayat-btn').html(`<i class="bi bi-clock-history"></i> 10. Riwayat Lengkap <span class="badge bg-success rounded-pill ms-1">${totalCount}</span>`);
    }

    function loadPasienDetail(noRawatOrB64) {
        if (!noRawatOrB64) return;

        // Tampilkan loading placeholder di seluruh container riwayat
        $('#riwayatSoapContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat riwayat SOAP...</div>');
        $('#riwayatAwalMedisContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat riwayat asesmen awal medis...</div>');
        $('#riwayatLabContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat riwayat laboratorium...</div>');
        $('#riwayatRadContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat riwayat radiologi...</div>');
        $('#riwayatResepContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat riwayat resep...</div>');
        $('#riwayatOpsContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat jadwal booking operasi...</div>');
        $('#riwayatLaporanOpsContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat laporan operasi...</div>');
        $('#selesaiOpsContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat tindakan operasi selesai...</div>');
        $('#diagnosaCurrentList, #diagnosaHistoryList').html('<div class="text-center py-3 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat diagnosa...</div>');
        $('#prosedurCurrentList, #prosedurHistoryList').html('<div class="text-center py-3 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat prosedur...</div>');
        $('#tindakanCurrentList, #tindakanHistoryList').html('<div class="text-center py-3 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat tindakan...</div>');
        $('#riwayatLengkapTimelineContainer').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1 text-success"></span> Memuat riwayat rekam medis komprehensif...</div>');

        const isRaw = (typeof noRawatOrB64 === 'string' && noRawatOrB64.includes('/'));
        const requestParams = isRaw ? { no_rawat: noRawatOrB64 } : { no_rawat_b64: noRawatOrB64 };

        $.ajax({
            url: "{{ route('rawat-jalan.pasien-detail') }}",
            type: 'GET',
            data: requestParams,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    // Update header demografi pasien secara lengkap
                    if (res.pasien) {
                        const p = res.pasien;
                        if (p.nm_pasien) {
                            $('#ofcNamaPasien').text(p.nm_pasien);
                            const initials = (p.nm_pasien.length >= 2 ? p.nm_pasien.substring(0, 2) : p.nm_pasien).toUpperCase();
                            $('#ofcAvatar').text(initials);
                        }
                        if (p.no_rkm_medis) $('#ofcNoRm').text('RM: ' + p.no_rkm_medis);
                        if (p.no_rawat) {
                            $('#ofcNoRawat').text(p.no_rawat);
                            $('#soapNoRawat, #awalNoRawat, #labNoRawat, #radNoRawat, #resepNoRawat, #opsNoRawat, #lapOpsNoRawat, #resumeNoRawat').val(p.no_rawat);
                        }
                        if (p.nm_poli) $('#ofcPoli').text(p.nm_poli);
                        if (p.jenis_bayar) $('#ofcBayar').text(p.jenis_bayar);
                        if (p.nm_dokter) $('#ofcDokter').text(p.nm_dokter);
                        if (p.no_tlp && p.no_tlp !== '0000000000' && p.no_tlp !== '-') $('#ofcTlp').text(p.no_tlp);
                        if (p.alamat && p.alamat !== '-') $('#ofcAlamatPasien').text(p.alamat);
                        if (p.jk) {
                            const jkHtml = (p.jk === 'L' || p.jk === 'Laki-laki')
                                ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1" style="font-weight:700;"><i class="bi bi-gender-male me-1"></i> Laki-laki (L)</span>`
                                : `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1" style="font-weight:700;"><i class="bi bi-gender-female me-1"></i> Perempuan (P)</span>`;
                            $('#ofcJk').html(jkHtml);
                        }
                    }

                    // Update Tab Badges
                    updateTabBadges(res);

                    // 1. Render History SOAP (Seluruh Kunjungan)
                    renderRiwayatSoap(res.soapList || []);

                    // 2. Render History Awal Medis & Keperawatan (Seluruh Kunjungan & Departemen)
                    renderRiwayatAwalMedis(res.awalMedisList || [], res.keperawatanList || []);

                    // 3. Render Permintaan & HASIL Laboratorium
                    renderRiwayatLab(res.labOrders || [], res.labResults || []);

                    // 4. Render Permintaan & HASIL Ekspertisi Radiologi
                    renderRiwayatRad(res.radOrders || [], res.radResults || []);

                    // 5. Render History Resep Dokter (SIMRS Namira Parity)
                    renderRiwayatResep(res.resepList || []);

                    // 6. Render Booking Operasi, Laporan Operasi & Tindakan Selesai
                    renderRiwayatOps(res.bookingOps || [], res.laporanOps || [], res.tagihanOperasi || []);

                    // 7. Render Diagnosa ICD-10 & Prosedur ICD-9
                    renderDiagnosaRajal(res.diagnosaList || [], res.riwayatDiagnosa || []);
                    renderProsedurRajal(res.prosedurList || [], res.riwayatProsedur || []);

                    // 8. Render Tindakan Medis (Dokter & Paramedis, Ralan & Ranap)
                    renderTindakanRajal(res.tindakanList || [], res.riwayatTindakan || []);

                    // 9. Render Resume Medis Rajal & Riwayat Resume Ranap
                    currentRajalData = res;
                    renderResumeRajal(res.resumePasien, res);

                    // 10. Render Riwayat Komprehensif Lengkap
                    renderRiwayatLengkap(res);
                } else {
                    console.warn('Gagal memuat detail pasien:', res.message);
                }
            },
            error: function (xhr, status, err) {
                console.error('Gagal mengambil data detail pasien:', status, err, xhr.responseText);
                $('#riwayatSoapContainer, #riwayatAwalMedisContainer, #riwayatLabContainer, #riwayatRadContainer, #riwayatResepContainer, #riwayatOpsContainer, #riwayatLaporanOpsContainer, #selesaiOpsContainer, #riwayatLengkapTimelineContainer').html(
                    '<div class="alert alert-danger text-xs m-3"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan saat memuat data pasien dari server. Silakan coba klik refresh atau buka ulang pelayanan.</div>'
                );
            }
        });
    }

    // ── 1. Render Riwayat SOAP ──────────────────────────────────
    function renderRiwayatSoap(list) {
        const c = $('#riwayatSoapContainer');
        if (!list || list.length === 0) {
            c.html('<div class="empty-state py-4"><i class="bi bi-journal-x fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada riwayat catatan SOAP.</p></div>');
            return;
        }
        const currentNoRawat = $('#soapNoRawat').val();
        let html = '<div class="timeline-riwayat">';
        list.forEach(s => {
            const isCurrent = s.is_current || (s.no_rawat === currentNoRawat);
            const cardClass = isCurrent ? 'timeline-card current-visit' : 'timeline-card';
            const typeBadge = s.tipe === 'ranap'
                ? '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 me-1">Rawat Inap</span>'
                : '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 me-1">Rawat Jalan</span>';
            const currentBadge = isCurrent
                ? '<span class="badge bg-success text-white"><i class="bi bi-check2-circle me-1"></i>Kunjungan Ini</span>'
                : `<span class="badge bg-light text-slate-600 border">No. Rawat: ${escapeHtml(s.no_rawat)}</span>`;

            const canModifySoap = isDokterOrAdmin && isCurrent && (s.tipe !== 'ranap');
            const soapActionButtons = canModifySoap ? `
                <button type="button" class="btn btn-xs btn-outline-primary py-0.5 px-2 font-bold text-xs ms-1" onclick="editSoapRajal('${escapeHtml(s.tgl_perawatan)}', '${escapeHtml(s.jam_rawat)}')">
                    <i class="bi bi-pencil-square me-1"></i>Edit
                </button>
                <button type="button" class="btn btn-xs btn-outline-danger py-0.5 px-2 font-bold text-xs ms-1" onclick="hapusSoapRajal('${escapeHtml(s.no_rawat)}', '${escapeHtml(s.tgl_perawatan)}', '${escapeHtml(s.jam_rawat)}')">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            ` : '';

            html += `
            <div class="timeline-item">
                <div class="timeline-dot" style="${isCurrent ? 'background:#16a34a;' : ''}"></div>
                <div class="${cardClass}">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                        <div class="timeline-date mb-0">
                            <i class="bi bi-calendar3 me-1"></i>${fmtDate(s.tgl_perawatan)}
                            <i class="bi bi-clock ms-2 me-1"></i>${(s.jam_rawat || '').substring(0, 5)}
                            <span class="ms-2 fw-semibold text-slate-700">· ${escapeHtml(s.nm_dokter || s.kd_dokter || 'Dokter DPJP')}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            ${typeBadge}
                            ${currentBadge}
                            ${soapActionButtons}
                        </div>
                    </div>

                    ${(s.tensi || s.nadi || s.suhu_tubuh || s.respirasi || s.spo2 || s.gcs || s.berat || s.tinggi) ? `
                    <div class="d-flex flex-wrap gap-1 mb-2.5">
                        ${vital('bi-heart-pulse', 'TD', s.tensi, '#dc2626')}
                        ${vital('bi-activity', 'Nadi', s.nadi ? s.nadi + ' x/m' : '', '#0284c7')}
                        ${vital('bi-thermometer-half', 'Suhu', s.suhu_tubuh ? s.suhu_tubuh + ' °C' : '', '#d97706')}
                        ${vital('bi-lungs', 'RR', s.respirasi ? s.respirasi + ' x/m' : '', '#059669')}
                        ${vital('bi-droplet', 'SpO2', s.spo2 ? s.spo2 + ' %' : '', '#7c3aed')}
                        ${vital('bi-person-bounding-box', 'GCS', s.gcs, '#4f46e5')}
                        ${vital('bi-speedometer', 'BB', s.berat ? s.berat + ' kg' : '', '#64748b')}
                        ${vital('bi-arrows-expand', 'TB', s.tinggi ? s.tinggi + ' cm' : '', '#64748b')}
                    </div>` : ''}

                    <div class="timeline-soap-row">
                        ${soapField('S — Subjektif / Keluhan', s.keluhan)}
                        ${soapField('O — Objektif / Pemeriksaan', s.pemeriksaan)}
                        ${soapField('A — Asesmen / Diagnosis', s.penilaian)}
                        ${soapField('P — Plan / Rencana Terapi', s.rtl)}
                    </div>
                    ${s.instruksi ? `<div class="p-2 rounded mt-1 text-xs" style="background:#f8fafc; border:1px solid #e2e8f0;"><strong>Instruksi Dokter:</strong> ${escapeHtml(s.instruksi)}</div>` : ''}
                    ${s.evaluasi ? `<div class="p-2 rounded mt-1 text-xs" style="background:#f0fdf4; border:1px solid #bbf7d0;"><strong>Evaluasi Klinis:</strong> ${escapeHtml(s.evaluasi)}</div>` : ''}
                </div>
            </div>`;
        });
        html += '</div>';
        c.html(html);
    }

    // ── 2. Render Riwayat Awal Medis & Keperawatan ─────────────
    function renderRiwayatAwalMedis(list, keperawatanList) {
        const c = $('#riwayatAwalMedisContainer');
        const hasMedis = list && list.length > 0;
        const hasKep = keperawatanList && keperawatanList.length > 0;

        if (!hasMedis && !hasKep) {
            c.html('<div class="empty-state py-4"><i class="bi bi-clipboard2-x fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada riwayat penilaian awal medis maupun keperawatan.</p></div>');
            return;
        }

        const currentNoRawat = $('#soapNoRawat').val();
        let html = '<div class="timeline-riwayat">';

        // 1. Penilaian Medis (16 Spesialis/Departemen)
        if (hasMedis) {
            list.forEach(a => {
                const isCurrent = a.is_current || (a.no_rawat === currentNoRawat);
                const cardClass = isCurrent ? 'timeline-card current-visit' : 'timeline-card';
                const currentBadge = isCurrent
                    ? '<span class="badge bg-success text-white"><i class="bi bi-check2-circle me-1"></i>Kunjungan Ini</span>'
                    : `<span class="badge bg-light text-slate-600 border">No. Rawat: ${escapeHtml(a.no_rawat)}</span>`;

                html += `
                <div class="timeline-item">
                    <div class="timeline-dot" style="background:#0284c7;"></div>
                    <div class="${cardClass}">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 me-1 font-bold">
                                    <i class="bi bi-hospital me-1"></i>${escapeHtml(a.departemen || 'Poliklinik')}
                                </span>
                                <span class="text-slate-700 fw-bold text-xs"><i class="bi bi-calendar3 me-1"></i>${fmtDate(a.tanggal)}</span>
                                <span class="text-muted text-xs ms-1">· ${escapeHtml(a.nm_dokter || a.kd_dokter || '-')}</span>
                            </div>
                            <div>${currentBadge}</div>
                        </div>

                        ${(a.td || a.nadi || a.suhu || a.rr || a.spo || a.gcs) ? `
                        <div class="d-flex flex-wrap gap-1 mb-2.5">
                            ${vital('bi-heart-pulse', 'TD', a.td, '#dc2626')}
                            ${vital('bi-activity', 'Nadi', a.nadi ? a.nadi + ' x/m' : '', '#0284c7')}
                            ${vital('bi-thermometer-half', 'Suhu', a.suhu ? a.suhu + ' °C' : '', '#d97706')}
                            ${vital('bi-lungs', 'RR', a.rr ? a.rr + ' x/m' : '', '#059669')}
                            ${vital('bi-droplet', 'SpO2', a.spo ? a.spo + ' %' : '', '#7c3aed')}
                            ${vital('bi-person-bounding-box', 'GCS', a.gcs, '#4f46e5')}
                        </div>` : ''}

                        <div class="row g-2 text-xs mb-2">
                            <div class="col-md-6">
                                <div class="p-2 rounded bg-light border h-100">
                                    <div class="fw-bold text-slate-700 mb-1">ANAMNESIS MEDIS:</div>
                                    <div><strong>Keluhan Utama:</strong> ${escapeHtml(a.keluhan_utama || '-')}</div>
                                    ${a.rps ? `<div class="mt-1"><strong>RPS:</strong> ${escapeHtml(a.rps)}</div>` : ''}
                                    ${a.rpd ? `<div class="mt-1"><strong>RPD:</strong> ${escapeHtml(a.rpd)}</div>` : ''}
                                    ${a.alergi ? `<div class="mt-1 text-danger"><strong>Alergi:</strong> ${escapeHtml(a.alergi)}</div>` : ''}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 rounded bg-light border h-100">
                                    <div class="fw-bold text-primary mb-1">DIAGNOSIS &amp; TERAPI MEDIS:</div>
                                    <div><strong>Diagnosis:</strong> <span class="fw-bold text-slate-800">${escapeHtml(a.diagnosis || '-')}</span></div>
                                    <div class="mt-1"><strong>Tata Laksana:</strong> ${escapeHtml(a.tata || '-')}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
        }

        // 2. Penilaian Keperawatan (Ralan, IGD, Ranap, Kebidanan)
        if (hasKep) {
            keperawatanList.forEach(k => {
                const isCurrent = (k.no_rawat === currentNoRawat);
                const cardClass = isCurrent ? 'timeline-card current-visit' : 'timeline-card';
                const currentBadge = isCurrent
                    ? '<span class="badge bg-success text-white"><i class="bi bi-check2-circle me-1"></i>Kunjungan Ini</span>'
                    : `<span class="badge bg-light text-slate-600 border">No. Rawat: ${escapeHtml(k.no_rawat)}</span>`;

                html += `
                <div class="timeline-item">
                    <div class="timeline-dot" style="background:#0d9488;"></div>
                    <div class="${cardClass}">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                            <div>
                                <span class="badge px-2 py-1 me-1 font-bold" style="background:#ccfbf1; color:#0f766e; border:1px solid #99f6e4;">
                                    <i class="bi bi-person-heart me-1"></i>${escapeHtml(k.departemen || 'Asesmen Keperawatan')}
                                </span>
                                <span class="text-slate-700 fw-bold text-xs"><i class="bi bi-calendar3 me-1"></i>${fmtDate(k.tanggal)}</span>
                                <span class="text-muted text-xs ms-1">· ${escapeHtml(k.nama_petugas || k.nip || 'Petugas')}</span>
                            </div>
                            <div>${currentBadge}</div>
                        </div>

                        ${(k.td || k.nadi || k.suhu || k.rr || k.spo || k.gcs) ? `
                        <div class="d-flex flex-wrap gap-1 mb-2.5">
                            ${vital('bi-heart-pulse', 'TD', k.td, '#dc2626')}
                            ${vital('bi-activity', 'Nadi', k.nadi ? k.nadi + ' x/m' : '', '#0284c7')}
                            ${vital('bi-thermometer-half', 'Suhu', k.suhu ? k.suhu + ' °C' : '', '#d97706')}
                            ${vital('bi-lungs', 'RR', k.rr ? k.rr + ' x/m' : '', '#059669')}
                            ${vital('bi-droplet', 'SpO2', k.spo ? k.spo + ' %' : '', '#7c3aed')}
                            ${vital('bi-person-bounding-box', 'GCS', k.gcs, '#4f46e5')}
                        </div>` : ''}

                        <div class="row g-2 text-xs mb-2">
                            <div class="col-md-6">
                                <div class="p-2 rounded bg-light border h-100">
                                    <div class="fw-bold text-teal-800 mb-1" style="color:#0f766e;">PENGKAJIAN KEPERAWATAN:</div>
                                    <div><strong>Keluhan Utama:</strong> ${escapeHtml(k.keluhan_utama || '-')}</div>
                                    ${k.rps ? `<div class="mt-1"><strong>RPS:</strong> ${escapeHtml(k.rps)}</div>` : ''}
                                    ${k.rpd ? `<div class="mt-1"><strong>RPD:</strong> ${escapeHtml(k.rpd)}</div>` : ''}
                                    ${k.alergi ? `<div class="mt-1 text-danger"><strong>Alergi:</strong> ${escapeHtml(k.alergi)}</div>` : ''}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 rounded bg-light border h-100">
                                    <div class="fw-bold text-teal-800 mb-1" style="color:#0f766e;">MASALAH &amp; RENCANA KEPERAWATAN:</div>
                                    <div><strong>Masalah:</strong> <span class="fw-bold text-slate-800">${escapeHtml(k.masalah || '-')}</span></div>
                                    <div class="mt-1"><strong>Rencana:</strong> ${escapeHtml(k.rencana || '-')}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
        }

        html += '</div>';
        c.html(html);
    }

    // ── 3. Render Riwayat & Hasil Lab ───────────────────────────
    function renderRiwayatLab(orders, results) {
        // Orders
        const oc = $('#riwayatLabContainer');
        if (!orders || orders.length === 0) {
            oc.html('<div class="empty-state py-3"><i class="bi bi-eyedropper fs-4 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada riwayat permintaan lab.</p></div>');
        } else {
            let htmlOrder = '';
            orders.forEach(o => {
                htmlOrder += `
                <div class="card p-2.5 mb-2 border text-xs" style="border-radius:10px; background:#f0fdf4; border-color:#bbf7d0 !important;">
                    <div class="d-flex justify-content-between align-items-center font-bold mb-1 flex-wrap gap-1">
                        <span class="text-success fs-7"><i class="bi bi-eyedropper me-1"></i>Order: <code>${o.noorder}</code> (${fmtDate(o.tgl_permintaan)} ${(o.jam_permintaan||'').substring(0,5)})</span>
                        <span class="badge bg-light text-slate-600 border">No. Rawat: ${o.no_rawat}</span>
                    </div>
                    <div><strong>Pemeriksaan:</strong> <span class="text-slate-800 font-semibold">${escapeHtml(o.detail_pemeriksaan || '-')}</span></div>
                    ${o.diagnosa_klinis ? `<div class="text-slate-600 mt-0.5"><strong>Diagnosa Klinis:</strong> ${escapeHtml(o.diagnosa_klinis)}</div>` : ''}
                </div>`;
            });
            oc.html(htmlOrder);
        }

        // Results (Grouped by date/time)
        const rc = $('#hasilLabContainer');
        if (!results || results.length === 0) {
            rc.html('<div class="empty-state py-4"><i class="bi bi-graph-up fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada hasil pemeriksaan laboratorium.</p></div>');
        } else {
            const grouped = {};
            results.forEach(r => {
                const key = `${r.tgl_periksa} ${(r.jam||'').substring(0,5)}`;
                if (!grouped[key]) grouped[key] = [];
                grouped[key].push(r);
            });

            let htmlRes = '';
            Object.entries(grouped).forEach(([dt, rows]) => {
                htmlRes += `
                <div class="card p-3 mb-3 border text-xs shadow-xs" style="border-radius:12px; background:#fff;">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1.5 border-bottom">
                        <span class="fw-bold text-primary fs-7"><i class="bi bi-calendar3 me-1"></i>Hasil Pemeriksaan Lab: ${dt}</span>
                        <span class="badge bg-light text-slate-600 border">${rows.length} Parameter</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0 text-xs">
                            <thead class="table-light">
                                <tr>
                                    <th>Pemeriksaan / Parameter</th>
                                    <th style="width:110px; text-align:center;">Hasil</th>
                                    <th style="width:140px;">Nilai Rujukan</th>
                                    <th style="width:120px;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>`;
                rows.forEach(r => {
                    const isAbnormal = (r.keterangan && r.keterangan.trim() !== '') || 
                                       (r.nilai && r.nilai_rujukan && r.nilai !== r.nilai_rujukan && (r.keterangan||'').toLowerCase().includes('abnormal'));
                    const valClass = isAbnormal ? 'text-danger fw-bold' : 'text-slate-800 fw-semibold';
                    htmlRes += `
                    <tr class="${isAbnormal ? 'table-danger table-opacity-10' : ''}">
                        <td><strong>${escapeHtml(r.nm_perawatan || '-')}</strong> — ${escapeHtml(r.nama_pemeriksaan)}</td>
                        <td class="text-center ${valClass}">${escapeHtml(r.nilai)}</td>
                        <td class="text-muted">${escapeHtml(r.nilai_rujukan || '-')} ${escapeHtml(r.satuan || '')}</td>
                        <td>${isAbnormal ? `<span class="badge bg-danger">${escapeHtml(r.keterangan)}</span>` : (r.keterangan ? escapeHtml(r.keterangan) : '-')}</td>
                    </tr>`;
                });
                htmlRes += `</tbody></table></div></div>`;
            });
            rc.html(htmlRes);
        }
    }

    // ── 4. Render Riwayat & Hasil Radiologi ──────────────────────
    function renderRiwayatRad(orders, results) {
        // Orders
        const oc = $('#riwayatRadContainer');
        if (!orders || orders.length === 0) {
            oc.html('<div class="empty-state py-3"><i class="bi bi-intersect fs-4 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada riwayat permintaan radiologi.</p></div>');
        } else {
            let htmlOrder = '';
            orders.forEach(o => {
                htmlOrder += `
                <div class="card p-2.5 mb-2 border text-xs" style="border-radius:10px; background:#faf5ff; border-color:#e9d5ff !important;">
                    <div class="d-flex justify-content-between align-items-center font-bold mb-1 flex-wrap gap-1">
                        <span class="text-purple-700 fs-7"><i class="bi bi-intersect me-1"></i>Order: <code>${o.noorder}</code> (${fmtDate(o.tgl_permintaan)} ${(o.jam_permintaan||'').substring(0,5)})</span>
                        <span class="badge bg-light text-slate-600 border">No. Rawat: ${o.no_rawat}</span>
                    </div>
                    <div><strong>Pemeriksaan:</strong> <span class="text-slate-800 font-semibold">${escapeHtml(o.detail_pemeriksaan || '-')}</span></div>
                    ${o.diagnosa_klinis ? `<div class="text-slate-600 mt-0.5"><strong>Diagnosa Klinis:</strong> ${escapeHtml(o.diagnosa_klinis)}</div>` : ''}
                </div>`;
            });
            oc.html(htmlOrder);
        }

        // Results
        const rc = $('#hasilRadContainer');
        if (!results || results.length === 0) {
            rc.html('<div class="empty-state py-4"><i class="bi bi-file-earmark-medical fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada hasil ekspertisi radiologi.</p></div>');
        } else {
            let htmlRes = '';
            results.forEach(rr => {
                htmlRes += `
                <div class="card p-3 mb-2.5 border text-xs shadow-xs" style="border-radius:12px; background:#fff;">
                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                        <span class="text-purple-700 fs-7"><i class="bi bi-file-earmark-medical-fill me-1"></i>Hasil Ekspertisi Radiologi (${fmtDate(rr.tgl_periksa)} ${(rr.jam||'').substring(0,5)})</span>
                        <span class="badge bg-light text-slate-600 border">No. Rawat: ${rr.no_rawat}</span>
                    </div>
                    <div class="text-slate-800 p-2.5 bg-light rounded-2 mt-1" style="white-space:pre-wrap; line-height:1.6; font-family:inherit;">${escapeHtml(rr.hasil || '-')}</div>
                </div>`;
            });
            rc.html(htmlRes);
        }
    }

    // ── 5. Render History Resep Dokter ──────────────────────────
    function renderRiwayatResep(list) {
        const c = $('#riwayatResepContainer');
        if (!list || list.length === 0) {
            c.html('<div class="empty-state py-4"><i class="bi bi-capsule fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada riwayat resep obat pasien.</p></div>');
            return;
        }
        let html = '';
        list.forEach(r => {
            const jamStr = r.jam || r.jam_peresepan || '00:00:00';
            const drStr  = r.nm_dokter || 'Dokter DPJP';
            const canModifyResep = isDokterOrAdmin && r.can_delete;
            const statusBadge = canModifyResep
                ? `<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Menunggu Validasi Farmasi</span>
                   <button type="button" class="btn btn-xs btn-outline-primary py-0.5 px-2 ms-2 font-bold text-xs" onclick="editResepRajal('${r.no_resep}')">
                       <i class="bi bi-pencil-square me-1"></i>Edit Resep
                   </button>
                   <button type="button" class="btn btn-xs btn-outline-danger py-0.5 px-2 ms-1 font-bold text-xs" onclick="batalResepRajal('${r.no_resep}')">
                       <i class="bi bi-trash me-1"></i>Hapus Resep
                   </button>`
                : (r.can_delete
                    ? `<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Menunggu Validasi Farmasi</span>`
                    : `<span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Diserahkan Farmasi (${fmtDate(r.tgl_penyerahan)} ${(r.jam_penyerahan || '').substring(0,5)})</span>`
                );

            let obatBody = '';
            const nonRacikList = r.obat_list || r.obat_non_racik || [];
            if (nonRacikList.length > 0) {
                obatBody += `<div class="mb-2">
                    <div class="fw-bold text-success fs-8 mb-1"><i class="bi bi-capsule me-1"></i>Obat Jadi / Non-Racikan:</div>
                    <table class="table table-sm table-bordered mb-1 text-xs" style="background:#fafafa;">
                        <thead><tr class="table-light"><th>Nama Obat</th><th style="width:80px;text-align:center;">Jumlah</th><th>Aturan Pakai</th><th>Keterangan</th></tr></thead>
                        <tbody>`;
                nonRacikList.forEach(o => {
                    obatBody += `<tr>
                        <td><strong>${escapeHtml(o.nama_brng || o.kode_brng)}</strong></td>
                        <td class="text-center font-bold">${o.jml} ${escapeHtml(o.kode_sat || '')}</td>
                        <td><code>${escapeHtml(o.aturan_pakai || '-')}</code></td>
                        <td>${escapeHtml(o.keterangan || '-')}</td>
                    </tr>`;
                });
                obatBody += `</tbody></table></div>`;
            }

            const racikList = r.racik_list || r.obat_racikan || [];
            if (racikList.length > 0) {
                obatBody += `<div class="mb-2">
                    <div class="fw-bold text-primary fs-8 mb-1"><i class="bi bi-mortarboard-fill me-1"></i>Obat Racikan Dokter:</div>`;
                racikList.forEach(rc => {
                    obatBody += `
                    <div class="border rounded-2 p-2 mb-1.5" style="background:#f0f9ff; border-color:#bae6fd !important;">
                        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                            <span class="fw-bold text-primary fs-8"><i class="bi bi-box-seam me-1"></i>${escapeHtml(rc.nama_racik)} (${escapeHtml(rc.metode || 'Racikan')}) — Kemasan: <b>${rc.jml_dr}</b></span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">${escapeHtml(rc.aturan_pakai || '-')}</span>
                        </div>
                        ${rc.keterangan ? `<div class="text-xs text-muted mb-1"><em>Ket: ${escapeHtml(rc.keterangan)}</em></div>` : ''}`;

                    const detailList = rc.detail || rc.detail_racik || [];
                    if (detailList.length > 0) {
                        obatBody += `<table class="table table-sm table-bordered mb-0 text-xs bg-white">
                            <thead><tr class="table-light"><th>Bahan Obat</th><th style="width:80px;text-align:center;">P1 / P2</th><th style="width:100px;">Kandungan</th><th style="width:80px;text-align:center;">Jml Butuh</th></tr></thead><tbody>`;
                        detailList.forEach(d => {
                            obatBody += `<tr>
                                <td>${escapeHtml(d.nama_brng || d.kode_brng)}</td>
                                <td class="text-center">${d.p1 || 1} / ${d.p2 || 1}</td>
                                <td>${escapeHtml(d.kandungan || '-')}</td>
                                <td class="text-center font-bold">${d.jml}</td>
                            </tr>`;
                        });
                        obatBody += `</tbody></table>`;
                    }
                    obatBody += `</div>`;
                });
                obatBody += `</div>`;
            }

            if (!nonRacikList.length && !racikList.length) {
                obatBody = `<div class="text-slate-800 mt-1 ps-2" style="border-left:3px solid #7c3aed; line-height:1.6;">${escapeHtml(r.detail_obat || '-')}</div>`;
            }

            html += `
            <div class="card p-3 mb-2.5 border text-xs shadow-xs" style="border-radius:12px; background:#fff;">
                <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                    <span class="text-purple-700 fs-7"><i class="bi bi-capsule me-1"></i>No. Resep: <code>${r.no_resep}</code> (${fmtDate(r.tgl_perawatan || r.tgl_peresepan)} ${jamStr.substring(0,5)})</span>
                    <div class="d-flex align-items-center">${statusBadge}</div>
                </div>
                <div class="text-slate-500 mb-2">${drStr} | No. Rawat: <code>${r.no_rawat}</code></div>
                ${obatBody}
            </div>`;
        });
        c.html(html);
    }

    // ── 6. Render Riwayat Operasi & Booking ──────────────────────
    function renderRiwayatOps(bookingList, laporanList, tagihanOperasi) {
        // Update sub-tab counters
        const bCount = (bookingList || []).length;
        const lCount = (laporanList || []).length;
        const sCount = (tagihanOperasi || []).length;
        $('#countOpsBookingText').text(bCount);
        $('#countOpsLaporanText').text(lCount);
        $('#countOpsSelesaiText').text(sCount);

        // 1. Laporan Operasi
        const lc = $('#laporanOpsContainer');
        if (!laporanList || laporanList.length === 0) {
            lc.html('<div class="empty-state py-4"><i class="bi bi-file-earmark-text fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada laporan operasi untuk pasien ini.</p></div>');
        } else {
            let htmlLap = '';
            laporanList.forEach(lo => {
                const isCur = lo.is_current ? '<span class="badge bg-success ms-1">Kunjungan Ini</span>' : `<span class="badge bg-light text-slate-600 border ms-1">${escapeHtml(lo.no_rawat)}</span>`;
                const paBadge = lo.permintaan_pa === 'Ya' 
                    ? `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5"><i class="bi bi-check-circle-fill me-1"></i>Dikirim PA</span>`
                    : `<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-0.5">Tanpa PA</span>`;
                
                const katBadge = lo.kategori && lo.kategori !== '-'
                    ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5">Kategori: ${escapeHtml(lo.kategori)}</span>`
                    : '';

                const printBtn = `<button type="button" class="btn btn-xs btn-outline-secondary" onclick="cetakLaporanOpsRajal('${escapeHtml(lo.no_rawat)}', '${escapeHtml(lo.tanggal)}')"><i class="bi bi-printer me-1"></i>Cetak</button>`;

                const actionBtns = `
                    <div class="btn-group btn-group-sm">
                        ${printBtn}
                        ${isDokterOrAdmin ? `
                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="editLaporanOpsRajal('${escapeHtml(lo.no_rawat)}', '${escapeHtml(lo.tanggal)}', '${escapeHtml(lo.tgl_operasi||'')}', '${escapeHtml(lo.selesaioperasi||'')}', '${escapeHtml(lo.diagnosa_preop||'')}', '${escapeHtml(lo.diagnosa_postop||'')}', '${escapeHtml(lo.jaringan_dieksekusi||'')}', '${escapeHtml(lo.permintaan_pa||'Tidak')}', '${escapeHtml(lo.jenis_anasthesi||'')}', '${escapeHtml(lo.kategori||'-')}', ${JSON.stringify(lo.laporan_operasi||'').replace(/"/g, '&quot;')})">
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="hapusLaporanOpsRajal('${escapeHtml(lo.no_rawat)}', '${escapeHtml(lo.tanggal)}')">
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

        // 2. Booking Operasi
        const bc = $('#riwayatOpsContainer');
        if (!bookingList || bookingList.length === 0) {
            bc.html('<div class="empty-state py-4"><i class="bi bi-calendar-event fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada booking jadwal operasi.</p></div>');
        } else {
            let htmlBook = '';
            bookingList.forEach(b => {
                const sttsCls = b.status === 'Selesai' ? 'bg-success' : (b.status === 'Proses Operasi' ? 'bg-info text-white' : 'bg-warning text-dark');
                const kelasBadge = b.kelas ? `<span class="badge bg-primary bg-opacity-10 text-primary ms-1">Kelas ${escapeHtml(b.kelas)}</span>` : '';
                const pjBadge = b.png_jawab ? `<span class="badge bg-info bg-opacity-10 text-info ms-1">${escapeHtml(b.png_jawab)}</span>` : '';
                const ruangBadge = b.nm_ruang_ok ? `<span class="badge bg-purple-700 bg-opacity-10 text-purple-700 border border-purple-200 ms-1"><i class="bi bi-door-closed me-1"></i>${escapeHtml(b.nm_ruang_ok)}</span>` : '';

                const printBtn = `<button type="button" class="btn btn-xs btn-outline-secondary" onclick="cetakBookingOpsRajal('${escapeHtml(b.no_rawat)}', '${escapeHtml(b.kode_paket)}', '${escapeHtml(b.tanggal)}')"><i class="bi bi-printer me-1"></i>Cetak</button>`;

                const actionBtns = `
                    <div class="btn-group btn-group-sm">
                        ${printBtn}
                        ${isDokterOrAdmin ? `
                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="editBookingOpsRajal('${escapeHtml(b.no_rawat)}', '${escapeHtml(b.kode_paket)}', '${escapeHtml(b.tanggal)}', '${escapeHtml(b.jam_mulai||'')}', '${escapeHtml(b.jam_selesai||'')}', '${escapeHtml(b.status||'Menunggu')}', '${escapeHtml(b.kd_dokter||'')}', '${escapeHtml(b.kd_ruang_ok||'')}')">
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="hapusBookingOpsRajal('${escapeHtml(b.no_rawat)}', '${escapeHtml(b.kode_paket)}', '${escapeHtml(b.tanggal)}', '${escapeHtml(b.jam_mulai||'')}')">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                        ` : ''}
                    </div>
                `;

                htmlBook += `
                <div class="card p-3 mb-2.5 border text-xs shadow-xs" style="border-radius:12px; background:#fff;">
                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <span class="text-danger fs-7"><i class="bi bi-calendar-event me-1"></i>Tgl Operasi: ${fmtDate(b.tanggal)} (${(b.jam_mulai||'').substring(0,5)} - ${(b.jam_selesai||'').substring(0,5)})</span>
                            ${ruangBadge}
                        </div>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="badge ${sttsCls} px-2.5 py-1">${escapeHtml(b.status || 'Menunggu')}</span>
                            ${actionBtns}
                        </div>
                    </div>
                    <div><strong>Paket Operasi:</strong> <span class="text-slate-900 font-bold">${escapeHtml(b.nama_paket || b.kode_paket)}</span> <code>(${escapeHtml(b.kode_paket)})</code> ${kelasBadge} ${pjBadge}</div>
                    ${b.dokter_operator ? `<div class="mt-1 text-slate-600"><strong>Dokter Operator:</strong> ${escapeHtml(b.dokter_operator)}</div>` : ''}
                    <div class="text-slate-500 mt-1">No. Rawat: <code>${escapeHtml(b.no_rawat)}</code></div>
                </div>`;
            });
            bc.html(htmlBook);
        }

        // 3. Tindakan Operasi Selesai (Tabel operasi SIMRS)
        const sc = $('#selesaiOpsContainer');
        if (!tagihanOperasi || tagihanOperasi.length === 0) {
            sc.html('<div class="empty-state py-4"><i class="bi bi-shield-check fs-3 d-block mb-1 opacity-50"></i><p class="text-xs text-muted">Belum ada riwayat tindakan operasi selesai untuk pasien ini.</p></div>');
        } else {
            let htmlSelesai = '';
            tagihanOperasi.forEach(to => {
                const tot = Number(to.total_tagihan || 0);
                const op1 = Number(to.biayaoperator1 || 0);
                const anes = Number(to.biayadokteranestesi || 0);
                const sttsBadge = to.status === 'Ranap' 
                    ? '<span class="badge bg-purple-700 bg-opacity-10 text-purple-700 border border-purple-200">Rawat Inap</span>'
                    : '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Rawat Jalan</span>';
                
                htmlSelesai += `
                <div class="card p-3 mb-3 border text-xs shadow-xs" style="border-radius:12px; background:#fff;">
                    <div class="d-flex justify-content-between align-items-center font-bold pb-2 mb-2 border-bottom flex-wrap gap-1">
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <span class="text-success fs-7"><i class="bi bi-shield-check me-1"></i>Tindakan Operasi Selesai: ${fmtDate(to.tgl_operasi)} ${(to.tgl_operasi||'').substring(11,16)} WIB</span>
                            ${sttsBadge}
                        </div>
                        <div>
                            <span class="badge bg-success px-2.5 py-1">Total: Rp ${tot.toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                    <div class="row g-2 mb-2 text-slate-700">
                        <div class="col-md-6"><strong>Paket Operasi:</strong> <span class="fw-bold text-slate-900">${escapeHtml(to.nama_paket || to.kode_paket)}</span> <code>(${escapeHtml(to.kode_paket)})</code></div>
                        <div class="col-md-6"><strong>Operator:</strong> <span class="fw-bold text-slate-800">${escapeHtml(to.operator || '-')}</span></div>
                        <div class="col-md-6"><strong>Dokter Anestesi:</strong> <span>${escapeHtml(to.dokter_anestesi || '-')}</span></div>
                        <div class="col-md-6"><strong>No. Rawat:</strong> <code>${escapeHtml(to.no_rawat)}</code></div>
                    </div>
                    <div class="d-flex flex-wrap gap-3 p-2 bg-light rounded border text-xs font-mono">
                        <div>Operator 1: <strong>Rp ${op1.toLocaleString('id-ID')}</strong></div>
                        <div>Anestesi: <strong>Rp ${anes.toLocaleString('id-ID')}</strong></div>
                        <div class="text-success font-bold">Total Tagihan: <strong>Rp ${tot.toLocaleString('id-ID')}</strong></div>
                    </div>
                </div>`;
            });
            sc.html(htmlSelesai);
        }
    }

    // ── 10. Render Riwayat Lengkap Komprehensif (Tab 10) ─────────
    function renderRiwayatLengkap(data) {
        const c = $('#riwayatLengkapTimelineContainer');
        if (!c.length) return;

        let items = [];

        // 1. Asesmen Awal Medis
        (data.awalMedisList || []).forEach(a => {
            items.push({
                type: 'awalmedis',
                subType: 'medis',
                date: a.tanggal,
                time: '00:00',
                sortKey: `${a.tanggal} 00:00:00`,
                data: a
            });
        });

        // 2. Asesmen Keperawatan
        (data.keperawatanList || []).forEach(k => {
            items.push({
                type: 'awalmedis',
                subType: 'keperawatan',
                date: k.tanggal,
                time: '00:00',
                sortKey: `${k.tanggal} 00:00:00`,
                data: k
            });
        });

        // 3. SOAP
        (data.soapList || []).forEach(s => {
            const jam = (s.jam_rawat || '00:00:00').substring(0, 8);
            items.push({
                type: 'soap',
                date: s.tgl_perawatan,
                time: jam.substring(0, 5),
                sortKey: `${s.tgl_perawatan} ${jam}`,
                data: s
            });
        });

        // 4. Resep
        (data.resepList || []).forEach(r => {
            const tgl = r.tgl_peresepan || r.tgl_perawatan || '1970-01-01';
            const jam = (r.jam_peresepan || r.jam || '00:00:00').substring(0, 8);
            items.push({
                type: 'resep',
                date: tgl,
                time: jam.substring(0, 5),
                sortKey: `${tgl} ${jam}`,
                data: r
            });
        });

        // 5. Lab Results (grouped by date)
        const groupedLab = {};
        (data.labResults || []).forEach(lr => {
            const key = `${lr.tgl_periksa} ${(lr.jam || '00:00').substring(0, 5)}`;
            if (!groupedLab[key]) groupedLab[key] = [];
            groupedLab[key].push(lr);
        });
        Object.entries(groupedLab).forEach(([dt, rows]) => {
            const parts = dt.split(' ');
            const tgl = parts[0];
            const jam = parts[1] || '00:00';
            items.push({
                type: 'lab',
                date: tgl,
                time: jam,
                sortKey: `${tgl} ${jam}:00`,
                data: rows
            });
        });

        // 6. Radiologi Results
        (data.radResults || []).forEach(rr => {
            const jam = (rr.jam || '00:00:00').substring(0, 8);
            items.push({
                type: 'rad',
                date: rr.tgl_periksa,
                time: jam.substring(0, 5),
                sortKey: `${rr.tgl_periksa} ${jam}`,
                data: rr
            });
        });

        // 7. Laporan Operasi
        (data.laporanOps || []).forEach(lo => {
            items.push({
                type: 'operasi',
                subType: 'laporan',
                date: lo.tanggal,
                time: '00:00',
                sortKey: `${lo.tanggal} 00:00:00`,
                data: lo
            });
        });

        // 8. Booking Operasi
        (data.bookingOps || []).forEach(b => {
            items.push({
                type: 'operasi',
                subType: 'booking',
                date: b.tanggal,
                time: (b.jam_mulai || '00:00').substring(0, 5),
                sortKey: `${b.tanggal} ${b.jam_mulai || '00:00:00'}`,
                data: b
            });
        });

        // 9. Tindakan Operasi Selesai (Tabel operasi)
        (data.tagihanOperasi || []).forEach(to => {
            items.push({
                type: 'operasi',
                subType: 'selesai',
                date: (to.tgl_operasi || '').substring(0, 10),
                time: (to.tgl_operasi || '').substring(11, 16) || '00:00',
                sortKey: `${to.tgl_operasi || ''}`,
                data: to
            });
        });

        // 10. Diagnosa ICD-10 (Aktif & Riwayat)
        const allDiag = [...(data.diagnosaList || []), ...(data.riwayatDiagnosa || [])];
        allDiag.forEach(d => {
            items.push({
                type: 'diagnosa',
                subType: 'icd10',
                date: d.tgl_registrasi || d.tgl_perawatan || (data.pasien ? data.pasien.tgl_registrasi : '1970-01-01'),
                time: '00:00',
                sortKey: `${d.tgl_registrasi || d.tgl_perawatan || '1970-01-01'} 00:00:00`,
                data: d
            });
        });

        // 11. Prosedur ICD-9 (Aktif & Riwayat)
        const allPros = [...(data.prosedurList || []), ...(data.riwayatProsedur || [])];
        allPros.forEach(p => {
            items.push({
                type: 'diagnosa',
                subType: 'icd9',
                date: p.tgl_registrasi || p.tgl_perawatan || (data.pasien ? data.pasien.tgl_registrasi : '1970-01-01'),
                time: '00:00',
                sortKey: `${p.tgl_registrasi || p.tgl_perawatan || '1970-01-01'} 00:00:00`,
                data: p
            });
        });

        // 12. Tindakan Medis (Aktif & Riwayat Ralan/Ranap)
        const allTindakan = [...(data.tindakanList || []), ...(data.riwayatTindakan || [])];
        allTindakan.forEach(t => {
            const jam = (t.jam_rawat || '00:00:00').substring(0, 8);
            items.push({
                type: 'tindakan',
                date: t.tgl_perawatan || '1970-01-01',
                time: jam.substring(0, 5),
                sortKey: `${t.tgl_perawatan || '1970-01-01'} ${jam}`,
                data: t
            });
        });

        // 13. Resume Pasien Ralan & Ranap
        if (data.resumePasien && data.resumePasien.no_rawat) {
            items.push({
                type: 'resume',
                subType: 'ralan',
                date: data.pasien ? data.pasien.tgl_registrasi : '1970-01-01',
                time: '00:00',
                sortKey: `${data.pasien ? data.pasien.tgl_registrasi : '1970-01-01'} 00:00:00`,
                data: data.resumePasien
            });
        }
        if (data.resumeRanap && data.resumeRanap.no_rawat) {
            items.push({
                type: 'resume',
                subType: 'ranap',
                date: data.resumeRanap.tgl_keluar || (data.pasien ? data.pasien.tgl_registrasi : '1970-01-01'),
                time: '00:00',
                sortKey: `${data.resumeRanap.tgl_keluar || '1970-01-01'} 00:00:00`,
                data: data.resumeRanap
            });
        }

        if (items.length === 0) {
            c.html('<div class="empty-state py-5"><i class="bi bi-clock-history fs-2 d-block mb-2 opacity-50"></i><p class="text-slate-600">Belum ada riwayat rekam medis untuk pasien ini.</p></div>');
            return;
        }

        // Sort descending by date & time
        items.sort((a, b) => b.sortKey.localeCompare(a.sortKey));

        let html = '<div class="timeline-riwayat">';
        items.forEach(it => {
            html += `<div class="timeline-item timeline-filter-item" data-type="${it.type}">`;
            
            if (it.type === 'soap') {
                const s = it.data;
                const isCurrent = s.is_current;
                html += `
                <div class="timeline-dot" style="${isCurrent ? 'background:#16a34a;' : ''}"></div>
                <div class="timeline-card ${isCurrent ? 'current-visit' : ''}">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                        <div class="timeline-date mb-0">
                            <i class="bi bi-journal-medical me-1"></i>Catatan SOAP (${fmtDate(s.tgl_perawatan)} ${(s.jam_rawat||'').substring(0,5)})
                            <span class="text-slate-700 ms-2 fw-semibold">· ${escapeHtml(s.nm_dokter || s.kd_dokter || 'Dokter')}</span>
                        </div>
                        <div>
                            ${s.tipe === 'ranap' ? '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 me-1">Ranap</span>' : '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 me-1">Ralan</span>'}
                            ${isCurrent ? '<span class="badge bg-success">Kunjungan Ini</span>' : `<span class="badge bg-light text-slate-600 border">${s.no_rawat}</span>`}
                        </div>
                    </div>
                    ${(s.tensi || s.nadi || s.suhu_tubuh || s.respirasi || s.spo2) ? `
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        ${vital('bi-heart-pulse', 'TD', s.tensi, '#dc2626')}
                        ${vital('bi-activity', 'Nadi', s.nadi, '#0284c7')}
                        ${vital('bi-thermometer-half', 'S', s.suhu_tubuh, '#d97706')}
                        ${vital('bi-lungs', 'RR', s.respirasi, '#059669')}
                        ${vital('bi-droplet', 'SpO2', s.spo2, '#7c3aed')}
                    </div>` : ''}
                    <div class="timeline-soap-row">
                        ${soapField('S', s.keluhan)}
                        ${soapField('O', s.pemeriksaan)}
                        ${soapField('A', s.penilaian)}
                        ${soapField('P', s.rtl)}
                    </div>
                </div>`;
            } else if (it.type === 'awalmedis') {
                const a = it.data;
                const isKep = it.subType === 'keperawatan';
                const dotColor = isKep ? '#0d9488' : '#0284c7';
                const iconClass = isKep ? 'bi-person-heart' : 'bi-clipboard2-user-fill';
                const titleText = isKep ? `Asesmen Keperawatan: ${escapeHtml(a.departemen || 'Keperawatan')}` : `Asesmen Awal: ${escapeHtml(a.departemen || 'Medis')}`;
                const officerName = isKep ? (a.nama_petugas || a.nip || 'Petugas') : (a.nm_dokter || a.kd_dokter || 'Dokter');

                html += `
                <div class="timeline-dot" style="background:${dotColor};"></div>
                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                        <div class="timeline-date mb-0" style="color:${dotColor};">
                            <i class="bi ${iconClass} me-1"></i>${titleText} (${fmtDate(a.tanggal)})
                            <span class="text-slate-700 ms-2 fw-semibold">· ${escapeHtml(officerName)}</span>
                        </div>
                        <span class="badge bg-light text-slate-600 border">${a.no_rawat}</span>
                    </div>
                    <div class="text-xs">
                        <div><strong>Keluhan Utama:</strong> ${escapeHtml(a.keluhan_utama || '-')}</div>
                        ${isKep ? `
                            ${a.masalah ? `<div class="mt-1"><strong>Masalah Keperawatan:</strong> <span class="fw-bold text-slate-800">${escapeHtml(a.masalah)}</span></div>` : ''}
                            ${a.rencana ? `<div class="mt-1"><strong>Rencana Keperawatan:</strong> ${escapeHtml(a.rencana)}</div>` : ''}
                        ` : `
                            ${a.diagnosis ? `<div class="mt-1"><strong>Diagnosis:</strong> <span class="fw-bold text-slate-800">${escapeHtml(a.diagnosis)}</span></div>` : ''}
                            ${a.tata ? `<div class="mt-1"><strong>Tata Laksana:</strong> ${escapeHtml(a.tata)}</div>` : ''}
                        `}
                    </div>
                </div>`;
            } else if (it.type === 'resep') {
                const r = it.data;
                html += `
                <div class="timeline-dot" style="background:#7c3aed;"></div>
                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                        <div class="timeline-date mb-0" style="color:#7c3aed;">
                            <i class="bi bi-capsule me-1"></i>Resep: <code>${r.no_resep}</code> (${fmtDate(r.tgl_peresepan || r.tgl_perawatan)})
                            <span class="text-slate-700 ms-2 fw-semibold">· ${escapeHtml(r.nm_dokter || '')}</span>
                        </div>
                        <span class="badge ${r.can_delete ? 'bg-warning text-dark' : 'bg-success'}">${r.can_delete ? 'Menunggu Farmasi' : 'Diserahkan'}</span>
                    </div>
                    <div class="text-xs text-slate-800 ps-1">
                        ${escapeHtml(r.detail_obat || '-')}
                    </div>
                </div>`;
            } else if (it.type === 'lab') {
                const rows = it.data;
                html += `
                <div class="timeline-dot" style="background:#059669;"></div>
                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                        <div class="timeline-date mb-0" style="color:#059669;">
                            <i class="bi bi-eyedropper me-1"></i>Hasil Laboratorium (${fmtDate(it.date)} ${it.time})
                        </div>
                        <span class="badge bg-light text-slate-600 border">${rows.length} Parameter</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0 text-xs">
                            <thead class="table-light"><tr><th>Parameter</th><th style="width:100px;text-align:center;">Hasil</th><th>Rujukan</th><th>Ket</th></tr></thead>
                            <tbody>`;
                rows.forEach(r => {
                    const isAb = (r.keterangan && r.keterangan.trim() !== '') || ((r.keterangan||'').toLowerCase().includes('abnormal'));
                    html += `<tr><td>${escapeHtml(r.nama_pemeriksaan)}</td><td class="text-center ${isAb ? 'text-danger font-bold' : ''}">${escapeHtml(r.nilai)}</td><td>${escapeHtml(r.nilai_rujukan||'')} ${escapeHtml(r.satuan||'')}</td><td>${isAb ? `<span class="badge bg-danger">${escapeHtml(r.keterangan)}</span>` : '-'}</td></tr>`;
                });
                html += `</tbody></table></div></div>`;
            } else if (it.type === 'rad') {
                const rr = it.data;
                html += `
                <div class="timeline-dot" style="background:#4338ca;"></div>
                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                        <div class="timeline-date mb-0" style="color:#4338ca;">
                            <i class="bi bi-intersect me-1"></i>Hasil Radiologi (${fmtDate(rr.tgl_periksa)} ${(rr.jam||'').substring(0,5)})
                        </div>
                        <span class="badge bg-light text-slate-600 border">${rr.no_rawat}</span>
                    </div>
                    <div class="text-xs p-2 bg-light rounded" style="white-space:pre-wrap;">${escapeHtml(rr.hasil || '-')}</div>
                </div>`;
            } else if (it.type === 'operasi') {
                if (it.subType === 'booking') {
                    const b = it.data;
                    html += `
                    <div class="timeline-dot" style="background:#eab308;"></div>
                    <div class="timeline-card">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                            <div class="timeline-date mb-0" style="color:#ca8a04;">
                                <i class="bi bi-calendar-event me-1"></i>Booking Operasi (${fmtDate(b.tanggal)} ${(b.jam_mulai||'').substring(0,5)})
                                <span class="text-slate-700 ms-2 fw-semibold">· ${escapeHtml(b.nm_ruang_ok || '')}</span>
                            </div>
                            <span class="badge ${b.status === 'Selesai' ? 'bg-success' : 'bg-warning text-dark'}">${escapeHtml(b.status || 'Menunggu')}</span>
                        </div>
                        <div class="text-xs">
                            <div><strong>Paket:</strong> <span class="fw-bold">${escapeHtml(b.nama_paket || b.kode_paket)}</span> <code>(${escapeHtml(b.kode_paket)})</code></div>
                            ${b.dokter_operator ? `<div class="mt-1"><strong>Operator:</strong> ${escapeHtml(b.dokter_operator)}</div>` : ''}
                        </div>
                    </div>`;
                } else if (it.subType === 'selesai') {
                    const to = it.data;
                    html += `
                    <div class="timeline-dot" style="background:#16a34a;"></div>
                    <div class="timeline-card">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                            <div class="timeline-date mb-0 text-success">
                                <i class="bi bi-shield-check me-1"></i>Tindakan Operasi Selesai (${fmtDate(to.tgl_operasi)})
                                <span class="text-slate-700 ms-2 fw-semibold">· ${escapeHtml(to.operator || '-')}</span>
                            </div>
                            <span class="badge bg-success">Rp ${Number(to.total_tagihan||0).toLocaleString('id-ID')}</span>
                        </div>
                        <div class="text-xs">
                            <div><strong>Operasi:</strong> <span class="fw-bold">${escapeHtml(to.nama_paket || to.kode_paket)}</span> (${escapeHtml(to.status || 'Ralan')})</div>
                            <div class="mt-1 text-muted">Dokter Anestesi: ${escapeHtml(to.dokter_anestesi || '-')}</div>
                        </div>
                    </div>`;
                } else {
                    const lo = it.data;
                    html += `
                    <div class="timeline-dot" style="background:#dc2626;"></div>
                    <div class="timeline-card">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                            <div class="timeline-date mb-0" style="color:#dc2626;">
                                <i class="bi bi-file-earmark-text-fill me-1"></i>Laporan Operasi (${fmtDate(lo.tanggal)})
                            </div>
                            <span class="badge bg-danger">Anestesi: ${escapeHtml(lo.jenis_anasthesi || '-')}</span>
                        </div>
                        <div class="text-xs">
                            <div><strong>Pre-Op:</strong> ${escapeHtml(lo.diagnosa_preop || '-')} | <strong>Post-Op:</strong> ${escapeHtml(lo.diagnosa_postop || '-')}</div>
                            <div class="mt-1 p-2 bg-light rounded" style="white-space:pre-wrap;">${escapeHtml(lo.laporan_operasi || '-')}</div>
                        </div>
                    </div>`;
                }
            } else if (it.type === 'diagnosa') {
                const d = it.data;
                const isIcd10 = it.subType === 'icd10';
                const code = isIcd10 ? d.kd_penyakit : d.kode;
                const name = isIcd10 ? d.nm_penyakit : (d.deskripsi_panjang || d.deskripsi_pendek);
                const dotColor = isIcd10 ? '#0284c7' : '#7c3aed';
                const typeLabel = isIcd10 ? 'Diagnosa ICD-10' : 'Prosedur ICD-9-CM';

                html += `
                <div class="timeline-dot" style="background:${dotColor};"></div>
                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-1 pb-1 border-bottom">
                        <div class="timeline-date mb-0" style="color:${dotColor};">
                            <i class="bi bi-tags-fill me-1"></i>${typeLabel}: <span class="badge bg-light text-dark font-monospace border">${escapeHtml(code)}</span>
                        </div>
                        <span class="badge ${d.prioritas == 1 ? 'bg-danger' : 'bg-secondary'}">Prioritas ${d.prioritas || 1}</span>
                    </div>
                    <div class="text-xs font-bold text-slate-800">
                        ${escapeHtml(name || '-')}
                    </div>
                    <div class="text-slate-400 text-xs mt-1">No. Rawat: <code>${escapeHtml(d.no_rawat || '')}</code></div>
                </div>`;
            } else if (it.type === 'tindakan') {
                const t = it.data;
                const biaya = Number(t.total_byr || 0);
                const isParamedis = t.jenis && t.jenis.toLowerCase().includes('paramedis');
                const pelaksanaBadge = isParamedis
                    ? '<span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 px-2 py-0.5">Dr &amp; Paramedis</span>'
                    : '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5">Dokter</span>';

                html += `
                <div class="timeline-dot" style="background:#059669;"></div>
                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-1 pb-1 border-bottom">
                        <div class="timeline-date mb-0 text-success">
                            <i class="bi bi-activity me-1"></i>Tindakan: ${escapeHtml(t.nm_perawatan || t.kd_jenis_prw)} (${fmtDate(t.tgl_perawatan)})
                        </div>
                        <span class="font-bold text-success text-xs">Rp ${biaya.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-xs mt-1">
                        <div>${pelaksanaBadge} <span class="text-slate-600 ms-1">${escapeHtml(t.petugas || t.nm_dokter || '')}</span></div>
                        <div class="text-slate-400">No. Rawat: <code>${escapeHtml(t.no_rawat || '')}</code></div>
                    </div>
                </div>`;
            } else if (it.type === 'resume') {
                const resm = it.data;
                const isRanap = it.subType === 'ranap';
                const title = isRanap ? 'Resume Medis Pasien Rawat Inap (Discharge Summary)' : 'Resume Medis Pasien Rawat Jalan';
                const dotColor = isRanap ? '#9333ea' : '#0284c7';

                html += `
                <div class="timeline-dot" style="background:${dotColor};"></div>
                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 border-bottom pb-1.5">
                        <div class="timeline-date mb-0" style="color:${dotColor};">
                            <i class="bi bi-file-earmark-medical-fill me-1"></i>${title}
                        </div>
                        <span class="badge bg-light text-slate-600 border">${resm.no_rawat}</span>
                    </div>
                    <div class="text-xs">
                        <div><strong>Keluhan / Anamnesis:</strong> ${escapeHtml(resm.keluhan_utama || '-')}</div>
                        ${resm.diagnosa_utama ? `<div class="mt-1"><strong>Diagnosa Utama:</strong> <span class="fw-bold">${escapeHtml(resm.diagnosa_utama)}</span> <code>(${escapeHtml(resm.kd_diagnosa_utama||'')})</code></div>` : ''}
                        ${resm.obat_pulang ? `<div class="mt-1"><strong>Obat Pulang:</strong> <span class="text-slate-700">${escapeHtml(resm.obat_pulang)}</span></div>` : ''}
                        ${resm.kondisi_pulang ? `<div class="mt-1"><strong>Kondisi Pulang:</strong> <span class="badge bg-success">${escapeHtml(resm.kondisi_pulang)}</span></div>` : ''}
                    </div>
                </div>`;
            }

            html += `</div>`;
        });
        html += '</div>';
        c.html(html);
    }

    // Quick filter buttons for Tab 10
    $(document).on('click', '.filter-riwayat-btn', function () {
        $('.filter-riwayat-btn').removeClass('btn-success active').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-success active');
        const filter = $(this).data('filter');
        if (filter === 'all') {
            $('.timeline-filter-item').show();
        } else {
            $('.timeline-filter-item').hide();
            $(`.timeline-filter-item[data-type="${filter}"]`).show();
        }
    });

    $('#btnRefreshRiwayatLengkap').on('click', function () {
        const noRawat = $('#soapNoRawat').val() || activeNoRawat;
        if (noRawat) loadPasienDetail(noRawat);
    });

    // 1. Simpan / Update SOAP
    $('#btnSimpanSoap').on('click', function () {
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
        $.ajax({
            url: '{{ route("rawat-jalan.simpan-soap") }}',
            type: 'POST',
            data: $('#formSoap').serialize() + '&_token={{ csrf_token() }}',
            success: function (res) {
                notifySuccess(res.message || 'SOAP berhasil disimpan.');
                batalEditSoapRajal();
                const noRawat = $('#soapNoRawat').val();
                if (noRawat) loadPasienDetail(btoa(noRawat));
            },
            error: function (err) {
                notifyError('Gagal menyimpan SOAP: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
            },
            complete: function () {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // EDIT & HAPUS SOAP (HANYA DOKTER & ADMIN UTAMA)
    window.editSoapRajal = function (tgl, jam) {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang mengedit data SOAP.');
            return;
        }

        if (!currentRajalData || !currentRajalData.soapList) {
            notifyWarning('Data SOAP tidak ditemukan.');
            return;
        }

        const s = currentRajalData.soapList.find(item => item.tgl_perawatan === tgl && item.jam_rawat === jam);
        if (!s) {
            notifyWarning('Pemeriksaan SOAP tidak ditemukan.');
            return;
        }

        $('#soapSuhu').val(s.suhu_tubuh || '');
        $('#soapTensi').val(s.tensi || '');
        $('#soapNadi').val(s.nadi || '');
        $('#soapRespirasi').val(s.respirasi || '');
        $('#soapTinggi').val(s.tinggi || '');
        $('#soapBerat').val(s.berat || '');
        $('#soapSpo2').val(s.spo2 || '');
        $('#soapGcs').val(s.gcs || '');
        $('#soapKesadaran').val(s.kesadaran || 'Compos Mentis').trigger('change');
        $('#soapLingkarPerut').val(s.lingkar_perut || '');
        $('#soapAlergi').val(s.alergi || '');
        $('#soapKeluhan').val(s.keluhan || '');
        $('#soapPemeriksaan').val(s.pemeriksaan || '');
        $('#soapPenilaian').val(s.penilaian || '');
        $('#soapRtl').val(s.rtl || '');
        $('#soapInstruksi').val(s.instruksi || '');
        $('#soapEvaluasi').val(s.evaluasi || '');

        $('#soapTglPerawatan').val(s.tgl_perawatan);
        $('#soapJamRawat').val(s.jam_rawat);

        $('#editSoapTglText').text(fmtDate(s.tgl_perawatan));
        $('#editSoapJamText').text((s.jam_rawat || '').substring(0, 5));
        $('#alertEditSoap').removeClass('d-none');
        $('#btnSimpanSoap').html('<i class="bi bi-pencil-square me-1"></i> Perbarui Data SOAP');

        $('#tab-soap-btn').tab('show');
        document.getElementById('tab-soap')?.scrollIntoView({ behavior: 'smooth' });
        notifySuccess('Data pemeriksaan SOAP dimuat ke formulir untuk diedit.');
    };

    window.batalEditSoapRajal = function () {
        if ($('#formSoap').length && $('#formSoap')[0]) $('#formSoap')[0].reset();
        $('#soapTglPerawatan').val('');
        $('#soapJamRawat').val('');
        $('#alertEditSoap').addClass('d-none');
        $('#btnSimpanSoap').html('<i class="bi bi-save-fill me-1"></i> Simpan Data SOAP');
    };

    window.hapusSoapRajal = function (noRawat, tgl, jam) {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang menghapus data SOAP.');
            return;
        }

        Swal.fire({
            title: 'Hapus Catatan SOAP?',
            html: `Apakah Anda yakin ingin menghapus data pemeriksaan SOAP tanggal <b>${fmtDate(tgl)}</b> jam <b>${(jam||'').substring(0,5)}</b>?<br><small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus SOAP',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("rawat-jalan.hapus-soap") }}',
                    type: 'DELETE',
                    data: {
                        no_rawat: noRawat,
                        tgl_perawatan: tgl,
                        jam_rawat: jam,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        notifySuccess(res.message || 'Data SOAP berhasil dihapus.');
                        loadPasienDetail(btoa(noRawat));
                    },
                    error: function (err) {
                        notifyError('Gagal menghapus SOAP: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
                    }
                });
            }
        });
    };

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
                        const satStr   = item.kode_sat ? ` (${item.kode_sat})` : '';
                        const stokDepo = item.stok_depo !== undefined ? item.stok_depo : (item.stok || 0);
                        const stokTot  = item.stok_total !== undefined ? item.stok_total : (item.stok || 0);
                        const depoBadge = stokDepo > 0
                            ? `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-1.5 py-0.5"><i class="bi bi-check2-circle me-1"></i>Depo Ralan: ${stokDepo}</span>`
                            : `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-1.5 py-0.5"><i class="bi bi-x-circle me-1"></i>Depo Habis</span>`;
                        const totalBadge = `<span class="badge bg-light text-slate-600 border px-1.5 py-0.5">Total RS: ${stokTot}</span>`;
                        const hargaStr = item.ralan ? fmtRupiah(item.ralan) : '';

                        html += `
                            <div class="dropdown-item select-obat-item py-2 px-2.5 border-bottom cursor-pointer"
                                 data-kode="${item.kode_brng}"
                                 data-nama="${item.nama_brng}"
                                 data-satuan="${item.kode_sat || ''}">
                                <div class="d-flex justify-content-between align-items-center mb-0.5">
                                    <div class="font-bold text-slate-800 text-xs">${escapeHtml(item.nama_brng)}${satStr}</div>
                                    <div class="d-flex gap-1">${depoBadge} ${totalBadge}</div>
                                </div>
                                <div class="d-flex justify-content-between text-slate-500 text-xs">
                                    <span>Kode: <code>${item.kode_brng}</code> · ${escapeHtml(item.kategori || item.jenis || 'Obat')}</span>
                                    <span class="text-success fw-bold">${hargaStr}</span>
                                </div>
                            </div>`;
                    });
                } else {
                    html = `<div class="p-2.5 text-muted text-xs text-center">Obat tidak ditemukan di master farmasi.</div>`;
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
            notifySuccess(`"${nama}" ditambahkan ke daftar Obat Jadi.`);
        } else {
            notifyWarning(`"${nama}" sudah ada di daftar resep.`);
        }
        $('#searchObatInput').val('');
        $('#searchObatDropdown').hide();
    });

    // ============================================================
    // 5. RESEP DOKTER: OBAT JADI & RACIKAN (FORMAT SIMRS-NAMIRA)
    // ============================================================
    // (Note: selectedObatItems, selectedRacikanItems, allMasterObatRajal, isMasterObatLoaded are declared at the top of script)

    function updateResepSummary() {
        const countJadi = selectedObatItems.length;
        const countRacik = selectedRacikanItems.length;
        let totalBahan = 0;
        selectedRacikanItems.forEach(r => {
            totalBahan += (r.detail ? r.detail.length : 0);
        });

        $('#countObatJadiText').text(countJadi);
        $('#countRacikanText').text(countRacik);
        $('#badgeCountJadiSummary').text(`${countJadi} item obat dipilih`);
        $('#resepSummaryText').html(`<i class="bi bi-cart3 me-1"></i> Total: <b>${countJadi}</b> Obat Jadi, <b>${countRacik}</b> Racikan (${totalBahan} Bahan)`);
    }

    // --- TAB 1: OBAT JADI (NON-RACIKAN) ---
    function renderSelectedObat() {
        const tbody = $('#selectedObatTbody');
        if (selectedObatItems.length === 0) {
            tbody.html('<tr><td colspan="8" class="text-center text-muted py-3 fs-7">Belum ada obat jadi yang dipilih. Silakan pilih dari Katalog Obat di bawah.</td></tr>');
            updateResepSummary();
            return;
        }

        let html = '';
        selectedObatItems.forEach((item, idx) => {
            const stok = parseFloat(item.stok !== undefined ? item.stok : (item.stok_depo || 0));
            const isStokKosong = stok <= 0;
            const stokBadge = isStokKosong
                ? `<span class="badge bg-danger text-white"><i class="bi bi-x-circle me-1"></i>Stok Kosong</span>`
                : `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5 font-bold"><i class="bi bi-check2 me-1"></i>${stok} ${item.kode_sat || ''}</span>`;

            const rowClass = (isStokKosong || item.jml > stok) ? 'table-danger' : '';

            html += `
                <tr class="${rowClass}">
                    <td class="text-center font-bold text-muted">${idx + 1}</td>
                    <td><code class="font-monospace text-slate-700">${escapeHtml(item.kode)}</code></td>
                    <td>
                        <strong class="text-slate-800">${escapeHtml(item.nama)}</strong>
                        <div class="text-muted text-xs">Satuan: <b>${escapeHtml(item.kode_sat || '-')}</b></div>
                    </td>
                    <td class="text-center">${stokBadge}</td>
                    <td style="width:90px; text-align:center;">
                        <input type="number" class="form-control form-control-sm obat-jml text-center font-bold" data-idx="${idx}" value="${item.jml}" min="1" max="${stok > 0 ? stok : 1}">
                    </td>
                    <td style="width:220px;">
                        <input type="text" list="listAturanPakai" class="form-control form-control-sm obat-signa" data-idx="${idx}" value="${escapeHtml(item.aturan_pakai)}" placeholder="Misal: 3 X 1 Sehari Sesudah Makan">
                    </td>
                    <td style="width:150px;">
                        <input type="text" class="form-control form-control-sm obat-ket" data-idx="${idx}" value="${escapeHtml(item.keterangan || '')}" placeholder="Keterangan obat...">
                    </td>
                    <td style="width:45px; text-align:center;">
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1.5 remove-obat" data-idx="${idx}" title="Hapus obat ini">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });
        tbody.html(html);
        updateResepSummary();
    }

    $(document).on('change', '.obat-jml', function () {
        const idx = $(this).data('idx');
        const item = selectedObatItems[idx];
        if (!item) return;

        let val = parseFloat($(this).val()) || 1;
        const stok = parseFloat(item.stok !== undefined ? item.stok : (item.stok_depo || 0));

        if (stok <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Kosong',
                text: `Stok obat "${item.nama}" kosong di Depo Farmasi Ralan (G002). Harap hapus item ini dari resep.`
            });
            $(this).val(1);
            item.jml = 1;
            renderSelectedObat();
            return;
        }

        if (val <= 0) {
            notifyWarning('Jumlah obat minimal 1.');
            val = 1;
            $(this).val(val);
        } else if (val > stok) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok Tidak Mencukupi',
                text: `Maaf, stok tidak mencukupi! Stok "${item.nama}" di Depo Farmasi Ralan hanya ${stok} ${item.kode_sat || ''}, sedangkan permintaan ${val}. Nilai otomatis disesuaikan ke stok maksimal.`
            });
            val = stok;
            $(this).val(val);
        }

        item.jml = val;
        renderSelectedObat();
    });

    $(document).on('change', '.obat-signa', function () {
        const idx = $(this).data('idx');
        if (selectedObatItems[idx]) {
            selectedObatItems[idx].aturan_pakai = $(this).val().trim();
        }
    });

    $(document).on('change', '.obat-ket', function () {
        const idx = $(this).data('idx');
        if (selectedObatItems[idx]) {
            selectedObatItems[idx].keterangan = $(this).val().trim();
        }
    });

    $(document).on('click', '.remove-obat', function () {
        const idx = $(this).data('idx');
        selectedObatItems.splice(idx, 1);
        renderSelectedObat();
        notifyInfo('Obat dihapus dari draft.');
    });

    // --- TAB 2: OBAT RACIKAN DOKTER (FORMAT PERSIS SIMRS-NAMIRA) ---
    $('#btnBuatGrupRacikan').on('click', function () {
        const nama = $('#racikNamaInput').val().trim();
        const metode = $('#racikMetodeSelect').val();
        const metodeName = $('#racikMetodeSelect option:selected').text();
        const jml = parseInt($('#racikJmlInput').val()) || 10;
        const aturan = $('#racikAturanInput').val().trim() || '3 X 1 Bungkus';
        const ket = $('#racikKetInput').val().trim();

        if (!nama) {
            Swal.fire('Nama Racikan Kosong', 'Harap masukkan nama racikan terlebih dahulu (contoh: Puyer Batuk Pilek).', 'warning');
            $('#racikNamaInput').focus();
            return;
        }
        if (jml <= 0) {
            Swal.fire('Jumlah Kemasan Tidak Valid', 'Jumlah kemasan racikan harus minimal 1.', 'warning');
            $('#racikJmlInput').focus();
            return;
        }
        if (!aturan) {
            Swal.fire('Aturan Pakai Kosong', 'Harap masukkan aturan pakai untuk racikan ini.', 'warning');
            $('#racikAturanInput').focus();
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
        $('#racikJmlInput').val('10');
        renderDaftarRacikan();
        notifySuccess(`Racikan "${nama}" berhasil dibuat. Silakan tambahkan bahan obat.`);
    });

    function renderDaftarRacikan() {
        const container = $('#daftarRacikanContainer');
        if (!selectedRacikanItems || selectedRacikanItems.length === 0) {
            container.html(`
                <div class="text-center py-4 border rounded-3 bg-light text-muted fs-7">
                    <i class="bi bi-mortarboard fs-2 opacity-50 d-block mb-1"></i>
                    Belum ada obat racikan dibuat. Silakan isi form di atas dan klik <b>Buat Racikan Baru</b>.
                </div>`);
            updateResepSummary();
            return;
        }

        let html = '';
        selectedRacikanItems.forEach((r, rIdx) => {
            let bahanRows = '';
            if (r.detail && r.detail.length > 0) {
                r.detail.forEach((d, dIdx) => {
                    const stok = parseFloat(d.stok !== undefined ? d.stok : (d.stok_depo || 0));
                    const isStokKosong = stok <= 0;
                    const isStokKurang = d.jml > stok;

                    const stokBadge = isStokKosong
                        ? `<span class="badge bg-danger text-white"><i class="bi bi-x-circle me-1"></i>Habis</span>`
                        : `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-1.5 py-0.5 font-bold">${stok}</span>`;

                    const rowClass = (isStokKosong || isStokKurang) ? 'table-danger' : '';

                    bahanRows += `
                        <tr class="${rowClass}">
                            <td class="text-center font-bold text-muted">${dIdx + 1}</td>
                            <td><code class="font-monospace text-slate-700">${escapeHtml(d.kode_brng)}</code></td>
                            <td>
                                <strong class="text-slate-800">${escapeHtml(d.nama_brng)}</strong>
                            </td>
                            <td class="text-center font-semibold text-slate-700">${escapeHtml(d.kode_sat || '-')}</td>
                            <td class="text-center">${stokBadge}</td>
                            <td class="text-center text-slate-600">${d.kapasitas || '-'}</td>
                            <td style="width:115px; text-align:center;">
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm text-center racik-row-p1 font-bold" data-ridx="${rIdx}" data-didx="${dIdx}" value="${d.p1 || 1}" min="0.01" step="any" title="Pembilang (P1)">
                                    <span class="input-group-text px-1 font-bold">/</span>
                                    <input type="number" class="form-control form-control-sm text-center racik-row-p2 font-bold" data-ridx="${rIdx}" data-didx="${dIdx}" value="${d.p2 || 1}" min="1" step="any" title="Penyebut (P2)">
                                </div>
                            </td>
                            <td style="width:110px;">
                                <input type="text" class="form-control form-control-sm text-center racik-row-kandungan" data-ridx="${rIdx}" data-didx="${dIdx}" value="${escapeHtml(d.kandungan || '')}" placeholder="Kandungan mg...">
                            </td>
                            <td style="width:95px; text-align:center;">
                                <input type="number" class="form-control form-control-sm text-center racik-bahan-jml font-bold ${isStokKurang ? 'is-invalid text-danger' : 'text-success'}" data-ridx="${rIdx}" data-didx="${dIdx}" value="${d.jml}" min="0.1" step="any" title="Kebutuhan obat dihitung otomatis: ceil((P1/P2) * Jml Kemasan)">
                            </td>
                            <td style="width:40px; text-align:center;">
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1.5" onclick="hapusBahanRacik(${rIdx}, ${dIdx})" title="Hapus bahan ini">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                bahanRows = `<tr><td colspan="10" class="text-center text-muted py-3 fs-7"><i class="bi bi-info-circle me-1"></i>Belum ada bahan obat dalam racikan ini. Silakan cari bahan di bawah.</td></tr>`;
            }

            html += `
                <div class="card p-3 mb-3 border text-xs shadow-xs bg-white" style="border-radius:12px; border-left: 4px solid var(--ranap-primary, #0d7044) !important;">
                    {{-- Header Racikan --}}
                    <div class="d-flex justify-content-between align-items-center mb-2.5 flex-wrap gap-2 border-bottom pb-2">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="badge bg-primary px-2.5 py-1.5 font-bold">Racikan #${rIdx + 1}</span>
                            <div class="d-flex align-items-center gap-1">
                                <label class="text-slate-600 mb-0 font-bold">Nama:</label>
                                <input type="text" class="form-control form-control-sm racik-nama-edit font-bold" data-ridx="${rIdx}" value="${escapeHtml(r.nama_racik)}" style="width:160px; height:28px;">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="text-slate-600 mb-0">Metode:</label>
                                <select class="form-select form-select-sm racik-metode-edit" data-ridx="${rIdx}" style="width:95px; height:28px;">
                                    <option value="R01" ${r.kd_racik==='R01'?'selected':''}>Puyer</option>
                                    <option value="R02" ${r.kd_racik==='R02'?'selected':''}>Sirup</option>
                                    <option value="R03" ${r.kd_racik==='R03'?'selected':''}>Salep</option>
                                    <option value="R04" ${r.kd_racik==='R04'?'selected':''}>Kapsul</option>
                                </select>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="text-slate-600 mb-0 font-bold">Kemasan:</label>
                                <input type="number" class="form-control form-control-sm text-center racik-kemasan-edit font-bold" data-ridx="${rIdx}" value="${r.jml_dr}" min="1" style="width:65px; height:28px;" title="Jumlah bungkus / kemasan racikan">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="text-slate-600 mb-0 font-bold">Aturan:</label>
                                <input type="text" list="listAturanPakai" class="form-control form-control-sm racik-aturan-edit" data-ridx="${rIdx}" value="${escapeHtml(r.aturan_pakai)}" style="width:160px; height:28px;">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="text-slate-600 mb-0">Ket:</label>
                                <input type="text" class="form-control form-control-sm racik-ket-edit" data-ridx="${rIdx}" value="${escapeHtml(r.keterangan || '')}" placeholder="Keterangan..." style="width:140px; height:28px;">
                            </div>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2.5 font-bold" onclick="hapusGrupRacik(${rIdx})" title="Hapus racikan ini beserta seluruh bahannya">
                            <i class="bi bi-trash me-1"></i>Hapus Racikan
                        </button>
                    </div>

                    {{-- Tabel Bahan Racikan (Standar SIMRS-Namira) --}}
                    <div class="table-responsive border rounded-2 bg-white shadow-2xs mb-2.5">
                        <table class="table table-sm table-hover align-middle mb-0 text-xs">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:30px; text-align:center;">No</th>
                                    <th style="width:80px;">Kode</th>
                                    <th>Nama Bahan Obat</th>
                                    <th style="width:50px; text-align:center;">Sat</th>
                                    <th style="width:90px; text-align:center;">Stok G002</th>
                                    <th style="width:65px; text-align:center;">Kps(mg)</th>
                                    <th style="width:115px; text-align:center;">P1 / P2</th>
                                    <th style="width:110px; text-align:center;">Kandungan</th>
                                    <th style="width:95px; text-align:center;">Jml Butuh</th>
                                    <th style="width:40px; text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${bahanRows}
                            </tbody>
                        </table>
                    </div>

                    {{-- Bilah Pencarian & Tambah Bahan Racikan Langsung ke Racikan Ini --}}
                    <div class="p-2 border rounded-2 bg-light shadow-2xs">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">
                                    <i class="bi bi-search me-1 text-primary"></i>Cari Bahan Obat (Depo Farmasi Ralan G002)
                                </label>
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-sm search-bahan-input ps-4" data-ridx="${rIdx}" placeholder="Ketik nama bahan obat... (Live search)" autocomplete="off">
                                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size:0.75rem;"></i>
                                    <div class="search-results-dropdown bahan-dropdown-${rIdx}" style="display:none; max-height:220px; overflow-y:auto;"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">P1 / P2 (Dosis)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm text-center font-bold" id="p1_${rIdx}" value="1" min="0.01" step="any">
                                    <span class="input-group-text px-1 font-bold">/</span>
                                    <input type="number" class="form-control form-control-sm text-center font-bold" id="p2_${rIdx}" value="1" min="1" step="any">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Kandungan (mg)</label>
                                <input type="text" class="form-control form-control-sm text-center" id="kandungan_${rIdx}" placeholder="Kapasitas mg...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-bold text-xs text-slate-700 mb-1">Jml Butuh Obat</label>
                                <input type="number" class="form-control form-control-sm font-bold text-center text-success" id="jmlBahan_${rIdx}" value="${r.jml_dr}" min="0.1" step="any" readonly title="Dihitung otomatis: ceil((P1/P2) * Jml Kemasan)">
                            </div>
                        </div>
                    </div>
                </div>`;
        });
        container.html(html);
        updateResepSummary();
    }

    // Edit nama, metode, aturan, ket racikan inline
    $(document).on('change', '.racik-nama-edit', function () {
        const rIdx = $(this).data('ridx');
        if (selectedRacikanItems[rIdx]) selectedRacikanItems[rIdx].nama_racik = $(this).val().trim();
    });

    $(document).on('change', '.racik-metode-edit', function () {
        const rIdx = $(this).data('ridx');
        if (selectedRacikanItems[rIdx]) {
            selectedRacikanItems[rIdx].kd_racik = $(this).val();
            selectedRacikanItems[rIdx].metode_name = $(this).find('option:selected').text();
        }
    });

    $(document).on('change', '.racik-aturan-edit', function () {
        const rIdx = $(this).data('ridx');
        if (selectedRacikanItems[rIdx]) selectedRacikanItems[rIdx].aturan_pakai = $(this).val().trim();
    });

    $(document).on('change', '.racik-ket-edit', function () {
        const rIdx = $(this).data('ridx');
        if (selectedRacikanItems[rIdx]) selectedRacikanItems[rIdx].keterangan = $(this).val().trim();
    });

    // Auto-calculate Jml Butuh when P1 or P2 in inline form changes
    $(document).on('input change', '[id^="p1_"], [id^="p2_"]', function() {
        const id = $(this).attr('id');
        const rIdx = id.split('_')[1];
        const p1 = parseFloat($(`#p1_${rIdx}`).val()) || 1;
        const p2 = parseFloat($(`#p2_${rIdx}`).val()) || 1;
        const racik = selectedRacikanItems[rIdx];
        if (racik) {
            const jmlDr = racik.jml_dr || 10;
            const calculatedJml = Math.ceil((p1 / p2) * jmlDr);
            $(`#jmlBahan_${rIdx}`).val(calculatedJml);
        }
    });

    // Auto-calculate Jml Butuh when editing row P1/P2 in Bahan table
    $(document).on('input change', '.racik-row-p1, .racik-row-p2', function () {
        const rIdx = $(this).data('ridx');
        const dIdx = $(this).data('didx');
        const racik = selectedRacikanItems[rIdx];
        if (!racik || !racik.detail[dIdx]) return;

        const p1 = parseFloat($(`.racik-row-p1[data-ridx="${rIdx}"][data-didx="${dIdx}"]`).val()) || 1;
        const p2 = parseFloat($(`.racik-row-p2[data-ridx="${rIdx}"][data-didx="${dIdx}"]`).val()) || 1;
        racik.detail[dIdx].p1 = p1;
        racik.detail[dIdx].p2 = p2;

        const jmlDr = racik.jml_dr || 10;
        const newJml = Math.ceil((p1 / p2) * jmlDr);
        racik.detail[dIdx].jml = newJml;

        const stok = parseFloat(racik.detail[dIdx].stok || 0);
        const inputJml = $(`.racik-bahan-jml[data-ridx="${rIdx}"][data-didx="${dIdx}"]`);
        inputJml.val(newJml);

        if (newJml > stok) {
            inputJml.addClass('is-invalid text-danger').removeClass('text-success');
            Swal.fire({
                icon: 'warning',
                title: 'Stok Bahan Tidak Mencukupi',
                text: `Kebutuhan bahan "${racik.detail[dIdx].nama_brng}" (${newJml}) melebihi stok yang tersedia di Depo Farmasi Ralan (${stok}).`
            });
        } else {
            inputJml.removeClass('is-invalid text-danger').addClass('text-success');
        }
    });

    $(document).on('input change', '.racik-row-kandungan', function () {
        const rIdx = $(this).data('ridx');
        const dIdx = $(this).data('didx');
        if (selectedRacikanItems[rIdx] && selectedRacikanItems[rIdx].detail[dIdx]) {
            selectedRacikanItems[rIdx].detail[dIdx].kandungan = $(this).val();
        }
    });

    // When kemasan / jml_dr of racikan group is edited, recalculate all materials in it
    $(document).on('input change', '.racik-kemasan-edit', function () {
        const rIdx = $(this).data('ridx');
        const racik = selectedRacikanItems[rIdx];
        if (!racik) return;

        const newJmlDr = parseInt($(this).val()) || 1;
        if (newJmlDr <= 0) {
            $(this).val(1);
            racik.jml_dr = 1;
        } else {
            racik.jml_dr = newJmlDr;
        }
        $(`#jmlBahan_${rIdx}`).val(racik.jml_dr);

        racik.detail.forEach((d, dIdx) => {
            const p1 = d.p1 || 1;
            const p2 = d.p2 || 1;
            const updatedJml = Math.ceil((p1 / p2) * racik.jml_dr);
            d.jml = updatedJml;

            const stok = parseFloat(d.stok || 0);
            const inputJml = $(`.racik-bahan-jml[data-ridx="${rIdx}"][data-didx="${dIdx}"]`);
            inputJml.val(updatedJml);

            if (updatedJml > stok) {
                inputJml.addClass('is-invalid text-danger').removeClass('text-success');
            } else {
                inputJml.removeClass('is-invalid text-danger').addClass('text-success');
            }
        });
    });

    // Search bahan racik dengan live search
    let bahanSearchDebounce = null;
    $(document).on('keyup', '.search-bahan-input', function () {
        const input = $(this);
        const q = input.val().trim();
        const rIdx = input.data('ridx');
        const dropdown = $(`.bahan-dropdown-${rIdx}`);

        clearTimeout(bahanSearchDebounce);
        if (q.length < 2) { dropdown.hide(); return; }

        bahanSearchDebounce = setTimeout(() => {
            $.ajax({
                url: '{{ route("rawat-jalan.master-obat") }}',
                type: 'GET',
                data: { q: q },
                success: function (data) {
                    let html = '';
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const stokDepo = Number(item.stok_depo !== undefined ? item.stok_depo : (item.stok || 0));
                            const isKosong = stokDepo <= 0;
                            const depoBadge = !isKosong
                                ? `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-1.5 py-0.5 font-bold">Stok: ${stokDepo}</span>`
                                : `<span class="badge bg-danger text-white px-1.5 py-0.5 font-bold">Stok Kosong</span>`;

                            const itemClass = isKosong ? 'opacity-60 bg-light' : 'cursor-pointer select-bahan-item';

                            html += `
                                <div class="dropdown-item ${itemClass} py-1.5 px-2.5 border-bottom"
                                     data-ridx="${rIdx}"
                                     data-kode="${item.kode_brng}"
                                     data-nama="${escapeHtml(item.nama_brng)}"
                                     data-satuan="${escapeHtml(item.kode_sat || '')}"
                                     data-stok="${stokDepo}"
                                     data-kapasitas="${item.kapasitas || 0}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="font-bold text-slate-800 text-xs">${escapeHtml(item.nama_brng)} (${escapeHtml(item.kode_sat || '-')})</div>
                                        <div>${depoBadge}</div>
                                    </div>
                                    <div class="text-slate-500 text-xs mt-0.5">Kode: <code>${item.kode_brng}</code> · Kap: ${item.kapasitas || '-'}</div>
                                </div>`;
                        });
                    } else {
                        html = `<div class="p-2.5 text-muted text-xs text-center">Bahan obat tidak ditemukan.</div>`;
                    }
                    dropdown.html(html).show();
                }
            });
        }, 200);
    });

    $(document).on('click', '.select-bahan-item', function () {
        const rIdx = $(this).data('ridx');
        const kode = $(this).data('kode');
        const nama = $(this).data('nama');
        const satuan = $(this).data('satuan');
        const stok = parseFloat($(this).data('stok')) || 0;
        const kapasitas = parseFloat($(this).data('kapasitas')) || 0;

        // Perkondisian Stok Kosong
        if (stok <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Kosong',
                text: `Bahan obat "${nama}" kosong di Depo Farmasi Ralan (G002) sehingga tidak dapat dijadikan racikan.`
            });
            return;
        }

        const racik = selectedRacikanItems[rIdx];
        if (!racik) return;

        // Perkondisian Duplikasi Bahan dalam 1 Racikan
        if (racik.detail.some(d => d.kode_brng === kode)) {
            Swal.fire({
                icon: 'warning',
                title: 'Bahan Sudah Ada',
                text: `Bahan obat "${nama}" sudah ada dalam racikan "${racik.nama_racik}". Silakan sesuaikan dosis P1/P2 pada baris obat tersebut.`
            });
            return;
        }

        const p1 = parseFloat($(`#p1_${rIdx}`).val()) || 1;
        const p2 = parseFloat($(`#p2_${rIdx}`).val()) || 1;
        let kandungan = $(`#kandungan_${rIdx}`).val() || (kapasitas > 0 ? kapasitas : '');
        let jml = parseFloat($(`#jmlBahan_${rIdx}`).val()) || Math.ceil((p1 / p2) * (racik.jml_dr || 10));

        // Perkondisian Kebutuhan Melebihi Stok
        if (jml > stok) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok Bahan Tidak Mencukupi',
                text: `Kebutuhan bahan "${nama}" (${jml} ${satuan}) melebihi stok yang tersedia di Depo Farmasi Ralan (${stok} ${satuan}). Harap sesuaikan dosis atau jumlah kemasan.`
            });
            return;
        }

        racik.detail.push({
            kode_brng: kode,
            nama_brng: nama,
            kode_sat: satuan,
            kapasitas: kapasitas,
            stok: stok,
            p1: p1,
            p2: p2,
            kandungan: kandungan,
            jml: jml
        });

        $(`.search-bahan-input[data-ridx="${rIdx}"]`).val('');
        $(`.bahan-dropdown-${rIdx}`).hide();
        renderDaftarRacikan();
        notifySuccess(`"${nama}" ditambahkan ke racikan.`);
    });

    $(document).on('change', '.racik-bahan-jml', function () {
        const rIdx = $(this).data('ridx');
        const dIdx = $(this).data('didx');
        const racik = selectedRacikanItems[rIdx];
        if (!racik || !racik.detail[dIdx]) return;

        let val = parseFloat($(this).val()) || 1;
        const stok = parseFloat(racik.detail[dIdx].stok || 0);

        if (val <= 0) {
            val = 1;
            $(this).val(val);
        } else if (val > stok) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok Bahan Tidak Mencukupi',
                text: `Kebutuhan bahan "${racik.detail[dIdx].nama_brng}" (${val}) melebihi stok yang tersedia di Depo Farmasi Ralan (${stok}).`
            });
        }
        racik.detail[dIdx].jml = val;
    });

    window.hapusGrupRacik = function (rIdx) {
        const racik = selectedRacikanItems[rIdx];
        const nama = racik ? racik.nama_racik : '';
        Swal.fire({
            title: 'Hapus Racikan?',
            text: `Apakah Anda yakin ingin menghapus racikan "${nama}" beserta seluruh bahannya?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((res) => {
            if (res.isConfirmed) {
                selectedRacikanItems.splice(rIdx, 1);
                // Re-numbering racikan
                selectedRacikanItems.forEach((r, idx) => { r.no_racik = idx + 1; });
                renderDaftarRacikan();
                notifySuccess(`Racikan "${nama}" berhasil dihapus.`);
            }
        });
    };

    window.hapusBahanRacik = function (rIdx, dIdx) {
        if (selectedRacikanItems[rIdx]) {
            selectedRacikanItems[rIdx].detail.splice(dIdx, 1);
            renderDaftarRacikan();
        }
    };

    // --- MASTER DATA SELURUH OBAT DEPO FARMASI RALAN (G002) & LIVE SEARCH ---
    function loadLiveTableObat(force = false) {
        if (isMasterObatLoaded && !force) {
            filterLiveTableObat();
            return;
        }

        const tbody = $('#tbodyLiveMasterObat');
        tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm text-success me-1"></span> Mengambil seluruh obat dari Depo Farmasi Ralan (G002)...</td></tr>');
        $('#textInfoLiveObat').text('Mengambil data...');

        $.ajax({
            url: '{{ route("rawat-jalan.master-obat") }}',
            type: 'GET',
            data: { hanya_stok: 0 },
            success: function (data) {
                allMasterObatRajal = data || [];
                isMasterObatLoaded = true;
                filterLiveTableObat();
            },
            error: function () {
                tbody.html('<tr><td colspan="9" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat data master obat. Silakan klik Muat Ulang Data.</td></tr>');
                $('#textInfoLiveObat').text('Gagal memuat');
            }
        });
    }

    function filterLiveTableObat() {
        const q = ($('#liveSearchObatInput').val() || '').trim().toLowerCase();
        const kat = $('#liveKategoriFilter').val();
        const hanyaStok = $('#liveStokFilter').is(':checked');
        const tbody = $('#tbodyLiveMasterObat');

        let filtered = allMasterObatRajal;

        if (q) {
            filtered = filtered.filter(item => {
                const nama = (item.nama_brng || '').toLowerCase();
                const kode = (item.kode_brng || '').toLowerCase();
                return nama.includes(q) || kode.includes(q);
            });
        }

        if (kat) {
            filtered = filtered.filter(item => {
                const k = (item.kategori || item.jenis || '').toUpperCase();
                if (kat === 'OBAT') {
                    return !k.includes('ALKES') && !k.includes('BHP');
                } else if (kat === 'ALKES') {
                    return k.includes('ALKES') || k.includes('BHP');
                }
                return true;
            });
        }

        if (hanyaStok) {
            filtered = filtered.filter(item => {
                const stok = Number(item.stok_depo !== undefined ? item.stok_depo : (item.stok || 0));
                return stok > 0;
            });
        }

        $('#textInfoLiveObat').text(`Menampilkan ${Math.min(filtered.length, 120)} dari ${filtered.length} obat (Total: ${allMasterObatRajal.length})`);

        if (filtered.length === 0) {
            tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted"><i class="bi bi-search fs-4 d-block mb-1 opacity-50"></i> Tidak ada obat yang sesuai dengan pencarian/filter di Depo Farmasi Ralan.</td></tr>');
            return;
        }

        let html = '';
        const limit = 120;
        const displayList = filtered.slice(0, limit);

        displayList.forEach(item => {
            const stok = Number(item.stok_depo !== undefined ? item.stok_depo : (item.stok || 0));
            const isKosong = stok <= 0;

            const stokBadge = !isKosong
                ? `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5 font-bold"><i class="bi bi-check2 me-1"></i>${stok}</span>`
                : `<span class="badge bg-danger text-white px-2 py-0.5 font-bold"><i class="bi bi-x-circle me-1"></i>Stok Kosong</span>`;

            const hargaStr = item.ralan ? fmtRupiah(item.ralan) : '-';
            const itemJson = JSON.stringify(item).replace(/'/g, "&apos;");

            const defaultJml = isKosong ? 0 : Math.min(10, stok);
            const rowClass = isKosong ? 'table-danger-subtle opacity-65' : '';

            const actionButtons = isKosong
                ? `<button type="button" disabled class="btn btn-xs btn-secondary disabled font-semibold py-1 px-2.5 opacity-60 w-100" title="Stok Habis di Depo Farmasi Ralan">
                       <i class="bi bi-slash-circle me-1"></i>Habis
                   </button>`
                : `<button type="button" class="btn btn-xs btn-success font-bold py-1 px-2.5 btn-tambah-live-jadi w-100" data-item='${itemJson}' title="Tambahkan ke Obat Jadi">
                       <i class="bi bi-plus-circle me-1"></i>+ Jadi
                   </button>`;

            html += `
                <tr data-kode="${item.kode_brng}" class="${rowClass}">
                    <td><code class="font-monospace text-slate-700">${escapeHtml(item.kode_brng)}</code></td>
                    <td>
                        <strong class="text-slate-800">${escapeHtml(item.nama_brng)}</strong>
                        <div class="text-muted text-xs">Satuan: <b>${escapeHtml(item.kode_sat || '-')}</b> ${item.kapasitas ? `· Kap: ${item.kapasitas}` : ''}</div>
                    </td>
                    <td><span class="badge bg-light text-slate-700 border">${escapeHtml(item.kategori || item.jenis || 'Obat')}</span></td>
                    <td class="text-center">${stokBadge}</td>
                    <td class="text-end fw-semibold text-slate-800">${hargaStr}</td>
                    <td style="width: 75px; text-align: center;">
                        <input type="number" class="form-control form-control-sm text-center font-bold live-row-jml" value="${defaultJml}" min="1" max="${stok}" ${isKosong ? 'disabled' : ''} style="height: 28px; padding: 2px 4px;">
                    </td>
                    <td style="width: 160px;">
                        <input type="text" list="listAturanPakai" class="form-control form-control-sm live-row-aturan" value="3 X 1 Sehari" placeholder="Aturan pakai..." ${isKosong ? 'disabled placeholder="Stok Habis"' : ''} style="height: 28px; font-size: 0.75rem;">
                    </td>
                    <td style="width: 130px;">
                        <input type="text" class="form-control form-control-sm live-row-ket" placeholder="Keterangan..." ${isKosong ? 'disabled' : ''} style="height: 28px; font-size: 0.75rem;">
                    </td>
                    <td class="text-center" style="width: 95px;">
                        ${actionButtons}
                    </td>
                </tr>`;
        });

        if (filtered.length > limit) {
            html += `<tr><td colspan="9" class="text-center py-2 bg-light text-muted text-xs">Menampilkan 120 obat teratas. Gunakan kotak pencarian untuk menemukan obat lainnya.</td></tr>`;
        }

        tbody.html(html);
    }

    // Event Handler Input Live Search & Filter
    let liveSearchDebounce = null;
    $('#liveSearchObatInput').on('input keyup', function () {
        clearTimeout(liveSearchDebounce);
        liveSearchDebounce = setTimeout(() => {
            filterLiveTableObat();
        }, 150);
    });

    $('#liveKategoriFilter, #liveStokFilter').on('change', function () {
        filterLiveTableObat();
    });

    $('#btnRefreshLiveObat').on('click', function () {
        loadLiveTableObat(true);
    });

    // Tambah Obat Jadi dari Baris Live Table
    $(document).on('click', '.btn-tambah-live-jadi', function () {
        const item = $(this).data('item');
        if (!item) return;

        const stok = Number(item.stok_depo !== undefined ? item.stok_depo : (item.stok || 0));

        // 1. Perkondisian Stok Kosong
        if (stok <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Kosong',
                text: `Obat "${item.nama_brng}" tidak memiliki stok di Depo Farmasi Ralan (G002) sehingga tidak dapat diresepkan.`
            });
            return;
        }

        const tr = $(this).closest('tr');
        const jml = parseFloat(tr.find('.live-row-jml').val()) || 1;
        const aturan = tr.find('.live-row-aturan').val().trim() || '3 X 1 Sehari';
        const ket = tr.find('.live-row-ket').val().trim();

        // 2. Perkondisian Jumlah Harus > 0
        if (jml <= 0) {
            Swal.fire('Jumlah Tidak Valid', 'Jumlah obat harus lebih dari 0.', 'warning');
            return;
        }

        // 3. Perkondisian Permintaan Melebihi Stok
        if (jml > stok) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok Tidak Mencukupi',
                text: `Jumlah permintaan obat "${item.nama_brng}" (${jml}) melebihi stok yang tersedia di Depo Farmasi Ralan (${stok}).`
            });
            return;
        }

        // 4. Perkondisian Aturan Pakai Kosong
        if (!aturan || aturan === '-') {
            Swal.fire('Aturan Pakai Wajib Diisi', `Harap masukkan aturan pakai untuk obat "${item.nama_brng}".`, 'warning');
            return;
        }

        const existing = selectedObatItems.find(i => i.kode === item.kode_brng);
        if (existing) {
            const totalBaru = existing.jml + jml;
            if (totalBaru > stok) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Mencukupi',
                    text: `Total obat "${item.nama_brng}" (${totalBaru}) akan melebihi stok yang tersedia di Depo Farmasi Ralan (${stok}).`
                });
                return;
            }
            existing.jml = totalBaru;
            if (aturan) existing.aturan_pakai = aturan;
            if (ket) existing.keterangan = ket;
            notifySuccess(`Jumlah "${item.nama_brng}" diperbarui menjadi ${existing.jml}.`);
        } else {
            selectedObatItems.push({
                kode: item.kode_brng,
                nama: item.nama_brng,
                kode_sat: item.kode_sat || '',
                stok: stok,
                stok_depo: stok,
                jml: jml,
                aturan_pakai: aturan,
                keterangan: ket
            });
            notifySuccess(`"${item.nama_brng}" ditambahkan ke Obat Jadi (${jml} ${item.kode_sat||''}).`);
        }

        renderSelectedObat();
        // Scroll smoothly to draft table if needed
        document.getElementById('pills-obat-jadi')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // Muat data master obat saat Tab Resep dibuka
    $('#tab-resep-btn').on('shown.bs.tab', function () {
        if (!isMasterObatLoaded) {
            loadLiveTableObat();
        }
    });

    // ============================================================
    // SIMPAN E-RESEP (VALIDASI KETAT DOKTER & ADMIN UTAMA)
    // ============================================================
    $('#btnSimpanResep').on('click', function () {
        if (!isDokterOrAdmin) {
            Swal.fire('Akses Ditolak', 'Hanya Dokter dan Admin Utama yang berwenang menyimpan resep.', 'error');
            return;
        }

        const noRawat = $('#resepNoRawat').val();
        if (!noRawat) {
            Swal.fire('Error', 'Nomor rawat pasien belum dipilih.', 'warning');
            return;
        }

        if (selectedObatItems.length === 0 && selectedRacikanItems.length === 0) {
            Swal.fire('Resep Kosong', 'Pilih minimal 1 obat jadi atau 1 obat racikan terlebih dahulu.', 'warning');
            return;
        }

        // 1. Validasi Kondisi Seluruh Obat Jadi
        for (let i = 0; i < selectedObatItems.length; i++) {
            const item = selectedObatItems[i];
            const stok = parseFloat(item.stok !== undefined ? item.stok : (item.stok_depo || 0));

            if (!item.jml || item.jml <= 0) {
                Swal.fire('Validasi Gagal', `Jumlah untuk obat "${item.nama}" harus lebih dari 0.`, 'warning');
                $('#pills-obat-jadi-tab').tab('show');
                return;
            }
            if (stok <= 0) {
                Swal.fire('Stok Habis', `Obat "${item.nama}" tidak memiliki stok di Depo Farmasi Ralan (G002). Silakan hapus dari resep.`, 'error');
                $('#pills-obat-jadi-tab').tab('show');
                return;
            }
            if (item.jml > stok) {
                Swal.fire('Stok Tidak Mencukupi', `Jumlah obat "${item.nama}" (${item.jml}) melebihi stok yang tersedia di Depo Farmasi Ralan (${stok}).`, 'error');
                $('#pills-obat-jadi-tab').tab('show');
                return;
            }
            if (!item.aturan_pakai || item.aturan_pakai.trim() === '' || item.aturan_pakai.trim() === '-') {
                Swal.fire('Aturan Pakai Kosong', `Aturan pakai untuk obat "${item.nama}" wajib diisi.`, 'warning');
                $('#pills-obat-jadi-tab').tab('show');
                return;
            }
        }

        // 2. Validasi Kondisi Seluruh Obat Racikan
        for (let i = 0; i < selectedRacikanItems.length; i++) {
            const r = selectedRacikanItems[i];

            if (!r.nama_racik || r.nama_racik.trim() === '') {
                Swal.fire('Validasi Gagal', `Nama racikan ke-${i + 1} wajib diisi.`, 'warning');
                $('#pills-obat-racik-tab').tab('show');
                return;
            }
            if (!r.jml_dr || r.jml_dr <= 0) {
                Swal.fire('Validasi Gagal', `Jumlah kemasan untuk racikan "${r.nama_racik}" harus minimal 1.`, 'warning');
                $('#pills-obat-racik-tab').tab('show');
                return;
            }
            if (!r.aturan_pakai || r.aturan_pakai.trim() === '' || r.aturan_pakai.trim() === '-') {
                Swal.fire('Aturan Pakai Kosong', `Aturan pakai untuk racikan "${r.nama_racik}" wajib diisi.`, 'warning');
                $('#pills-obat-racik-tab').tab('show');
                return;
            }
            if (!r.detail || r.detail.length === 0) {
                Swal.fire('Racikan Tanpa Bahan', `Racikan "${r.nama_racik}" belum memiliki bahan obat. Masukkan minimal 1 bahan racik.`, 'warning');
                $('#pills-obat-racik-tab').tab('show');
                return;
            }

            for (let j = 0; j < r.detail.length; j++) {
                const d = r.detail[j];
                const stok = parseFloat(d.stok !== undefined ? d.stok : (d.stok_depo || 0));

                if (!d.jml || d.jml <= 0) {
                    Swal.fire('Validasi Gagal', `Kebutuhan bahan "${d.nama_brng}" pada racikan "${r.nama_racik}" harus lebih dari 0.`, 'warning');
                    $('#pills-obat-racik-tab').tab('show');
                    return;
                }
                if (stok <= 0) {
                    Swal.fire('Stok Bahan Habis', `Bahan obat "${d.nama_brng}" pada racikan "${r.nama_racik}" kosong di Depo Farmasi Ralan (G002). Silakan sesuaikan bahan racikan.`, 'error');
                    $('#pills-obat-racik-tab').tab('show');
                    return;
                }
                if (d.jml > stok) {
                    Swal.fire('Stok Bahan Tidak Mencukupi', `Kebutuhan bahan "${d.nama_brng}" (${d.jml}) pada racikan "${r.nama_racik}" melebihi stok yang tersedia di Depo Farmasi Ralan (${stok}).`, 'error');
                    $('#pills-obat-racik-tab').tab('show');
                    return;
                }
            }
        }

        const editNoResep = $('#resepEditNoResep').val();
        const actionLabel = editNoResep ? 'Menyimpan Perubahan E-Resep' : 'Kirim E-Resep ke Farmasi';

        Swal.fire({
            title: actionLabel + '?',
            html: `<div class="text-start text-xs">
                       <p class="mb-1">Total: <b>${selectedObatItems.length}</b> Obat Jadi, <b>${selectedRacikanItems.length}</b> Racikan.</p>
                       <p class="text-muted mb-0">Resep akan diteruskan ke Depo Farmasi Ralan (G002) untuk diproses.</p>
                   </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan & Kirim',
            cancelButtonText: 'Batal'
        }).then((confirmRes) => {
            if (!confirmRes.isConfirmed) return;

            const payload = {
                _token: '{{ csrf_token() }}',
                no_rawat: noRawat,
                no_resep: editNoResep || null,
                items: selectedObatItems.map(i => ({
                    kode_brng: i.kode,
                    nama: i.nama,
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
                        nama_brng: d.nama_brng,
                        p1: d.p1 || 1,
                        p2: d.p2 || 1,
                        kandungan: d.kandungan || '-',
                        jml: d.jml || 1
                    }))
                }))
            };

            const btn = $('#btnSimpanResep');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

            $.ajax({
                url: '{{ route("rawat-jalan.simpan-resep") }}',
                type: 'POST',
                data: payload,
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'E-Resep dokter berhasil disimpan/dikirim ke Farmasi.'
                    });
                    $('#resepEditNoResep').val('');
                    $('#alertEditResep').addClass('d-none');
                    $('#btnBatalEditResep').addClass('d-none');
                    $('#btnSimpanResep').html('<i class="bi bi-send-check me-1"></i> Simpan &amp; Kirim E-Resep ke Farmasi');

                    selectedObatItems = [];
                    selectedRacikanItems = [];
                    renderSelectedObat();
                    renderDaftarRacikan();
                    updateResepSummary();
                    if (noRawat) loadPasienDetail(btoa(noRawat));
                },
                error: function (err) {
                    const msg = err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem saat menyimpan resep.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan Resep',
                        text: msg
                    });
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="bi bi-send-check me-1"></i> Simpan &amp; Kirim E-Resep ke Farmasi');
                }
            });
        });
    });

    // EDIT RESEP RAJAL (HANYA DOKTER & ADMIN UTAMA)
    window.editResepRajal = function (noResep) {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang mengedit resep.');
            return;
        }

        if (!currentRajalData || !currentRajalData.resepList) {
            notifyWarning('Data resep tidak ditemukan.');
            return;
        }

        const r = currentRajalData.resepList.find(item => item.no_resep === noResep);
        if (!r) {
            notifyWarning(`Resep ${noResep} tidak ditemukan.`);
            return;
        }

        if (!r.can_delete) {
            notifyWarning('Resep ini sudah diserahkan/divalidasi oleh Farmasi dan tidak dapat diubah.');
            return;
        }

        // Reset draft
        selectedObatItems = [];
        selectedRacikanItems = [];

        // Load obat non racikan
        const nonRacikList = r.obat_list || r.obat_non_racik || [];
        nonRacikList.forEach(o => {
            selectedObatItems.push({
                kode: o.kode_brng,
                nama: o.nama_brng || o.kode_brng,
                kode_sat: o.kode_sat || '',
                jml: parseFloat(o.jml) || 1,
                aturan_pakai: o.aturan_pakai || '3 X 1 Sehari',
                keterangan: o.keterangan || ''
            });
        });

        // Load racikan
        const racikList = r.racik_list || r.obat_racikan || [];
        racikList.forEach((rc, idx) => {
            const details = (rc.detail || rc.detail_racik || []).map(d => ({
                kode_brng: d.kode_brng,
                nama_brng: d.nama_brng || d.kode_brng,
                p1: parseFloat(d.p1) || 1,
                p2: parseFloat(d.p2) || 1,
                kandungan: d.kandungan || '-',
                jml: parseFloat(d.jml) || 1
            }));

            selectedRacikanItems.push({
                no_racik: idx + 1,
                nama_racik: rc.nama_racik,
                kd_racik: rc.kd_racik || 'R01',
                metode_name: rc.metode || 'Puyer',
                jml_dr: parseInt(rc.jml_dr) || 10,
                aturan_pakai: rc.aturan_pakai || '3 X 1 Sehari',
                keterangan: rc.keterangan || '',
                detail: details
            });
        });

        // Setup edit state
        $('#resepEditNoResep').val(noResep);
        $('#editNoResepText').text(noResep);
        $('#alertEditResep').removeClass('d-none');
        $('#btnBatalEditResep').removeClass('d-none');
        $('#btnSimpanResep').html(`<i class="bi bi-check2-circle me-1"></i> Simpan Perubahan E-Resep (${noResep})`);

        renderSelectedObat();
        renderDaftarRacikan();
        updateResepSummary();

        // Switch to Resep tab & scroll to top
        $('#tab-resep-btn').tab('show');
        document.getElementById('tab-resep')?.scrollIntoView({ behavior: 'smooth' });

        notifySuccess(`Resep ${noResep} berhasil dimuat ke formulir untuk diedit.`);
    };

    window.batalEditResepRajal = function () {
        $('#resepEditNoResep').val('');
        $('#alertEditResep').addClass('d-none');
        $('#btnBatalEditResep').addClass('d-none');
        $('#btnSimpanResep').html('<i class="bi bi-send-check me-1"></i> Simpan &amp; Kirim E-Resep ke Farmasi');

        selectedObatItems = [];
        selectedRacikanItems = [];
        renderSelectedObat();
        renderDaftarRacikan();
        updateResepSummary();

        notifyInfo('Mode edit resep dibatalkan. Formulir dikosongkan untuk resep baru.');
    };

    // BATAL RESEP (HAPUS RESEP SEBELUM DISERAHKAN) - HANYA DOKTER & ADMIN UTAMA
    window.batalResepRajal = function (noResep) {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang membatalkan/menghapus resep.');
            return;
        }

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
                        no_rawat: $('#resepNoRawat').val(),
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
            if ($('#formResumeRajal').length && $('#formResumeRajal')[0]) $('#formResumeRajal')[0].reset();
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

    // ===================================================================
    // 6. MODUL OPERASI (BOOKING JADWAL & LAPORAN OPERASI SIMRS-NAMIRA)
    // ===================================================================
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
        loadMasterRuangOk();
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

    function loadMasterRuangOk() {
        $.ajax({
            url: '{{ route("rawat-jalan.master-ruang-ok") }}',
            type: 'GET',
            success: function (data) {
                let html = '';
                data.forEach(item => {
                    html += `<option value="${item.kd_ruang_ok}">${item.nm_ruang_ok} (${item.kd_ruang_ok})</option>`;
                });
                if (html) {
                    $('#opsKdRuangOk').html(html);
                }
            }
        });
    }

    // Helper format datetime for <input type="datetime-local">
    function toDateTimeLocalValue(dateStr) {
        if (!dateStr) return '';
        try {
            // If dateStr is "YYYY-MM-DD HH:mm:ss" or similar
            const s = dateStr.replace(' ', 'T');
            return s.substring(0, 16);
        } catch (e) {
            return '';
        }
    }

    // SIMPAN / EDIT BOOKING JADWAL OPERASI
    $('#btnSimpanOperasi').on('click', function () {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang menjadwalkan operasi.');
            return;
        }

        const noRawat = $('#opsNoRawat').val();
        if (!noRawat) {
            notifyWarning('Pilih pasien rawat jalan terlebih dahulu.');
            return;
        }

        if (!$('#opsKodePaket').val()) {
            notifyWarning('Pilih paket operasi terlebih dahulu.');
            return;
        }

        if (!$('#opsKdDokter').val()) {
            notifyWarning('Pilih dokter operator terlebih dahulu.');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

        $.ajax({
            url: '{{ route("rawat-jalan.simpan-booking-operasi") }}',
            type: 'POST',
            data: $('#formOperasi').serialize() + '&_token={{ csrf_token() }}',
            success: function (res) {
                notifySuccess(res.message || 'Jadwal operasi berhasil disimpan.');
                batalEditBookingOpsRajal();
                if (noRawat) loadPasienDetail(btoa(noRawat));
            },
            error: function (err) {
                notifyError('Gagal menyimpan jadwal operasi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="bi bi-calendar-check-fill me-1"></i> Simpan Jadwal Operasi');
            }
        });
    });

    // EDIT BOOKING OPERASI
    window.editBookingOpsRajal = function (noRawat, kodePaket, tanggal, jamMulai, jamSelesai, status, kdDokter, kdRuangOk) {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang mengedit booking operasi.');
            return;
        }

        $('#opsNoRawat').val(noRawat);
        $('#opsOldKodePaket').val(kodePaket);
        $('#opsOldTanggal').val(tanggal);
        $('#opsOldJamMulai').val(jamMulai);

        $('#opsTanggal').val(tanggal);
        if (jamMulai) $('#opsJamMulai').val(jamMulai.substring(0, 5));
        if (jamSelesai) $('#opsJamSelesai').val(jamSelesai.substring(0, 5));
        if (status) $('#opsStatus').val(status);

        if (kdDokter) $('#opsKdDokter').val(kdDokter).trigger('change');
        if (kdRuangOk) $('#opsKdRuangOk').val(kdRuangOk).trigger('change');

        // Pastikan opsi paket operasi terpilih
        if (kodePaket) {
            let exists = false;
            $('#opsKodePaket option').each(function () {
                if ($(this).val() === kodePaket) { exists = true; return false; }
            });
            if (!exists) {
                $('#opsKodePaket').append(new Option(kodePaket, kodePaket, true, true));
            } else {
                $('#opsKodePaket').val(kodePaket);
            }
            $('#opsKodePaket').trigger('change');
        }

        $('#editBookingOpsTglText').text(`${tanggal} (${(jamMulai||'').substring(0,5)})`);
        $('#alertEditBookingOps').removeClass('d-none');
        $('#btnSimpanOperasi').html('<i class="bi bi-pencil-square me-1"></i> Simpan Perubahan Jadwal');

        // Aktifkan sub-tab 1 Booking
        const triggerEl = document.querySelector('#pills-ops-booking-tab');
        if (triggerEl) {
            const tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
            tab.show();
        }

        document.getElementById('formOperasi')?.scrollIntoView({ behavior: 'smooth' });
        notifySuccess(`Jadwal operasi ${kodePaket} tanggal ${tanggal} dimuat untuk diedit.`);
    };

    window.batalEditBookingOpsRajal = function () {
        $('#opsOldKodePaket').val('');
        $('#opsOldTanggal').val('');
        $('#opsOldJamMulai').val('');
        $('#alertEditBookingOps').addClass('d-none');
        $('#btnSimpanOperasi').html('<i class="bi bi-calendar-check-fill me-1"></i> Simpan Jadwal Operasi');
    };

    // CETAK JADWAL BOOKING OPERASI (SIMRS-NAMIRA STANDAR)
    window.cetakBookingOpsRajal = function (noRawat, kodePaket, tanggal) {
        if (!noRawat) return;
        const baseUrl = "{{ route('rawat-jalan.cetak-booking-operasi') }}";
        const url = `${baseUrl}?no_rawat=${encodeURIComponent(noRawat)}&kode_paket=${encodeURIComponent(kodePaket||'')}&tanggal=${encodeURIComponent(tanggal||'')}`;
        window.open(url, '_blank');
    };

    // CETAK LAPORAN OPERASI (SIMRS-NAMIRA STANDAR)
    window.cetakLaporanOpsRajal = function (noRawat, tanggal) {
        if (!noRawat) return;
        const baseUrl = "{{ route('rawat-jalan.cetak-laporan-operasi') }}";
        const url = `${baseUrl}?no_rawat=${encodeURIComponent(noRawat)}${tanggal ? '&tanggal=' + encodeURIComponent(tanggal) : ''}`;
        window.open(url, '_blank');
    };

    // HAPUS BOOKING OPERASI
    window.hapusBookingOpsRajal = function (noRawat, kodePaket, tanggal, jamMulai) {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang menghapus booking operasi.');
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
                    url: '{{ route("rawat-jalan.hapus-booking-operasi") }}',
                    type: 'DELETE',
                    data: {
                        no_rawat: noRawat,
                        kode_paket: kodePaket,
                        tanggal: tanggal,
                        jam_mulai: jamMulai || '',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        notifySuccess(res.message || 'Jadwal operasi berhasil dihapus.');
                        if (noRawat) loadPasienDetail(btoa(noRawat));
                    },
                    error: function (err) {
                        notifyError('Gagal menghapus jadwal operasi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
                    }
                });
            }
        });
    };

    // ── MODAL CARI & PILIH PAKET OPERASI ──
    let timerCariPaketOps = null;

    window.bukaModalCariPaketRajal = function () {
        $('#modalCariPaketOperasi').modal('show');
        loadPaketOperasiModal($('#searchPaketOpsInput').val() || '');
    };

    window.loadPaketOperasiModal = function (q) {
        const query = (typeof q === 'string' ? q : $('#searchPaketOpsInput').val()) || '';
        const kategori = $('#filterKategoriOpsSelect').val() || '';
        const kelas = $('#filterKelasOpsSelect').val() || '';

        $('#tbodyModalPaketOps').html(`
            <tr><td colspan="7" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat paket operasi...</td></tr>
        `);

        $.ajax({
            url: '{{ route("rawat-jalan.master-operasi") }}',
            type: 'GET',
            data: { q: query, kategori: kategori, kelas: kelas },
            success: function (data) {
                if (!data || data.length === 0) {
                    $('#tbodyModalPaketOps').html(`
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
                            <button type="button" class="btn btn-xs btn-primary font-bold px-2.5 py-1 rounded-2" onclick="pilihPaketOperasiRajal('${item.kode_paket}', '${safeName}', ${totalTarif})">
                                <i class="bi bi-check2-circle me-1"></i>Pilih
                            </button>
                        </td>
                    </tr>`;
                });
                $('#tbodyModalPaketOps').html(html);
            },
            error: function () {
                $('#tbodyModalPaketOps').html(`
                    <tr><td colspan="7" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat data paket operasi.</td></tr>
                `);
            }
        });
    };

    $('#searchPaketOpsInput').on('input', function () {
        clearTimeout(timerCariPaketOps);
        const val = $(this).val();
        timerCariPaketOps = setTimeout(() => {
            loadPaketOperasiModal(val);
        }, 300);
    });

    $('#filterKategoriOpsSelect, #filterKelasOpsSelect').on('change', function () {
        loadPaketOperasiModal($('#searchPaketOpsInput').val());
    });

    window.pilihPaketOperasiRajal = function (kode, nama, tarif) {
        // Cek apakah opsi sudah ada di select opsKodePaket
        let exists = false;
        $('#opsKodePaket option').each(function () {
            if ($(this).val() === kode) {
                exists = true;
                return false;
            }
        });

        if (!exists) {
            $('#opsKodePaket').append(new Option(`${nama} (${kode})`, kode, true, true));
        } else {
            $('#opsKodePaket').val(kode);
        }
        $('#opsKodePaket').trigger('change');

        $('#modalCariPaketOperasi').modal('hide');
        notifySuccess(`Paket operasi ${nama} berhasil dipilih.`);
    };

    // ── MODAL PILIH TEMPLATE LAPORAN OPERASI SIMRS-NAMIRA ──
    let timerTemplateOps = null;
    let selectedTemplateOpsData = null;

    window.bukaModalTemplateLaporanRajal = function () {
        $('#modalTemplateLaporanOperasi').modal('show');
        selectedTemplateOpsData = null;
        $('#btnTerapkanTemplateOps').prop('disabled', true);
        $('#previewTemplateNoText').text('-');
        $('#previewTemplateNamaText').text('-');
        $('#previewTemplatePreopText').text('-');
        $('#previewTemplatePostopText').text('-');
        $('#previewTemplateJaringanText').text('-');
        $('#previewTemplatePaText').text('-');
        $('#previewTemplateLaporanText').val('');

        loadTemplateOperasiModal($('#searchTemplateOpsInput').val() || '');
    };

    window.loadTemplateOperasiModal = function (q) {
        const query = (typeof q === 'string' ? q : $('#searchTemplateOpsInput').val()) || '';

        $('#tbodyModalTemplateOps').html(`
            <tr><td colspan="5" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Memuat template laporan operasi...</td></tr>
        `);

        $.ajax({
            url: '{{ route("rawat-jalan.master-template-laporan-operasi") }}',
            type: 'GET',
            data: { q: query },
            success: function (data) {
                if (!data || data.length === 0) {
                    $('#tbodyModalTemplateOps').html(`
                        <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> Tidak ada template yang cocok.</td></tr>
                    `);
                    return;
                }

                window.__cachedTemplatesOps = data;
                let html = '';
                data.forEach((t, idx) => {
                    const paBadge = t.permintaan_pa === 'Ya'
                        ? `<span class="badge bg-danger">Ya</span>`
                        : `<span class="badge bg-secondary">Tidak</span>`;

                    html += `
                    <tr style="cursor:pointer;" onclick="previewTemplateLaporanOpsItem(${idx})">
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
                            <button type="button" class="btn btn-xs btn-outline-primary font-bold px-2 py-1 rounded-2" onclick="event.stopPropagation(); previewTemplateLaporanOpsItem(${idx});">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                $('#tbodyModalTemplateOps').html(html);
            },
            error: function () {
                $('#tbodyModalTemplateOps').html(`
                    <tr><td colspan="5" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat template laporan operasi.</td></tr>
                `);
            }
        });
    };

    $('#searchTemplateOpsInput').on('input', function () {
        clearTimeout(timerTemplateOps);
        const val = $(this).val();
        timerTemplateOps = setTimeout(() => {
            loadTemplateOperasiModal(val);
        }, 300);
    });

    window.previewTemplateLaporanOpsItem = function (idx) {
        if (!window.__cachedTemplatesOps || !window.__cachedTemplatesOps[idx]) return;
        const t = window.__cachedTemplatesOps[idx];
        selectedTemplateOpsData = t;

        $('#previewTemplateNoText').text(t.no_template);
        $('#previewTemplateNamaText').text(t.nama_operasi);
        $('#previewTemplatePreopText').text(t.diagnosa_preop || '-');
        $('#previewTemplatePostopText').text(t.diagnosa_postop || '-');
        $('#previewTemplateJaringanText').text(t.jaringan_dieksisi || '-');
        $('#previewTemplatePaText').text(t.permintaan_pa || 'Tidak');
        $('#previewTemplateLaporanText').val(t.laporan_operasi || '');

        $('#btnTerapkanTemplateOps').prop('disabled', false);

        // Highlight active row
        $('#tbodyModalTemplateOps tr').removeClass('table-primary');
        $(`#tbodyModalTemplateOps tr:eq(${idx})`).addClass('table-primary');
    };

    $('#btnTerapkanTemplateOps').on('click', function () {
        if (!selectedTemplateOpsData) {
            notifyWarning('Pilih salah satu template laporan operasi terlebih dahulu.');
            return;
        }

        const t = selectedTemplateOpsData;
        if (t.diagnosa_preop) $('#lapOpsPreop').val(t.diagnosa_preop);
        if (t.diagnosa_postop) $('#lapOpsPostop').val(t.diagnosa_postop);
        if (t.jaringan_dieksisi) $('#lapOpsJaringan').val(t.jaringan_dieksisi);
        if (t.permintaan_pa) $('#lapOpsPa').val(t.permintaan_pa);
        if (t.laporan_operasi) $('#lapOpsLaporan').val(t.laporan_operasi);

        $('#modalTemplateLaporanOperasi').modal('hide');
        notifySuccess(`Template "${t.nama_operasi}" berhasil diterapkan ke formulir laporan operasi.`);
    });

    // ── SIMPAN / EDIT LAPORAN OPERASI ──
    $('#btnSimpanLaporanOperasi').on('click', function () {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang menyimpan Laporan Operasi.');
            return;
        }

        const noRawat = $('#lapOpsNoRawat').val();
        if (!noRawat) {
            notifyWarning('Pilih pasien rawat jalan terlebih dahulu.');
            return;
        }

        const preop = $('#lapOpsPreop').val().trim();
        const postop = $('#lapOpsPostop').val().trim();
        const laporan = $('#lapOpsLaporan').val().trim();

        if (!preop) {
            notifyWarning('Diagnosa Pra Bedah (Pre-Op) wajib diisi.');
            $('#lapOpsPreop').focus();
            return;
        }

        if (!postop) {
            notifyWarning('Diagnosa Pasca Bedah (Post-Op) wajib diisi.');
            $('#lapOpsPostop').focus();
            return;
        }

        if (!laporan) {
            notifyWarning('Uraian laporan operasi / jalannya tindakan pembedahan wajib diisi.');
            $('#lapOpsLaporan').focus();
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

        $.ajax({
            url: '{{ route("rawat-jalan.simpan-laporan-operasi") }}',
            type: 'POST',
            data: $('#formLaporanOperasi').serialize() + '&_token={{ csrf_token() }}',
            success: function (res) {
                notifySuccess(res.message || 'Laporan operasi berhasil disimpan.');
                batalEditLaporanOpsRajal();
                if (noRawat) loadPasienDetail(btoa(noRawat));
            },
            error: function (err) {
                notifyError('Gagal menyimpan laporan operasi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="bi bi-save-fill me-1"></i> Simpan Laporan Operasi');
            }
        });
    });

    // EDIT LAPORAN OPERASI
    window.editLaporanOpsRajal = function (noRawat, tanggal, tglOperasi, selesaiOperasi, preop, postop, jaringan, pa, anesthesi, kategori, laporan) {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang mengedit Laporan Operasi.');
            return;
        }

        $('#lapOpsNoRawat').val(noRawat);
        $('#lapOpsOldTanggal').val(tanggal);

        $('#lapOpsTanggal').val(toDateTimeLocalValue(tanggal));
        if (tglOperasi) $('#lapOpsTglOperasi').val(toDateTimeLocalValue(tglOperasi));
        if (selesaiOperasi) $('#lapOpsSelesaiOperasi').val(toDateTimeLocalValue(selesaiOperasi));

        $('#lapOpsPreop').val(preop || '');
        $('#lapOpsPostop').val(postop || '');
        $('#lapOpsJaringan').val(jaringan || '');
        $('#lapOpsPa').val(pa || 'Tidak');
        $('#lapOpsAnasthesi').val(anesthesi || 'SPINAL');
        $('#lapOpsKategori').val(kategori || 'Besar');
        $('#lapOpsLaporan').val(laporan || '');

        $('#editLapOpsTglText').text(tanggal);
        $('#alertEditLaporanOps').removeClass('d-none');
        $('#btnSimpanLaporanOperasi').html('<i class="bi bi-pencil-square me-1"></i> Simpan Perubahan Laporan Operasi');

        // Buka sub-tab 2 Laporan Operasi
        const triggerEl = document.querySelector('#pills-ops-laporan-tab');
        if (triggerEl) {
            const tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
            tab.show();
        }

        document.getElementById('formLaporanOperasi')?.scrollIntoView({ behavior: 'smooth' });
        notifySuccess(`Laporan operasi tanggal ${tanggal} dimuat ke formulir.`);
    };

    window.batalEditLaporanOpsRajal = function () {
        $('#lapOpsOldTanggal').val('');
        $('#alertEditLaporanOps').addClass('d-none');
        $('#btnSimpanLaporanOperasi').html('<i class="bi bi-save-fill me-1"></i> Simpan Laporan Operasi');
        resetFormLaporanOpsRajal();
    };

    window.resetFormLaporanOpsRajal = function () {
        const noRawat = $('#lapOpsNoRawat').val();
        if ($('#formLaporanOperasi').length && $('#formLaporanOperasi')[0]) $('#formLaporanOperasi')[0].reset();
        $('#lapOpsNoRawat').val(noRawat);
        $('#lapOpsOldTanggal').val('');

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

        $('#lapOpsTanggal').val(nowIso);
        $('#lapOpsTglOperasi').val(nowIso);
        $('#lapOpsSelesaiOperasi').val(endIso);
        $('#lapOpsPa').val('Tidak');
        $('#lapOpsAnasthesi').val('SPINAL');
        $('#lapOpsKategori').val('Besar');
    };

    // HAPUS LAPORAN OPERASI
    window.hapusLaporanOpsRajal = function (noRawat, tanggal) {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang menghapus Laporan Operasi.');
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
                    url: '{{ route("rawat-jalan.hapus-laporan-operasi") }}',
                    type: 'DELETE',
                    data: {
                        no_rawat: noRawat,
                        tanggal: tanggal,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        notifySuccess(res.message || 'Laporan operasi berhasil dihapus.');
                        if (noRawat) loadPasienDetail(btoa(noRawat));
                    },
                    error: function (err) {
                        notifyError('Gagal menghapus laporan operasi: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan'));
                    }
                });
            }
        });
    };

    // Close dropdowns when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#searchLabInput, #searchLabDropdown').length) { $('#searchLabDropdown').hide(); }
        if (!$(e.target).closest('#searchRadInput, #searchRadDropdown').length) { $('#searchRadDropdown').hide(); }
        if (!$(e.target).closest('#searchObatInput, #searchObatDropdown').length) { $('#searchObatDropdown').hide(); }
    });

    // ===================================================================
    // DIAGNOSA & TINDAKAN RAWAT JALAN JAVASCRIPT
    // ===================================================================
    // ===================================================================
    // DIAGNOSA, PROSEDUR & TINDAKAN RAWAT JALAN JAVASCRIPT
    // ===================================================================
    function renderDiagnosaRajal(list, riwayatList) {
        // 1. Diagnosa Kunjungan Ini
        if (!list || list.length === 0) {
            $('#rajalDiagnosaContainer').html(`
                <div class="text-center py-4 px-3 border rounded-3 bg-light text-muted">
                    <i class="bi bi-search-heart text-primary fs-2 opacity-50 d-block mb-1"></i>
                    <div class="fw-bold fs-7 text-slate-700">Belum Ada Diagnosa Dicatat</div>
                    <div class="text-xs text-slate-500 mt-1">Pilih kode ICD-10 pada form di atas dan klik Tambah.</div>
                </div>
            `);
        } else {
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
                            ${isDokterOrAdmin ? `
                            <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 rounded-2" onclick="hapusDiagnosaRajal('${d.kd_penyakit}', '${(d.nm_penyakit||'').replace(/'/g, "\\'")}')" title="Hapus diagnosa">
                                <i class="bi bi-trash"></i>
                            </button>` : `<span class="text-muted text-xs">-</span>`}
                        </td>
                    </tr>`;
            });

            html += `   </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center px-1 text-slate-500 text-xs">
                    <span><i class="bi bi-info-circle me-1"></i>Total <strong>${list.length}</strong> diagnosa terdaftar untuk kunjungan ini</span>
                </div>`;

            $('#rajalDiagnosaContainer').html(html);
        }

        // 2. Riwayat Diagnosa Lampau Seluruh Kunjungan
        const rc = $('#rajalRiwayatDiagnosaContainer');
        if (!riwayatList || riwayatList.length === 0) {
            rc.html('<div class="empty-state py-3"><p class="text-xs text-muted mb-0">Belum ada riwayat diagnosa lampau.</p></div>');
        } else {
            let rHtml = `
                <div class="table-responsive border rounded-3 bg-white shadow-xs" style="max-height:300px;">
                    <table class="table table-sm table-hover align-middle mb-0 text-xs">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:30px;">#</th>
                                <th style="width:80px;">Tgl</th>
                                <th style="width:85px;">ICD-10</th>
                                <th>Diagnosa</th>
                                <th style="width:70px;text-align:center;">Prio</th>
                                <th style="width:110px;">No. Rawat</th>
                            </tr>
                        </thead>
                        <tbody>`;
            riwayatList.forEach((rd, i) => {
                rHtml += `
                    <tr>
                        <td class="text-muted">${i + 1}</td>
                        <td>${rd.tgl_registrasi || '-'}</td>
                        <td><span class="badge bg-light text-dark border font-monospace">${rd.kd_penyakit}</span></td>
                        <td class="fw-semibold text-slate-800">${rd.nm_penyakit || '-'}</td>
                        <td class="text-center"><span class="badge ${rd.prioritas == 1 ? 'bg-danger' : 'bg-secondary'}">${rd.prioritas || 1}</span></td>
                        <td><code class="text-xs">${rd.no_rawat}</code></td>
                    </tr>`;
            });
            rHtml += `</tbody></table></div>`;
            rc.html(rHtml);
        }
    }
    window.renderDiagnosaRajal = renderDiagnosaRajal;

    function renderProsedurRajal(list, riwayatList) {
        // 1. Prosedur ICD-9 Kunjungan Ini
        if (!list || list.length === 0) {
            $('#rajalProsedurContainer').html(`
                <div class="text-center py-4 px-3 border rounded-3 bg-light text-muted">
                    <i class="bi bi-diagram-3 text-purple-700 fs-2 opacity-50 d-block mb-1"></i>
                    <div class="fw-bold fs-7 text-slate-700">Belum Ada Prosedur Dicatat</div>
                    <div class="text-xs text-slate-500 mt-1">Pilih kode ICD-9-CM pada form di atas dan klik Tambah.</div>
                </div>
            `);
        } else {
            let html = `
                <div class="table-responsive border rounded-3 overflow-hidden shadow-xs bg-white mb-2">
                    <table class="table table-hover table-emr-detail align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px; text-align:center;">#</th>
                                <th style="width:115px;">Kode ICD-9</th>
                                <th>Deskripsi Prosedur / Tindakan</th>
                                <th style="width:145px; text-align:center;">Prioritas</th>
                                <th style="width:65px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>`;
            
            list.forEach((p, idx) => {
                let prioBadge = '';
                if (p.prioritas == 1) {
                    prioBadge = '<span class="badge bg-purple-700 bg-opacity-10 text-purple-700 border border-purple-200 px-2.5 py-1 font-bold"><i class="bi bi-star-fill me-1 text-purple-700"></i>Utama (1)</span>';
                } else if (p.prioritas == 2) {
                    prioBadge = `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 font-semibold">Sekunder (2)</span>`;
                } else {
                    prioBadge = `<span class="badge bg-secondary bg-opacity-10 text-slate-700 border border-secondary border-opacity-25 px-2.5 py-1">Tambahan (${p.prioritas})</span>`;
                }

                const deskripsi = p.deskripsi_panjang || p.deskripsi_pendek || '-';

                html += `
                    <tr>
                        <td style="text-align:center; color:#94a3b8; font-weight:600;">${idx + 1}</td>
                        <td><span class="badge bg-purple-700 bg-opacity-10 text-purple-700 border border-purple-200 font-monospace px-2 py-1 fs-7">${p.kode}</span></td>
                        <td>
                            <div class="font-bold text-slate-800 fs-7">${deskripsi}</div>
                        </td>
                        <td style="text-align:center;">${prioBadge}</td>
                        <td style="text-align:center;">
                            ${isDokterOrAdmin ? `
                            <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 rounded-2" onclick="hapusProsedurRajal('${p.kode}', '${deskripsi.replace(/'/g, "\\'")}')" title="Hapus prosedur">
                                <i class="bi bi-trash"></i>
                            </button>` : `<span class="text-muted text-xs">-</span>`}
                        </td>
                    </tr>`;
            });

            html += `   </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center px-1 text-slate-500 text-xs">
                    <span><i class="bi bi-info-circle me-1"></i>Total <strong>${list.length}</strong> prosedur ICD-9 terdaftar untuk kunjungan ini</span>
                </div>`;

            $('#rajalProsedurContainer').html(html);
        }

        // 2. Riwayat Prosedur Lampau Seluruh Kunjungan
        const rc = $('#rajalRiwayatProsedurContainer');
        if (!riwayatList || riwayatList.length === 0) {
            rc.html('<div class="empty-state py-3"><p class="text-xs text-muted mb-0">Belum ada riwayat prosedur lampau.</p></div>');
        } else {
            let rHtml = `
                <div class="table-responsive border rounded-3 bg-white shadow-xs" style="max-height:300px;">
                    <table class="table table-sm table-hover align-middle mb-0 text-xs">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:30px;">#</th>
                                <th style="width:80px;">Tgl</th>
                                <th style="width:85px;">ICD-9</th>
                                <th>Deskripsi</th>
                                <th style="width:70px;text-align:center;">Prio</th>
                                <th style="width:110px;">No. Rawat</th>
                            </tr>
                        </thead>
                        <tbody>`;
            riwayatList.forEach((rp, i) => {
                const dsk = rp.deskripsi_panjang || rp.deskripsi_pendek || '-';
                rHtml += `
                    <tr>
                        <td class="text-muted">${i + 1}</td>
                        <td>${rp.tgl_registrasi || '-'}</td>
                        <td><span class="badge bg-light text-dark border font-monospace">${rp.kode}</span></td>
                        <td class="fw-semibold text-slate-800">${dsk}</td>
                        <td class="text-center"><span class="badge ${rp.prioritas == 1 ? 'bg-purple-700' : 'bg-secondary'}">${rp.prioritas || 1}</span></td>
                        <td><code class="text-xs">${rp.no_rawat}</code></td>
                    </tr>`;
            });
            rHtml += `</tbody></table></div>`;
            rc.html(rHtml);
        }
    }
    window.renderProsedurRajal = renderProsedurRajal;

    function renderTindakanRajal(list, riwayatList) {
        // 1. Tindakan Kunjungan Ini
        if (!list || list.length === 0) {
            $('#rajalTindakanContainer').html(`
                <div class="text-center py-4 px-3 border rounded-3 bg-light text-muted">
                    <i class="bi bi-activity text-success fs-2 opacity-50 d-block mb-1"></i>
                    <div class="fw-bold fs-7 text-slate-700">Belum Ada Tindakan Dicatat</div>
                    <div class="text-xs text-slate-500 mt-1">Pilih tindakan dan tarif pada form di atas dan klik Simpan.</div>
                </div>
            `);
        } else {
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

                let jenisBadge = '';
                const jnsLower = (t.jenis || '').toLowerCase();
                if (jnsLower.includes('dr & paramedis') || jnsLower.includes('dokter & paramedis')) {
                    jenisBadge = `<span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-people-fill me-1"></i>Dr &amp; Paramedis</span>`;
                } else if (jnsLower.includes('paramedis')) {
                    jenisBadge = `<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-person-fill me-1"></i>Paramedis</span>`;
                } else {
                    jenisBadge = `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-person-badge-fill me-1"></i>Dokter</span>`;
                }

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
                            ${isDokterOrAdmin ? `
                            <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 rounded-2" onclick="hapusTindakanRajal('${t.kd_jenis_prw}', '${t.tgl_perawatan}', '${t.jam_rawat}', '${t.jenis || 'Dokter'}', '${(t.nm_perawatan||'').replace(/'/g, "\\'")}')" title="Hapus tindakan">
                                <i class="bi bi-trash"></i>
                            </button>` : `<span class="text-muted text-xs">-</span>`}
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

        // 2. Riwayat Tindakan Lampau (Ralan & Ranap)
        const rc = $('#rajalRiwayatTindakanContainer');
        if (!riwayatList || riwayatList.length === 0) {
            rc.html('<div class="empty-state py-3"><p class="text-xs text-muted mb-0">Belum ada riwayat tindakan lampau.</p></div>');
        } else {
            let rHtml = `
                <div class="table-responsive border rounded-3 bg-white shadow-xs" style="max-height:300px;">
                    <table class="table table-sm table-hover align-middle mb-0 text-xs">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:30px;">#</th>
                                <th style="width:80px;">Tgl</th>
                                <th>Tindakan / Prosedur</th>
                                <th style="width:110px;">Pelaksana</th>
                                <th style="width:100px;">Kategori</th>
                                <th style="width:100px;text-align:right;">Biaya</th>
                                <th style="width:110px;">No. Rawat</th>
                            </tr>
                        </thead>
                        <tbody>`;
            riwayatList.forEach((rt, i) => {
                const b = Number(rt.total_byr || 0);
                rHtml += `
                    <tr>
                        <td class="text-muted">${i + 1}</td>
                        <td>${rt.tgl_perawatan || '-'}</td>
                        <td class="fw-semibold text-slate-800">${rt.nm_perawatan || rt.kd_jenis_prw}</td>
                        <td><span class="badge bg-light text-dark border">${rt.jenis || 'Dokter'}</span></td>
                        <td class="text-muted">${rt.kategori || 'Ralan'}</td>
                        <td class="text-end font-mono text-success">Rp ${b.toLocaleString('id-ID')}</td>
                        <td><code class="text-xs">${rt.no_rawat}</code></td>
                    </tr>`;
            });
            rHtml += `</tbody></table></div>`;
            rc.html(rHtml);
        }
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

    // Select2 Diagnosa Rajal (ICD-10)
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

    // Select2 Prosedur Rajal (ICD-9-CM)
    $('#rajalProsedurSelect').select2({
        theme: 'bootstrap-5',
        placeholder: 'Ketik kode / deskripsi prosedur ICD-9-CM...',
        minimumInputLength: 2,
        dropdownParent: $('#offcanvasPelayanan'),
        ajax: {
            url: '{{ route("rawat-jalan.master-prosedur") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: (data || []).map(i => ({ id: i.kode, text: `${i.kode} — ${i.deskripsi_panjang || i.deskripsi_pendek}` }))
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
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang menghapus diagnosa.');
            return;
        }

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

    // Tambah Prosedur Rajal (ICD-9)
    $('#btnTambahProsedurRajal').on('click', function () {
        const noRawat = $('#soapNoRawat').val();
        const kode = $('#rajalProsedurSelect').val();
        const prioritas = $('#rajalProsedurPrioritas').val();
        if (!kode) {
            notifyWarning('Pilih prosedur ICD-9 terlebih dahulu.');
            return;
        }

        $.ajax({
            url: '{{ route("rawat-jalan.simpan-prosedur") }}',
            type: 'POST',
            data: {
                no_rawat: noRawat,
                kode: kode,
                prioritas: prioritas,
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                notifySuccess(res.message || 'Prosedur ICD-9 berhasil ditambahkan.');
                loadPasienDetail(btoa(noRawat));
                $('#rajalProsedurSelect').val(null).trigger('change');
            },
            error: function (err) {
                notifyError('Gagal menambah prosedur: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
            }
        });
    });

    window.hapusProsedurRajal = function (kode, deskripsi) {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang menghapus prosedur.');
            return;
        }

        Swal.fire({
            title: 'Hapus Prosedur ICD-9?',
            html: `Apakah Anda yakin ingin menghapus prosedur <b>${kode}</b> ${deskripsi ? '— ' + deskripsi : ''}?`,
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
                    url: '{{ route("rawat-jalan.hapus-prosedur") }}',
                    type: 'DELETE',
                    data: {
                        no_rawat: noRawat,
                        kode: kode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        notifySuccess(res.message || 'Prosedur ICD-9 berhasil dihapus.');
                        loadPasienDetail(btoa(noRawat));
                    },
                    error: function (err) {
                        notifyError('Gagal menghapus prosedur: ' + (err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem'));
                    }
                });
            }
        });
    };

    // Tambah Tindakan Rajal
    $('#btnTambahTindakanRajal').on('click', function () {
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang mencatat tindakan.');
            return;
        }

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
        if (!isDokterOrAdmin) {
            notifyWarning('Hanya Dokter dan Admin Utama yang berwenang menghapus tindakan.');
            return;
        }

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
