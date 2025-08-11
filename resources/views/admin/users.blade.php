@extends('admin.layout')

@section('title', 'Manage Users')

@section('content')
    <div class="admin-card">
        <h1>User Management</h1>
        <p>Manage user accounts and admin privileges.</p>
    </div>

    <div class="admin-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Games Played</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_admin)
                                <span class="badge badge-success">Admin</span>
                            @else
                                <span class="badge badge-secondary">User</span>
                            @endif
                        </td>
                        <td>{{ $user->gameSessions->count() }}</td>
                        <td>{{ $user->created_at->format('M j, Y') }}</td>
                        <td>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn {{ $user->is_admin ? 'btn-danger' : 'btn-success' }}">
                                        {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                    </button>
                                </form>
                            @else
                                <span class="badge badge-secondary">Current User</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $users->links() }}
    </div>
@endsection
