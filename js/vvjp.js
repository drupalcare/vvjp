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
        const viewportWidth = window.innerWidth || document.documentElement.clientWidth;

        parallaxElements.forEach(element => {
          const breakpoint = element.getAttribute('data-breakpoint');

          // Check if breakpoint is "all" and handle it
          const isMobile = breakpoint !== 'all' && parseInt(breakpoint, 10) > 0 && viewportWidth <= parseInt(breakpoint, 10);

          if (isMobile) {
            // Disable parallax on mobile by hiding the background and adding a regular image
            const background = element.querySelector('.parallax-background');
            const content = element.querySelector('.parallax-content');

            if (background) {
              background.style.display = 'none'; // Hide the parallax background

              // Insert a regular image if it doesn't already exist
              if (!element.querySelector('img')) {
                const img = document.createElement('img');
                img.src = background.style.backgroundImage.slice(5, -2); // Extract the URL from background-image
                img.alt = ''; // Optionally, add an alt attribute here
                img.style.width = '100%';
                img.style.height = 'auto';

                element.insertBefore(img, content); // Insert the image before the content
              }
            }
            return; // Exit the function early if mobile
          } else {
            // Handle the case where "all" is set or the screen is larger than the breakpoint
            const background = element.querySelector('.parallax-background');
            if (background) {
              background.style.display = ''; // Show the parallax background
            }

            // Remove any regular image that was added for small screens
            const img = element.querySelector('img');
            if (img) {
              img.remove();
            }
          }

          // The following logic applies the parallax effect on non-mobile screens
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

      // Handle window resize to update viewport width and parallax elements
      const handleResize = () => {
        updateParallax(); // Re-run parallax update to adjust images on resize
      };

      // Listen to the resize event and update parallax
      window.addEventListener('resize', handleResize);

      // Initial call to position elements correctly
      updateParallax();
    }
  };
})(Drupal, drupalSettings, once);


