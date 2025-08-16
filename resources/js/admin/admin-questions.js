// Admin Questions Page JavaScript

document.addEventListener('DOMContentLoaded', function () {
  initializeQuestionInteractions();
  initializeKeyboardShortcuts();
  initializeAnimations();
});

/**
 * Initialize question card interactions
 */
function initializeQuestionInteractions() {
  // Add click handlers for question toggle buttons
  const toggleButtons = document.querySelectorAll('.btn-toggle');
  toggleButtons.forEach(button => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      toggleQuestion(this);
    });
  });

  // Add click handlers for question headers (alternative toggle)
  const questionHeaders = document.querySelectorAll('.question-header');
  questionHeaders.forEach(header => {
    const toggleBtn = header.querySelector('.btn-toggle');
    if (toggleBtn) {
      header.style.cursor = 'pointer';
      header.addEventListener('click', function (e) {
        if (e.target === toggleBtn || e.target.closest('.btn-toggle')) {
          return; // Let the button handler take care of it
        }
        toggleQuestion(toggleBtn);
      });
    }
  });
}

/**
 * Toggle a single question's details
 * @param {HTMLElement} toggleButton - The toggle button element
 */
function toggleQuestion(toggleButton) {
  const questionCard = toggleButton.closest('.question-card');
  const questionDetails = questionCard.querySelector('.question-details');
  const icon = toggleButton.querySelector('i');

  if (questionDetails.classList.contains('collapsed')) {
    // Expand
    questionDetails.classList.remove('collapsed');
    toggleButton.classList.add('expanded');
    icon.style.transform = 'rotate(180deg)';

    // Smooth animation
    questionDetails.style.animation = 'expandContent 0.3s ease-out forwards';
  } else {
    // Collapse
    questionDetails.classList.add('collapsed');
    toggleButton.classList.remove('expanded');
    icon.style.transform = 'rotate(0deg)';

    // Smooth animation
    questionDetails.style.animation = 'collapseContent 0.3s ease-out forwards';
  }
}

/**
 * Toggle all questions in a category
 * @param {string} category - 'fallback' or 'api'
 */
function toggleAllQuestions(category) {
  const categoryContainer = document.getElementById(`${category}-questions`);
  if (!categoryContainer) return;

  const questionCards = categoryContainer.querySelectorAll('.question-card');
  const toggleButtons = Array.from(questionCards).map(card =>
    card.querySelector('.btn-toggle')
  );

  // Check if all are expanded or collapsed
  const expandedCount = toggleButtons.filter(btn =>
    btn.classList.contains('expanded')
  ).length;
  const shouldExpand = expandedCount < toggleButtons.length / 2;

  toggleButtons.forEach(button => {
    const questionDetails = button
      .closest('.question-card')
      .querySelector('.question-details');
    const icon = button.querySelector('i');

    if (shouldExpand) {
      // Expand all
      questionDetails.classList.remove('collapsed');
      button.classList.add('expanded');
      icon.style.transform = 'rotate(180deg)';
    } else {
      // Collapse all
      questionDetails.classList.add('collapsed');
      button.classList.remove('expanded');
      icon.style.transform = 'rotate(0deg)';
    }
  });

  // Update button text
  const toggleAllBtn = categoryContainer.parentElement.querySelector(
    '.section-actions .btn-outline'
  );
  if (toggleAllBtn) {
    const icon = toggleAllBtn.querySelector('i');
    if (shouldExpand) {
      toggleAllBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Collapse All';
    } else {
      toggleAllBtn.innerHTML = '<i class="fas fa-eye"></i> Expand All';
    }
  }
}

/**
 * Refresh API questions (placeholder for future functionality)
 */
function refreshApiQuestions() {
  const apiSection = document.getElementById('api-questions');
  const refreshBtn = document.querySelector(
    '[onclick="refreshApiQuestions()"]'
  );

  if (!apiSection || !refreshBtn) return;

  // Show loading state
  const originalContent = refreshBtn.innerHTML;
  refreshBtn.innerHTML = '<div class="spinner"></div> Loading...';
  refreshBtn.disabled = true;

  // Add loading spinner to the API section
  const loadingDiv = document.createElement('div');
  loadingDiv.className = 'questions-loading';
  loadingDiv.innerHTML =
    '<div class="spinner"></div> Fetching fresh API questions...';

  apiSection.style.opacity = '0.5';
  apiSection.insertBefore(loadingDiv, apiSection.firstChild);

  // Simulate API call (replace with actual implementation)
  setTimeout(() => {
    // For now, just remove loading and show a message
    apiSection.removeChild(loadingDiv);
    apiSection.style.opacity = '1';
    refreshBtn.innerHTML = originalContent;
    refreshBtn.disabled = false;

    // Show success message
    showNotification('API questions refreshed successfully!', 'success');

    // In a real implementation, you would:
    // 1. Make an AJAX call to refresh API questions
    // 2. Update the DOM with new questions
    // 3. Reinitialize event handlers
  }, 2000);
}

/**
 * Initialize keyboard shortcuts
 */
function initializeKeyboardShortcuts() {
  document.addEventListener('keydown', function (e) {
    // Ctrl/Cmd + A: Toggle all fallback questions
    if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
      e.preventDefault();
      toggleAllQuestions('fallback');
    }

    // Ctrl/Cmd + S: Toggle all API questions
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
      e.preventDefault();
      toggleAllQuestions('api');
    }

    // Ctrl/Cmd + R: Refresh API questions
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
      e.preventDefault();
      refreshApiQuestions();
    }

    // Escape: Collapse all questions
    if (e.key === 'Escape') {
      const allToggleButtons = document.querySelectorAll(
        '.btn-toggle.expanded'
      );
      allToggleButtons.forEach(button => {
        toggleQuestion(button);
      });
    }
  });
}

/**
 * Initialize entrance animations
 */
function initializeAnimations() {
  // Animate question cards on page load
  const questionCards = document.querySelectorAll('.question-card');
  questionCards.forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';

    setTimeout(() => {
      card.style.transition = 'all 0.4s ease-out';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, index * 100);
  });

  // Animate source cards
  const sourceCards = document.querySelectorAll('.source-card');
  sourceCards.forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';

    setTimeout(() => {
      card.style.transition = 'all 0.4s ease-out';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, index * 150);
  });

  // Animate stats section
  const statsSection = document.querySelector('.stats-section');
  if (statsSection) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const statItems = entry.target.querySelectorAll('.stat-item');
          statItems.forEach((item, index) => {
            setTimeout(() => {
              item.style.animation = 'expandContent 0.4s ease-out forwards';
            }, index * 100);
          });
          observer.unobserve(entry.target);
        }
      });
    });

    observer.observe(statsSection);
  }
}

/**
 * Show notification message
 * @param {string} message - The message to display
 * @param {string} type - 'success', 'error', 'warning', 'info'
 */
function showNotification(message, type = 'info') {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll(
    '.notification-toast'
  );
  existingNotifications.forEach(notification => {
    notification.remove();
  });

  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification-toast notification-${type}`;
  notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

  // Add styles
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    `;

  // Add to page
  document.body.appendChild(notification);

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentElement) {
      notification.style.animation = 'slideOutRight 0.3s ease-out';
      setTimeout(() => {
        notification.remove();
      }, 300);
    }
  }, 5000);
}

/**
 * Get notification icon based on type
 * @param {string} type
 * @returns {string}
 */
function getNotificationIcon(type) {
  const icons = {
    success: 'check-circle',
    error: 'exclamation-circle',
    warning: 'exclamation-triangle',
    info: 'info-circle',
  };
  return icons[type] || icons.info;
}

/**
 * Get notification color based on type
 * @param {string} type
 * @returns {string}
 */
function getNotificationColor(type) {
  const colors = {
    success: 'linear-gradient(135deg, #10b981, #059669)',
    error: 'linear-gradient(135deg, #ef4444, #dc2626)',
    warning: 'linear-gradient(135deg, #f59e0b, #d97706)',
    info: 'linear-gradient(135deg, #3b82f6, #2563eb)',
  };
  return colors[type] || colors.info;
}

/**
 * Search questions functionality (if needed in the future)
 * @param {string} searchTerm
 */
function searchQuestions(searchTerm) {
  const questionCards = document.querySelectorAll('.question-card');
  const normalizedSearch = searchTerm.toLowerCase().trim();

  questionCards.forEach(card => {
    const questionText = card
      .querySelector('.question-text')
      .textContent.toLowerCase();
    const optionTexts = Array.from(card.querySelectorAll('.option-text'))
      .map(option => option.textContent.toLowerCase())
      .join(' ');

    const isMatch =
      questionText.includes(normalizedSearch) ||
      optionTexts.includes(normalizedSearch);

    if (isMatch || !normalizedSearch) {
      card.style.display = 'block';
      card.style.animation = 'expandContent 0.3s ease-out';
    } else {
      card.style.display = 'none';
    }
  });
}

/**
 * Filter questions by category
 * @param {string} category - 'all', 'fallback', 'api'
 */
function filterQuestions(category) {
  const questionCards = document.querySelectorAll('.question-card');

  questionCards.forEach(card => {
    const cardCategory = card.getAttribute('data-category');

    if (category === 'all' || cardCategory === category) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
}

// Add CSS for animations and notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .notification-toast {
        border-left: 4px solid rgba(255, 255, 255, 0.5);
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
    }
    
    .notification-close {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.8);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .notification-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }
`;
document.head.appendChild(style);
