<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true', 
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
    },
    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
    }
}" :class="{ 'dark-theme': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'QuillNova') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons CDNs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Custom CSS Core Design System -->
    <style>
        :root {
            --font-primary: 'Plus Jakarta Sans', sans-serif;
            --transition-smooth: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            
            /* Light Theme Variables */
            --bg-primary: #f6f8fd;
            --bg-secondary: rgba(255, 255, 255, 0.65);
            --glass-bg: rgba(255, 255, 255, 0.65);
            --glass-border: rgba(99, 102, 241, 0.08);
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --border-color: rgba(99, 102, 241, 0.08);
            --sidebar-bg: rgba(255, 255, 255, 0.7);
            --sidebar-text: #64748b;
            --sidebar-active: #ffffff;
            --sidebar-active-bg: linear-gradient(135deg, #6366f1, #4f46e5);
            --accent-primary: #6366f1;
            --accent-glow: rgba(99, 102, 241, 0.15);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.02);
            --shadow-md: 0 10px 30px rgba(99, 102, 241, 0.05), 
                        inset 0 1px 1px rgba(255, 255, 255, 0.6),
                        0 1px 2px rgba(0, 0, 0, 0.02);
            --shadow-lg: 0 20px 40px rgba(99, 102, 241, 0.1), 
                        inset 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        .dark-theme {
            /* Dark Theme Variables */
            --bg-primary: #060813;
            --bg-secondary: rgba(15, 22, 42, 0.6);
            --glass-bg: rgba(15, 22, 42, 0.6);
            --glass-border: rgba(255, 255, 255, 0.07);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.06);
            --sidebar-bg: rgba(8, 10, 20, 0.75);
            --sidebar-text: #94a3b8;
            --sidebar-active: #ffffff;
            --sidebar-active-bg: linear-gradient(135deg, #818cf8, #6366f1);
            --accent-primary: #818cf8;
            --accent-glow: rgba(129, 140, 248, 0.25);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.2);
            --shadow-md: 0 10px 30px rgba(0, 0, 0, 0.3), 
                        inset 0 1px 1px rgba(255, 255, 255, 0.06),
                        0 1px 2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 25px 50px rgba(0, 0, 0, 0.45), 
                        inset 0 1px 2px rgba(255, 255, 255, 0.1);
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            font-family: var(--font-primary);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: var(--transition-smooth);
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }

        /* Ambient Background Glowing Blobs (SaaS Trend) */
        .aurora-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(130px);
            opacity: 0.35;
            z-index: -1;
            pointer-events: none;
            transition: var(--transition-smooth);
            animation: pulse-glow 8s infinite alternate ease-in-out;
        }
        .dark-theme .aurora-blob {
            opacity: 0.25;
        }
        .blob-1 {
            top: -10%;
            left: -10%;
            width: 45vw;
            height: 45vw;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, rgba(99, 102, 241, 0) 70%);
        }
        .blob-2 {
            bottom: -10%;
            right: -10%;
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0) 70%);
            animation-delay: -3s;
        }
        .blob-3 {
            top: 35%;
            left: 45%;
            width: 35vw;
            height: 35vw;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.15) 0%, rgba(236, 72, 153, 0) 70%);
            animation-delay: -5s;
        }
        @keyframes pulse-glow {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.15) translate(3%, 3%); }
        }

        /* 3D Frosted Glassmorphism Cards */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), 
                        box-shadow 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), 
                        border-color 0.3s;
        }
        .glass-card:hover {
            /* 3D Springy lift and hover float effect */
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--shadow-lg);
            border-color: rgba(99, 102, 241, 0.35);
        }
        .dark-theme .glass-card:hover {
            border-color: rgba(129, 140, 248, 0.35);
        }

        /* 3D Capsule-styled Progress Bars */
        .progress {
            background: rgba(0, 0, 0, 0.05) !important;
            border-radius: 20px !important;
            height: 8px !important;
            overflow: visible !important;
            border: 1px solid var(--glass-border);
        }
        .dark-theme .progress {
            background: rgba(255, 255, 255, 0.05) !important;
        }
        .progress-bar {
            border-radius: 20px !important;
            box-shadow: 0 0 12px var(--accent-glow);
            background: linear-gradient(90deg, var(--accent-primary), #818cf8) !important;
        }

        /* Premium 3D Gradient Buttons */
        .btn-primary, .btn-custom {
            background: linear-gradient(135deg, var(--accent-primary), #4f46e5) !important;
            border: none !important;
            border-radius: 12px !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            padding: 10px 20px !important;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3), 
                        inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), 
                        box-shadow 0.3s, 
                        filter 0.2s !important;
        }
        .btn-primary:hover, .btn-custom:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.45), 
                        inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
            filter: brightness(1.05);
        }
        .btn-primary:active, .btn-custom:active {
            transform: translateY(1px) scale(0.98);
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        /* Sleek Outline Buttons */
        .btn-outline-secondary {
            border: 1px solid var(--glass-border) !important;
            border-radius: 12px !important;
            transition: var(--transition-smooth);
            background: rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(5px);
        }
        .dark-theme .btn-outline-secondary {
            background: rgba(255, 255, 255, 0.03) !important;
        }
        .btn-outline-secondary:hover {
            background-color: var(--accent-primary) !important;
            color: #ffffff !important;
            border-color: var(--accent-primary) !important;
            transform: translateY(-2px);
        }

        /* Layout Structure */
        #app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Modern Frosted Sidebar */
        #sidebar {
            width: 260px;
            background: var(--sidebar-bg) !important;
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            color: var(--sidebar-text);
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            border-right: 1px solid var(--glass-border) !important;
            box-shadow: 10px 0 40px rgba(0, 0, 0, 0.02) !important;
            flex-shrink: 0;
            height: 100vh;
            position: sticky;
            top: 0;
        }
        .dark-theme #sidebar {
            box-shadow: 10px 0 40px rgba(0, 0, 0, 0.25) !important;
        }
        #sidebar.collapsed {
            width: 80px;
        }
        .sidebar-brand {
            padding: 20px;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-primary);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 12px;
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar-brand i {
            font-size: 1.5rem;
            color: var(--accent-primary);
        }
        .sidebar-menu {
            list-style: none;
            padding: 15px 10px;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }
        .sidebar-item {
            margin-bottom: 6px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 10px;
            transition: var(--transition-smooth);
            gap: 15px;
            white-space: nowrap;
        }
        .sidebar-link:hover {
            color: var(--text-primary);
            background-color: rgba(99, 102, 241, 0.06) !important;
            transform: translateX(4px);
        }
        .dark-theme .sidebar-link:hover {
            background-color: rgba(129, 140, 248, 0.06) !important;
        }
        .sidebar-item.active .sidebar-link {
            color: #ffffff !important;
            background: var(--sidebar-active-bg) !important;
            transform: scale(1.03) translateX(2px);
            box-shadow: 0 4px 20px var(--accent-glow) !important;
        }
        .sidebar-link i {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }
        .sidebar-text {
            transition: var(--transition-smooth);
            opacity: 1;
        }
        #sidebar.collapsed .sidebar-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        /* Main Content Panel */
        #content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Top Navbar */
        .navbar-main {
            height: 70px;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            transition: var(--transition-smooth);
            position: relative;
            z-index: 1010;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-theme-toggle, .btn-notify {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--glass-border);
            background: transparent;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition-smooth);
        }
        .btn-theme-toggle:hover, .btn-notify:hover {
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--accent-primary);
            border-color: var(--accent-primary);
            transform: scale(1.08) rotate(15deg);
        }

        /* Inner Page Layout */
        .page-content {
            padding: 24px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 24px;
        }
        .page-title {
            font-size: 1.85rem;
            font-weight: 800;
            margin-bottom: 4px;
            letter-spacing: -0.03em;
        }
        .page-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        /* Custom Theme helpers */
        .text-muted-custom {
            color: var(--text-secondary) !important;
        }
        .border-custom {
            border-color: var(--border-color) !important;
        }

        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, var(--border-color) 25%, var(--glass-border) 50%, var(--border-color) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Global customized Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        /* SweetAlert Glassmorphism Overrides */
        .swal2-popup {
            background: var(--bg-secondary) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--glass-border) !important;
            border-radius: 18px !important;
            backdrop-filter: blur(15px);
        }
        .swal2-title, .swal2-html-container {
            color: var(--text-primary) !important;
        }

        /* Navbar search element */
        .navbar-search {
            display: flex;
            align-items: center;
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid var(--glass-border);
            padding: 6px 14px;
            border-radius: 99px;
            width: 320px;
            transition: var(--transition-smooth);
        }
        .dark-theme .navbar-search {
            background: rgba(255, 255, 255, 0.03);
        }
        .navbar-search:focus-within {
            border-color: var(--accent-primary);
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.15);
        }

        /* Prevent dropdown menus from inheriting lift & scale transform transitions */
        .dropdown-menu.glass-card {
            transform: none !important;
            transition: none !important;
            z-index: 1100 !important;
        }
        .dropdown-menu.glass-card:hover {
            transform: none !important;
            box-shadow: var(--shadow-md) !important;
            border-color: var(--glass-border) !important;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Trendy Glowing Background Blobs -->
    <div class="aurora-blob blob-1"></div>
    <div class="aurora-blob blob-2"></div>
    <div class="aurora-blob blob-3"></div>

    <div id="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside id="sidebar" :class="{ 'collapsed': sidebarCollapsed }">
            <div class="sidebar-brand">
                <i class="bi bi-rocket-takeoff-fill text-indigo"></i>
                <span class="sidebar-text fw-extrabold text-primary" style="background: linear-gradient(135deg, var(--accent-primary), #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">QuillNova</span>
            </div>
            
            <ul class="sidebar-menu">
                @if(auth()->user()->isSuperAdmin())
                    <!-- Super Admin Menu -->
                    <li class="sidebar-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                            <i class="bi bi-speedometer2"></i>
                            <span class="sidebar-text">Admin Panel</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin.users') ? 'active' : '' }}">
                        <a href="{{ route('admin.users') }}" class="sidebar-link">
                            <i class="bi bi-people-fill"></i>
                            <span class="sidebar-text">Manage Users</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin.templates.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.templates.index') }}" class="sidebar-link">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            <span class="sidebar-text">Templates & Cats</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin.settings') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings') }}" class="sidebar-link">
                            <i class="bi bi-gear-fill"></i>
                            <span class="sidebar-text">System Settings</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Request::routeIs('admin.logs') ? 'active' : '' }}">
                        <a href="{{ route('admin.logs') }}" class="sidebar-link">
                            <i class="bi bi-shield-lock-fill"></i>
                            <span class="sidebar-text">Activity Logs</span>
                        </a>
                    </li>
                    <li class="hr my-3 border-secondary border-opacity-25"></li>
                @endif

                <!-- Standard User Menu -->
                <li class="sidebar-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="sidebar-link">
                        <i class="bi bi-columns-gap"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item {{ Request::routeIs('templates.index') || Request::routeIs('templates.show') ? 'active' : '' }}">
                    <a href="{{ route('templates.index') }}" class="sidebar-link">
                        <i class="bi bi-lightning-fill"></i>
                        <span class="sidebar-text">AI Templates</span>
                    </a>
                </li>
                <li class="sidebar-item {{ Request::routeIs('blog.index') ? 'active' : '' }}">
                    <a href="{{ route('blog.index') }}" class="sidebar-link">
                        <i class="bi bi-journal-richtext"></i>
                        <span class="sidebar-text">Blog Generator</span>
                    </a>
                </li>
                <li class="sidebar-item {{ Request::routeIs('email.index') ? 'active' : '' }}">
                    <a href="{{ route('email.index') }}" class="sidebar-link">
                        <i class="bi bi-envelope-at-fill"></i>
                        <span class="sidebar-text">Email Writer</span>
                    </a>
                </li>
                <li class="sidebar-item {{ Request::routeIs('chat.index') || Request::routeIs('chat.show') ? 'active' : '' }}">
                    <a href="{{ route('chat.index') }}" class="sidebar-link">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span class="sidebar-text">AI Interactive Chat</span>
                    </a>
                </li>
                <li class="sidebar-item {{ Request::routeIs('history.index') ? 'active' : '' }}">
                    <a href="{{ route('history.index') }}" class="sidebar-link">
                        <i class="bi bi-clock-history"></i>
                        <span class="sidebar-text">Content History</span>
                    </a>
                </li>
                <li class="sidebar-item {{ Request::routeIs('notifications.index') ? 'active' : '' }}">
                    <a href="{{ route('notifications.index') }}" class="sidebar-link d-flex justify-content-between align-items-center w-100">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-bell-fill"></i>
                            <span class="sidebar-text">Notifications</span>
                        </div>
                        @php
                            $unreadCount = auth()->user()->unreadNotifications->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill px-2 py-1 sidebar-text" style="font-size: 0.65rem;">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="sidebar-item {{ Request::routeIs('profile.edit') ? 'active' : '' }}">
                    <a href="{{ route('profile.edit') }}" class="sidebar-link">
                        <i class="bi bi-person-fill-gear"></i>
                        <span class="sidebar-text">My Profile</span>
                    </a>
                </li>
            </ul>
            


        </aside>

        <!-- Page Content View Wrapper -->
        <main id="content-wrapper">
            <!-- Top Navbar -->
            <nav class="navbar-main">
                <!-- Sidebar Toggle & Search -->
                <div class="d-flex align-items-center gap-3">
                    <button class="btn border-0 text-primary p-0 fs-4" @click="toggleSidebar()">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="navbar-search d-none d-md-flex">
                        <i class="bi bi-search text-muted-custom me-2" style="font-size: 0.85rem;"></i>
                        <input type="text" class="bg-transparent border-0 text-primary w-100" style="font-size: 0.8rem; outline: none;" placeholder="Search templates, history or anything...">
                        <span class="badge bg-secondary border border-custom text-muted-custom rounded px-1.5 py-0.5 ms-2" style="font-size: 0.65rem;">Ctrl + K</span>
                    </div>
                </div>

                <!-- Navbar Actions -->
                <div class="navbar-actions">
                    <!-- Notifications Dropdown Indicator -->
                    <a href="{{ route('notifications.index') }}" class="btn-notify text-decoration-none">
                        <div class="position-relative">
                            <i class="bi bi-bell"></i>
                            @if(auth()->user()->unreadNotifications->isNotEmpty())
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="padding: 4px; font-size: 0px;">
                                    <span class="visually-hidden">Unread alerts</span>
                                </span>
                            @endif
                        </div>
                    </a>

                    <!-- Theme Switcher -->
                    <button class="btn-theme-toggle" @click="toggleTheme()">
                        <i class="bi" :class="darkMode ? 'bi-sun-fill text-warning' : 'bi-moon-stars-fill'"></i>
                    </button>

                    <!-- User Dropdown Profile -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2 text-primary" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(auth()->user()->profile && auth()->user()->profile->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->profile->avatar) }}" alt="Avatar" width="32" height="32" class="rounded-circle">
                            @else
                                <div class="bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: bold; background-color: var(--accent-primary);">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                            @endif
                            <span class="d-none d-sm-inline fw-semibold text-primary">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end glass-card border-custom p-2 mt-2" style="background: var(--bg-secondary);">
                            <li><a class="dropdown-item rounded py-2 text-primary" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> My Profile</a></li>
                            <li><a class="dropdown-item rounded py-2 text-primary" href="{{ route('history.index') }}"><i class="bi bi-clock-history me-2"></i> Content History</a></li>
                            <li><hr class="dropdown-divider border-custom"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item rounded py-2 text-danger w-100 text-start" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Log Out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Inner Page Yield -->
            <div class="page-content">
                @if(session('success'))
                    <script>
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    </script>
                @endif
                @if(session('error'))
                    <script>
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: "{{ session('error') }}",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    </script>
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @yield('scripts')
</body>
</html>
