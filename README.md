# Views Vanilla JavaScript Parallax

## Overview
The **Views Vanilla JavaScript Parallax** module (machine name: `vvjp`) is a 
powerful and flexible Views style plugin designed to create dynamic and 
responsive parallax effects for displaying content items. This module enhances 
the visual appeal of your Drupal site by leveraging pure vanilla JavaScript for 
seamless parallax scrolling experiences.

## Features
- **Parallax Effects**: Renders content items with smooth parallax effects for 
  engaging visual displays.
- **Customizable Options**: Offers adjustable settings for speed, background 
  position, overlay color and opacity, animation speed, easing, and more.
- **Responsive Design**: Includes breakpoints to ensure the parallax adapts 
  seamlessly to different screen sizes.
- **Scroll Effects**: Choose from various effects like fade, scale, rotate, 3D 
  transformations, shadow, glow, smooth transitions, hover interactions, and 
  combined effects.
- **Unique ID Generation**: Automatically generates a unique numeric ID for each 
  view display to ensure consistency and prevent conflicts.

## Configuration
After installing and enabling the module, you can configure it through the Views 
UI:

1. **Parallax Speed**: Set the speed of the parallax effect (e.g., 0.1 for slow, 
   2 for fast).
2. **Background Position**: Choose the position of the background image (e.g., 
   center, top, bottom, left, right).
3. **Overlay Color**: Select a color overlay for the parallax background.
4. **Overlay Opacity**: Define the opacity level for the overlay.
5. **Background Animation Speed**: Set the speed of the background animation.
6. **Background Animation Easing**: Choose the easing function for the 
   animation (e.g., ease, ease-in, ease-out, linear).
7. **Scroll Effect**: Select from various effects like fade, scale, rotate, 3D 
   transformations, shadow, glow, smooth transitions, hover interactions, or 
   combined effects.
8. **Section Height**: Define the height of the parallax section in viewport 
   height units.
9. **Available Breakpoints**: Select the maximum screen width (in pixels) at 
   which the parallax effect should be disabled. Selecting "none" will keep the 
   parallax effect active on all screen sizes.

## Token Support in Views Text Areas

In Views headers, footers, or empty text areas—when using *Global: Text area* or
*Global: Unfiltered text*—there is an option called **"Use replacement tokens
from the first row."**

The default Twig-style tokens (e.g., `{{ title }}` or `{{ field_image }}`)
**will not work** with the VVJP style. Instead, use the custom tokens provided
by VVJP:

**Examples:**

- `{{ title }}` → `[vvjp:title]`  
- `{{ field_image }}` → `[vvjp:field_image]`

To strip any HTML from the output, you can append `:plain` to the token:

- `[vvjp:title:plain]`

These tokens pull data from the **first row** of the View result and are
designed to work seamlessly with the VVJP rendering system.

## Example Usage
1. Create a new view or edit an existing view.
2. Under the "Format" section, select "Views Vanilla JavaScript Parallax" as the 
   style.
3. Configure the parallax speed, background position, overlay settings, 
   animation type, and responsive breakpoints as needed.
4. Save the view and see the parallax effect in action on the front end.

## Additional Information
- **Dependencies**: This module does not have any dependencies on other 
  contributed modules.
- **Compatibility**: Fully compatible with Drupal 10 and follows Drupal best 
  practices.

## Installation
1. Download and install the module from 
   [Drupal.org](https://www.drupal.org/project/vvjp).
2. Enable the module through the Drupal admin interface or using Drush: 
   `drush en vvjp`
3. Clear caches to ensure the new plugin is available: `drush cr`

## Troubleshooting
If you encounter any issues or have suggestions for improvements, please open 
an issue in the module's issue queue on Drupal.org.

## Maintainers
- [Alaa Haddad](https://www.drupal.org/u/flashwebcenter)

## Support
For issues, feature requests, and general support, please use the 
[issue queue](https://www.drupal.org/project/issues/vvjp).

## License
This project is licensed under the 
[GNU General Public License, version 2 or later](http://www.gnu.org/licenses/gpl-2.0.html).

---

This file follows the Drupal best practices for module documentation, ensuring 
that users have a clear understanding of the module's purpose, features, and 
usage. It also includes important notes on responsive design and accessibility 
to guide users in setting up the module correctly.
