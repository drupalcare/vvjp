<?php

declare(strict_types=1);

namespace Drupal\vvjp;

/**
 * Defines constants for the VVJP module.
 *
 * This class centralizes all magic strings, configuration keys, and default
 * values to improve maintainability and reduce duplication.
 */
final class VvjpConstants {

  /**
   * Plugin ID for the Views style plugin.
   */
  public const PLUGIN_ID = 'views_vvjp';

  /**
   * Theme hook for the main parallax view.
   */
  public const THEME_HOOK = 'views_view_vvjp';

  /**
   * Theme hook for field rows.
   */
  public const THEME_HOOK_FIELDS = 'views_view_vvjp_fields';

  /**
   * CSS class for the parallax wrapper.
   */
  public const CSS_CLASS_WRAPPER = 'vvj-parallax';

  /**
   * Library name for core JavaScript.
   */
  public const LIBRARY_JS = 'vvjp/vvjp';

  /**
   * Library name for CSS styles.
   */
  public const LIBRARY_CSS = 'vvjp/vvjp-style';

  /**
   * Library name prefix for responsive breakpoints.
   */
  public const LIBRARY_BREAKPOINT_PREFIX = 'vvjp/vvjp__';

  /**
   * Token namespace for VVJP tokens.
   */
  public const TOKEN_NAMESPACE = 'vvjp';

  /**
   * Token suffix for plain text output.
   */
  public const TOKEN_SUFFIX_PLAIN = ':plain';

  /**
   * Token validation pattern.
   */
  public const TOKEN_PATTERN = '/^[a-zA-Z0-9_]+(:plain)?$/';

  /**
   * Configuration key: Unique ID.
   */
  public const OPTION_UNIQUE_ID = 'unique_id';

  /**
   * Configuration key: Parallax speed.
   */
  public const OPTION_PARALLAX_SPEED = 'parallax_speed';

  /**
   * Configuration key: Background position.
   */
  public const OPTION_BG_POSITION = 'background_position';

  /**
   * Configuration key: Overlay color.
   */
  public const OPTION_OVERLAY_COLOR = 'overlay_color';

  /**
   * Configuration key: Overlay opacity.
   */
  public const OPTION_OVERLAY_OPACITY = 'overlay_opacity';

  /**
   * Configuration key: Section height.
   */
  public const OPTION_SECTION_HEIGHT = 'section_height';

  /**
   * Configuration key: Available breakpoints.
   */
  public const OPTION_BREAKPOINTS = 'available_breakpoints';

  /**
   * Configuration key: Background animation easing.
   */
  public const OPTION_BG_EASING = 'bg_animation_easing';

  /**
   * Configuration key: Background animation speed.
   */
  public const OPTION_BG_SPEED = 'bg_animation_speed';

  /**
   * Configuration key: Maximum width.
   */
  public const OPTION_MAX_WIDTH = 'max_width';

  /**
   * Configuration key: Enable CSS library.
   */
  public const OPTION_ENABLE_CSS = 'enable_css';

  /**
   * Configuration key: Scroll effect.
   */
  public const OPTION_SCROLL_EFFECT = 'scroll_effect';

  /**
   * Configuration key: Disable overlay.
   */
  public const OPTION_DISABLE_OVERLAY = 'disable_overlay';

  /**
   * Configuration key: Apply overlay over content only.
   */
  public const OPTION_OVER_CONTENT_ONLY = 'over_content_only';

  /**
   * Data attribute: Parallax speed.
   */
  public const DATA_ATTR_PARALLAX_SPEED = 'data-parallax-speed';

  /**
   * Data attribute: Background position.
   */
  public const DATA_ATTR_BG_POSITION = 'data-background-position';

  /**
   * Data attribute: Background animation speed.
   */
  public const DATA_ATTR_BG_SPEED = 'data-bg-animation-speed';

  /**
   * Data attribute: Scroll effect.
   */
  public const DATA_ATTR_SCROLL_EFFECT = 'data-scroll-effect';

  /**
   * Data attribute: Disable overlay.
   */
  public const DATA_ATTR_DISABLE_OVERLAY = 'data-disable-overlay';

  /**
   * Data attribute: Over content only.
   */
  public const DATA_ATTR_OVER_CONTENT_ONLY = 'data-over-content-only';

  /**
   * Data attribute: Maximum width.
   */
  public const DATA_ATTR_MAX_WIDTH = 'data-max-width';

  /**
   * Data attribute: Available breakpoints.
   */
  public const DATA_ATTR_BREAKPOINTS = 'data-available-breakpoints';

  /**
   * Data attribute: Animation easing.
   */
  public const DATA_ATTR_EASING = 'data-animation-easing';

  /**
   * Data attribute: Enable CSS.
   */
  public const DATA_ATTR_ENABLE_CSS = 'data-enable-css';

  /**
   * Default: Parallax speed.
   */
  public const DEFAULT_PARALLAX_SPEED = 0.3;

  /**
   * Default: Background position.
   */
  public const DEFAULT_BG_POSITION = 'center';

  /**
   * Default: Overlay color.
   */
  public const DEFAULT_OVERLAY_COLOR = '#ffffff';

  /**
   * Default: Overlay opacity.
   */
  public const DEFAULT_OVERLAY_OPACITY = 0.7;

  /**
   * Default: Section height value.
   */
  public const DEFAULT_HEIGHT_VALUE = 75;

  /**
   * Default: Section height unit.
   */
  public const DEFAULT_HEIGHT_UNIT = 'vh';

  /**
   * Default: Background animation easing.
   */
  public const DEFAULT_BG_EASING = 'ease';

  /**
   * Default: Background animation speed.
   */
  public const DEFAULT_BG_SPEED = 0.5;

  /**
   * Default: Maximum width (0 = 100%).
   */
  public const DEFAULT_MAX_WIDTH = 0;

  /**
   * Default: Scroll effect.
   */
  public const DEFAULT_SCROLL_EFFECT = 'fade';

  /**
   * Default: Breakpoint (none = all screens).
   */
  public const DEFAULT_BREAKPOINT = 'none';

  /**
   * Minimum: Parallax speed.
   */
  public const MIN_PARALLAX_SPEED = 0.1;

  /**
   * Maximum: Parallax speed.
   */
  public const MAX_PARALLAX_SPEED = 2.0;

  /**
   * Step: Parallax speed.
   */
  public const STEP_PARALLAX_SPEED = 0.1;

  /**
   * Minimum: Overlay opacity.
   */
  public const MIN_OVERLAY_OPACITY = 0.0;

  /**
   * Maximum: Overlay opacity.
   */
  public const MAX_OVERLAY_OPACITY = 1.0;

  /**
   * Step: Overlay opacity.
   */
  public const STEP_OVERLAY_OPACITY = 0.1;

  /**
   * Form element: Section height width.
   */
  public const FORM_ELEMENT_WIDTH = '150px';

  /**
   * Random ID: Minimum value.
   */
  public const RANDOM_ID_MIN = 10000000;

  /**
   * Random ID: Maximum value.
   */
  public const RANDOM_ID_MAX = 99999999;

  /**
   * Required field type for first field.
   */
  public const REQUIRED_FIELD_TYPE = 'image_url';

  /**
   * Example view ID.
   */
  public const EXAMPLE_VIEW_ID = 'vvjp_example';

  /**
   * Private constructor to prevent instantiation.
   */
  private function __construct() {
  }

}
