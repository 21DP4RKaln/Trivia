<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Trivia Admin</title>
    <link rel="icon" href="{{ asset('image/logo.png') }}" type="image/png">
    @vite(['resources/scss/app.scss', 'resources/scss/admin/admin.scss', 'resources/scss/admin/admin-statistics.scss', 'resources/scss/admin/admin-questions.scss', 'resources/scss/admin/terms-of-service.scss', 'resources/css/admin/admin-users.css', 'resources/css/admin/admin-dashboard.css', 'resources/css/admin/admin-statistics.css', 'resources/css/admin/admin-questions.css', 'resources/css/admin/terms-of-service.css', 'resources/css/pagination.css', 'resources/js/app.js', 'resources/js/admin/admin-users.js', 'resources/js/admin/admin-dashboard.js', 'resources/js/admin/admin-questions.js', 'resources/js/admin/terms-of-service.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    @stack('styles')
    
    <!-- Additional styling to ensure buttons are visible -->
    <style>
        .admin-nav .user-actions .btn-ghost.btn-sm {
            background: rgba(255, 255, 255, 0.1) !important;
            color: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            min-width: 40px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .admin-nav .user-actions .btn-ghost.btn-sm:hover {
            background: rgba(255, 255, 255, 0.25) !important;
            color: white !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
            transform: translateY(-1px) !important;
        }
        
        .admin-nav .user-actions .btn-ghost.btn-sm i {
            font-size: 0.9rem !important;
            opacity: 0.95 !important;
        }
        
        .admin-nav .user-actions .btn-ghost.btn-sm:hover i {
            opacity: 1 !important;
        }
        
        /* Scroll Progress Indicator */
        .scroll-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 1001;
            opacity: 0;
            transform: translateY(-4px);
            transition: all 0.3s ease;
        }
        
        .scroll-indicator.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .scroll-progress {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #3b82f6, #8b5cf6);
            width: 0%;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .scroll-progress::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite ease-in-out;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
    </style>
</head>
<body class="admin-body global-page">
    <!-- Global Background -->
    <div class="global-background"></div>
    
    <!-- Global Particles -->
    <div class="global-particles">
        <div class="global-particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="global-particle" style="left: 20%; animation-delay: 2s;"></div>
        <div class="global-particle" style="left: 30%; animation-delay: 4s;"></div>
        <div class="global-particle" style="left: 40%; animation-delay: 6s;"></div>
        <div class="global-particle" style="left: 50%; animation-delay: 8s;"></div>
        <div class="global-particle" style="left: 60%; animation-delay: 10s;"></div>
        <div class="global-particle" style="left: 70%; animation-delay: 12s;"></div>
        <div class="global-particle" style="left: 80%; animation-delay: 14s;"></div>
        <div class="global-particle" style="left: 90%; animation-delay: 16s;"></div>
    </div>
    
    <!-- Scroll Progress Indicator -->
    <div class="scroll-indicator" id="scrollIndicator">
        <div class="scroll-progress" id="scrollProgress"></div>
    </div>

    <!-- Navigation -->
    <nav class="admin-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <img src="{{ asset('image/logo.png') }}" alt="Trivia Logo" class="nav-logo">
                <h1>Admin Panel</h1>
            </div>
            
            <div class="nav-links">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    Users
                </a>
                <a href="{{ route('admin.statistics') }}" class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    Statistics
                </a>
                <a href="{{ route('admin.questions') }}" class="nav-link {{ request()->routeIs('admin.questions') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    Questions
                </a>
                <a href="{{ route('admin.terms-of-service') }}" class="nav-link {{ request()->routeIs('admin.terms-of-service') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i>
                    Terms of Service
                </a>
            </div>
            
            <div class="nav-user">
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </div>
                <div class="user-actions">
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">
                        <i class="fas fa-home"></i>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-container">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        // Scroll progress indicator with enhanced animations
        let scrollIndicator = null;
        let scrollProgress = null;
        let ticking = false;

        function updateScrollProgress() {
            if (scrollProgress) {
                const scrollTop = document.documentElement.scrollTop;
                const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                
                if (scrollHeight > 0) {
                    const scrollPercentage = (scrollTop / scrollHeight) * 100;
                    scrollProgress.style.width = scrollPercentage + '%';
                    
                    // Show/hide indicator based on scroll position
                    if (scrollTop > 100) {
                        scrollIndicator?.classList.add('visible');
                    } else {
                        scrollIndicator?.classList.remove('visible');
                    }
                } else {
                    scrollProgress.style.width = '0%';
                    scrollIndicator?.classList.remove('visible');
                }
            }
            ticking = false;
        }

        function requestScrollUpdate() {
            if (!ticking) {
                requestAnimationFrame(updateScrollProgress);
                ticking = true;
            }
        }

        // Initialize scroll progress on page load
        document.addEventListener('DOMContentLoaded', () => {
            scrollIndicator = document.getElementById('scrollIndicator');
            scrollProgress = document.getElementById('scrollProgress');
            
            // Add scroll listener with throttling
            window.addEventListener('scroll', requestScrollUpdate, { passive: true });
            
            // Initial calculation
            setTimeout(updateScrollProgress, 100);
        });
    </script>

    <!-- Global Loader Overlay for Admin -->
    <x-loader 
        overlay="true" 
        id="admin-loader-overlay" 
        size="medium"
        class="loader-hidden"
    />

    <script src="{{ asset('js/loader.js') }}"></script>
    @stack('scripts')
</body>
</html>