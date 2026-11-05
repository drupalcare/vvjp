/**
 * @file
 * VvjpParallaxElement — `<vvjp-parallax>` custom element.
 *
 * Extends `Drupal.Vvj.ElementBase` from `vvj_core`. Scroll-driven
 * parallax: each `.parallax-row` reads its `data-parallax-speed` and
 * `data-parallax-effect` and updates its `.parallax-background`'s
 * transform/filter/opacity on every scroll frame (RAF-throttled).
 *
 * Below the configured breakpoint (`data-breakpoint`), the parallax
 * is bypassed: the background is hidden and a regular `<img>` is
 * inserted (matches v1 fallback behavior).
 *
 * Reduced-motion users skip the scroll-tied transform entirely
 * (the element stays at offset 0).
 *
 * The base class is pulled from the `Drupal.Vvj` namespace — Drupal
 * core's cross-file sharing pattern — with load order guaranteed by
 * the `vvjp` library's `dependencies:`.
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 */

// phpcs:disable Drupal.NamingConventions.UpperCaseConstant
// phpcs:disable Generic.PHP.UpperCaseConstant
// phpcs:disable Squiz.WhiteSpace.OperatorSpacing

((Drupal) => {
  const { ElementBase } = Drupal.Vvj;

  class VvjpParallaxElement extends ElementBase {
    static get patternSlug() {
      return 'vvjp';
    }

    constructor() {
      super();
      this._rows = [];
      this._scrollPending = false;
    }

    onHydrate() {
      this._rows = this.querySelectorAll('.parallax-row');
      if (!this._rows.length) {
        return;
      }

      this._update();

      const onScroll = () => {
        if (this._scrollPending) {
          return;
        }
        this._scrollPending = true;
        requestAnimationFrame(() => {
          this._scrollPending = false;
          this._update();
        });
      };

      window.addEventListener('scroll', onScroll, {
        passive: true,
        signal: this.signal,
      });
      window.addEventListener('resize', () => this._update(), {
        signal: this.signal,
      });
    }

    /**
     * Update every parallax row for the current scroll position +
     * viewport width.
     */
    _update() {
      const viewportWidth =
        window.innerWidth || document.documentElement.clientWidth;
      const scrollPosition = window.scrollY;
      const windowHeight = window.innerHeight;
      const reducedMotion = this.reducedMotion;
      // Pattern views only: per-row scroll progress and the shared state.
      const patterned = this.classList.contains('vvj-pattern');
      let below = false;
      let active = -1;

      this._rows.forEach((row, rowIndex) => {
        const breakpoint = row.getAttribute('data-breakpoint') ?? 'all';
        const breakpointPx = parseInt(breakpoint, 10);
        const isMobile =
          breakpoint !== 'all' &&
          Number.isFinite(breakpointPx) &&
          breakpointPx > 0 &&
          viewportWidth <= breakpointPx;

        const background = row.querySelector('.parallax-background');
        const content = row.querySelector('.parallax-content');

        if (isMobile) {
          below = true;
          if (patterned) {
            this._writeRowProgress(row, 1, 1);
          }
          // Below breakpoint — bypass parallax, swap in a regular image.
          if (background) {
            background.style.display = 'none';
            if (!row.querySelector('img.vvjp-fallback-img')) {
              const url = this._extractBackgroundUrl(background);
              if (url !== '') {
                const img = document.createElement('img');
                img.src = url;
                img.alt = '';
                img.className = 'vvjp-fallback-img';
                img.style.width = '100%';
                img.style.height = 'auto';
                row.insertBefore(img, content);
              }
            }
          }
          return;
        }

        // Above breakpoint — re-show background, drop the fallback img.
        if (background) {
          background.style.display = '';
        }
        const fallback = row.querySelector('img.vvjp-fallback-img');
        if (fallback) {
          fallback.remove();
        }

        const speed =
          parseFloat(row.getAttribute('data-parallax-speed')) || 0.5;
        const effect = row.getAttribute('data-parallax-effect') || 'translate';
        const elementRect = row.getBoundingClientRect();
        const elementTop = elementRect.top + scrollPosition;
        const elementHeight = row.offsetHeight;

        if (patterned) {
          const enter = reducedMotion
            ? 1
            : (windowHeight - elementRect.top) / (windowHeight * 0.75);
          const progress = reducedMotion
            ? 1
            : (windowHeight - elementRect.top) / (windowHeight + elementHeight);
          this._writeRowProgress(row, enter, progress);
          if (elementRect.top <= windowHeight / 2) {
            active = rowIndex;
          }
        }

        const inViewport =
          scrollPosition + windowHeight > elementTop &&
          scrollPosition < elementTop + elementHeight;

        if (!inViewport) {
          if (content) {
            content.classList.remove('visible');
          }
          return;
        }

        if (background) {
          const offset = reducedMotion
            ? 0
            : (scrollPosition + windowHeight - elementTop) * speed -
              windowHeight * speed;
          this._applyEffect(background, effect, offset);
        }
        if (content) {
          content.classList.add('visible');
        }
      });

      if (patterned) {
        this.classList.toggle('vvjp-below', below);
        this.writeState({
          index: Math.max(0, active),
          total: this._rows.length,
          items: this._rows,
        });
      }
    }

    /**
     * Write `--vvjp-enter` and `--vvjp-progress` (0 to 1) on a row.
     *
     * Pattern views only. `enter` reaches 1 once the row's top has risen
     * three quarters of the viewport; `progress` runs across the whole time
     * the row is on screen.
     *
     * @param {HTMLElement} row
     *   The `.parallax-row` element.
     * @param {number} enter
     *   Raw enter value, clamped here.
     * @param {number} progress
     *   Raw progress value, clamped here.
     */
    _writeRowProgress(row, enter, progress) {
      const clamp = (n) => String(Math.min(1, Math.max(0, n)).toFixed(3));
      row.style.setProperty('--vvjp-enter', clamp(enter));
      row.style.setProperty('--vvjp-progress', clamp(progress));
    }

    /**
     * Extract `url("...")` from a `background-image` inline style.
     *
     * @param {HTMLElement} bg
     *   The element with the background-image set.
     * @return {string}
     *   The extracted URL or empty string.
     */
    _extractBackgroundUrl(bg) {
      const raw = bg.style.backgroundImage || '';
      const match = raw.match(/url\(["']?(.*?)["']?\)/);
      return match ? match[1] : '';
    }

    /**
     * Apply the configured effect to the background element.
     *
     * @param {HTMLElement} bg
     *   `.parallax-background` element.
     * @param {string} effect
     *   Effect name from `data-parallax-effect`.
     * @param {number} offset
     *   Computed scroll-tied offset.
     */
    _applyEffect(bg, effect, offset) {
      let transform = '';
      switch (effect) {
        case 'blur': {
          const blur = Math.min(Math.abs(offset) / 100, 10);
          bg.style.filter = `blur(${blur}px)`;
          return;
        }
        case 'opacity': {
          const op = Math.max(1 - Math.abs(offset) / 1000, 0);
          bg.style.opacity = String(op);
          return;
        }
        case 'scale':
          transform = `scale(${Math.max(1 + Math.abs(offset) / 1000, 1)})`;
          break;

        case 'rotate':
          transform = `rotate(${offset / 10}deg)`;
          break;

        case 'translate':
        default:
          transform = `translateY(${offset}px)`;
      }
      bg.style.transform = transform;
    }
  }

  if (!customElements.get('vvjp-parallax')) {
    customElements.define('vvjp-parallax', VvjpParallaxElement);
  }
})(Drupal);
