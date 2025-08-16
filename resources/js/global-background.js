// Global Background JavaScript Helper
// Ensures particles are properly initialized and animations work smoothly

document.addEventListener('DOMContentLoaded', function () {
  // Initialize global background if particles container exists
  const particlesContainer = document.querySelector('.global-particles');

  if (particlesContainer) {
    // Add more particles dynamically for better effect
    addRandomParticles(particlesContainer, 15);

    // Restart animations on page load to ensure smooth experience
    restartParticleAnimations();
  }

  // Handle page transitions smoothly
  handlePageTransitions();
});

function addRandomParticles(container, count) {
  for (let i = 0; i < count; i++) {
    const particle = document.createElement('div');
    particle.className = 'global-particle';

    // Random positioning and timing
    const leftPercent = Math.random() * 100;
    const animationDelay = Math.random() * 20;
    const animationDuration = 16 + Math.random() * 16; // 16-32s

    particle.style.left = leftPercent + '%';
    particle.style.animationDelay = animationDelay + 's';
    particle.style.animationDuration = animationDuration + 's';

    // Random particle color (matching our theme)
    const colors = [
      'rgba(6, 182, 212, 0.4)', // Cyan
      'rgba(139, 92, 246, 0.4)', // Purple
      'rgba(16, 185, 129, 0.4)', // Green
    ];
    particle.style.background =
      colors[Math.floor(Math.random() * colors.length)];

    container.appendChild(particle);
  }
}

function restartParticleAnimations() {
  const particles = document.querySelectorAll('.global-particle');
  particles.forEach(particle => {
    // Force animation restart
    particle.style.animation = 'none';
    particle.offsetHeight; // Trigger reflow
    particle.style.animation = null;
  });
}

function handlePageTransitions() {
  // Add smooth transitions for SPA-like experience
  const links = document.querySelectorAll('a[href]');
  links.forEach(link => {
    if (link.hostname === window.location.hostname) {
      link.addEventListener('click', function (e) {
        // Add transition effect
        document.body.style.transition = 'opacity 0.3s ease';
        setTimeout(() => {
          document.body.style.opacity = '0.9';
        }, 50);
      });
    }
  });
}

// Utility function to ensure background persists
function ensureGlobalBackground() {
  if (!document.querySelector('.global-background')) {
    const background = document.createElement('div');
    background.className = 'global-background';
    document.body.insertBefore(background, document.body.firstChild);
  }
}

// Export for manual calling if needed
window.GlobalBackground = {
  addRandomParticles,
  restartParticleAnimations,
  ensureGlobalBackground,
};
