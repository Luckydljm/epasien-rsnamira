@extends('layouts.auth')

@section('title', 'Login')

@push('styles')
<style>
    /* =============================================
       LOGIN PAGE — ePasien RS Namira
       Warna identik logo: Hijau tua #0d7044
    ============================================= */
    html, body { height: 100%; margin: 0; }

    .login-wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* === PANEL KIRI: Branding === */
    .login-panel-left {
        flex: 0 0 52%;
        background: linear-gradient(150deg, #063d26 0%, #0d7044 40%, #1a9958 72%, #2dba6e 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 3rem 4rem;
        position: relative;
        overflow: hidden;
    }

    .login-panel-left::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 80% 20%, rgba(77,200,122,.2) 0%, transparent 70%),
            radial-gradient(ellipse 50% 60% at 10% 90%, rgba(255,255,255,.06) 0%, transparent 70%);
    }

    /* Decorative circles */
    .deco-circle {
        position: absolute;
        border-radius: 50%;
        opacity: 0.07;
    }
    .deco-circle-1 { width: 420px; height: 420px; background: #fff; top: -120px; right: -80px; }
    .deco-circle-2 { width: 280px; height: 280px; background: #4cc87a; bottom: -60px; left: -40px; }
    .deco-circle-3 { width: 150px; height: 150px; background: #6dd998; bottom: 120px; right: 40px; }

    .brand-area {
        position: relative;
        z-index: 2;
        text-align: center;
        color: #fff;
        max-width: 460px;
    }

    /* Logo image wrapper */
    .brand-logo-wrapper {
        width: 130px;
        height: 130px;
        background: rgba(255,255,255,.15);
        border: 2px solid rgba(255,255,255,.3);
        border-radius: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.75rem;
        backdrop-filter: blur(12px);
        box-shadow: 0 8px 32px rgba(0,0,0,.18);
        transition: transform .3s ease;
        overflow: hidden;
        padding: 8px;
    }
    .brand-logo-wrapper:hover { transform: scale(1.05) rotate(-1deg); }
    .brand-logo-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .brand-rs-name {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        line-height: 1.2;
        margin-bottom: .5rem;
        text-shadow: 0 2px 12px rgba(0,0,0,.2);
    }
    .brand-rs-name span { color: #a8f0c6; }

    .brand-rs-sub {
        font-size: .88rem;
        color: rgba(255,255,255,.7);
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: 500;
        margin-bottom: 2.5rem;
    }

    /* Feature badges */
    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }
    .feature-list li {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: .65rem 1rem;
        border-radius: 12px;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        margin-bottom: .6rem;
        backdrop-filter: blur(6px);
        font-size: .875rem;
        color: rgba(255,255,255,.9);
        font-weight: 450;
        transition: background .25s;
    }
    .feature-list li:hover { background: rgba(255,255,255,.14); }
    .feature-list li i {
        font-size: 1.1rem;
        color: #a8f0c6;
        flex-shrink: 0;
    }

    .brand-tagline {
        margin-top: 2.5rem;
        font-size: .78rem;
        color: rgba(255,255,255,.4);
        letter-spacing: .5px;
    }

    /* === PANEL KANAN: Form Login === */
    .login-panel-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem;
        background: #fff;
    }

    .login-form-box {
        width: 100%;
        max-width: 430px;
    }

    .login-header { margin-bottom: 2.5rem; }
    .login-header .welcome-text {
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--primary);
        margin-bottom: .4rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .login-header .welcome-text::before {
        content: '';
        display: inline-block;
        width: 18px;
        height: 3px;
        background: var(--accent);
        border-radius: 2px;
    }
    .login-header h2 {
        font-size: 1.9rem;
        font-weight: 800;
        color: var(--primary-dark);
        letter-spacing: -0.5px;
        margin-bottom: .5rem;
    }
    .login-header p {
        font-size: .875rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* Logo kecil di panel kanan (mobile) */
    .login-logo-mobile {
        display: none;
        width: 72px;
        height: 72px;
        margin: 0 auto 1.5rem;
    }
    .login-logo-mobile img { width: 100%; object-fit: contain; }

    /* Alert */
    .alert-login {
        border-radius: 12px;
        font-size: .875rem;
        font-weight: 500;
        border: none;
        padding: .85rem 1.1rem;
        display: flex;
        align-items: flex-start;
        gap: .6rem;
    }
    .alert-login i { font-size: 1.05rem; flex-shrink: 0; margin-top: 1px; }

    /* Form labels */
    .form-label {
        font-size: .78rem;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: .3px;
        margin-bottom: .45rem;
        text-transform: uppercase;
    }

    .input-group-custom { position: relative; margin-bottom: 1.3rem; }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1rem;
        z-index: 5;
        pointer-events: none;
    }

    .form-control-custom {
        width: 100%;
        padding: .875rem 1rem .875rem 2.85rem;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-family: 'Inter', sans-serif;
        font-size: .925rem;
        color: var(--text-main);
        background: #f2faf5;
        transition: all .25s ease;
        outline: none;
        appearance: none;
    }
    .form-control-custom:focus {
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(13,112,68,.1);
    }
    .form-control-custom::placeholder { color: #8aab90; }
    .form-control-custom.is-invalid {
        border-color: var(--danger);
        box-shadow: 0 0 0 4px rgba(220,53,69,.08);
    }

    .toggle-pass {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 1rem;
        z-index: 5;
        padding: 0;
        transition: color .2s;
    }
    .toggle-pass:hover { color: var(--primary); }

    /* Remember Me */
    .remember-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.75rem;
    }
    .custom-check {
        display: flex;
        align-items: center;
        gap: .5rem;
        cursor: pointer;
        font-size: .85rem;
        color: var(--text-muted);
        font-weight: 500;
    }
    .custom-check input[type=checkbox] {
        width: 17px; height: 17px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    /* Login Button — gradien hijau logo */
    .btn-login {
        width: 100%;
        padding: .95rem;
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
        border: none;
        border-radius: 14px;
        color: #fff;
        font-family: 'Inter', sans-serif;
        font-size: .95rem;
        font-weight: 700;
        letter-spacing: .3px;
        cursor: pointer;
        transition: all .3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        box-shadow: 0 6px 22px rgba(13,112,68,.38);
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(13,112,68,.5);
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    }
    .btn-login:active { transform: translateY(0); }
    .btn-login:disabled { opacity: .7; cursor: not-allowed; transform: none; }
    .btn-login .spinner-border { width: 1.1rem; height: 1.1rem; border-width: 2px; }

    /* Footer */
    .login-footer {
        text-align: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
    }
    .login-footer p { font-size: .78rem; color: var(--text-muted); margin: 0; }
    .login-footer strong { color: var(--primary); }

    .version-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .72rem;
        color: var(--text-muted);
        background: #f2faf5;
        border: 1px solid var(--border);
        padding: .2rem .7rem;
        border-radius: 20px;
        margin-top: .6rem;
        font-weight: 500;
    }

    /* Error shake */
    @keyframes shake {
        0%,100% { transform: translateX(0); }
        20%      { transform: translateX(-7px); }
        40%      { transform: translateX(7px); }
        60%      { transform: translateX(-5px); }
        80%      { transform: translateX(5px); }
    }
    .shake { animation: shake .45s ease; }

    /* Fade in */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .login-form-box { animation: fadeUp .55s ease; }

    /* Divider line */
    .info-strip {
        display: flex;
        align-items: center;
        gap: .75rem;
        background: #f2faf5;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: .65rem .9rem;
        margin-bottom: 1.4rem;
    }
    .info-strip i { color: var(--primary); font-size: 1rem; }
    .info-strip span { font-size: .8rem; color: var(--text-muted); font-weight: 500; }

    /* Responsive */
    @media (max-width: 991.98px) {
        .login-panel-left { display: none; }
        .login-panel-right { padding: 1.5rem; }
        .login-logo-mobile { display: block; }
    }
    @media (max-width: 480px) {
        .login-panel-right { padding: 1rem; }
        .login-form-box { max-width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="login-wrapper">

    {{-- ===== PANEL KIRI — Branding ===== --}}
    <div class="login-panel-left" aria-hidden="true">
        <div class="deco-circle deco-circle-1"></div>
        <div class="deco-circle deco-circle-2"></div>
        <div class="deco-circle deco-circle-3"></div>

        <div class="brand-area">
            {{-- Logo RS Namira (diputihkan di atas bg hijau) --}}
            <div class="brand-logo-wrapper">
                <img src="{{ asset('images/logo-rsnamira.jpg') }}"
                     alt="Logo RS Namira Palembang">
            </div>

            <div class="brand-rs-name">
                <span>RS</span> NAMIRA<br>
                <span style="font-size:1rem;font-weight:500;opacity:.8;letter-spacing:1.5px;">PALEMBANG</span>
            </div>
            <div class="brand-rs-sub">Sistem Informasi Pasien</div>

            <ul class="feature-list">
                <li><i class="bi bi-person-check-fill"></i> Pendaftaran & Registrasi Pasien</li>
                <li><i class="bi bi-clipboard2-pulse-fill"></i> Rekam Medis Elektronik Terintegrasi</li>
                <li><i class="bi bi-building-fill-add"></i> Manajemen Rawat Inap & Rawat Jalan</li>
                <li><i class="bi bi-cash-stack"></i> Billing & Kasir Terpadu</li>
                <li><i class="bi bi-shield-lock-fill"></i> Keamanan Data Pasien Tingkat Tinggi</li>
            </ul>

            <p class="brand-tagline">Kompatibel dengan SIMRS Khanza</p>
        </div>
    </div>

    {{-- ===== PANEL KANAN — Form Login ===== --}}
    <div class="login-panel-right">
        <div class="login-form-box">

            {{-- Logo mobile --}}
            <div class="login-logo-mobile">
                <img src="{{ asset('images/logo-rsnamira.jpg') }}" alt="RS Namira">
            </div>

            {{-- Header --}}
            <div class="login-header">
                <div class="welcome-text">Sistem Informasi Rumah Sakit</div>
                <h2>Selamat Datang</h2>
                <p>Masukkan kredensial Anda untuk mengakses sistem ePasien RS Namira.</p>
            </div>

            {{-- Info strip --}}
            <div class="info-strip">
                <i class="bi bi-info-circle-fill"></i>
                <span>Gunakan ID User dan password yang sama dengan SIMRS Khanza.</span>
            </div>

            {{-- Alert Error --}}
            @if (session('error'))
                <div class="alert-login alert alert-danger mb-4 shake" role="alert" id="loginAlert">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="alert-login alert alert-success mb-4" role="alert">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST"
                  action="{{ route('login.post') }}"
                  id="loginForm"
                  autocomplete="off"
                  novalidate>
                @csrf

                {{-- ID User --}}
                <div class="mb-1">
                    <label for="id_user" class="form-label">ID User</label>
                    <div class="input-group-custom">
                        <i class="bi bi-person input-icon"></i>
                        <input
                            type="text"
                            id="id_user"
                            name="id_user"
                            class="form-control-custom {{ $errors->has('id_user') ? 'is-invalid' : '' }}"
                            placeholder="Masukkan ID User Anda"
                            value="{{ old('id_user') }}"
                            autocomplete="username"
                            spellcheck="false"
                            required
                            autofocus
                        >
                    </div>
                    @error('id_user')
                        <div style="font-size:.8rem;color:var(--danger);margin-top:.25rem;padding-left:.25rem;">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-1">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-lock input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control-custom {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Masukkan password Anda"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-pass" id="togglePass" title="Tampilkan/Sembunyikan">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div style="font-size:.8rem;color:var(--danger);margin-top:.25rem;padding-left:.25rem;">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="remember-row">
                    <label class="custom-check">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        Ingat Saya
                    </label>
                    <span style="font-size:.8rem;color:var(--text-muted);">
                        <i class="bi bi-headset"></i> Lupa password? Hub. Admin
                    </span>
                </div>

                {{-- Tombol Login --}}
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right" id="btnIcon"></i>
                    <span id="btnText">Masuk ke Sistem</span>
                    <span class="spinner-border spinner-border-sm d-none" id="btnSpinner" role="status"></span>
                </button>

            </form>

            {{-- Footer --}}
            <div class="login-footer">
                <p>
                    <i class="bi bi-shield-check-fill" style="color:var(--primary)"></i>
                    Koneksi Anda aman &amp; terenkripsi
                </p>
                <p style="margin-top:.4rem;">
                    <strong>{{ env('RS_NAMA', 'RS Namira Palembang') }}</strong>
                </p>
                <div class="version-badge">
                    <i class="bi bi-cpu"></i>
                    ePasien v1.0 · SIMRS Khanza Compatible
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // Toggle password visibility
    const toggleBtn  = document.getElementById('togglePass');
    const passInput  = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const hidden = passInput.type === 'password';
            passInput.type = hidden ? 'text' : 'password';
            toggleIcon.className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }

    // Loading state on submit
    const form      = document.getElementById('loginForm');
    const loginBtn  = document.getElementById('loginBtn');
    const btnText   = document.getElementById('btnText');
    const btnIcon   = document.getElementById('btnIcon');
    const btnSpinner = document.getElementById('btnSpinner');

    if (form) {
        form.addEventListener('submit', function () {
            const id = document.getElementById('id_user').value.trim();
            const pw = passInput.value.trim();
            if (!id || !pw) return;

            loginBtn.disabled = true;
            btnIcon.classList.add('d-none');
            btnText.textContent = 'Memverifikasi...';
            btnSpinner.classList.remove('d-none');

            setTimeout(() => {
                loginBtn.disabled = false;
                btnIcon.classList.remove('d-none');
                btnText.textContent = 'Masuk ke Sistem';
                btnSpinner.classList.add('d-none');
            }, 8000);
        });
    }

    // Auto-dismiss alert
    const alert = document.getElementById('loginAlert');
    if (alert) {
        setTimeout(() => {
            alert.style.transition = 'opacity .5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 600);
        }, 6000);
    }
})();
</script>
@endpush
