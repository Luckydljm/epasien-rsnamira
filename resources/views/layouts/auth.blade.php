<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Login') — ePasien RS Namira</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* =============================================
           COLOR PALETTE — Identik Logo RS Namira
           Hijau tua  : #0d7044  (primary)
           Hijau sedang: #1a9958  (primary-light)
           Hijau muda : #4cc87a  (accent)
           Putih      : #ffffff
        ============================================= */
        :root {
            --primary:       #0d7044;
            --primary-light: #1a9958;
            --primary-dark:  #085c34;
            --accent:        #4cc87a;
            --accent-light:  #6dd998;
            --success:       #28a745;
            --danger:        #dc3545;
            --warning:       #ffc107;
            --surface:       #f2faf5;
            --text-main:     #1a2e1f;
            --text-muted:    #5a7a62;
            --border:        #c8e6cf;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--surface);
            color: var(--text-main);
            min-height: 100vh;
        }

        @yield('extra-styles')
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
