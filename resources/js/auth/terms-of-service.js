// Terms of Service Page JavaScript
document.addEventListener('DOMContentLoaded', function () {
  // Enhanced scroll progress indicator with smooth animation
  let ticking = false;

  function updateScrollProgress() {
    const scrollProgress = document.getElementById('scrollProgress');
    if (!scrollProgress) return;

    const scrollTop = document.documentElement.scrollTop;
    const scrollHeight =
      document.documentElement.scrollHeight -
      document.documentElement.clientHeight;
    const scrollPercentage = (scrollTop / scrollHeight) * 100;
    scrollProgress.style.width = scrollPercentage + '%';
    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(updateScrollProgress);
      ticking = true;
    }
  });

  // Enhanced smooth scroll for internal links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        const offsetTop = target.offsetTop - 100;
        window.scrollTo({
          top: offsetTop,
          behavior: 'smooth',
        });
      }
    });
  });

  // Enhanced intersection observer with staggered animations
  const observerOptions = {
    threshold: [0.1, 0.3, 0.5],
    rootMargin: '0px 0px -80px 0px',
  };

  const observer = new IntersectionObserver(entries => {
    entries.forEach((entry, index) => {
      if (entry.isIntersecting) {
        // Add staggered delay based on element position
        setTimeout(() => {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0) scale(1)';
          entry.target.classList.add('animate-in');
        }, index * 100);
      }
    });
  }, observerOptions);

  // Observe all sections and subsections
  document
    .querySelectorAll('.section, .subsection')
    .forEach((section, index) => {
      section.style.transform = 'translateY(30px) scale(0.98)';
      section.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
      observer.observe(section);
    });

  // Enhanced parallax effect for background elements
  window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const rate = scrolled * -0.3;

    // Apply parallax to background
    document.body.style.backgroundPosition = `center ${rate}px`;

    // Add subtle movement to cards
    document.querySelectorAll('.subsection').forEach((element, index) => {
      const speed = 0.1 + index * 0.02;
      const yPos = -(scrolled * speed);
      element.style.transform = `translateY(${yPos}px)`;
    });
  });

  // Enhanced loading animation with fade-in sequence
  window.addEventListener('load', () => {
    document.body.style.opacity = '1';

    // Animate container entrance
    setTimeout(() => {
      const container = document.querySelector('.terms-container');
      if (container) {
        container.style.transform = 'translateY(0) scale(1)';
        container.style.opacity = '1';
      }
    }, 200);

    // Animate header elements
    setTimeout(() => {
      const header = document.querySelector('.terms-header');
      if (header) {
        header.style.transform = 'translateY(0)';
        header.style.opacity = '1';
      }
    }, 500);
  });

  // Initial setup with enhanced animations
  document.body.style.opacity = '0';
  document.body.style.transition = 'opacity 0.8s ease';

  const container = document.querySelector('.terms-container');
  if (container) {
    container.style.transform = 'translateY(50px) scale(0.95)';
    container.style.opacity = '0';
    container.style.transition = 'all 1s cubic-bezier(0.4, 0, 0.2, 1)';
  }

  const header = document.querySelector('.terms-header');
  if (header) {
    header.style.transform = 'translateY(30px)';
    header.style.opacity = '0';
    header.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
  }

  // Add mouse movement parallax effect
  document.addEventListener('mousemove', e => {
    const mouseX = e.clientX / window.innerWidth;
    const mouseY = e.clientY / window.innerHeight;

    // Subtle parallax for floating elements
    document.querySelectorAll('.section-title').forEach((element, index) => {
      const speed = 5 + index * 2;
      const x = (mouseX - 0.5) * speed;
      const y = (mouseY - 0.5) * speed;

      const beforeElement = window.getComputedStyle(element, '::before');
      if (beforeElement) {
        element.style.setProperty('--mouse-x', `${x}px`);
        element.style.setProperty('--mouse-y', `${y}px`);
      }
    });
  });

  // Add keyboard navigation enhancement
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      const banner = document.querySelector('.update-banner');
      if (banner && banner.style.display !== 'none') {
        dismissUpdateBanner();
      }
    }
  });

  // Add focus enhancement for accessibility
  document.querySelectorAll('a, button').forEach(element => {
    element.addEventListener('focus', function () {
      this.style.outline = '2px solid #8b5cf6';
      this.style.outlineOffset = '2px';
      this.style.boxShadow = '0 0 0 4px rgba(139, 92, 246, 0.2)';
    });

    element.addEventListener('blur', function () {
      this.style.outline = 'none';
      this.style.outlineOffset = '0';
      this.style.boxShadow = '';
    });
  });
});

// Enhanced dismiss update banner with smooth animation
function dismissUpdateBanner() {
  const banner = document.querySelector('.update-banner');
  if (banner) {
    banner.style.transform = 'translateY(-20px) scale(0.95)';
    banner.style.opacity = '0';
    setTimeout(() => {
      banner.style.display = 'none';
      localStorage.setItem('terms-update-dismissed', Date.now());
    }, 400);
  }
}

// Check banner dismissal with enhanced logic
window.addEventListener('load', () => {
  const banner = document.querySelector('.update-banner');
  const dismissed = localStorage.getItem('terms-update-dismissed');

  if (banner && dismissed) {
    const dismissedTime = parseInt(dismissed);
    const oneDayAgo = Date.now() - 24 * 60 * 60 * 1000;

    if (dismissedTime > oneDayAgo) {
      banner.style.display = 'none';
    } else {
      localStorage.removeItem('terms-update-dismissed');
    }
  }
});

// Reading progress tracker
function addReadingProgressTracker() {
  const sections = document.querySelectorAll('.section');
  const progressBar = document.querySelector('.scroll-progress');

  if (!sections.length || !progressBar) return;

  const sectionObserver = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const sectionIndex = Array.from(sections).indexOf(entry.target);
          const progress = ((sectionIndex + 1) / sections.length) * 100;

          // Add a subtle color change based on reading progress
          const hue = Math.floor(progress * 2.4); // 0-240 for full spectrum
          progressBar.style.background = `linear-gradient(90deg, hsl(${hue}, 70%, 60%), hsl(${hue + 30}, 70%, 60%))`;
        }
      });
    },
    {
      threshold: 0.5,
    }
  );

  sections.forEach(section => sectionObserver.observe(section));
}

// Initialize reading progress tracker
document.addEventListener('DOMContentLoaded', addReadingProgressTracker);

// Smooth scroll to top functionality
function addScrollToTop() {
  const scrollToTopBtn = document.createElement('button');
  scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
  scrollToTopBtn.className = 'scroll-to-top';
  scrollToTopBtn.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #8b5cf6, #3b82f6);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        z-index: 1000;
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    `;

  document.body.appendChild(scrollToTopBtn);

  // Show/hide button based on scroll position
  window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
      scrollToTopBtn.style.opacity = '1';
      scrollToTopBtn.style.transform = 'scale(1)';
    } else {
      scrollToTopBtn.style.opacity = '0';
      scrollToTopBtn.style.transform = 'scale(0.8)';
    }
  });

  // Scroll to top functionality
  scrollToTopBtn.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth',
    });
  });

  // Hover effects
  scrollToTopBtn.addEventListener('mouseenter', () => {
    scrollToTopBtn.style.transform = 'scale(1.1)';
    scrollToTopBtn.style.boxShadow = '0 6px 20px rgba(139, 92, 246, 0.4)';
  });

  scrollToTopBtn.addEventListener('mouseleave', () => {
    scrollToTopBtn.style.transform = 'scale(1)';
    scrollToTopBtn.style.boxShadow = '0 4px 12px rgba(139, 92, 246, 0.3)';
  });
}

// Initialize scroll to top
document.addEventListener('DOMContentLoaded', addScrollToTop);
