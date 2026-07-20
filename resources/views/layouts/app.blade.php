<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') — ePasien RS Namira</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* =============================================
           GLOBAL LAYOUT — ePasien RS Namira
           Warna identik logo RS Namira (Hijau):
           --primary:       #0d7044  (hijau tua)
           --primary-light: #1a9958  (hijau sedang)
           --accent:        #4cc87a  (hijau muda)
           --primary-dark:  #085c34  (hijau sangat tua)
        ============================================= */
        :root {
            --sidebar-w:       260px;
            --sidebar-bg:      #063d26;   /* hijau sangat tua — gelap */
            --sidebar-darker:  #042b1b;
            --sidebar-text:    rgba(255,255,255,.78);
            --sidebar-hover:   rgba(77,200,122,.1);
            --sidebar-active:  rgba(77,200,122,.18);
            --sidebar-border:  rgba(255,255,255,.07);

            --primary:         #0d7044;
            --primary-light:   #1a9958;
            --primary-dark:    #085c34;
            --accent:          #4cc87a;
            --accent-light:    #6dd998;
            --success:         #28a745;
            --warning:         #ffc107;
            --danger:          #dc3545;
            --info:            #20c997;

            --bg-body:         #f0faf4;   /* hijau sangat pucat */
            --bg-card:         #ffffff;
            --text-main:       #0e2618;
            --text-muted:      #4a7a5a;
            --border:          #c3e6cb;
            --shadow-sm:       0 1px 4px rgba(13,112,68,.07);
            --shadow-md:       0 4px 16px rgba(13,112,68,.11);
            --shadow-lg:       0 8px 32px rgba(13,112,68,.14);
            --radius:          14px;
            --radius-sm:       10px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            font-size: .9rem;
            line-height: 1.6;
        }

        /* =======================
           SIDEBAR
        ======================= */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }

        /* Brand / Logo */
        .sidebar-brand {
            padding: 1.1rem 1.2rem;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: .85rem;
            flex-shrink: 0;
            text-decoration: none;
            background: var(--sidebar-darker);
        }
        .sidebar-brand-logo {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(255,255,255,.1);
            border: 1.5px solid rgba(255,255,255,.18);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3px;
        }
        .sidebar-brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .sidebar-brand-text { flex: 1; min-width: 0; }
        .sidebar-brand-text .rs-name {
            font-size: .92rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: .3px;
        }
        .sidebar-brand-text .rs-name span { color: var(--accent-light); }
        .sidebar-brand-text .rs-sub {
            font-size: .68rem;
            color: rgba(255,255,255,.42);
            font-weight: 400;
            letter-spacing: .5px;
        }

        /* User info */
        .sidebar-user {
            padding: .9rem 1.2rem;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: .8rem;
            flex-shrink: 0;
        }
        .sidebar-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 700;
            color: #fff;
            font-size: .88rem;
            letter-spacing: .5px;
            border: 2px solid rgba(255,255,255,.15);
        }
        .sidebar-user-info .user-name {
            font-size: .82rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }
        .sidebar-user-info .user-role {
            font-size: .69rem;
            color: rgba(255,255,255,.48);
        }
        .online-dot {
            width: 8px; height: 8px;
            background: var(--accent);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(77,200,122,.3);
            animation: pulse-green 2s infinite;
        }
        @keyframes pulse-green {
            0%,100% { box-shadow: 0 0 0 0 rgba(77,200,122,.4); }
            50%      { box-shadow: 0 0 0 5px rgba(77,200,122,.0); }
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: .6rem 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.08) transparent;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        .nav-section-label {
            font-size: .64rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255,255,255,.28);
            padding: 1rem 1.4rem .3rem;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .58rem 1.4rem;
            text-decoration: none;
            color: var(--sidebar-text);
            font-size: .845rem;
            font-weight: 500;
            transition: all .2s ease;
            position: relative;
        }
        .nav-item-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .nav-item-link.active {
            background: var(--sidebar-active);
            color: var(--accent-light);
            font-weight: 600;
        }
        .nav-item-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 5px; bottom: 5px;
            width: 3px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }
        .nav-item-link .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
            color: rgba(255,255,255,.55);
        }
        .nav-item-link.active .nav-icon,
        .nav-item-link:hover .nav-icon {
            color: var(--accent-light);
        }
        .nav-item-link .nav-badge {
            margin-left: auto;
            font-size: .65rem;
            padding: .15rem .55rem;
            border-radius: 20px;
            font-weight: 600;
        }

        /* === Sidebar Count Bubble === */
        .sidebar-count {
            margin-left: auto;
            min-width: 22px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            font-size: .65rem;
            font-weight: 700;
            padding: 0 .5rem;
            letter-spacing: 0;
            transition: transform .2s;
        }
        .sidebar-count.ralan {
            background: rgba(77,200,122,.22);
            color: #6dd998;
            border: 1px solid rgba(77,200,122,.25);
        }
        .sidebar-count.ranap {
            background: rgba(96,165,250,.2);
            color: #93c5fd;
            border: 1px solid rgba(96,165,250,.25);
        }
        .sidebar-count.pulse {
            animation: count-pulse 2s infinite;
        }
        @keyframes count-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(77,200,122,.4); }
            50%       { box-shadow: 0 0 0 4px rgba(77,200,122,.0); }
        }
        .nav-item-link:hover .sidebar-count { transform: scale(1.1); }

        /* Sidebar footer */
        .sidebar-footer {
            padding: .85rem 1.2rem;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }
        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: .6rem;
            color: rgba(255,255,255,.48);
            text-decoration: none;
            font-size: .8rem;
            font-weight: 500;
            padding: .45rem .7rem;
            border-radius: 9px;
            transition: all .2s;
        }
        .sidebar-footer a:hover {
            background: rgba(220,53,69,.15);
            color: #ff8a8a;
        }
        .sidebar-footer a i { font-size: 1rem; }

        /* =======================
           MAIN CONTENT
        ======================= */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .topnav {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 0 1.75rem;
            height: 62px;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 1px 4px rgba(13,112,68,.06);
        }

        .topnav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-muted);
            cursor: pointer;
            padding: .25rem .4rem;
            border-radius: 8px;
            transition: background .2s;
        }
        .topnav-toggle:hover { background: var(--bg-body); color: var(--primary); }

        .topnav-breadcrumb { flex: 1; }
        .breadcrumb-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1;
        }
        .breadcrumb-sub { font-size: .73rem; color: var(--text-muted); margin: 0; }

        .topnav-actions { display: flex; align-items: center; gap: .75rem; }

        .topnav-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 1.05rem;
            transition: all .2s;
            position: relative;
            text-decoration: none;
        }
        .topnav-btn:hover {
            background: var(--bg-body);
            color: var(--primary);
            border-color: var(--primary);
        }
        .topnav-btn .badge-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .topnav-user {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .3rem .75rem;
            border-radius: 12px;
            cursor: pointer;
            transition: background .2s;
            text-decoration: none;
            color: var(--text-main);
            border: 1.5px solid var(--border);
        }
        .topnav-user:hover { background: var(--bg-body); border-color: var(--primary); }
        .topnav-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: .78rem;
        }
        .topnav-user-info .un { font-size: .82rem; font-weight: 600; color: var(--text-main); line-height: 1.1; }
        .topnav-user-info .ur { font-size: .7rem; color: var(--text-muted); }

        /* Page Content */
        .page-content { flex: 1; padding: 1.75rem; }

        /* =======================
           CARDS
        ======================= */
        .card-modern {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: box-shadow .25s, transform .25s;
        }
        .card-modern:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }

        /* Sidebar Overlay (mobile) */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 999;
            backdrop-filter: blur(2px);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .main-wrapper { margin-left: 0; }
            .topnav-toggle { display: flex; }
        }
        @media (max-width: 575.98px) {
            .topnav { padding: 0 1rem; }
            .page-content { padding: 1rem; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #b2dfdb; border-radius: 6px; }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ========================
     SIDEBAR
========================= --}}
<aside class="sidebar" id="sidebar">

    {{-- Brand: Logo RS Namira --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="sidebar-brand-logo">
            <img src="{{ asset('images/logo-rsnamira.jpg') }}" alt="Logo RS Namira">
        </div>
        <div class="sidebar-brand-text">
            <div class="rs-name"><span>RS</span> NAMIRA</div>
            <div class="rs-sub">Sistem Informasi Pasien</div>
        </div>
    </a>

    {{-- User Info --}}
    <div class="sidebar-user">
        <div class="sidebar-avatar">
            {{ strtoupper(substr(session('auth_user.nama', 'U'), 0, 2)) }}
        </div>
        <div class="sidebar-user-info">
            <div class="user-name">{{ session('auth_user.nama', 'Pengguna') }}</div>
            <div class="user-role">
                <span class="online-dot me-1"></span>
                @if(session('auth_user.is_admin'))
                    Admin Utama
                @elseif(session('auth_user.is_dokter'))
                    dr. {{ session('auth_user.spesialis') ? session('auth_user.spesialis') : 'Dokter' }}
                @else
                    Pengguna
                @endif
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        <div class="nav-section-label">Utama</div>
        <a href="{{ route('dashboard') }}"
           class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill nav-icon"></i>
            Dashboard
        </a>

        <div class="nav-section-label">Layanan</div>
        <a href="{{ route('rawat-jalan') }}"
           class="nav-item-link {{ request()->routeIs('rawat-jalan*') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-pulse-fill nav-icon"></i>
            <span>Rawat Jalan</span>
            @if(($sidebarCounts['rajal'] ?? 0) > 0)
            <span class="sidebar-count ralan {{ ($sidebarCounts['rajal'] ?? 0) >= 10 ? 'pulse' : '' }}">
                {{ $sidebarCounts['rajal'] }}
            </span>
            @endif
        </a>
        <a href="{{ route('rawat-inap') }}"
           class="nav-item-link {{ request()->routeIs('rawat-inap*') ? 'active' : '' }}">
            <i class="bi bi-hospital nav-icon"></i>
            <span>Rawat Inap</span>
            @if(($sidebarCounts['ranap'] ?? 0) > 0)
            <span class="sidebar-count ranap">
                {{ $sidebarCounts['ranap'] }}
            </span>
            @endif
        </a>

        <div class="nav-section-label">Penunjang Medis</div>
        <a href="#" class="nav-item-link">
            <i class="bi bi-eyedropper nav-icon"></i>
            Laboratorium
        </a>
        <a href="#" class="nav-item-link">
            <i class="bi bi-camera nav-icon"></i>
            Radiologi
        </a>
        <a href="#" class="nav-item-link">
            <i class="bi bi-capsule nav-icon"></i>
            Apotek / Farmasi
        </a>

        <div class="nav-section-label">Keuangan</div>
        <a href="#" class="nav-item-link">
            <i class="bi bi-receipt nav-icon"></i>
            Kasir & Billing
        </a>
        <a href="#" class="nav-item-link">
            <i class="bi bi-graph-up-arrow nav-icon"></i>
            Laporan Keuangan
        </a>

        <div class="nav-section-label">Master Data</div>
        <a href="#" class="nav-item-link">
            <i class="bi bi-people-fill nav-icon"></i>
            Data Pasien
        </a>
        <a href="#" class="nav-item-link">
            <i class="bi bi-person-badge-fill nav-icon"></i>
            Data Dokter
        </a>
        <a href="#" class="nav-item-link">
            <i class="bi bi-building nav-icon"></i>
            Data Pegawai
        </a>

        @if(session('auth_user.is_admin') || session('auth_user.akses.admin'))
        <div class="nav-section-label">Administrasi</div>
        <a href="#" class="nav-item-link">
            <i class="bi bi-people nav-icon"></i>
            Manajemen User
        </a>
        <a href="#" class="nav-item-link">
            <i class="bi bi-shield-check nav-icon"></i>
            Hak Akses
        </a>
        <a href="#" class="nav-item-link">
            <i class="bi bi-gear-fill nav-icon"></i>
            Pengaturan Sistem
        </a>
        @endif

    </nav>

    {{-- Logout --}}
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}" id="logoutFormSidebar">
            @csrf
            <a href="#" onclick="document.getElementById('logoutFormSidebar').submit(); return false;">
                <i class="bi bi-box-arrow-left"></i>
                <span>Keluar dari Sistem</span>
            </a>
        </form>
    </div>

</aside>

{{-- ========================
     MAIN WRAPPER
========================= --}}
<div class="main-wrapper">

    {{-- TOP NAVBAR --}}
    <nav class="topnav">
        <button class="topnav-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="topnav-breadcrumb">
            <p class="breadcrumb-title mb-0">@yield('page-title', 'Dashboard')</p>
            <p class="breadcrumb-sub mb-0">
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>

        <div class="topnav-actions">
            <a href="#" class="topnav-btn" title="Notifikasi">
                <i class="bi bi-bell"></i>
                <span class="badge-dot"></span>
            </a>
            <a href="#" class="topnav-btn" title="Pencarian">
                <i class="bi bi-search"></i>
            </a>

            <div class="dropdown">
                <a class="topnav-user dropdown-toggle"
                   href="#"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                    <div class="topnav-avatar">
                        {{ strtoupper(substr(session('auth_user.nama', 'U'), 0, 2)) }}
                    </div>
                    <div class="topnav-user-info d-none d-sm-block">
                        <div class="un">{{ session('auth_user.nama', 'Pengguna') }}</div>
                        <div class="ur">
                            @if(session('auth_user.is_admin'))
                                Admin Utama
                            @elseif(session('auth_user.is_dokter'))
                                Dokter
                            @else
                                Pengguna
                            @endif
                        </div>
                    </div>
                    <i class="bi bi-chevron-down ms-1" style="font-size:.7rem;color:var(--text-muted)"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                    style="min-width:210px;border-radius:12px;border-color:var(--border);">
                    <li>
                        <div class="px-3 py-2 border-bottom">
                            <div style="font-size:.82rem;font-weight:600;color:var(--text-main);">
                                {{ session('auth_user.nama') }}
                            </div>
                            <div style="font-size:.72rem;color:var(--text-muted);">
                                Login: {{ session('auth_user.login_at') }}
                            </div>
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="#" style="font-size:.855rem;">
                            <i class="bi bi-person-circle" style="color:var(--primary)"></i> Profil Saya
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="#" style="font-size:.855rem;">
                            <i class="bi bi-key" style="color:var(--primary)"></i> Ganti Password
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" id="logoutFormTop">
                            @csrf
                            <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="#"
                               onclick="document.getElementById('logoutFormTop').submit(); return false;"
                               style="font-size:.855rem;">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- PAGE CONTENT --}}
    <main class="page-content">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
                 role="alert" style="border-radius:12px;border:none;background:#d4edda;color:#155724;font-size:.875rem;">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
                 role="alert" style="border-radius:12px;border:none;font-size:.875rem;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle  = document.getElementById('sidebarToggle');

    function open()  { sidebar.classList.add('show'); overlay.classList.add('show'); }
    function close() { sidebar.classList.remove('show'); overlay.classList.remove('show'); }

    if (toggle)  toggle.addEventListener('click', open);
    if (overlay) overlay.addEventListener('click', close);
})();
</script>
@stack('scripts')
</body>
</html>
