// Admin Users Page Enhanced JavaScript
document.addEventListener('DOMContentLoaded', function () {
  // Enhanced search functionality
  const searchInput = document.querySelector('.search-input');
  const filterSelect = document.querySelector('.filter-select');
  const userRows = document.querySelectorAll('.user-row');

  // Real-time search (debounced)
  if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', function () {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        const searchTerm = this.value.toLowerCase();

        userRows.forEach(row => {
          const userName =
            row.querySelector('.user-name')?.textContent.toLowerCase() || '';
          const userEmail =
            row.querySelector('.email')?.textContent.toLowerCase() || '';

          if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
            row.style.display = '';
            row.style.animation = 'fadeIn 0.3s ease-in';
          } else {
            row.style.display = 'none';
          }
        });

        // Update results count
        updateResultsCount();
      }, 300);
    });
  }

  // Enhanced form submission with loading states
  const toggleForms = document.querySelectorAll('.toggle-form');
  toggleForms.forEach(form => {
    form.addEventListener('submit', function (e) {
      const button = this.querySelector('.btn');
      const originalContent = button.innerHTML;

      // Add loading state
      button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
      button.disabled = true;
      this.classList.add('loading');

      // If user cancels in confirm dialog, restore button
      setTimeout(() => {
        if (!this.classList.contains('submitted')) {
          button.innerHTML = originalContent;
          button.disabled = false;
          this.classList.remove('loading');
        }
      }, 100);
    });
  });

  // Enhanced table interactions
  const tableRows = document.querySelectorAll('.user-row');
  tableRows.forEach((row, index) => {
    // Staggered animation on page load
    row.style.opacity = '0';
    row.style.transform = 'translateY(20px)';

    setTimeout(() => {
      row.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
      row.style.opacity = '1';
      row.style.transform = 'translateY(0)';
    }, index * 50);

    // Enhanced hover effects
    row.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-2px) scale(1.01)';
      this.style.zIndex = '10';
    });

    row.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0) scale(1)';
      this.style.zIndex = '1';
    });
  });

  // Keyboard navigation
  document.addEventListener('keydown', function (e) {
    // Escape key to clear search
    if (e.key === 'Escape' && searchInput) {
      searchInput.value = '';
      searchInput.dispatchEvent(new Event('input'));
      searchInput.focus();
    }

    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      if (searchInput) {
        searchInput.focus();
        searchInput.select();
      }
    }
  });

  // Auto-save filter preferences
  if (filterSelect) {
    filterSelect.addEventListener('change', function () {
      localStorage.setItem('admin-users-filter', this.value);
    });

    // Restore filter preference
    const savedFilter = localStorage.getItem('admin-users-filter');
    if (savedFilter) {
      filterSelect.value = savedFilter;
    }
  }

  // Enhanced alerts with auto-dismiss
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    closeBtn.className = 'alert-close';
    closeBtn.style.cssText = `
            background: none;
            border: none;
            color: currentColor;
            cursor: pointer;
            padding: 0;
            margin-left: auto;
            opacity: 0.7;
            transition: opacity 0.3s;
        `;

    closeBtn.addEventListener('click', () => {
      alert.style.animation = 'slideOutUp 0.3s ease-in';
      setTimeout(() => alert.remove(), 300);
    });

    closeBtn.addEventListener('mouseenter', () => {
      closeBtn.style.opacity = '1';
    });

    closeBtn.addEventListener('mouseleave', () => {
      closeBtn.style.opacity = '0.7';
    });

    alert.appendChild(closeBtn);

    // Auto-dismiss success alerts after 5 seconds
    if (alert.classList.contains('alert-success')) {
      setTimeout(() => {
        if (document.contains(alert)) {
          closeBtn.click();
        }
      }, 5000);
    }
  });

  // Smooth scrolling for pagination
  const paginationLinks = document.querySelectorAll('.pagination-btn');
  paginationLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      if (this.href) {
        e.preventDefault();

        // Add loading state to pagination
        const container = document.querySelector('.table-section');
        if (container) {
          container.style.opacity = '0.6';
          container.style.pointerEvents = 'none';
        }

        // Navigate to new page
        window.location.href = this.href;
      }
    });
  });

  // Table sorting (client-side for current page)
  const tableHeaders = document.querySelectorAll('.users-table th');
  tableHeaders.forEach((header, index) => {
    if (index === 0 || index === 1 || index === 3) {
      header.style.cursor = 'pointer';
      header.style.userSelect = 'none';

      header.addEventListener('click', function () {
        sortTable(index);
      });

      // Add sort indicator
      const sortIcon = document.createElement('i');
      sortIcon.className = 'fas fa-sort sort-icon';
      sortIcon.style.marginLeft = '0.5rem';
      sortIcon.style.opacity = '0.5';
      header.appendChild(sortIcon);
    }
  });

  // Utility functions
  function updateResultsCount() {
    const visibleRows = document.querySelectorAll(
      '.user-row[style*="display: none"]'
    ).length;
    const totalRows = userRows.length;
    const showing = totalRows - visibleRows;

    // Update pagination info if exists
    const paginationText = document.querySelector('.pagination-text');
    if (paginationText && searchInput.value) {
      paginationText.textContent = `Showing ${showing} of ${totalRows} filtered results`;
    }
  }

  function sortTable(columnIndex) {
    const table = document.querySelector('.users-table tbody');
    const rows = Array.from(table.querySelectorAll('.user-row'));
    const header = tableHeaders[columnIndex];
    const icon = header.querySelector('.sort-icon');

    // Reset other sort icons
    tableHeaders.forEach(h => {
      const i = h.querySelector('.sort-icon');
      if (i && i !== icon) {
        i.className = 'fas fa-sort sort-icon';
      }
    });

    // Determine sort direction
    const isAsc = icon.classList.contains('fa-sort-up');
    const isDesc = icon.classList.contains('fa-sort-down');

    let sortDirection = 'asc';
    if (isAsc) {
      sortDirection = 'desc';
      icon.className = 'fas fa-sort-down sort-icon';
    } else {
      sortDirection = 'asc';
      icon.className = 'fas fa-sort-up sort-icon';
    }

    // Sort rows
    rows.sort((a, b) => {
      let aValue, bValue;

      switch (columnIndex) {
        case 0: // Name
          aValue = a.querySelector('.user-name').textContent.toLowerCase();
          bValue = b.querySelector('.user-name').textContent.toLowerCase();
          break;
        case 1: // Email
          aValue = a.querySelector('.email').textContent.toLowerCase();
          bValue = b.querySelector('.email').textContent.toLowerCase();
          break;
        case 3: // Registration date
          aValue = new Date(a.querySelector('.date').textContent);
          bValue = new Date(b.querySelector('.date').textContent);
          break;
        default:
          return 0;
      }

      if (sortDirection === 'asc') {
        return aValue > bValue ? 1 : -1;
      } else {
        return aValue < bValue ? 1 : -1;
      }
    });

    // Re-append sorted rows
    rows.forEach(row => table.appendChild(row));

    // Add animation
    rows.forEach((row, index) => {
      row.style.animation = `slideInLeft 0.3s ease-out ${index * 0.02}s both`;
    });
  }

  // Add custom CSS animations
  const style = document.createElement('style');
  style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideOutUp {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .sort-icon {
            transition: all 0.3s ease;
        }
        
        .alert-close:hover {
            transform: scale(1.1);
        }
    `;
  document.head.appendChild(style);

  console.log('Admin Users page enhanced JavaScript loaded successfully!');
});
