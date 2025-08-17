@extends('admin.layout')

@section('title', 'Terms of Service Management')

@section('content')
<!-- Terms of Service Management Header -->
<div class="terms-header">
    <div class="header-content">
        <div class="header-text">
            <h1 class="page-title">
                <i class="fas fa-file-contract"></i>
                Terms of Service Management
            </h1>
            <p class="page-subtitle">Manage and update your application's terms of service</p>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $currentTerms->version ?? '1.0' }}</div>
                <div class="stat-label">Current Version</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $history->total() }}</div>
                <div class="stat-label">Total Versions</div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="terms-tabs">
    <div class="tab-nav">
        <button class="tab-btn active" data-tab="editor">
            <i class="fas fa-edit"></i>
            Editor
        </button>
        <button class="tab-btn" data-tab="preview">
            <i class="fas fa-eye"></i>
            Preview
        </button>
        <button class="tab-btn" data-tab="history">
            <i class="fas fa-history"></i>
            Version History
        </button>
    </div>
</div>

<!-- Tab Content -->
<div class="terms-content">
    <!-- Editor Tab -->
    <div id="editor-tab" class="tab-pane active">
        <div class="editor-container">
            <form method="POST" action="{{ route('admin.terms-of-service.update') }}" id="terms-form">
                @csrf
                
                <div class="editor-toolbar">
                    <div class="toolbar-group">
                        <button type="button" class="toolbar-btn" data-action="bold" title="Bold">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button type="button" class="toolbar-btn" data-action="italic" title="Italic">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button type="button" class="toolbar-btn" data-action="underline" title="Underline">
                            <i class="fas fa-underline"></i>
                        </button>
                    </div>
                    
                    <div class="toolbar-group">
                        <button type="button" class="toolbar-btn" data-action="h1" title="Heading 1">
                            <strong>H1</strong>
                        </button>
                        <button type="button" class="toolbar-btn" data-action="h2" title="Heading 2">
                            <strong>H2</strong>
                        </button>
                        <button type="button" class="toolbar-btn" data-action="h3" title="Heading 3">
                            <strong>H3</strong>
                        </button>
                    </div>
                    
                    <div class="toolbar-group">
                        <button type="button" class="toolbar-btn" data-action="ul" title="Bullet List">
                            <i class="fas fa-list-ul"></i>
                        </button>
                        <button type="button" class="toolbar-btn" data-action="ol" title="Numbered List">
                            <i class="fas fa-list-ol"></i>
                        </button>
                    </div>
                    
                    <div class="toolbar-group">
                        <button type="button" class="toolbar-btn" data-action="link" title="Insert Link">
                            <i class="fas fa-link"></i>
                        </button>
                        <button type="button" class="toolbar-btn" data-action="quote" title="Quote">
                            <i class="fas fa-quote-right"></i>
                        </button>
                    </div>
                    
                    <div class="toolbar-spacer"></div>
                    
                    <div class="autosave-indicator">
                        <i class="fas fa-circle" id="save-status"></i>
                        <span id="save-text">Saved</span>
                    </div>
                </div>
                  <div class="form-group-grid">
                    <div class="form-group">
                        <label for="version">Version</label>
                        <input type="text" id="version" name="version" 
                               value="{{ old('version', $currentTerms->version ?? '1.0') }}" 
                               placeholder="e.g., 2.1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="effective_date">Effective Date</label>
                        <input type="date" id="effective_date" name="effective_date" 
                               value="{{ old('effective_date', $currentTerms ? $currentTerms->effective_date->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                               required>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="contact-section">
                    <h3><i class="fas fa-address-book"></i> Contact Information</h3>
                    <p class="section-description">Manage the contact information displayed in the terms of service</p>
                    
                    <div class="form-group-grid">
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" id="company_name" name="company_name" 
                                   value="{{ old('company_name', $currentTerms->company_name ?? 'Trivia Game') }}" 
                                   placeholder="Company or Service Name">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" id="contact_email" name="contact_email" 
                                   value="{{ old('contact_email', $currentTerms->contact_email ?? 'support@trivia.com') }}" 
                                   placeholder="contact@example.com">
                        </div>
                    </div>
                    
                    <div class="form-group-grid">
                        <div class="form-group">
                            <label for="contact_phone">Contact Phone (Optional)</label>
                            <input type="tel" id="contact_phone" name="contact_phone" 
                                   value="{{ old('contact_phone', $currentTerms->contact_phone ?? '') }}" 
                                   placeholder="+1 (555) 123-4567">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_address">Address</label>
                            <input type="text" id="contact_address" name="contact_address" 
                                   value="{{ old('contact_address', $currentTerms->contact_address ?? 'Trivia Game') }}" 
                                   placeholder="Company Address">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="content">Terms of Service Content</label>
                    <div class="editor-wrapper">
                        <textarea id="content" name="content" 
                                  placeholder="Enter the terms of service content..." 
                                  required>{{ old('content', $currentTerms->content ?? '') }}</textarea>
                        <div class="editor-stats">
                            <span id="word-count">0 words</span>
                            <span class="separator">•</span>
                            <span id="char-count">0 characters</span>
                            <span class="separator">•</span>
                            <span id="read-time">~1 min read</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="preview-btn">
                        <i class="fas fa-eye"></i>
                        Preview Changes
                    </button>
                    
                    <button type="button" class="btn btn-info" id="reset-btn">
                        <i class="fas fa-undo"></i>
                        Reset to Current
                    </button>
                    
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        <i class="fas fa-save"></i>
                        Update Terms of Service
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Preview Tab -->
    <div id="preview-tab" class="tab-pane">
        <div class="preview-container">
            <div class="preview-header">
                <h2>Terms of Service Preview</h2>
                <div class="preview-meta">
                    <span>Version: <strong id="preview-version">{{ $currentTerms->version ?? '1.0' }}</strong></span>
                    <span>Effective: <strong id="preview-date">{{ $currentTerms ? $currentTerms->effective_date->format('M d, Y') : now()->format('M d, Y') }}</strong></span>
                </div>
            </div>
              <div class="preview-content">
                <div id="preview-text">
                    {!! nl2br(e($currentTerms->content ?? 'No content available.')) !!}
                </div>
                
                <!-- Contact Information Preview -->
                <div class="contact-info-preview">
                    <h3><i class="fas fa-envelope"></i> Contact Information</h3>
                    <p>If you have any questions about these Terms of Service, please contact us:</p>
                    <ul class="contact-list">
                        <li><strong>Email:</strong> <span id="preview-contact-email">{{ $currentTerms->contact_email ?? 'support@trivia.com' }}</span></li>
                        <li id="preview-contact-phone-item" style="{{ empty($currentTerms->contact_phone) ? 'display: none;' : '' }}">
                            <strong>Phone:</strong> <span id="preview-contact-phone">{{ $currentTerms->contact_phone ?? '' }}</span>
                        </li>
                        <li><strong>Address:</strong> <span id="preview-contact-address">{{ $currentTerms->contact_address ?? 'Trivia Game' }}</span></li>
                        <li id="preview-company-name-item" style="{{ empty($currentTerms->company_name) || $currentTerms->company_name === $currentTerms->contact_address ? 'display: none;' : '' }}">
                            <strong>Company:</strong> <span id="preview-company-name">{{ $currentTerms->company_name ?? '' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="preview-actions">
                <button type="button" class="btn btn-secondary" id="back-to-editor">
                    <i class="fas fa-arrow-left"></i>
                    Back to Editor
                </button>
                
                <button type="button" class="btn btn-success" id="approve-changes">
                    <i class="fas fa-check"></i>
                    Approve & Publish
                </button>
            </div>
        </div>
    </div>
    
    <!-- History Tab -->
    <div id="history-tab" class="tab-pane">
        <div class="history-container">
            <div class="history-header">
                <h2>Version History</h2>
                <div class="history-actions">
                    <button type="button" class="btn btn-info" id="export-history">
                        <i class="fas fa-download"></i>
                        Export History
                    </button>
                </div>
            </div>
            
            <div class="history-list">
                @if($history->count() > 0)
                    @foreach($history as $version)
                        <div class="history-item {{ $version->is_active ? 'active' : '' }}">
                            <div class="history-info">
                                <div class="version-badge">
                                    <span class="version-number">v{{ $version->version }}</span>
                                    @if($version->is_active)
                                        <span class="active-badge">Current</span>
                                    @endif
                                </div>
                                
                                <div class="version-details">
                                    <div class="version-meta">
                                        <span class="effective-date">
                                            <i class="fas fa-calendar"></i>
                                            Effective: {{ $version->effective_date->format('M d, Y') }}
                                        </span>
                                        <span class="updated-date">
                                            <i class="fas fa-clock"></i>
                                            Updated: {{ $version->created_at->format('M d, Y H:i') }}
                                        </span>
                                        <span class="updated-by">
                                            <i class="fas fa-user"></i>
                                            By: {{ $version->updatedBy->name ?? 'System' }}
                                        </span>
                                    </div>
                                    
                                    <div class="version-stats">
                                        <span class="word-count">
                                            {{ str_word_count(strip_tags($version->content)) }} words
                                        </span>
                                        <span class="read-time">
                                            ~{{ ceil(str_word_count(strip_tags($version->content)) / 250) }} min read
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="history-actions">
                                <button type="button" class="btn btn-sm btn-secondary view-version" 
                                        data-version-id="{{ $version->id }}">
                                    <i class="fas fa-eye"></i>
                                    View
                                </button>
                                
                                @if(!$version->is_active)
                                    <button type="button" class="btn btn-sm btn-info restore-version" 
                                            data-version-id="{{ $version->id }}">
                                        <i class="fas fa-undo"></i>
                                        Restore
                                    </button>
                                @endif
                                
                                <button type="button" class="btn btn-sm btn-primary compare-version" 
                                        data-version-id="{{ $version->id }}">
                                    <i class="fas fa-code-compare"></i>
                                    Compare
                                </button>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Pagination -->
                    @if($history->hasPages())
                        <div class="pagination-wrapper">
                            {{ $history->links('pagination.custom') }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h3>No Version History</h3>
                        <p>Create your first terms of service version to see history here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Version Comparison Modal -->
<div id="comparison-modal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Version Comparison</h3>
            <button type="button" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="comparison-container">
                <div class="comparison-side">
                    <h4 id="current-version-title">Current Version</h4>
                    <div id="current-version-content" class="version-content"></div>
                </div>
                <div class="comparison-side">
                    <h4 id="selected-version-title">Selected Version</h4>
                    <div id="selected-version-content" class="version-content"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close">Close</button>
            <button type="button" class="btn btn-primary" id="use-selected-version">
                Use Selected Version
            </button>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="export-modal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Export Terms of Service</h3>
            <button type="button" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="export-options">
                <div class="export-format">
                    <h4>Export Format</h4>
                    <div class="format-options">
                        <label class="format-option">
                            <input type="radio" name="export_format" value="html" checked>
                            <span class="format-icon"><i class="fab fa-html5"></i></span>
                            <span class="format-label">HTML</span>
                        </label>
                        <label class="format-option">
                            <input type="radio" name="export_format" value="markdown">
                            <span class="format-icon"><i class="fab fa-markdown"></i></span>
                            <span class="format-label">Markdown</span>
                        </label>
                        <label class="format-option">
                            <input type="radio" name="export_format" value="pdf">
                            <span class="format-icon"><i class="fas fa-file-pdf"></i></span>
                            <span class="format-label">PDF</span>
                        </label>
                        <label class="format-option">
                            <input type="radio" name="export_format" value="json">
                            <span class="format-icon"><i class="fas fa-code"></i></span>
                            <span class="format-label">JSON</span>
                        </label>
                    </div>
                </div>
                
                <div class="export-version">
                    <h4>Version to Export</h4>
                    <select id="export-version-select" class="form-control">
                        <option value="current">Current Active Version</option>
                        @foreach($history as $version)
                            <option value="{{ $version->id }}">Version {{ $version->version }} ({{ $version->effective_date->format('M d, Y') }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close">Cancel</button>
            <button type="button" class="btn btn-primary" id="export-confirm">
                <i class="fas fa-download"></i>
                Export
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Initialize analytics data for JavaScript
window.termsAnalytics = @json($analytics);
window.termsHistory = @json($history->items());
window.currentTerms = @json($currentTerms);
</script>
@endpush