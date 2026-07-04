# Changelog

## [2.0.0] — UNRELEASED

**Drop-in upgrade for 1.x sites.** v2 modernizes internals — extends `vvj_core`
foundation, ships `<vvjp-parallax>` custom element extending `VvjElementBase`,
RAF-throttled scroll handler, AbortController-tracked listeners — while
preserving every option key, library name, CSS class, plugin ID, theme hook, JS
behavior key, and the mobile-bypass fallback from 1.x.

### Fixed

- Restored v1's image-specific setup warning in the Views options form (shown
  only when the first field is not a "URL to image" formatter) — the shared base
  showed a generic "requires Fields as row style" note instead. Deep-link toggle
  also repaired via the `vvj_core` base fix.

### Added

- `drupal/vvj_core` dependency (auto-installed).
- `vvjp.install` with `vvjp_update_10001()` to auto-enable vvj_core during
  `drush updb`.
- `<vvjp-parallax>` custom element. Lazy IntersectionObserver hydration;
  AbortController-tracked scroll + resize listeners; RAF throttle on scroll
  updates.
- `prefers-reduced-motion` awareness — scroll-tied transform is forced to 0 for
  reduced-motion users (overlay/effects unchanged).

### Changed

- Plugin extends `VvjStylePluginBase`. Implements four small subclass hooks;
  standard sections come from the base.
- `supportsDeeplinking()` returns FALSE.
- Token resolution delegates to `vvj_core.token_resolver`.
- Hooks moved to OOP `#[Hook]` classes.
- `available_breakpoints` default normalized from v1's array `['none']` to a
  string `'all'` (the actual rendered behavior is unchanged — v1 fell back to
  "active on all screens" anyway).

### Drupal core / PHP

- Requires Drupal `^11.3 || ^12`, PHP `>=8.3`.
