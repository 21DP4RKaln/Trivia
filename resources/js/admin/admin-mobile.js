// Mobile Admin Panel JavaScript Enhancements
document.addEventListener('DOMContentLoaded', function () {
  // Only run mobile enhancements on mobile devices
  if (window.innerWidth <= 768) {
    initializeMobileEnhancements();
  }

  // Re-initialize on window resize
  window.addEventListener('resize', function () {
    if (window.innerWidth <= 768) {
      initializeMobileEnhancements();
    }
  });
});

function initializeMobileEnhancements() {
  initializePullToRefresh();
  initializeTouchGestures();
  initializeMobileFloatingActionButton();
  initializeMobileKeyboardHandling();
  initializeMobileAnimations();
  initializeMobileAccessibility();
  initializeMobilePerformance();

  console.log('Mobile admin enhancements initialized');
}

// Pull to refresh functionality
function initializePullToRefresh() {
  let startY = 0;
  let currentY = 0;
  let isPulling = false;
  let threshold = 100;

  const refreshIndicator = document.createElement('div');
  refreshIndicator.className = 'mobile-pull-refresh';
  refreshIndicator.innerHTML =
    '<i class="fas fa-sync-alt"></i> Pull to refresh';
  document.body.appendChild(refreshIndicator);

  const adminMain = document.querySelector('.admin-main');
  if (!adminMain) return;

  adminMain.addEventListener(
    'touchstart',
    function (e) {
      if (window.scrollY === 0) {
        startY = e.touches[0].clientY;
        isPulling = true;
      }
    },
    { passive: true }
  );

  adminMain.addEventListener(
    'touchmove',
    function (e) {
      if (!isPulling) return;

      currentY = e.touches[0].clientY;
      const diff = currentY - startY;

      if (diff > 0 && window.scrollY === 0) {
        e.preventDefault();

        if (diff > threshold) {
          refreshIndicator.classList.add('visible');
          refreshIndicator.innerHTML =
            '<i class="fas fa-arrow-down"></i> Release to refresh';
        } else {
          refreshIndicator.classList.remove('visible');
          refreshIndicator.innerHTML =
            '<i class="fas fa-sync-alt"></i> Pull to refresh';
        }
      }
    },
    { passive: false }
  );

  adminMain.addEventListener(
    'touchend',
    function (e) {
      if (!isPulling) return;

      const diff = currentY - startY;
      isPulling = false;

      if (diff > threshold) {
        refreshIndicator.innerHTML =
          '<i class="fas fa-spinner fa-spin"></i> Refreshing...';

        // Simulate refresh
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        refreshIndicator.classList.remove('visible');
      }
    },
    { passive: true }
  );
}

// Touch gesture enhancements
function initializeTouchGestures() {
  // Swipe gestures for navigation
  let touchStartX = 0;
  let touchStartY = 0;
  let touchEndX = 0;
  let touchEndY = 0;

  document.addEventListener(
    'touchstart',
    function (e) {
      touchStartX = e.changedTouches[0].screenX;
      touchStartY = e.changedTouches[0].screenY;
    },
    { passive: true }
  );

  document.addEventListener(
    'touchend',
    function (e) {
      touchEndX = e.changedTouches[0].screenX;
      touchEndY = e.changedTouches[0].screenY;
      handleSwipeGesture();
    },
    { passive: true }
  );

  function handleSwipeGesture() {
    const deltaX = touchEndX - touchStartX;
    const deltaY = touchEndY - touchStartY;
    const minSwipeDistance = 100;

    // Horizontal swipe (left/right)
    if (Math.abs(deltaX) > minSwipeDistance && Math.abs(deltaY) < 50) {
      if (deltaX > 0) {
        // Swipe right - open mobile menu if closed
        const navLinks = document.getElementById('mobile-nav-links');
        const toggleButton = document.querySelector('.mobile-nav-toggle');
        if (navLinks && !navLinks.classList.contains('mobile-nav-open')) {
          toggleMobileNav();
        }
      } else {
        // Swipe left - close mobile menu if open
        const navLinks = document.getElementById('mobile-nav-links');
        if (navLinks && navLinks.classList.contains('mobile-nav-open')) {
          toggleMobileNav();
        }
      }
    }
  }

  // Enhanced touch feedback for buttons
  const buttons = document.querySelectorAll(
    '.btn, .mobile-action-card, .nav-link'
  );
  buttons.forEach(button => {
    button.addEventListener(
      'touchstart',
      function () {
        this.style.transform = 'scale(0.95)';
      },
      { passive: true }
    );

    button.addEventListener(
      'touchend',
      function () {
        setTimeout(() => {
          this.style.transform = '';
        }, 150);
      },
      { passive: true }
    );
  });
}

// Mobile floating action button
function initializeMobileFloatingActionButton() {
  const fab = document.createElement('button');
  fab.className = 'mobile-fab';
  fab.innerHTML = '<i class="fas fa-plus"></i>';
  fab.setAttribute('aria-label', 'Quick Actions');

  // Position based on current page
  const currentPath = window.location.pathname;
  let fabAction = '';

  if (currentPath.includes('/users')) {
    fab.innerHTML = '<i class="fas fa-user-plus"></i>';
    fabAction = 'Add User';
  } else if (currentPath.includes('/questions')) {
    fab.innerHTML = '<i class="fas fa-question"></i>';
    fabAction = 'Add Question';
  } else if (currentPath.includes('/dashboard')) {
    fab.innerHTML = '<i class="fas fa-refresh"></i>';
    fabAction = 'Refresh Dashboard';
  }

  fab.addEventListener('click', function () {
    if (currentPath.includes('/dashboard')) {
      showMobileRefreshAnimation();
      setTimeout(() => window.location.reload(), 1000);
    } else {
      showQuickActionsMenu();
    }
  });

  document.body.appendChild(fab);

  // Hide FAB when scrolling down, show when scrolling up
  let lastScrollY = window.scrollY;
  window.addEventListener(
    'scroll',
    function () {
      const currentScrollY = window.scrollY;

      if (currentScrollY > lastScrollY && currentScrollY > 100) {
        fab.style.transform = 'translateY(100px)';
      } else {
        fab.style.transform = 'translateY(0)';
      }

      lastScrollY = currentScrollY;
    },
    { passive: true }
  );
}

// Show quick actions menu
function showQuickActionsMenu() {
  const overlay = document.createElement('div');
  overlay.className = 'mobile-quick-actions-overlay';
  overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1001;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 2rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;

  const menu = document.createElement('div');
  menu.className = 'mobile-quick-actions-menu';
  menu.style.cssText = `
        background: linear-gradient(135deg, #1e293b, #334155);
        border-radius: 20px 20px 8px 8px;
        padding: 1.5rem;
        width: 100%;
        max-width: 400px;
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    `;

  const actions = [
    { icon: 'fas fa-chart-line', title: 'Dashboard', path: '/admin/dashboard' },
    { icon: 'fas fa-users', title: 'Users', path: '/admin/users' },
    {
      icon: 'fas fa-chart-bar',
      title: 'Statistics',
      path: '/admin/statistics',
    },
    {
      icon: 'fas fa-question-circle',
      title: 'Questions',
      path: '/admin/questions',
    },
    {
      icon: 'fas fa-file-contract',
      title: 'Terms',
      path: '/admin/terms-of-service',
    },
  ];

  menu.innerHTML = `
        <div style="text-align: center; margin-bottom: 1rem;">
            <h3 style="color: white; margin: 0; font-size: 1.1rem;">Quick Actions</h3>
        </div>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
            ${actions
              .map(
                action => `
                <a href="${action.path}" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 1rem;
                    background: rgba(255, 255, 255, 0.1);
                    border-radius: 12px;
                    color: white;
                    text-decoration: none;
                    transition: all 0.3s ease;
                " onmouseover="this.style.background='rgba(255,255,255,0.2)'" 
                   onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                    <i class="${action.icon}" style="font-size: 1.5rem; margin-bottom: 0.5rem; color: #10b981;"></i>
                    <span style="font-size: 0.9rem; font-weight: 500;">${action.title}</span>
                </a>
            `
              )
              .join('')}
        </div>
        <button onclick="this.closest('.mobile-quick-actions-overlay').remove()" style="
            width: 100%;
            padding: 1rem;
            margin-top: 1rem;
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: none;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
        ">Cancel</button>
    `;

  overlay.appendChild(menu);
  document.body.appendChild(overlay);

  // Animate in
  setTimeout(() => {
    overlay.style.opacity = '1';
    menu.style.transform = 'translateY(0)';
  }, 10);

  // Close on overlay click
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) {
      overlay.remove();
    }
  });
}

// Mobile keyboard handling
function initializeMobileKeyboardHandling() {
  const inputs = document.querySelectorAll('input, textarea, select');
  let originalViewportHeight = window.innerHeight;

  inputs.forEach(input => {
    input.addEventListener('focus', function () {
      // Prevent zoom on input focus
      this.style.fontSize = '16px';

      // Adjust viewport for keyboard
      setTimeout(() => {
        if (window.innerHeight < originalViewportHeight * 0.7) {
          document.body.classList.add('keyboard-open');

          // Hide FAB when keyboard is open
          const fab = document.querySelector('.mobile-fab');
          if (fab) fab.style.display = 'none';
        }
      }, 300);
    });

    input.addEventListener('blur', function () {
      document.body.classList.remove('keyboard-open');

      // Show FAB when keyboard closes
      const fab = document.querySelector('.mobile-fab');
      if (fab) fab.style.display = 'flex';
    });
  });
}

// Mobile animations and loading states
function initializeMobileAnimations() {
  // Intersection Observer for scroll animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px',
  };

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-in');

        // Stagger animations for children
        const children = entry.target.querySelectorAll(
          '.stat-card, .dashboard-card, .action-item'
        );
        children.forEach((child, index) => {
          setTimeout(() => {
            child.classList.add('animate-in');
          }, index * 100);
        });
      }
    });
  }, observerOptions);

  // Observe elements for animation
  const animatableElements = document.querySelectorAll(
    '.stats-grid, .dashboard-grid, .actions-grid'
  );
  animatableElements.forEach(el => observer.observe(el));

  // Loading skeleton for slow connections
  if (
    navigator.connection &&
    navigator.connection.effectiveType === 'slow-2g'
  ) {
    showMobileLoadingSkeleton();
  }
}

// Show mobile refresh animation
function showMobileRefreshAnimation() {
  const fab = document.querySelector('.mobile-fab');
  if (fab) {
    fab.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    fab.style.transform = 'scale(1.1)';

    setTimeout(() => {
      fab.style.transform = 'scale(1)';
    }, 1000);
  }
}

// Mobile accessibility enhancements
function initializeMobileAccessibility() {
  // Add skip link for mobile
  const skipLink = document.createElement('a');
  skipLink.href = '#main-content';
  skipLink.textContent = 'Skip to main content';
  skipLink.className = 'mobile-skip-link';
  skipLink.style.cssText = `
        position: fixed;
        top: -100px;
        left: 20px;
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        z-index: 1002;
        transition: top 0.3s ease;
        text-decoration: none;
        font-weight: 500;
    `;

  skipLink.addEventListener('focus', function () {
    this.style.top = '80px';
  });

  skipLink.addEventListener('blur', function () {
    this.style.top = '-100px';
  });

  document.body.appendChild(skipLink);

  // Add main content ID
  const adminMain = document.querySelector('.admin-main');
  if (adminMain) {
    adminMain.id = 'main-content';
  }

  // Enhanced touch targets
  const smallButtons = document.querySelectorAll('.btn-sm, .btn-icon');
  smallButtons.forEach(button => {
    const rect = button.getBoundingClientRect();
    if (rect.width < 44 || rect.height < 44) {
      button.style.minWidth = '44px';
      button.style.minHeight = '44px';
    }
  });

  // Voice control hints
  if ('speechSynthesis' in window) {
    const voiceButton = document.createElement('button');
    voiceButton.className = 'mobile-voice-hint';
    voiceButton.innerHTML = '<i class="fas fa-microphone"></i>';
    voiceButton.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            width: 44px;
            height: 44px;
            background: rgba(16, 185, 129, 0.9);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1rem;
            z-index: 100;
            display: none;
        `;

    voiceButton.addEventListener('click', function () {
      const utterance = new SpeechSynthesisUtterance(
        'Admin panel loaded. Swipe right to open menu, or use the floating action button for quick actions.'
      );
      speechSynthesis.speak(utterance);
    });

    document.body.appendChild(voiceButton);

    // Show voice hint for accessibility users
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      voiceButton.style.display = 'flex';
    }
  }
}

// Mobile performance optimizations
function initializeMobilePerformance() {
  // Lazy load images
  const images = document.querySelectorAll('img[data-src]');
  const imageObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.removeAttribute('data-src');
        imageObserver.unobserve(img);
      }
    });
  });

  images.forEach(img => imageObserver.observe(img));

  // Debounce scroll events
  let scrollTimeout;
  let isScrolling = false;

  window.addEventListener(
    'scroll',
    function () {
      if (!isScrolling) {
        isScrolling = true;
        requestAnimationFrame(function () {
          // Handle scroll
          isScrolling = false;
        });
      }
    },
    { passive: true }
  );

  // Optimize touch events
  const touchElements = document.querySelectorAll(
    '.mobile-action-card, .stat-card, .btn'
  );
  touchElements.forEach(element => {
    element.addEventListener(
      'touchstart',
      function () {
        this.classList.add('touch-active');
      },
      { passive: true }
    );

    element.addEventListener(
      'touchend',
      function () {
        setTimeout(() => {
          this.classList.remove('touch-active');
        }, 150);
      },
      { passive: true }
    );
  });

  // Memory management
  window.addEventListener('beforeunload', function () {
    // Clean up event listeners and observers
    if (typeof observer !== 'undefined') {
      observer.disconnect();
    }
    if (typeof imageObserver !== 'undefined') {
      imageObserver.disconnect();
    }
  });
}

// Show mobile loading skeleton
function showMobileLoadingSkeleton() {
  const container = document.querySelector('.admin-container');
  if (!container) return;

  const skeleton = document.createElement('div');
  skeleton.className = 'mobile-loading-skeleton';
  skeleton.innerHTML = `
        <div class="mobile-skeleton-stat mobile-loading-shimmer"></div>
        <div class="mobile-skeleton-stat mobile-loading-shimmer"></div>
        <div class="mobile-skeleton-action mobile-loading-shimmer"></div>
        <div class="mobile-skeleton-action mobile-loading-shimmer"></div>
        <div class="mobile-skeleton-activity mobile-loading-shimmer"></div>
    `;

  container.insertBefore(skeleton, container.firstChild);

  // Remove skeleton when content loads
  window.addEventListener('load', function () {
    setTimeout(() => {
      skeleton.remove();
    }, 500);
  });
}

// Add mobile-specific CSS classes based on device characteristics
function addMobileDeviceClasses() {
  const html = document.documentElement;

  // Screen size classes
  if (window.innerWidth <= 480) {
    html.classList.add('mobile-small');
  } else if (window.innerWidth <= 768) {
    html.classList.add('mobile-large');
  }

  // Touch device detection
  if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
    html.classList.add('touch-device');
  }

  // Network information
  if (navigator.connection) {
    html.classList.add(`connection-${navigator.connection.effectiveType}`);
  }

  // Orientation
  if (window.innerHeight > window.innerWidth) {
    html.classList.add('portrait');
  } else {
    html.classList.add('landscape');
  }
}

// Initialize device classes immediately
addMobileDeviceClasses();

// Update classes on orientation change
window.addEventListener('orientationchange', function () {
  setTimeout(addMobileDeviceClasses, 100);
});

// Export functions for global access
window.toggleMobileNav = toggleMobileNav;
window.showQuickActionsMenu = showQuickActionsMenu;
window.showMobileRefreshAnimation = showMobileRefreshAnimation;
