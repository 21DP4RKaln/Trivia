// Modern Terms of Service Admin - Enhanced JavaScript

class ModernTermsOfService {
  constructor() {
    this.currentTab = 'editor';
    this.autosaveInterval = null;
    this.lastSaveTime = Date.now();
    this.isDirty = false;
    this.wordCount = 0;
    this.fabMenuOpen = false;

    this.init();
  }

  init() {
    this.initializeTabs();
    this.initializeEditor();
    this.initializeAutosave();
    this.initializeFAB();
    this.initializeAnimations();
    this.initializeKeyboardShortcuts();
    this.updateWordCount();

    // Initialize Chart.js for analytics
    if (typeof Chart !== 'undefined') {
      this.initializeChart();
    }

    // Enhanced initialization
    this.initializeAllFeatures();

    console.log('Modern Terms of Service initialized');
  }

  // Tab Management
  initializeTabs() {
    const tabButtons = document.querySelectorAll('.modern-tab-button');
    const tabContents = document.querySelectorAll('.modern-tab-content');

    tabButtons.forEach(button => {
      button.addEventListener('click', e => {
        const tabId = button.dataset.tab;
        this.switchTab(tabId, tabButtons, tabContents);
      });
    });
  }

  switchTab(tabId, tabButtons, tabContents) {
    if (this.currentTab === tabId) return;

    // Remove active class from all tabs
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));

    // Add active class to selected tab
    const activeButton = document.querySelector(`[data-tab="${tabId}"]`);
    const activeContent = document.querySelector(
      `.modern-tab-content[data-tab="${tabId}"]`
    );

    if (activeButton && activeContent) {
      activeButton.classList.add('active');
      activeContent.classList.add('active');
      this.currentTab = tabId;

      // Trigger tab-specific initialization
      this.onTabSwitch(tabId);
    }
  }

  onTabSwitch(tabId) {
    switch (tabId) {
      case 'analytics':
        this.refreshAnalytics();
        break;
      case 'preview':
        this.refreshPreview();
        break;
      case 'history':
        this.loadVersionHistory();
        break;
    }
  }

  // Editor Functionality
  initializeEditor() {
    const textarea = document.getElementById('content');
    const toolbar = document.querySelector('.modern-editor-toolbar');

    if (!textarea || !toolbar) return;

    // Toolbar functionality
    toolbar.addEventListener('click', e => {
      const button = e.target.closest('.toolbar-btn');
      if (!button) return;

      e.preventDefault();
      const action = button.dataset.action;
      this.executeEditorCommand(action, textarea);
    });

    // Track content changes
    textarea.addEventListener('input', () => {
      this.isDirty = true;
      this.updateWordCount();
      this.debounceAutosave();
    });

    // Fullscreen editor
    const fullscreenBtn = document.getElementById('fullscreen-editor');
    if (fullscreenBtn) {
      fullscreenBtn.addEventListener('click', () => {
        this.toggleFullscreenEditor();
      });
    }

    // Auto-resize textarea
    this.autoResizeTextarea(textarea);
  }

  executeEditorCommand(action, textarea) {
    const selection = this.getTextareaSelection(textarea);
    let replacement = '';

    switch (action) {
      case 'bold':
        replacement = `<strong>${selection.text || 'Bold text'}</strong>`;
        break;
      case 'italic':
        replacement = `<em>${selection.text || 'Italic text'}</em>`;
        break;
      case 'underline':
        replacement = `<u>${selection.text || 'Underlined text'}</u>`;
        break;
      case 'h1':
        replacement = `<h1>${selection.text || 'Heading 1'}</h1>`;
        break;
      case 'h2':
        replacement = `<h2>${selection.text || 'Heading 2'}</h2>`;
        break;
      case 'h3':
        replacement = `<h3>${selection.text || 'Heading 3'}</h3>`;
        break;
      case 'insertUnorderedList':
        replacement = `<ul><li>${selection.text || 'List item'}</li></ul>`;
        break;
      case 'insertOrderedList':
        replacement = `<ol><li>${selection.text || 'List item'}</li></ol>`;
        break;
    }

    if (replacement) {
      this.replaceSelection(textarea, replacement);
      this.isDirty = true;
      this.updateWordCount();
    }
  }

  getTextareaSelection(textarea) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value.substring(start, end);
    return { start, end, text };
  }

  replaceSelection(textarea, replacement) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const before = textarea.value.substring(0, start);
    const after = textarea.value.substring(end);

    textarea.value = before + replacement + after;

    // Set cursor position after replacement
    const newPosition = start + replacement.length;
    textarea.setSelectionRange(newPosition, newPosition);
    textarea.focus();
  }

  autoResizeTextarea(textarea) {
    const resize = () => {
      textarea.style.height = 'auto';
      textarea.style.height = Math.max(400, textarea.scrollHeight) + 'px';
    };

    textarea.addEventListener('input', resize);
    resize(); // Initial resize
  }

  toggleFullscreenEditor() {
    const editorWrapper = document.querySelector('.modern-editor-wrapper');
    const fullscreenBtn = document.getElementById('fullscreen-editor');

    if (!editorWrapper || !fullscreenBtn) return;

    const isFullscreen = editorWrapper.classList.contains('fullscreen');

    if (isFullscreen) {
      editorWrapper.classList.remove('fullscreen');
      fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
      document.body.style.overflow = '';
    } else {
      editorWrapper.classList.add('fullscreen');
      fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
      document.body.style.overflow = 'hidden';
    }
  }

  // Word Count
  updateWordCount() {
    const textarea = document.getElementById('content');
    const wordCountElement = document.getElementById('word-count');

    if (!textarea || !wordCountElement) return;

    const text = textarea.value.replace(/<[^>]*>/g, ''); // Remove HTML tags
    const words = text
      .trim()
      .split(/\s+/)
      .filter(word => word.length > 0);
    this.wordCount = words.length;

    wordCountElement.textContent = this.wordCount.toLocaleString();

    // Update preview word count if visible
    const previewWordCount = document.getElementById('preview-word-count');
    if (previewWordCount) {
      previewWordCount.textContent = this.wordCount;
    }

    // Update estimated read time (average 200 words per minute)
    const readTime = Math.max(1, Math.ceil(this.wordCount / 200));
    const previewReadTime = document.getElementById('preview-read-time');
    if (previewReadTime) {
      previewReadTime.textContent = readTime;
    }
  }

  // Autosave Functionality
  initializeAutosave() {
    const toggle = document.getElementById('autosave-toggle');
    const status = document.getElementById('autosave-status');

    if (toggle) {
      toggle.addEventListener('click', () => {
        this.toggleAutosave();
      });
    }

    // Start autosave by default
    this.startAutosave();
    this.updateLastSaved();
  }

  toggleAutosave() {
    const status = document.getElementById('autosave-status');

    if (this.autosaveInterval) {
      this.stopAutosave();
      status.textContent = 'OFF';
      status.classList.remove('active');
    } else {
      this.startAutosave();
      status.textContent = 'ON';
      status.classList.add('active');
    }
  }

  startAutosave() {
    this.stopAutosave(); // Clear existing interval
    this.autosaveInterval = setInterval(() => {
      if (this.isDirty) {
        this.performAutosave();
      }
    }, 10000); // Autosave every 10 seconds
  }

  stopAutosave() {
    if (this.autosaveInterval) {
      clearInterval(this.autosaveInterval);
      this.autosaveInterval = null;
    }
  }

  debounceAutosave() {
    clearTimeout(this.autosaveDebounce);
    this.autosaveDebounce = setTimeout(() => {
      if (this.isDirty && this.autosaveInterval) {
        this.performAutosave();
      }
    }, 2000); // Debounce for 2 seconds
  }

  async performAutosave() {
    const form = document.getElementById('terms-form');
    if (!form) return;

    const formData = new FormData(form);
    formData.append('_draft', '1');

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN':
            document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
      });

      if (response.ok) {
        this.isDirty = false;
        this.lastSaveTime = Date.now();
        this.updateLastSaved();
        this.showNotification('Draft saved automatically', 'success');
      }
    } catch (error) {
      console.error('Autosave failed:', error);
      this.showNotification('Failed to save draft', 'error');
    }
  }

  updateLastSaved() {
    const lastSavedElement = document.getElementById('last-saved');
    if (!lastSavedElement) return;

    const timeAgo = this.getTimeAgo(this.lastSaveTime);
    lastSavedElement.textContent = `Saved ${timeAgo}`;
  }

  getTimeAgo(timestamp) {
    const now = Date.now();
    const diff = now - timestamp;
    const seconds = Math.floor(diff / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);

    if (seconds < 60) return 'just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    return new Date(timestamp).toLocaleDateString();
  }

  // Floating Action Button
  initializeFAB() {
    const mainFab = document.getElementById('main-fab');
    const fabMenu = document.getElementById('fab-menu');

    if (!mainFab || !fabMenu) return;

    mainFab.addEventListener('click', () => {
      this.toggleFABMenu();
    });

    // Handle secondary FAB actions
    fabMenu.addEventListener('click', e => {
      const fab = e.target.closest('.secondary-fab');
      if (!fab) return;

      const action = fab.dataset.action;
      this.handleFABAction(action);
      this.closeFABMenu();
    });

    // Close FAB menu when clicking outside
    document.addEventListener('click', e => {
      if (!e.target.closest('.floating-actions') && this.fabMenuOpen) {
        this.closeFABMenu();
      }
    });
  }

  toggleFABMenu() {
    if (this.fabMenuOpen) {
      this.closeFABMenu();
    } else {
      this.openFABMenu();
    }
  }

  openFABMenu() {
    const fabMenu = document.getElementById('fab-menu');
    const mainFab = document.getElementById('main-fab');

    if (fabMenu) {
      fabMenu.classList.add('open');
      this.fabMenuOpen = true;
    }

    if (mainFab) {
      mainFab.style.transform = 'rotate(45deg)';
    }
  }

  closeFABMenu() {
    const fabMenu = document.getElementById('fab-menu');
    const mainFab = document.getElementById('main-fab');

    if (fabMenu) {
      fabMenu.classList.remove('open');
      this.fabMenuOpen = false;
    }

    if (mainFab) {
      mainFab.style.transform = 'rotate(0deg)';
    }
  }

  handleFABAction(action) {
    switch (action) {
      case 'new-version':
        this.createNewVersion();
        break;
      case 'export':
        this.showExportModal();
        break;
      case 'import':
        this.showImportModal();
        break;
    }
  }

  // Analytics
  refreshAnalytics() {
    const refreshBtn = document.getElementById('refresh-analytics');
    if (refreshBtn) {
      refreshBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i><span class="btn-text">Refreshing...</span>';

      // Simulate API call
      setTimeout(() => {
        refreshBtn.innerHTML =
          '<i class="fas fa-sync"></i><span class="btn-text">Refresh</span>';
        this.showNotification('Analytics refreshed', 'success');
      }, 1500);
    }
  }

  initializeChart() {
    const ctx = document.getElementById('acceptances-chart');
    if (!ctx) return;

    // Sample data - replace with actual data from backend
    const data = {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      datasets: [
        {
          label: 'Daily Acceptances',
          data: [12, 19, 3, 5, 2, 3, 10],
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99, 102, 241, 0.1)',
          tension: 0.4,
          fill: true,
        },
      ],
    };

    new Chart(ctx, {
      type: 'line',
      data: data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          x: {
            grid: {
              color: 'rgba(255, 255, 255, 0.1)',
            },
            ticks: {
              color: '#9ca3af',
            },
          },
          y: {
            grid: {
              color: 'rgba(255, 255, 255, 0.1)',
            },
            ticks: {
              color: '#9ca3af',
            },
          },
        },
        elements: {
          point: {
            radius: 4,
            hoverRadius: 6,
          },
        },
      },
    });
  }

  // Preview functionality
  refreshPreview() {
    const previewContent = document.getElementById('preview-content');
    const textarea = document.getElementById('content');
    const versionElement = document.getElementById('preview-version');
    const docVersionElement = document.getElementById('doc-version');
    const dateElement = document.getElementById('preview-date');
    const docDateElement = document.getElementById('doc-date');

    if (previewContent && textarea) {
      previewContent.innerHTML = textarea.value;
    }

    if (versionElement && docVersionElement) {
      const versionInput = document.getElementById('version');
      const version = versionInput?.value || '1.0';
      versionElement.textContent = version;
      docVersionElement.textContent = version;
    }

    if (dateElement && docDateElement) {
      const dateInput = document.getElementById('effective_date');
      if (dateInput?.value) {
        const date = new Date(dateInput.value);
        const formattedDate = date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        });
        dateElement.textContent = formattedDate;
        docDateElement.textContent = formattedDate;
      }
    }

    this.updateWordCount();
  }

  // Enhanced preview initialization
  initializePreview() {
    const refreshBtn = document.getElementById('refresh-preview');
    const exportBtn = document.getElementById('export-preview');

    if (refreshBtn) {
      refreshBtn.addEventListener('click', () => {
        this.refreshPreview();
        this.showNotification('Preview refreshed', 'success');
      });
    }

    if (exportBtn) {
      exportBtn.addEventListener('click', () => {
        this.exportPreview();
      });
    }

    // Auto-refresh preview when switching to preview tab
    const previewTab = document.querySelector('[data-tab="preview"]');
    if (previewTab) {
      previewTab.addEventListener('click', () => {
        setTimeout(() => this.refreshPreview(), 100);
      });
    }
  }

  exportPreview() {
    const previewContent = document.getElementById('preview-content');
    const docVersion = document.getElementById('doc-version');
    const docDate = document.getElementById('doc-date');

    if (!previewContent) return;

    const content = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Version ${docVersion?.textContent || '1.0'}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #333; text-align: center; }
        .meta { text-align: center; color: #666; margin-bottom: 30px; }
    </style>
</head>
<body>
    <h1>Terms of Service</h1>
    <div class="meta">
        <p>Version ${docVersion?.textContent || '1.0'} | Effective ${docDate?.textContent || 'N/A'}</p>
    </div>
    ${previewContent.innerHTML}
</body>
</html>
        `;

    const blob = new Blob([content], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `terms-of-service-v${docVersion?.textContent || '1.0'}.html`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    this.showNotification('Preview exported successfully', 'success');
  }

  // History functionality
  initializeHistory() {
    const compareBtn = document.getElementById('compare-versions-btn');

    if (compareBtn) {
      compareBtn.addEventListener('click', () => {
        this.showCompareModal();
      });
    }
  }

  showCompareModal() {
    // Implementation for showing version comparison modal
    this.showNotification('Version comparison feature coming soon', 'info');
  }

  // Animations
  initializeAnimations() {
    // Animate cards on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px',
    };

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);

    // Observe all cards
    document
      .querySelectorAll('.content-card, .analytics-card, .template-card')
      .forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
      });
  }

  // Keyboard Shortcuts
  initializeKeyboardShortcuts() {
    document.addEventListener('keydown', e => {
      // Ctrl/Cmd + S for save
      if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        this.saveForm();
      }

      // Ctrl/Cmd + P for preview
      if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        this.switchTab(
          'preview',
          document.querySelectorAll('.modern-tab-button'),
          document.querySelectorAll('.modern-tab-content')
        );
      }

      // Escape to close modals
      if (e.key === 'Escape') {
        this.closeAllModals();
        this.closeFABMenu();
      }
    });
  }

  // Utility Functions
  saveForm() {
    const form = document.getElementById('terms-form');
    if (form) {
      form.submit();
    }
  }

  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
            <i class="fas fa-${this.getNotificationIcon(type)}"></i>
            <span>${message}</span>
        `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
      notification.classList.add('show');
    }, 100);

    // Auto remove
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 3000);
  }

  getNotificationIcon(type) {
    const icons = {
      success: 'check-circle',
      error: 'exclamation-triangle',
      warning: 'exclamation-circle',
      info: 'info-circle',
    };
    return icons[type] || 'info-circle';
  }

  closeAllModals() {
    document.querySelectorAll('.modal').forEach(modal => {
      modal.style.display = 'none';
    });
  }

  // Template Functions (to be implemented)
  createNewVersion() {
    console.log('Creating new version...');
    this.showNotification('New version created', 'success');
  }

  showExportModal() {
    console.log('Showing export modal...');
  }

  showImportModal() {
    console.log('Showing import modal...');
  }

  loadVersionHistory() {
    console.log('Loading version history...');
  }

  // Enhanced initialization
  initializeAllFeatures() {
    this.initializePreview();
    this.initializeHistory();
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
  window.modernTerms = new ModernTermsOfService();
});

// Global template functions
function loadTemplate(templateType) {
  console.log('Loading template:', templateType);
  if (window.modernTerms) {
    window.modernTerms.showNotification(
      `Loading ${templateType} template...`,
      'info'
    );
  }
}

function previewTemplate(templateType) {
  console.log('Previewing template:', templateType);
  // Implementation for template preview
}

function viewVersion(versionId) {
  console.log('Viewing version:', versionId);
  // Implementation for viewing specific version
}

function restoreVersion(versionId) {
  if (
    confirm(
      'Are you sure you want to restore this version? This will deactivate the current terms.'
    )
  ) {
    console.log('Restoring version:', versionId);
    // Implementation for restoring version
  }
}

function compareVersions() {
  console.log('Comparing versions');
  // Implementation for version comparison
}

function exportTerms() {
  const format = document.getElementById('export-format')?.value || 'html';
  const includeMetadata =
    document.getElementById('include-metadata')?.checked || false;

  console.log('Exporting terms:', { format, includeMetadata });

  if (window.modernTerms) {
    window.modernTerms.showNotification('Export started...', 'info');
  }

  closeModal('export-modal');
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'none';
  }
}

// Additional global functions for enhanced features
function createFirstVersion() {
  if (window.modernTerms) {
    window.modernTerms.showNotification(
      'Creating your first terms version...',
      'info'
    );
    // Switch to editor tab
    const editorTab = document.querySelector('[data-tab="editor"]');
    if (editorTab) {
      editorTab.click();
    }
  }
}

function downloadVersion(versionId) {
  console.log('Downloading version:', versionId);
  if (window.modernTerms) {
    window.modernTerms.showNotification('Preparing download...', 'info');
  }
}

// Enhanced refresh preview function
function refreshPreviewData() {
  if (window.modernTerms) {
    window.modernTerms.refreshPreview();
  }
}

// Auto-update preview when form data changes
document.addEventListener('DOMContentLoaded', function () {
  const versionInput = document.getElementById('version');
  const dateInput = document.getElementById('effective_date');
  const contentTextarea = document.getElementById('content');

  if (versionInput) {
    versionInput.addEventListener('input', refreshPreviewData);
  }

  if (dateInput) {
    dateInput.addEventListener('change', refreshPreviewData);
  }

  if (contentTextarea) {
    contentTextarea.addEventListener('input', refreshPreviewData);
  }
});
