@extends('admin.layout')

@section('title', 'Users Management')

@section('content')
<div class="page-header">
    <div class="header-content">
        <div class="header-text">
            <h1 class="page-title">
                <i class="fas fa-users"></i>
                Users Management
            </h1>
            <p class="page-subtitle">Manage user accounts and admin privileges</p>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $users->total() }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $users->where('is_admin', true)->count() }}</div>
                <div class="stat-label">Admins</div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Controls -->
<div class="controls-section">
    <form method="GET" action="{{ route('admin.users') }}" class="search-form">
        <div class="search-group">
            <div class="search-input-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search users by name or email..." 
                    value="{{ request('search') }}"
                    class="search-input"
                >
            </div>
            
            <select name="filter" class="filter-select">
                <option value="">All Users</option>
                <option value="admin" {{ request('filter') === 'admin' ? 'selected' : '' }}>Admins Only</option>
                <option value="regular" {{ request('filter') === 'regular' ? 'selected' : '' }}>Regular Users</option>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i>
                Filter
            </button>
            
            @if(request('search') || request('filter'))
                <a href="{{ route('admin.users') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="table-section">
    @if($users->count() > 0)
        <div class="table-wrapper">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registration</th>
                        <th>Games Stats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="user-row {{ $user->is_admin ? 'admin-user' : '' }}">
                            <td class="user-cell">
                                <div class="user-info">
                                    <div class="user-details">
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-id">#{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="email-cell">
                                <div class="email-info">
                                    <span class="email">{{ $user->email }}</span>
                                    @if($user->email_verified_at)
                                        <span class="verified-badge">
                                            <i class="fas fa-check-circle"></i>
                                            Verified
                                        </span>
                                    @else
                                        <span class="unverified-badge">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Unverified
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="role-cell">
                                @if($user->is_admin)
                                    <span class="role-badge admin-badge">
                                        <i class="fas fa-crown"></i>
                                        Admin
                                    </span>
                                @else
                                    <span class="role-badge user-badge">
                                        <i class="fas fa-user"></i>
                                        User
                                    </span>
                                @endif
                            </td>
                            
                            <td class="date-cell">
                                <div class="date-info">
                                    <div class="date">{{ $user->created_at->format('M j, Y') }}</div>
                                    <div class="time">{{ $user->created_at->diffForHumans() }}</div>
                                </div>
                            </td>
                            
                            <td class="stats-cell">
                                <div class="user-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-gamepad"></i>
                                        <span>{{ $user->gameSessions->count() }} games</span>
                                    </div>
                                    @if($user->gameSessions->count() > 0)
                                        <div class="stat-item">
                                            <i class="fas fa-trophy"></i>
                                            <span>{{ number_format($user->average_accuracy, 1) }}% avg</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" 
                                          class="toggle-form" 
                                          onsubmit="return confirmToggle('{{ $user->name }}', {{ $user->is_admin ? 'true' : 'false' }})">
                                        @csrf
                                        @if($user->is_admin)
                                            <button type="submit" class="btn btn-danger btn-sm" title="Remove Admin">
                                                <i class="fas fa-user-minus"></i>
                                                Remove Admin
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-success btn-sm" title="Make Admin">
                                                <i class="fas fa-user-plus"></i>
                                                Make Admin
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="pagination-wrapper">
                {{ $users->withQueryString()->links('pagination.custom') }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>No Users Found</h3>
            <p>
                @if(request('search') || request('filter'))
                    No users match your current search criteria. Try adjusting your filters.
                @else
                    There are no users in the system yet.
                @endif
            </p>
            @if(request('search') || request('filter'))
                <a href="{{ route('admin.users') }}" class="btn btn-primary">
                    <i class="fas fa-refresh"></i>
                    View All Users
                </a>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script src="{{ asset('resources/js/admin/admin-users.js') }}"></script>
<script>
function confirmToggle(userName, isAdmin) {
    const action = isAdmin ? 'remove admin privileges from' : 'grant admin privileges to';
    const confirmed = confirm(`Are you sure you want to ${action} "${userName}"?`);
    
    if (confirmed) {
        // Mark form as submitted to prevent loading state reset
        event.target.closest('form').classList.add('submitted');
    }
    
    return confirmed;
}

// Auto-submit search form on filter change
document.querySelector('.filter-select')?.addEventListener('change', function() {
    this.closest('form').submit();
});

// Clear search on escape key
document.querySelector('.search-input')?.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        this.value = '';
        this.closest('form').submit();
    }
});

// Add keyboard shortcut hint to search input
document.querySelector('.search-input')?.setAttribute('title', 'Press Ctrl+K to focus, Escape to clear');
</script>
@endpush
@endsection