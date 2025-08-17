@extends('admin.layout')

@section('title', 'Ban User')

@section('content')
<div class="admin-content">
    <div class="admin-header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-ban"></i>
                </div>
                <div class="header-text">
                    <h1 class="page-title">Ban User</h1>
                    <p class="page-subtitle">Restrict access for {{ $user->name }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.users') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="ban-form-container">
        <div class="ban-form-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-user-slash"></i>
                    Ban {{ $user->name }}
                </h2>
                <div class="user-info">
                    <span class="user-email">{{ $user->email }}</span>
                    <span class="user-joined">Joined {{ $user->created_at->format('M j, Y') }}</span>
                </div>
            </div>

            <div class="card-content">
                <form action="{{ route('admin.users.ban', $user) }}" method="POST" class="ban-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="ban_reason" class="form-label">
                            <i class="fas fa-exclamation-circle"></i>
                            Reason for Ban <span class="required">*</span>
                        </label>
                        <textarea 
                            id="ban_reason" 
                            name="ban_reason" 
                            class="form-textarea @error('ban_reason') error @enderror"
                            rows="4"
                            placeholder="Please provide a detailed reason for this ban..."
                            required
                        >{{ old('ban_reason') }}</textarea>
                        @error('ban_reason')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock"></i>
                            Ban Duration <span class="required">*</span>
                        </label>
                        <div class="ban-type-options">
                            <div class="radio-option">
                                <input 
                                    type="radio" 
                                    id="permanent" 
                                    name="ban_type" 
                                    value="permanent"
                                    {{ old('ban_type', 'permanent') === 'permanent' ? 'checked' : '' }}
                                >
                                <label for="permanent" class="radio-label">
                                    <span class="radio-button"></span>
                                    <div class="radio-content">
                                        <strong>Permanent Ban</strong>
                                        <small>User will be banned indefinitely</small>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="radio-option">
                                <input 
                                    type="radio" 
                                    id="temporary" 
                                    name="ban_type" 
                                    value="temporary"
                                    {{ old('ban_type') === 'temporary' ? 'checked' : '' }}
                                >
                                <label for="temporary" class="radio-label">
                                    <span class="radio-button"></span>
                                    <div class="radio-content">
                                        <strong>Temporary Ban</strong>
                                        <small>User will be automatically unbanned after specified time</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @error('ban_type')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group duration-group" style="display: {{ old('ban_type') === 'temporary' ? 'block' : 'none' }};">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            Duration
                        </label>
                        <div class="duration-inputs">
                            <input 
                                type="number" 
                                id="ban_duration" 
                                name="ban_duration" 
                                class="form-input duration-number @error('ban_duration') error @enderror"
                                value="{{ old('ban_duration', 1) }}"
                                min="1"
                                max="365"
                                placeholder="1"
                            >
                            <select 
                                id="ban_duration_unit" 
                                name="ban_duration_unit" 
                                class="form-select duration-unit @error('ban_duration_unit') error @enderror"
                            >
                                <option value="hours" {{ old('ban_duration_unit', 'days') === 'hours' ? 'selected' : '' }}>Hours</option>
                                <option value="days" {{ old('ban_duration_unit', 'days') === 'days' ? 'selected' : '' }}>Days</option>
                                <option value="weeks" {{ old('ban_duration_unit', 'days') === 'weeks' ? 'selected' : '' }}>Weeks</option>
                                <option value="months" {{ old('ban_duration_unit', 'days') === 'months' ? 'selected' : '' }}>Months</option>
                            </select>
                        </div>
                        @error('ban_duration')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        @error('ban_duration_unit')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="warning-box">
                        <div class="warning-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="warning-content">
                            <strong>Warning:</strong> This action will immediately ban the user and log them out of all sessions. 
                            They will not be able to access their account until the ban is lifted.
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-danger ban-submit">
                            <i class="fas fa-ban"></i>
                            Ban User
                        </button>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.ban-form-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 2rem;
}

.ban-form-card {
    background: linear-gradient(135deg, var(--glass-bg-enhanced), rgba(255, 255, 255, 0.06));
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border-enhanced);
    border-radius: var(--border-radius-xl);
    box-shadow: var(--glass-shadow-enhanced);
    overflow: hidden;
}

.card-header {
    padding: 2rem 2.5rem 1.5rem;
    border-bottom: 1px solid var(--glass-border);
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
}

.card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-title i {
    color: #ef4444;
}

.user-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.user-email {
    font-size: 1rem;
    color: var(--text-primary);
    font-weight: 600;
}

.user-joined {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.card-content {
    padding: 2.5rem;
}

.ban-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.form-label {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.required {
    color: #ef4444;
}

.form-textarea,
.form-input,
.form-select {
    padding: 1rem;
    background: var(--admin-surface);
    border: 1px solid var(--glass-border);
    border-radius: var(--border-radius);
    color: var(--text-primary);
    font-size: 1rem;
    transition: all var(--transition-fast) ease;
    resize: vertical;
}

.form-textarea {
    min-height: 120px;
    font-family: inherit;
}

.form-textarea:focus,
.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: var(--emerald-500);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-textarea.error,
.form-input.error,
.form-select.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.ban-type-options {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.radio-option {
    position: relative;
}

.radio-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.radio-label {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: var(--admin-surface);
    border: 1px solid var(--glass-border);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all var(--transition-fast) ease;
}

.radio-label:hover {
    border-color: var(--emerald-500);
    background: var(--admin-surface-hover);
}

.radio-option input[type="radio"]:checked + .radio-label {
    border-color: var(--emerald-500);
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
}

.radio-button {
    width: 20px;
    height: 20px;
    border: 2px solid var(--glass-border);
    border-radius: 50%;
    position: relative;
    transition: all var(--transition-fast) ease;
}

.radio-option input[type="radio"]:checked + .radio-label .radio-button {
    border-color: var(--emerald-500);
}

.radio-option input[type="radio"]:checked + .radio-label .radio-button::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: var(--emerald-500);
    border-radius: 50%;
}

.radio-content {
    flex: 1;
}

.radio-content strong {
    display: block;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.radio-content small {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.duration-inputs {
    display: flex;
    gap: 1rem;
}

.duration-number {
    flex: 1;
    max-width: 150px;
}

.duration-unit {
    flex: 2;
    min-width: 120px;
}

.warning-box {
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: var(--border-radius);
}

.warning-icon {
    flex-shrink: 0;
    color: #f59e0b;
    font-size: 1.25rem;
}

.warning-content {
    color: var(--text-primary);
    line-height: 1.5;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1px solid var(--glass-border);
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: 1px solid transparent;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.error-message {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

@media (max-width: 768px) {
    .ban-form-container {
        padding: 0 1rem;
    }

    .card-header,
    .card-content {
        padding: 1.5rem;
    }

    .duration-inputs {
        flex-direction: column;
    }

    .form-actions {
        flex-direction: column-reverse;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const banTypeRadios = document.querySelectorAll('input[name="ban_type"]');
    const durationGroup = document.querySelector('.duration-group');
    
    banTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'temporary') {
                durationGroup.style.display = 'block';
            } else {
                durationGroup.style.display = 'none';
            }
        });
    });

    // Form validation
    const form = document.querySelector('.ban-form');
    const submitButton = document.querySelector('.ban-submit');
    
    form.addEventListener('submit', function(e) {
        const reason = document.getElementById('ban_reason').value.trim();
        const banType = document.querySelector('input[name="ban_type"]:checked').value;
        
        if (!reason) {
            e.preventDefault();
            alert('Please provide a reason for the ban.');
            return;
        }
        
        if (banType === 'temporary') {
            const duration = document.getElementById('ban_duration').value;
            if (!duration || duration < 1) {
                e.preventDefault();
                alert('Please specify a valid duration for the temporary ban.');
                return;
            }
        }
        
        // Confirm the action
        const confirmMessage = banType === 'permanent' 
            ? 'Are you sure you want to permanently ban this user? This action will immediately log them out.'
            : 'Are you sure you want to temporarily ban this user? This action will immediately log them out.';
            
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return;
        }
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Banning User...';
    });
});
</script>
@endsection
