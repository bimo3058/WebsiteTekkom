/**
 * ════════════════════════════════════════════════════════════════════
 * Snackbar.js - Notification Component
 * ════════════════════════════════════════════════════════════════════
 * Handles display, auto-hide, and removal of snackbar notifications.
 * 
 * Usage:
 *   // Auto-initialize existing snackbars in DOM
 *   Snackbar.init();
 *   
 *   // Show programmatic snackbar
 *   Snackbar.show('Success!', 'success', 3000);
 *   
 *   // Remove/hide snackbar
 *   Snackbar.hide(element);
 */

class SnackbarManager {
  constructor() {
    this.snackbars = new Map();
    this.defaultTimeout = 5000; // 5 seconds
    this.animationDuration = 300; // ms
  }

  /**
   * Auto-initialize all snackbars already in DOM (from Blade flash messages)
   */
  init() {
    const snackbars = document.querySelectorAll('.snackbar');
    snackbars.forEach(snackbar => {
      this._setupSnackbar(snackbar);
      // Auto-hide after default timeout
      this._autoHide(snackbar, this.defaultTimeout);
    });
  }

  /**
   * Setup snackbar element (add close button handler, etc)
   * @private
   */
  _setupSnackbar(element) {
    if (this.snackbars.has(element)) return; // Already setup
    
    const closeBtn = element.querySelector('.snackbar-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.hide(element));
    }
    
    this.snackbars.set(element, {
      timeoutId: null,
      isShown: true
    });
  }

  /**
   * Auto-hide snackbar after X milliseconds
   * @private
   */
  _autoHide(element, timeout) {
    if (!this.snackbars.has(element)) {
      this._setupSnackbar(element);
    }

    const data = this.snackbars.get(element);
    if (data.timeoutId) clearTimeout(data.timeoutId);

    data.timeoutId = setTimeout(() => {
      this.hide(element);
    }, timeout);
  }

  /**
   * Show snackbar (programmatically create one if needed)
   * @param {string} message - Snackbar message/content
   * @param {string} type - 'success', 'error', 'warning', 'info'
   * @param {number} timeout - Auto-hide timeout in milliseconds (default: 5000)
   * @returns {HTMLElement} The snackbar element
   */
  show(message, type = 'info', timeout = this.defaultTimeout) {
    // Create snackbar element
    const snackbar = document.createElement('div');
    snackbar.className = `snackbar snackbar-${type}`;
    snackbar.setAttribute('role', 'alert');

    // Icon mapping
    const icons = {
      success: 'fas fa-check-circle',
      error: 'fas fa-exclamation-circle',
      warning: 'fas fa-exclamation-triangle',
      info: 'fas fa-info-circle'
    };

    const icon = icons[type] || icons.info;

    // Build HTML
    snackbar.innerHTML = `
      <i class="${icon}"></i>
      <span>${message}</span>
      <button type="button" class="snackbar-close" title="Tutup">
        <i class="fas fa-times"></i>
      </button>
    `;

    // Add to DOM
    document.body.appendChild(snackbar);

    // Setup and show
    this._setupSnackbar(snackbar);
    this._autoHide(snackbar, timeout);

    return snackbar;
  }

  /**
   * Hide and remove snackbar element
   * @param {HTMLElement} element - The snackbar element to hide
   */
  hide(element) {
    if (!element || !element.classList.contains('snackbar')) return;

    const data = this.snackbars.get(element);
    if (data?.timeoutId) clearTimeout(data.timeoutId);

    // Trigger slide-out animation
    element.style.animation = 'slideOutDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards';

    // Remove after animation completes
    setTimeout(() => {
      element.remove();
      this.snackbars.delete(element);
    }, this.animationDuration);
  }

  /**
   * Close all snackbars
   */
  closeAll() {
    const snackbars = Array.from(this.snackbars.keys());
    snackbars.forEach(snackbar => this.hide(snackbar));
  }
}

// Export singleton instance
const Snackbar = new SnackbarManager();

// Auto-initialize on DOM ready if not in module context
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    Snackbar.init();
  });
} else {
  Snackbar.init();
}
