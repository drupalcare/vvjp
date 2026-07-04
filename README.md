# Views Vanilla JavaScript Parallax (VVJP)

A vanilla-JavaScript parallax-section **Views format** for Drupal 11.3+ / 12.
Renders Views rows as scroll-driven parallax sections — image background with a
configurable transform-on-scroll effect (translate / blur / opacity / scale /
rotate), color overlay, and configurable scroll effects on the foreground
content. Includes a mobile bypass that swaps in a regular `<img>` below a
configurable breakpoint.

v2.0 builds on the **`drupal/vvj_core` foundation** (composer auto-installs it).
Behavior runs in a `<vvjp-parallax>` custom element extending `VvjElementBase`.

## Features

- Scroll-tied background transform (RAF-throttled)
- Five effect modes: `translate` (default) / `blur` / `opacity` / `scale` /
  `rotate`
- Configurable parallax speed (0.1 - 2.0)
- 11 scroll-effect options for foreground content
- Configurable section height in `vw` / `vh` / `px` / `%` / `em` / `rem`
- 18 background-position options
- Configurable max content width
- Color overlay with adjustable opacity (or scoped to content only, or disabled)
- Mobile bypass: below the chosen breakpoint, the parallax is dropped and a
  regular `<img>` is rendered instead
- 6 responsive breakpoints (`all` / 576 / 768 / 992 / 1200 / 1400 px)
- `prefers-reduced-motion` honored — parallax offset is forced to 0 for reduced-
  motion users
- Lazy IntersectionObserver hydration; AbortController-tracked listeners
- **Views tokens** — `[vvjp:field]` / `[vvjp:field:plain]` in Views text areas
  with *Use replacement tokens from the first row*
- **Drupal admin help** — `/admin/help/vvjp`

## Token Support in Views Text Areas

In Views headers, footers, or empty text with *Use replacement tokens from the
first row*, default Twig tokens (`{{ title }}`) do not work. Use VVJP tokens
instead:

- `{{ title }}` → `[vvjp:title]`
- `{{ field_image }}` → `[vvjp:field_image]`
- Append `:plain` for plain text: `[vvjp:title:plain]`

v2 resolves tokens through the shared `vvj_core.token_resolver` service; syntax
is unchanged from v1. Tokens read from the **first row** of rendered View
fields. Complex field rewrites are not supported.

## Installation

```bash
composer require drupal/vvjp:^2.0
drush en vvjp
```

## Usage

1. Create or edit a View.
2. Set **Format** to *Views Vanilla JavaScript Parallax*.
3. Set **Show** to *Fields*.
4. The first field MUST be an image with the **URL to image** formatter — its
   URL becomes the parallax background.
5. Place a `<div class="vvjp-separator"></div>` field between the URL and the
   foreground content fields.
6. Configure responsive / dimensions / parallax / background / scroll effects /
   overlay in the format settings.
7. Save.

**Sample view:** Optional config `views.view.vvjp_example` (`config/optional/`)
installs when there is no ID conflict.

## Drop-in upgrade from 1.x

`composer update drupal/vvjp && drush updb && drush cr` is sufficient. v2
preserves:

- Plugin ID `views_vvjp`, theme hook `views_view_vvjp`
- Twig template names
- All option keys (incl. `section_height: {value, unit}` array shape)
- Library names (`vvjp`, `vvjp-style`, `vvjp__all/576/768/992/1200/1400`)
- JS behavior key `Drupal.behaviors.VVJParallax`
- All CSS class names (`.vvjp`, `.vvjp-inner`, `.parallax-row`, `.parallax-
  background`, `.parallax-overlay`, `.parallax-content`, `.parallax-content-
  inner`)
- Separator marker `<div class="vvjp-separator"></div>`

Outer rendered tag changed from `<div>` to `<vvjp-parallax>`. Theme selectors
targeting `.vvjp` keep matching.

## License

GPL-2.0-or-later.
