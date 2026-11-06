# Changelog

## [2.0.0] (UNRELEASED)

**Drop-in upgrade for 1.x sites.** v2 modernizes internals: extends `vvj_core`
foundation, ships `<vvjp-parallax>` custom element extending `VvjElementBase`,
RAF-throttled scroll handler, AbortController-tracked listeners, while
preserving every option key, library name, CSS class, plugin ID, theme hook, JS
behavior key, and the mobile-bypass fallback from 1.x.

### Fixed

- Patterns: under Solo and APEX, the muted words on the shore and paper grounds use 88% of the text color (was 70%), so they keep a 4.5:1 contrast.
- Patterns: the below-breakpoint state (`.vvjp-below` and the fallback image) now follows the Pattern's own width (a ResizeObserver on the element) instead of the window. Views without a Pattern still use the window width.
- A Pattern no longer takes layout from the module's breakpoint files: their rules now skip any element with `vvj-pattern` (same specificity, classic rendering unchanged), and what a Pattern needs from them (photo and content position, the scroll effects, the narrow grid) lives in `vvjp-pattern-base.css` with the same values, split by `.vvjp-below`.
- Accessibility: Horizon and Showroom put a soft dark scrim under the words that sit on a photo, so the text keeps its contrast on a light photo.
- Accessibility: a Pattern view with a label renders it as a visually hidden h2 that names the region, so the row titles (h3) no longer skip a level; the Skyline numeral has empty alternative text and is not announced.
- The scroll update reads every row's geometry in one pass between the write passes, so a page with many rows does one layout per frame instead of one per row (JS-RULES.md rule 3).
- Update 10003 converts v1 views to the v2 option shape (value types).
- Restored v1's image-specific setup warning in the Views options form (shown
  only when the first field is not a "URL to image" formatter). The shared base
  showed a generic "requires Fields as row style" note instead. Deep-link toggle
  also repaired via the `vvj_core` base fix.

### Added

- Patterns: 5 ready-made designs (Horizon, Skyline, Showroom, Shoreline, Field book) chosen in the format settings. They need the `vvj_patterns` sub-module of `drupal/vvj_core`.
- Patterns: the bridge reads the shared `--vvj-theme-*` roles instead of spelling theme names.
- Pattern theme bridges `pattern-bridge-solo` and `pattern-bridge-apex`: under Solo or APEX the Patterns take the region colors; every Pattern color is now a token declared once in `vvjp-pattern-base.css`, with the same black-and-white look elsewhere.
- `drupal/vvj_core` dependency (auto-installed).
- `vvjp.install` with `vvjp_update_10001()` to auto-enable vvj_core during
  `drush updb`.
- `<vvjp-parallax>` custom element. Lazy IntersectionObserver hydration;
  AbortController-tracked scroll + resize listeners; RAF throttle on scroll
  updates.
- `prefers-reduced-motion` awareness: scroll-tied transform is forced to 0 for
  reduced-motion users (overlay/effects unchanged).

### Changed

- Plugin extends `VvjStylePluginBase`. Implements four small subclass hooks;
  standard sections come from the base.
- `supportsDeeplinking()` returns FALSE.
- Token resolution delegates to `vvj_core.token_resolver`.
- Hooks moved to OOP `#[Hook]` classes.
- `available_breakpoints` default normalized from v1's array `['none']` to a
  string `'all'` (the actual rendered behavior is unchanged. V1 fell back to
  "active on all screens" anyway).
- Code style: JavaScript and CSS follow Drupal core's ESLint, Stylelint and Prettier rules, and the spell-check job passes with a project word file. Rendering and behavior are unchanged.

### Drupal core / PHP

- Requires Drupal `^11.3 || ^12`, PHP `>=8.3`.
