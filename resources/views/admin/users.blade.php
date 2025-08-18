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
            <table class="users-table compact-table">
                <thead>
                    <tr>
                        <th class="user-col">User</th>
                        <th class="role-col">Role</th>
                        <th class="status-col">Status</th>
                        <th class="date-col">Joined</th>
                        <th class="stats-col">Stats</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="user-row {{ $user->is_admin ? 'admin-user' : '' }} {{ $user->isBanned() ? 'banned-user' : '' }}">
                            <td class="user-cell" data-label="User">
                                <div class="user-info">
                                    <div class="user-details">
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-meta">
                                            <span class="user-id">#{{ $user->id }}</span>
                                            <span class="user-email">{{ $user->email }}</span>
                                            @if($user->email_verified_at)
                                                <span class="verified-badge mini">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            @else
                                                <span class="unverified-badge mini">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="role-cell" data-label="Role">
                                @if($user->is_admin)
                                    <span class="role-badge admin-badge">
                                        <i class="fas fa-crown"></i>
                                        ADMIN
                                    </span>
                                @else
                                    <span class="role-badge user-badge">
                                        <i class="fas fa-user"></i>
                                        USER
                                    </span>
                                @endif
                            </td>
                            
                            <td class="status-cell" data-label="Status">
                                @if($user->isBanned())
                                    @if($user->hasPermanentBan())
                                        <span class="status-badge banned-badge">
                                            <i class="fas fa-ban"></i>
                                            BANNED
                                        </span>
                                    @else
                                        <span class="status-badge temp-banned-badge">
                                            <i class="fas fa-clock"></i>
                                            UNTIL {{ $user->ban_expires_at->format('M j, Y') }}
                                        </span>
                                    @endif
                                    @if($user->ban_reason)
                                        <div class="ban-reason-mini" title="{{ $user->ban_reason }}">
                                            {{ Str::limit($user->ban_reason, 30) }}
                                        </div>
                                    @endif
                                @else
                                    <span class="status-badge active-badge">
                                        <i class="fas fa-check"></i>
                                        ACTIVE
                                    </span>
                                @endif
                            </td>
                            
                            <td class="date-cell" data-label="Joined">
                                <div class="date-info-compact">
                                    <div class="date">{{ $user->created_at->format('M j, Y') }}</div>
                                    <div class="time-ago">{{ $user->created_at->diffForHumans() }}</div>
                                </div>
                            </td>
                            
                            <td class="stats-cell" data-label="Stats">
                                <div class="user-stats-compact">
                                    <div class="stat-compact">
                                        <i class="fas fa-gamepad"></i>
                                        {{ $user->gameSessions->count() }}
                                    </div>
                                    @if($user->gameSessions->count() > 0)
                                        <div class="stat-compact">
                                            <i class="fas fa-trophy"></i>
                                            {{ number_format($user->average_accuracy, 1) }}%
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="actions-cell" data-label="Actions">
                                <div class="action-buttons-compact">
                                    <!-- Email Verification Actions -->
                                    @if(!$user->email_verified_at)
                                        <form method="POST" action="{{ route('admin.users.send-verification', $user) }}" 
                                              class="verification-form inline-form">
                                            @csrf
                                            <button type="submit" class="btn-icon btn-info" title="Send Verification Email">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Admin Toggle Actions -->
                                    @if($user->id !== Auth::id())
                                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" 
                                              class="toggle-form inline-form" 
                                              onsubmit="return confirmToggle('{{ $user->name }}', {{ $user->is_admin ? 'true' : 'false' }})">
                                            @csrf
                                            @if($user->is_admin)
                                                <button type="submit" class="btn-icon btn-danger" title="Remove Admin">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn-icon btn-success" title="Make Admin">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            @endif
                                        </form>
                                    @endif

                                    <!-- Ban/Unban Actions -->
                                    @if($user->id !== Auth::id() && !$user->is_admin)
                                        @if($user->isBanned())
                                            <form method="POST" action="{{ route('admin.users.unban', $user) }}" 
                                                  class="unban-form inline-form" 
                                                  onsubmit="return confirm('Are you sure you want to unban {{ $user->name }}?')">
                                                @csrf
                                                <button type="submit" class="btn-icon btn-warning" title="Unban User">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.users.ban-form', $user) }}" 
                                               class="btn-icon btn-outline" 
                                               title="Ban User">
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        @endif
                                    @endif
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