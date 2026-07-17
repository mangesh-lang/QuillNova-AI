<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Auth') - {{ config('app.name', 'QuillNova') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons CDNs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --font-primary: 'Plus Jakarta Sans', sans-serif;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --bg-primary: #0b0f19;
            --bg-secondary: #111827;
            --glass-bg: rgba(17, 24, 39, 0.65);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-primary: #818cf8;
            --accent-glow: rgba(129, 140, 248, 0.25);
        }

        body {
            font-family: var(--font-primary);
            background: linear-gradient(135deg, #0b0f19 0%, #1e1b4b 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        /* Glassmorphism Auth Card */
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-logo {
            font-size: 2.25rem;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
            text-decoration: none;
        }
        .auth-logo i {
            color: var(--accent-primary);
            filter: drop-shadow(0 0 8px var(--accent-glow));
        }

        .form-control-custom {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: #ffffff !important;
            padding: 12px 16px;
            transition: var(--transition-smooth);
        }
        .form-control-custom:focus {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: var(--accent-primary);
            box-shadow: 0 0 10px var(--accent-glow);
            outline: none;
        }

        .btn-custom {
            background-color: var(--accent-primary);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: var(--transition-smooth);
            box-shadow: 0 4px 12px var(--accent-glow);
        }
        .btn-custom:hover {
            background-color: #6366f1;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px var(--accent-glow);
        }

        .auth-link {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-smooth);
        }
        .auth-link:hover {
            color: #ffffff;
            text-shadow: 0 0 8px var(--accent-glow);
        }

        .text-secondary-custom {
            color: var(--text-secondary);
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <a href="/" class="auth-logo">
            <i class="bi bi-rocket-takeoff-fill"></i>
            <span>QuillNova</span>
        </a>
        @yield('content')
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
