/**
 * Loader Helper Functions
 * Easy-to-use functions for showing and hiding loaders throughout the app
 */

class TriviaLoader {
  constructor() {
    this.defaultOverlayId = 'app-loader-overlay';
    this.init();
  }

  init() {
    // Create a default overlay loader if it doesn't exist
    if (!document.getElementById(this.defaultOverlayId)) {
      this.createDefaultOverlay();
    }
  }

  createDefaultOverlay() {
    const overlay = document.createElement('div');
    overlay.id = this.defaultOverlayId;
    overlay.className = 'loader-overlay';
    overlay.style.display = 'none';

    overlay.innerHTML = `
            <div class="loader-backdrop"></div>
            <div class="loader">
                <div class="cell d-0"></div>
                <div class="cell d-1"></div>
                <div class="cell d-2"></div>
                <div class="cell d-1"></div>
                <div class="cell d-2"></div>
                <div class="cell d-2"></div>
                <div class="cell d-3"></div>
                <div class="cell d-3"></div>
                <div class="cell d-4"></div>
            </div>
        `;

    document.body.appendChild(overlay);
  }

  /**
   * Show the overlay loader
   * @param {string} id - Optional loader ID (defaults to app-loader-overlay)
   */
  show(id = this.defaultOverlayId) {
    const loader = document.getElementById(id);
    if (loader) {
      loader.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
  }

  /**
   * Hide the overlay loader
   * @param {string} id - Optional loader ID (defaults to app-loader-overlay)
   */
  hide(id = this.defaultOverlayId) {
    const loader = document.getElementById(id);
    if (loader) {
      loader.style.display = 'none';
      document.body.style.overflow = '';
    }
  }

  /**
   * Show loader for a specific duration
   * @param {number} duration - Duration in milliseconds
   * @param {string} id - Optional loader ID
   */
  showFor(duration, id = this.defaultOverlayId) {
    this.show(id);
    setTimeout(() => {
      this.hide(id);
    }, duration);
  }

  /**
   * Show loader while a promise is executing
   * @param {Promise} promise - The promise to wait for
   * @param {string} id - Optional loader ID
   */
  async showWhile(promise, id = this.defaultOverlayId) {
    this.show(id);
    try {
      const result = await promise;
      return result;
    } finally {
      this.hide(id);
    }
  }

  /**
   * Toggle loader visibility
   * @param {string} id - Optional loader ID
   */
  toggle(id = this.defaultOverlayId) {
    const loader = document.getElementById(id);
    if (loader) {
      if (loader.style.display === 'none' || loader.style.display === '') {
        this.show(id);
      } else {
        this.hide(id);
      }
    }
  }
}

// Create global instance
const triviaLoader = new TriviaLoader();

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = TriviaLoader;
}

// Global convenience functions
window.showLoader = id => triviaLoader.show(id);
window.hideLoader = id => triviaLoader.hide(id);
window.toggleLoader = id => triviaLoader.toggle(id);
window.showLoaderFor = (duration, id) => triviaLoader.showFor(duration, id);
window.showLoaderWhile = (promise, id) => triviaLoader.showWhile(promise, id);

// jQuery support if available
if (typeof $ !== 'undefined') {
  $.fn.showLoader = function () {
    this.each(function () {
      triviaLoader.show(this.id);
    });
    return this;
  };

  $.fn.hideLoader = function () {
    this.each(function () {
      triviaLoader.hide(this.id);
    });
    return this;
  };
}
