# Views Vanilla JavaScript Parallax (VVJP)

A vanilla-JavaScript parallax-section **Views format** for Drupal.
Renders Views rows as scroll-driven parallax sections: image background with a
configurable transform-on-scroll effect (translate / blur / opacity / scale /
rotate), color overlay, and configurable scroll effects on the foreground
content. Includes a mobile bypass that swaps in a regular `<img>` below a
configurable breakpoint.

- Project page: [drupal.org/project/vvjp](https://www.drupal.org/project/vvjp)
- Issue queue:
  [drupal.org/project/issues/vvjp](https://www.drupal.org/project/issues/vvjp)

v2.0 builds on the **`drupal/vvj_core` foundation** (composer auto-installs it).
Behavior runs in a `<vvjp-parallax>` custom element extending `VvjElementBase`.


## Table of contents

- Overview
- Requirements
- Installation
- Configuration
- Patterns
- Usage
- Troubleshooting
- Changelog
- Maintainers
- License


## Overview


### Features

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
- `prefers-reduced-motion` honored: parallax offset is forced to 0 for
  reduced-motion users
- Lazy IntersectionObserver hydration; AbortController-tracked listeners
- **Views tokens**: `[vvjp:field]` / `[vvjp:field:plain]` in Views text areas
  with *Use replacement tokens from the first row*
- **Drupal admin help**: `/admin/help/vvjp`


## Requirements

- Drupal core `^11.3 || ^12`
- PHP 8.3 or later
- Drupal core modules Views (`drupal:views`) and Filter (`drupal:filter`)
- [VVJ Core](https://www.drupal.org/project/vvj_core) (`drupal/vvj_core`
  `^2.0.1`), which composer installs for you

Optional: the `vvj_patterns` sub-module of `drupal/vvj_core` adds the Pattern
select to the format settings (see [Patterns](#patterns)), and
`vvj_patterns_demo` adds example content.


## Installation

```bash
composer require drupal/vvjp:^2.0
drush en vvjp
```


### Upgrading from 1.x

`composer update drupal/vvjp && drush updb && drush cr` is sufficient. v2
preserves:

- Plugin ID `views_vvjp`, theme hook `views_view_vvjp`
- Twig template names
- All option keys (incl. `section_height: {value, unit}` array shape)
- Library names (`vvjp`, `vvjp-style`, `vvjp__all/576/768/992/1200/1400`)
- JS behavior key `Drupal.behaviors.VVJParallax`
- All CSS class names (`.vvjp`, `.vvjp-inner`, `.parallax-row`,
  `.parallax-background`, `.parallax-overlay`, `.parallax-content`,
  `.parallax-content-inner`)
- Separator marker `<div class="vvjp-separator"></div>`

Outer rendered tag changed from `<div>` to `<vvjp-parallax>`. Theme selectors
targeting `.vvjp` keep matching.

`drush updb` enables `vvj_core` (`vvjp_update_10001`), normalizes
`available_breakpoints` from a one-item list to a string (`vvjp_update_10002`)
and converts existing views to the v2 option shape (`vvjp_update_10003`).


## Configuration

All settings live in the format settings of the *Views Vanilla JavaScript
Parallax* style on a View. They are grouped into responsive, dimensions,
parallax, background, scroll effects and overlay sections, and a **Patterns**
section when `vvj_patterns` is enabled. The extended help topic is at
`/admin/help/vvjp`.

Settings are stored in the View itself, as options of the `views_vvjp` style
plugin; their types are declared in `config/schema/vvjp.schema.yml`.


## Patterns

A Pattern is a ready-made design for this format. VVJP ships 5. Enable the
`vvj_patterns` sub-module of `drupal/vvj_core` (`drush en vvj_patterns`), open
the format settings and choose one in **Patterns** > **Pattern**.

| Pattern | Look |
|---|---|
| Horizon | Full-height chapters; the photo rises in behind a slanted line and a huge title sits bottom left. |
| Skyline | Split rows: a dark card with an outlined number on the left, the photo settling on the right. |
| Showroom | A black stage; a spotlight opens on the photo as it scrolls in, with centered thin words. |
| Shoreline | Photos pull into focus; a frosted card alternates left and right. |
| Field book | Framed plates that open to full bleed, with a paper caption and a plate number. |

When you choose a different Pattern and save, its settings replace the layout,
animation, styling and deep linking settings of the view; you can still change
them afterwards. Choose **None** to keep your own settings. Drupal then shows a
message naming the settings the Pattern set.

Each Pattern also lists what the view still needs, for example the fields to
add, their order and the CSS classes to give them. The list appears under **To
finish the Pattern** in the Patterns section and as a warning after you save.
The `vvj_patterns_demo` sub-module shows a working setup: it adds the "VVJ
Slide" content type (fields `vvj_nav_label`, `vvj_image`, `vvj_kicker`,
`vvj_summary` and `vvj_link`, besides the title), five demo slides and, as
optional config, an example view for each VVJ module named `vvj_demo_<module>`.


## Usage

1. Create or edit a View.
1. Set **Format** to *Views Vanilla JavaScript Parallax*.
1. Set **Show** to *Fields*.
1. The first field MUST be an image with the **URL to image** formatter. Its
   URL becomes the parallax background.
1. Place a `<div class="vvjp-separator"></div>` field between the URL and the
   foreground content fields.
1. Configure responsive / dimensions / parallax / background / scroll effects /
   overlay in the format settings.
1. Save.

**Sample view:** Optional config `views.view.vvjp_example` (`config/optional/`)
installs when there is no ID conflict.


### Tokens in Views text areas

In Views headers, footers, or empty text with *Use replacement tokens from the
first row*, default Twig tokens (`{{ title }}`) do not work. Use VVJP tokens
instead:

- `{{ title }}` → `[vvjp:title]`
- `{{ field_image }}` → `[vvjp:field_image]`
- Append `:plain` for plain text: `[vvjp:title:plain]`

v2 resolves tokens through the shared `vvj_core.token_resolver` service; syntax
is unchanged from v1. Tokens read from the **first row** of rendered View
fields. Complex field rewrites are not supported.


## Troubleshooting

- **There is no Patterns section.** The section appears only while
  `vvj_patterns` is enabled. Run `drush en vvj_patterns`.
- **Saving the view changed my settings.** Choosing a different Pattern replaces
  the layout, animation, styling and deep linking settings. Choose **None** to
  keep your own.
- **A Pattern looks unfinished.** Follow the list under **To finish the
  Pattern**: the fields, their order and the CSS classes the Pattern needs.
- **After upgrading from 1.x the view fails or `vvj_core` is not enabled.** Run
  `drush updb`; update `vvjp_update_10001` enables `vvj_core`. Composer installs
  it automatically, and a manual install needs `drupal/vvj_core` in
  `modules/contrib/vvj_core` first.
- **Test errors that name missing infrastructure.** See *Running the tests* in
  [CONTRIBUTING.md](./CONTRIBUTING.md).
- **Anything else.** Search or open an issue in the
  [issue queue](https://www.drupal.org/project/issues/vvjp).


## Changelog

See [CHANGELOG.md](./CHANGELOG.md).


## Maintainers

- Alaa Haddad - [flashwebcenter](https://www.drupal.org/u/flashwebcenter)


## License

GPL-2.0-or-later.
