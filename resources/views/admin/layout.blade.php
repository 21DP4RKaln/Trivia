<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Trivia Admin</title>
    <link rel="icon" href="{{ asset('image/logo.png') }}" type="image/png">
    @vite(['resources/scss/app.scss', 'resources/scss/admin/admin.scss', 'resources/scss/admin/admin-statistics.scss', 'resources/scss/admin/admin-questions.scss', 'resources/css/admin/admin-users.css', 'resources/css/admin/admin-dashboard.css', 'resources/css/admin/admin-statistics.css', 'resources/css/admin/admin-questions.css', 'resources/css/pagination.css', 'resources/js/app.js', 'resources/js/admin/admin-users.js', 'resources/js/admin/admin-dashboard.js', 'resources/js/admin/admin-questions.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
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
    </style>
</head>
<body class="admin-body">
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

    @stack('scripts')
</body>
</html>