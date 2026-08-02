@extends('layouts.auth')

@section('title', 'Portal Layanan Medis — RS Namira')

@push('styles')
<style>
    /* =============================================
       FULLSCREEN PORTAL PAGE (TANPA SIDEBAR)
       ePasien RS Namira Palembang
    ============================================= */
    body {
        background: #f4faf6;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Top Navbar */
    .portal-topbar {
        background: #ffffff;
        border-bottom: 1px solid var(--border);
        padding: 0.85rem 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,.03);
    }
    .portal-brand {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        text-decoration: none;
    }
    .portal-brand img {
        height: 42px;
        width: auto;
        object-fit: contain;
    }
    .portal-brand-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--primary);
        line-height: 1.2;
        letter-spacing: -0.3px;
    }
    .portal-brand-title span {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-muted);
        display: block;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .portal-user-badge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #f2faf5;
        border: 1px solid var(--border);
        padding: 0.4rem 0.85rem;
        border-radius: 30px;
    }
    .portal-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
    }

    /* Main Container */
    .portal-main-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1.5rem;
    }
    .portal-container {
        width: 100%;
        max-width: 1100px;
    }

    /* Hero Banner */
    .portal-hero-banner {
        background: linear-gradient(135deg, #042b1b 0%, #063d26 40%, #0d7044 75%, #1a9958 100%);
        border-radius: 24px;
        padding: 2.25rem 2.5rem;
        color: #fff;
        margin-bottom: 2rem;
        box-shadow: 0 12px 32px rgba(13,112,68,.22);
        position: relative;
        overflow: hidden;
    }
    .portal-hero-banner::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
        pointer-events: none;
    }

    .portal-welcome-text {
        font-size: 1.85rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0.35rem;
    }
    .portal-sub-text {
        font-size: 0.95rem;
        color: rgba(255,255,255,.85);
        margin: 0;
    }

    .portal-pill-date {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.22);
        padding: 0.35rem 0.85rem;
        border-radius: 30px;
        font-size: 0.78rem;
        color: #e2f9ec;
        font-weight: 500;
        backdrop-filter: blur(8px);
        margin-bottom: 0.85rem;
    }

    /* Cards Grid */
    .portal-card {
        background: #ffffff;
        border: 1.5px solid var(--border);
        border-radius: 24px;
        padding: 2.25rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        overflow: hidden;
    }
    .portal-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        transition: height 0.3s ease;
    }
    .portal-card-rajal::before {
        background: linear-gradient(90deg, #10b981, #059669);
    }
    .portal-card-ranap::before {
        background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    }

    .portal-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 22px 45px rgba(13,112,68,.14);
        border-color: rgba(13,112,68,.35);
    }
    .portal-card:hover::before {
        height: 10px;
    }

    .portal-card-icon {
        width: 76px;
        height: 76px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.3rem;
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
    }
    .portal-card:hover .portal-card-icon {
        transform: scale(1.08) rotate(-4deg);
    }

    .icon-bg-rajal {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .icon-bg-ranap {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .portal-card-h2 {
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.6rem;
    }
    .portal-card-p {
        font-size: 0.88rem;
        color: var(--text-muted);
        line-height: 1.55;
        margin-bottom: 1.5rem;
    }

    .portal-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .portal-tag-item {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
    }
    .tag-rajal-style { background: #f0fdf4; color: #166534; border: 1px solid #dcfce7; }
    .tag-ranap-style { background: #f0f9ff; color: #1e40af; border: 1px solid #e0f2fe; }

    .portal-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 12px;
        font-size: 0.83rem;
        font-weight: 600;
        margin-bottom: 1.75rem;
    }
    .count-rajal-style { background: #e6f4ea; color: #0d7044; }
    .count-ranap-style { background: #e8f2ff; color: #1d4ed8; }

    .btn-action-portal {
        width: 100%;
        padding: 0.95rem 1.25rem;
        border-radius: 14px;
        font-weight: 750;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
        transition: all 0.25s ease;
        text-decoration: none;
        border: none;
    }
    .btn-action-rajal {
        background: linear-gradient(135deg, #0d7044 0%, #1a9958 100%);
        color: #fff;
        box-shadow: 0 6px 20px rgba(13,112,68,.25);
    }
    .btn-action-rajal:hover {
        background: linear-gradient(135deg, #085c34 0%, #0d7044 100%);
        color: #fff;
        box-shadow: 0 10px 28px rgba(13,112,68,.38);
        transform: translateY(-2px);
    }

    .btn-action-ranap {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: #fff;
        box-shadow: 0 6px 20px rgba(30,64,175,.25);
    }
    .btn-action-ranap:hover {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: #fff;
        box-shadow: 0 10px 28px rgba(30,64,175,.38);
        transform: translateY(-2px);
    }

    /* Footer */
    .portal-footer {
        text-align: center;
        padding: 1.25rem;
        font-size: 0.78rem;
        color: var(--text-muted);
        border-top: 1px solid var(--border);
        background: #fff;
    }
</style>
@endpush

@section('content')

{{-- TOP BAR HEADER (TANPA SIDEBAR) --}}
<header class="portal-topbar">
    <div class="container-fluid max-width-1200 d-flex align-items-center justify-content-between">
        <a href="{{ route('portal') }}" class="portal-brand">
            <img src="{{ asset('images/logo-rsnamira.jpg') }}" alt="RS Namira Logo">
            <div class="portal-brand-title">
                RS NAMIRA PALEMBANG
                <span>Sistem Informasi Pasien</span>
            </div>
        </a>

        <div class="d-flex align-items-center gap-3">
            <div class="portal-user-badge">
                <div class="portal-avatar">
                    {{ strtoupper(substr(session('auth_user.nama', 'U'), 0, 2)) }}
                </div>
                <div style="font-size: 0.83rem; font-weight: 600; color: var(--text-main);">
                    @if($isAdmin)
                        Admin Utama
                    @elseif($isDokter)
                        dr. {{ $nmDokter }}
                    @else
                        {{ session('auth_user.nama', 'Pengguna') }}
                    @endif
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" style="font-size: .78rem; font-weight: 600;">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </form>
        </div>
    </div>
</header>

{{-- MAIN CONTENT AREA --}}
<main class="portal-main-wrapper">
    <div class="portal-container">

        {{-- BANNER SAMBUTAN --}}
        <div class="portal-hero-banner">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="portal-pill-date">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</span>
                        <span class="mx-1">•</span>
                        <i class="bi bi-clock"></i>
                        <span id="liveClockDisplay">{{ \Carbon\Carbon::now()->format('H:i') }} WIB</span>
                    </div>
                    <h1 class="portal-welcome-text">
                        Selamat Datang,
                        @if($isAdmin)
                            Admin Utama
                        @elseif($isDokter)
                            dr. {{ $nmDokter }}
                        @else
                            {{ session('auth_user.nama', 'Pengguna') }}
                        @endif
                    </h1>
                    <p class="portal-sub-text">
                        Pilih modul pelayanan pasien yang ingin Anda buka untuk masuk ke Dashboard.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-white text-success px-3 py-2 rounded-pill shadow-sm font-weight-bold" style="font-size: .8rem;">
                        <i class="bi bi-shield-lock-fill me-1"></i> Pilih Layanan Untuk Melanjutkan
                    </span>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- 2 PILIHAN PORTAL UTAMA --}}
        <div class="row g-4 mb-3">

            {{-- PORTAL 1: RAWAT JALAN --}}
            <div class="col-md-6">
                <div class="portal-card portal-card-rajal">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="portal-card-icon icon-bg-rajal">
                                <i class="bi bi-clipboard2-pulse-fill"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-semibold" style="font-size: .78rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Modul Rawat Jalan
                            </span>
                        </div>

                        <h2 class="portal-card-h2">Pelayanan Rawat Jalan & Penunjang</h2>
                        <p class="portal-card-p">
                            Akses dashboard dan data pasien Rawat Jalan (Poliklinik, UGD/IGD), rekam medis elektronik (RME), serta layanan pemeriksaan penunjang medis.
                        </p>

                        <div class="portal-tags">
                            <span class="portal-tag-item tag-rajal-style"><i class="bi bi-check2 me-1"></i> Poliklinik & IGD</span>
                            <span class="portal-tag-item tag-rajal-style"><i class="bi bi-check2 me-1"></i> Rekam Medis (RME)</span>
                            <span class="portal-tag-item tag-rajal-style"><i class="bi bi-check2 me-1"></i> Penunjang Ralan</span>
                        </div>

                        <div class="portal-count-badge count-rajal-style">
                            <i class="bi bi-people-fill fs-6"></i>
                            <span>{{ $stats['rajal'] }} Pasien Terdaftar Hari Ini</span>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('portal.select', 'rajal') }}" class="btn-action-portal btn-action-rajal">
                            <span>Masuk Dashboard Rawat Jalan</span>
                            <i class="bi bi-arrow-right-circle-fill fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- PORTAL 2: RAWAT INAP --}}
            <div class="col-md-6">
                <div class="portal-card portal-card-ranap">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="portal-card-icon icon-bg-ranap">
                                <i class="bi bi-hospital"></i>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-semibold" style="font-size: .78rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Modul Rawat Inap
                            </span>
                        </div>

                        <h2 class="portal-card-h2">Pelayanan Rawat Inap & Penunjang</h2>
                        <p class="portal-card-p">
                            Akses dashboard dan pengelolaan pasien rawat inap yang sedang dirawat di bangsal/kamar, monitoring dokter DPJP, dan penunjang medis inap.
                        </p>

                        <div class="portal-tags">
                            <span class="portal-tag-item tag-ranap-style"><i class="bi bi-check2 me-1"></i> Manajemen Bangsal & Kamar</span>
                            <span class="portal-tag-item tag-ranap-style"><i class="bi bi-check2 me-1"></i> Monitoring DPJP</span>
                            <span class="portal-tag-item tag-ranap-style"><i class="bi bi-check2 me-1"></i> Penunjang Ranap</span>
                        </div>

                        <div class="portal-count-badge count-ranap-style">
                            <i class="bi bi-door-open-fill fs-6"></i>
                            <span>{{ $stats['ranap'] }} Pasien Sedang Dirawat</span>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('portal.select', 'ranap') }}" class="btn-action-portal btn-action-ranap">
                            <span>Masuk Dashboard Rawat Inap</span>
                            <i class="bi bi-arrow-right-circle-fill fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

<footer class="portal-footer">
    <strong>RS Namira Palembang</strong> · Sistem Informasi Pasien & Penunjang Medis Terpadu
</footer>

@endsection

@push('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const elem = document.getElementById('liveClockDisplay');
        if (elem) elem.textContent = `${hrs}:${mins} WIB`;
    }
    setInterval(updateClock, 10000);
</script>
@endpush
