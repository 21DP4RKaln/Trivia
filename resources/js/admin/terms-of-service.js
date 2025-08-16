// Admin Terms of Service - Enhanced JavaScript Functionality

class AdminTermsOfService {
  constructor() {
    this.particles = [];
    this.particleCount = 15;
    this.isInitialized = false;
    this.currentTab = 'editor';
    this.autosaveInterval = null;
    this.lastSaveTime = null;

    this.init();
  }

  init() {
    if (this.isInitialized) return;

    this.createBackground();
    this.createParticles();
    this.initializeTabs();
    this.initializeEditor();
    this.initializeNotifications();
    this.initializeAutosave();
    this.initializeKeyboardShortcuts();
    this.initializeAnimations();

    this.isInitialized = true;
  }

  createBackground() {
    const background = document.createElement('div');
    background.className = 'admin-background';
    document.body.prepend(background);

    // Add admin-terms-page class to body for styling
    document.body.classList.add('admin-terms-page');
  }

  createParticles() {
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'admin-particles';
    document.body.appendChild(particlesContainer);

    for (let i = 0; i < this.particleCount; i++) {
      this.createParticle(particlesContainer);
    }
  }

  createParticle(container) {
    const particle = document.createElement('div');
    particle.className = 'admin-particle';

    // Random positioning and animation delay
    particle.style.left = Math.random() * 100 + '%';
    particle.style.animationDelay = Math.random() * 20 + 's';
    particle.style.animationDuration = Math.random() * 8 + 12 + 's';

    container.appendChild(particle);
    this.particles.push(particle);
  }

  initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
      button.addEventListener('click', e => {
        e.preventDefault();
        const targetTab = button.getAttribute('data-tab');
        this.switchTab(targetTab, tabButtons, tabPanes);
      });
    });
  }

  switchTab(targetTab, tabButtons, tabPanes) {
    // Remove active class from all tabs and panes
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabPanes.forEach(pane => pane.classList.remove('active'));

    // Add active class to target tab and pane
    const targetButton = document.querySelector(`[data-tab="${targetTab}"]`);
    const targetPane = document.getElementById(`${targetTab}-tab`);

    if (targetButton && targetPane) {
      targetButton.classList.add('active');
      targetPane.classList.add('active');
      this.currentTab = targetTab;

      // Update preview if switching to preview tab
      if (targetTab === 'preview') {
        this.updatePreview();
      }
    }
  }

  initializeEditor() {
    const textarea = document.getElementById('content');
    if (!textarea) return;

    // Add real-time preview updates
    textarea.addEventListener(
      'input',
      this.debounce(() => {
        if (this.currentTab === 'preview') {
          this.updatePreview();
        }
        this.markAsModified();
      }, 500)
    );

    // Add line numbers and better editing experience
    this.enhanceTextarea(textarea);
  }

  enhanceTextarea(textarea) {
    // Add line counting
    const updateLineCount = () => {
      const lines = textarea.value.split('\n').length;
      let lineCounter = textarea.parentElement.querySelector('.line-counter');

      if (!lineCounter) {
        lineCounter = document.createElement('div');
        lineCounter.className = 'line-counter';
        textarea.parentElement.appendChild(lineCounter);
      }

      lineCounter.textContent = `Lines: ${lines}`;
    };

    textarea.addEventListener('input', updateLineCount);
    updateLineCount();

    // Add word count
    const updateWordCount = () => {
      const words = textarea.value
        .trim()
        .split(/\s+/)
        .filter(word => word.length > 0).length;
      let wordCounter = textarea.parentElement.querySelector('.word-counter');

      if (!wordCounter) {
        wordCounter = document.createElement('div');
        wordCounter.className = 'word-counter';
        textarea.parentElement.appendChild(wordCounter);
      }

      wordCounter.textContent = `Words: ${words}`;
    };

    textarea.addEventListener('input', updateWordCount);
    updateWordCount();
  }

  updatePreview() {
    const content = document.getElementById('content')?.value || '';
    const previewContainer = document.getElementById('preview-content');

    if (!previewContainer) return;

    // Convert markdown-like content to HTML
    const htmlContent = this.convertToHTML(content);
    previewContainer.innerHTML = htmlContent;
  }

  convertToHTML(content) {
    return content
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.*?)\*/g, '<em>$1</em>')
      .replace(/^## (.*$)/gm, '<h2>$1</h2>')
      .replace(/^# (.*$)/gm, '<h1>$1</h1>')
      .replace(/^- (.*$)/gm, '<li>$1</li>')
      .replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>')
      .replace(/\n---\n/g, '<hr>')
      .replace(/\n/g, '<br>');
  }

  initializeNotifications() {
    // Auto-hide notifications after 5 seconds
    document.addEventListener('DOMContentLoaded', () => {
      const notifications = document.querySelectorAll('.notification');
      notifications.forEach(notification => {
        setTimeout(() => {
          this.hideNotification(notification);
        }, 5000);
      });
    });
  }

  showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    const content = document.createElement('div');
    content.className = 'notification-content';

    const icon = document.createElement('i');
    icon.className = 'notification-icon fas ' + this.getNotificationIcon(type);

    const messageSpan = document.createElement('span');
    messageSpan.className = 'notification-message';
    messageSpan.textContent = message;

    const closeButton = document.createElement('button');
    closeButton.className = 'notification-close';
    closeButton.innerHTML = '&times;';
    closeButton.onclick = () => this.hideNotification(notification);

    content.appendChild(icon);
    content.appendChild(messageSpan);
    content.appendChild(closeButton);
    notification.appendChild(content);

    document.body.appendChild(notification);

    // Auto-hide after 5 seconds
    setTimeout(() => {
      this.hideNotification(notification);
    }, 5000);
  }

  getNotificationIcon(type) {
    const icons = {
      success: 'fa-check-circle',
      error: 'fa-exclamation-circle',
      warning: 'fa-exclamation-triangle',
      info: 'fa-info-circle',
    };
    return icons[type] || icons.info;
  }

  hideNotification(notification) {
    if (notification && notification.parentNode) {
      notification.style.animation = 'slideOut 0.3s ease-in';
      setTimeout(() => {
        notification.remove();
      }, 300);
    }
  }

  initializeAutosave() {
    const form = document.querySelector('form');
    if (!form) return;

    this.autosaveInterval = setInterval(() => {
      this.autosave();
    }, 30000); // Autosave every 30 seconds
  }

  autosave() {
    const content = document.getElementById('content')?.value;
    if (!content || content === this.lastSaveTime) return;

    // Save to localStorage as backup
    localStorage.setItem('admin_terms_draft', content);
    localStorage.setItem('admin_terms_draft_time', Date.now());

    this.lastSaveTime = content;
    this.showNotification('Draft saved automatically', 'info');
  }

  initializeKeyboardShortcuts() {
    document.addEventListener('keydown', e => {
      // Ctrl+S to save
      if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        this.saveForm();
      }

      // Ctrl+Tab to switch tabs
      if (e.ctrlKey && e.key === 'Tab') {
        e.preventDefault();
        this.switchToNextTab();
      }

      // Esc to close notifications
      if (e.key === 'Escape') {
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach(notification => {
          this.hideNotification(notification);
        });
      }
    });
  }

  switchToNextTab() {
    const tabs = ['editor', 'preview', 'templates'];
    const currentIndex = tabs.indexOf(this.currentTab);
    const nextIndex = (currentIndex + 1) % tabs.length;
    const nextTab = tabs[nextIndex];

    const tabButton = document.querySelector(`[data-tab="${nextTab}"]`);
    if (tabButton) {
      tabButton.click();
    }
  }

  saveForm() {
    const form = document.querySelector('form');
    if (form) {
      this.showNotification('Saving...', 'info');
      form.submit();
    }
  }

  markAsModified() {
    const saveButton = document.querySelector('.btn-primary');
    if (saveButton && !saveButton.classList.contains('modified')) {
      saveButton.classList.add('modified');
      saveButton.textContent = saveButton.textContent + ' *';
    }
  }

  initializeAnimations() {
    // Animate cards on scroll
    const cards = document.querySelectorAll('.card');
    const observer = new IntersectionObserver(
      entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.animation = 'fadeIn 0.6s ease-out';
          }
        });
      },
      { threshold: 0.1 }
    );

    cards.forEach(card => {
      observer.observe(card);
    });

    // Animate form elements
    const formElements = document.querySelectorAll('.form-control, .btn');
    formElements.forEach((element, index) => {
      element.style.animation = `fadeIn 0.5s ease-out ${index * 0.1}s both`;
    });
  }

  // Utility function for debouncing
  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // Cleanup method
  destroy() {
    if (this.autosaveInterval) {
      clearInterval(this.autosaveInterval);
    }

    this.particles.forEach(particle => {
      if (particle.parentNode) {
        particle.parentNode.removeChild(particle);
      }
    });

    document.body.classList.remove('admin-terms-page');

    const background = document.querySelector('.admin-background');
    if (background) background.remove();

    const particlesContainer = document.querySelector('.admin-particles');
    if (particlesContainer) particlesContainer.remove();

    this.isInitialized = false;
  }
}

// Toolbar functions for the editor
function insertText(before, after) {
  const textarea = document.getElementById('content');
  if (!textarea) return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const selectedText = textarea.value.substring(start, end);

  const newText = before + selectedText + after;

  textarea.value =
    textarea.value.substring(0, start) +
    newText +
    textarea.value.substring(end);

  // Update cursor position
  const newCursorPos = start + before.length + selectedText.length;
  textarea.setSelectionRange(newCursorPos, newCursorPos);
  textarea.focus();

  // Trigger input event for preview update
  textarea.dispatchEvent(new Event('input'));
}

function insertSection() {
  const sectionTemplate = `

## New Section

Enter your section content here...

---

`;

  const textarea = document.getElementById('content');
  if (!textarea) return;

  const cursorPos = textarea.selectionStart;
  textarea.value =
    textarea.value.substring(0, cursorPos) +
    sectionTemplate +
    textarea.value.substring(cursorPos);

  // Position cursor after the heading
  const newCursorPos = cursorPos + 16; // Position after "## New Section"
  textarea.setSelectionRange(newCursorPos, newCursorPos);
  textarea.focus();

  // Trigger input event
  textarea.dispatchEvent(new Event('input'));
}

// Template loading functions
function loadTemplate(templateType) {
  const templates = {
    basic: `# Terms of Service

## 1. Acceptance of Terms
By accessing and using this service, you accept and agree to be bound by the terms and provision of this agreement.

## 2. Use License
Permission is granted to temporarily download one copy of the materials on our website for personal, non-commercial transitory viewing only.

## 3. Disclaimer
The materials on our website are provided on an 'as is' basis. We make no warranties, expressed or implied.

## 4. Limitations
In no event shall our company or its suppliers be liable for any damages arising out of the use or inability to use the materials on our website.

## 5. Contact Information
For any questions about these Terms of Service, please contact us at [contact information].

---

*Last updated: ${new Date().toLocaleDateString()}*`,

    detailed: `# Comprehensive Terms of Service

## 1. Introduction
Welcome to our service. These terms govern your use of our platform.

## 2. Account Registration
Users must provide accurate information when creating an account.

## 3. User Conduct
Users agree to use the service in compliance with all applicable laws.

## 4. Privacy Policy
Your privacy is important to us. Please review our Privacy Policy.

## 5. Intellectual Property
All content on this platform is protected by copyright and other intellectual property laws.

## 6. Termination
We reserve the right to terminate accounts that violate these terms.

## 7. Governing Law
These terms are governed by the laws of [Jurisdiction].

---

*Effective Date: ${new Date().toLocaleDateString()}*`,

    ecommerce: `# E-Commerce Terms of Service

## 1. General Terms
These terms apply to all purchases made through our platform.

## 2. Product Information
We strive to provide accurate product descriptions and pricing.

## 3. Orders and Payment
All orders are subject to acceptance and credit verification.

## 4. Shipping and Delivery
Shipping times and costs vary by location and product.

## 5. Returns and Refunds
Items may be returned within 30 days of purchase in original condition.

## 6. Warranties
Products come with manufacturer warranties where applicable.

## 7. Limitation of Liability
Our liability is limited to the purchase price of the item.

---

*Last updated: ${new Date().toLocaleDateString()}*`,
  };

  const template = templates[templateType];
  if (!template) return;

  const textarea = document.getElementById('content');
  if (textarea) {
    textarea.value = template;
    textarea.dispatchEvent(new Event('input'));

    // Switch to editor tab
    const editorTab = document.querySelector('[data-tab="editor"]');
    if (editorTab) {
      editorTab.click();
    }

    adminTermsApp.showNotification(
      `${templateType.charAt(0).toUpperCase() + templateType.slice(1)} template loaded`,
      'success'
    );
  }
}

// Form control functions
function toggleEditForm() {
  const form = document.getElementById('editForm');
  if (!form) return;

  const isVisible = form.style.display !== 'none';
  form.style.display = isVisible ? 'none' : 'block';

  if (!isVisible) {
    // Scroll to form
    form.scrollIntoView({ behavior: 'smooth' });
  }
}

function showFullPreview() {
  const modal = document.createElement('div');
  modal.className = 'modal-overlay';
  modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Full Terms Preview</h3>
                <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="terms-preview-content">
                    ${document.querySelector('.terms-preview-rendered').innerHTML}
                </div>
            </div>
        </div>
    `;

  document.body.appendChild(modal);
}

// Notification functions
function hideNotification() {
  const notification = document.querySelector('.notification');
  if (notification && adminTermsApp) {
    adminTermsApp.hideNotification(notification);
  }
}

// Initialize the application
let adminTermsApp;

document.addEventListener('DOMContentLoaded', function () {
  adminTermsApp = new AdminTermsOfService();

  // Check for draft recovery
  const draft = localStorage.getItem('admin_terms_draft');
  const draftTime = localStorage.getItem('admin_terms_draft_time');

  if (draft && draftTime) {
    const timeDiff = Date.now() - parseInt(draftTime);
    if (timeDiff < 86400000) {
      // Less than 24 hours
      const textarea = document.getElementById('content');
      if (textarea && !textarea.value.trim()) {
        if (confirm('Found a recent draft. Would you like to restore it?')) {
          textarea.value = draft;
          textarea.dispatchEvent(new Event('input'));
        }
      }
    }
  }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function () {
  if (adminTermsApp) {
    adminTermsApp.destroy();
  }
});

// Add CSS for modal and additional styles
const additionalStyles = `
<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(10px);
}

.modal-content {
    background: var(--admin-secondary);
    border-radius: 20px;
    max-width: 90vw;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    color: var(--admin-text);
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    color: var(--admin-text-muted);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: var(--admin-transition);
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--admin-text);
}

.modal-body {
    padding: 2rem;
    max-height: 70vh;
    overflow-y: auto;
}

.line-counter,
.word-counter {
    position: absolute;
    bottom: 0.5rem;
    right: 1rem;
    font-size: 0.75rem;
    color: var(--admin-text-muted);
    background: rgba(0, 0, 0, 0.5);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.word-counter {
    right: 5rem;
}

.btn.modified {
    background: linear-gradient(135deg, var(--admin-warning), #d97706);
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes slideOut {
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', additionalStyles);
