// Modal Management System
class ModalManager {
  constructor() {
    this.activeModal = null;
    this.init();
  }

  init() {
    // Close modal when clicking outside
    document.addEventListener('click', e => {
      if (e.target.classList.contains('modal-overlay')) {
        this.closeModal(this.activeModal);
      }
    });

    // Close modal with escape key
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && this.activeModal) {
        this.closeModal(this.activeModal);
      }
    });

    // Initialize tab functionality
    this.initTabs();
  }

  openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Close any open modal first
    if (this.activeModal) {
      this.closeModal(this.activeModal);
    }

    this.activeModal = modalId;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Trigger animation
    requestAnimationFrame(() => {
      modal.classList.add('show');
    });

    // Initialize any specific modal functionality
    this.initModalFeatures(modalId);
  }

  closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.remove('show');

    setTimeout(() => {
      modal.style.display = 'none';
      document.body.style.overflow = '';
      this.activeModal = null;
    }, 300);
  }

  initModalFeatures(modalId) {
    switch (modalId) {
      case 'scoreDistributionModal':
        this.animateDistributionBars();
        break;
      case 'leaderboardModal':
        this.initLeaderboardTabs();
        break;
      case 'recentGamesModal':
        // Any specific features for recent games modal
        break;
    }
  }

  animateDistributionBars() {
    setTimeout(() => {
      const bars = document.querySelectorAll('.distribution-fill-enhanced');
      bars.forEach((bar, index) => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
          bar.style.transition = 'width 1.5s cubic-bezier(0.4, 0, 0.2, 1)';
          bar.style.width = width;
        }, index * 200);
      });
    }, 100);
  }

  initTabs() {
    document.addEventListener('click', e => {
      if (e.target.classList.contains('tab-btn')) {
        const tabId = e.target.getAttribute('data-tab');
        this.switchTab(tabId, e.target);
      }
    });
  }

  initLeaderboardTabs() {
    // Reset to first tab when modal opens
    const firstTab = document.querySelector(
      '.tab-btn[data-tab="top-performers"]'
    );
    if (firstTab) {
      this.switchTab('top-performers', firstTab);
    }
  }

  switchTab(tabId, clickedTab) {
    // Remove active class from all tabs and contents
    document.querySelectorAll('.tab-btn').forEach(tab => {
      tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.remove('active');
    });

    // Add active class to clicked tab and corresponding content
    clickedTab.classList.add('active');
    const tabContent = document.getElementById(tabId);
    if (tabContent) {
      tabContent.classList.add('active');
    }

    // Animate leaderboard items
    this.animateLeaderboardItems(tabId);
  }

  animateLeaderboardItems(tabId) {
    const items = document.querySelectorAll(
      `#${tabId} .leaderboard-item-enhanced`
    );
    items.forEach((item, index) => {
      item.style.opacity = '0';
      item.style.transform = 'translateX(-20px)';
      setTimeout(() => {
        item.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        item.style.opacity = '1';
        item.style.transform = 'translateX(0)';
      }, index * 100);
    });
  }
}

// Initialize modal manager
const modalManager = new ModalManager();

// Global functions for backward compatibility
function openModal(modalId) {
  modalManager.openModal(modalId);
}

function closeModal(modalId) {
  modalManager.closeModal(modalId);
}

// Enhanced Analytics Button Interactions
document.addEventListener('DOMContentLoaded', function () {
  // Add ripple effect to analytics buttons
  const analyticsButtons = document.querySelectorAll('.analytics-btn');

  analyticsButtons.forEach(button => {
    button.addEventListener('click', function (e) {
      const ripple = document.createElement('span');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;

      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = x + 'px';
      ripple.style.top = y + 'px';
      ripple.classList.add('ripple-effect');

      this.appendChild(ripple);

      setTimeout(() => {
        ripple.remove();
      }, 600);
    });
  });

  // Animate analytics cards on page load
  const analyticsCards = document.querySelectorAll('.analytics-card');
  analyticsCards.forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    setTimeout(() => {
      card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, index * 200);
  });
});

// Add ripple effect styles
const rippleStyles = document.createElement('style');
rippleStyles.textContent = `
    .analytics-btn {
        position: relative;
        overflow: hidden;
    }
    
    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleStyles);

// Toast notification system for modal actions
class ToastManager {
  constructor() {
    this.container = this.createContainer();
  }

  createContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container';
    container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        `;
    document.body.appendChild(container);
    return container;
  }

  show(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
            background: ${this.getBackgroundColor(type)};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            min-width: 250px;
            position: relative;
        `;

    toast.textContent = message;
    this.container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
      toast.style.transform = 'translateX(0)';
    });

    // Auto remove
    setTimeout(() => {
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }, duration);
  }

  getBackgroundColor(type) {
    const colors = {
      info: 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
      success: 'linear-gradient(135deg, #10b981, #059669)',
      warning: 'linear-gradient(135deg, #f59e0b, #d97706)',
      error: 'linear-gradient(135deg, #ef4444, #dc2626)',
    };
    return colors[type] || colors.info;
  }
}

// Initialize toast manager
const toastManager = new ToastManager();

// Export for global use
window.showToast = (message, type, duration) => {
  toastManager.show(message, type, duration);
};
