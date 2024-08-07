/**
 * @file
 * VVJ Parallax.
 *
 * Filename:     vvjp.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */


((Drupal, drupalSettings, once) => {
  'use strict';

  Drupal.behaviors.VVJParallax = {
    attach: function(context, settings) {
      const parallaxElements = once('vvj-parallax', '.parallax-row', context);

      // Function to update parallax elements based on scroll position
      const updateParallax = () => {
        parallaxElements.forEach(element => {
          const breakpoint = parseInt(element.getAttribute('data-breakpoint'), 10);

          // Disable parallax if the window width is smaller than the breakpoint (and breakpoint is not zero)
          const isMobile = breakpoint > 0 && window.innerWidth <= breakpoint;

          if (isMobile) {
            // Disable parallax on mobile by removing styles and stopping execution
            const background = element.querySelector('.parallax-background');
            if (background) {
              background.style.transform = 'none';
              background.style.transition = 'none';
              background.style.filter = 'none';
            }
            return; // Exit the function early if mobile
          }

          const scrollPosition = window.pageYOffset;
          const windowHeight = window.innerHeight;

          const speed = parseFloat(element.getAttribute('data-parallax-speed')) || 0.5;
          const effect = element.getAttribute('data-parallax-effect') || 'translate';
          const elementRect = element.getBoundingClientRect();
          const elementTop = elementRect.top + scrollPosition;
          const elementHeight = element.offsetHeight;

          if (scrollPosition + windowHeight > elementTop && scrollPosition < elementTop + elementHeight) {
            const offset = ((scrollPosition + windowHeight - elementTop) * speed) - (windowHeight * speed);
            const background = element.querySelector('.parallax-background');
            const content = element.querySelector('.parallax-content');

            if (background) {
              let transform = '';

              switch (effect) {
                case 'translate':
                  transform = `translateY(${offset}px)`;
                  break;
                case 'blur':
                  const blurAmount = Math.min(Math.abs(offset) / 100, 10);
                  background.style.filter = `blur(${blurAmount}px)`;
                  break;
                case 'opacity':
                  const opacity = Math.max(1 - Math.abs(offset) / 1000, 0);
                  background.style.opacity = opacity;
                  break;
                case 'scale':
                  const scaleAmount = Math.max(1 + Math.abs(offset) / 1000, 1);
                  transform = `scale(${scaleAmount})`;
                  break;
                case 'rotate':
                  const rotateAmount = offset / 10;
                  transform = `rotate(${rotateAmount}deg)`;
                  break;
                default:
                  transform = `translateY(${offset}px)`;
              }

              background.style.transform = transform;

              if (content) {
                content.classList.add('visible'); // Add visible class when in view
              }
            }
          } else {
            const content = element.querySelector('.parallax-content');
            if (content) {
              content.classList.remove('visible'); // Remove visible class when out of view
            }
          }
        });
      };

      // Function to handle scroll events using requestAnimationFrame
      const handleScroll = () => {
        requestAnimationFrame(updateParallax);
      };

      // Listen to the scroll event and update parallax
      window.addEventListener('scroll', handleScroll);

      // Initial call to position elements correctly
      updateParallax();
    }
  };
})(Drupal, drupalSettings, once);
