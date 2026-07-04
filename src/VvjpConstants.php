<?php

declare(strict_types=1);

namespace Drupal\vvjp;

/**
 * Vvjp-specific constants — parallax pattern-only values.
 *
 * Cross-cutting constants live in vvj_core's `ValidationBounds`.
 *
 * @internal Final, non-instantiable.
 */
final class VvjpConstants {

  /**
   * Breakpoint values — `all` keeps parallax active at every viewport.
   */
  public const string BREAKPOINT_ALL = 'all';
  public const string DEFAULT_BREAKPOINT = self::BREAKPOINT_ALL;

  /**
   * Section-height units.
   */
  public const string UNIT_VW = 'vw';
  public const string UNIT_VH = 'vh';
  public const string UNIT_PX = 'px';
  public const string UNIT_PERCENT = '%';
  public const string UNIT_EM = 'em';
  public const string UNIT_REM = 'rem';
  public const string DEFAULT_HEIGHT_UNIT = self::UNIT_VH;
  public const int DEFAULT_HEIGHT_VALUE = 75;

  /**
   * Background position values (CSS `background-position`).
   */
  public const string BG_POSITION_CENTER = 'center';
  public const string BG_POSITION_TOP = 'top';
  public const string BG_POSITION_BOTTOM = 'bottom';
  public const string BG_POSITION_LEFT = 'left';
  public const string BG_POSITION_RIGHT = 'right';
  public const string BG_POSITION_TOP_LEFT = 'top left';
  public const string BG_POSITION_TOP_RIGHT = 'top right';
  public const string BG_POSITION_BOTTOM_LEFT = 'bottom left';
  public const string BG_POSITION_BOTTOM_RIGHT = 'bottom right';
  public const string BG_POSITION_CENTER_TOP = 'center top';
  public const string BG_POSITION_CENTER_BOTTOM = 'center bottom';
  public const string BG_POSITION_50_50 = '50% 50%';
  public const string BG_POSITION_0_0 = '0% 0%';
  public const string BG_POSITION_100_100 = '100% 100%';
  public const string BG_POSITION_30_70 = '30% 70%';
  public const string BG_POSITION_10PX_20PX = '10px 20px';
  public const string BG_POSITION_100PX_50 = '100px 50%';
  public const string BG_POSITION_LEFT_10 = 'left 10%';
  public const string DEFAULT_BG_POSITION = self::BG_POSITION_CENTER;

  /**
   * Easing options.
   */
  public const string EASING_EASE = 'ease';
  public const string EASING_EASE_IN = 'ease-in';
  public const string EASING_EASE_OUT = 'ease-out';
  public const string EASING_EASE_IN_OUT = 'ease-in-out';
  public const string EASING_LINEAR = 'linear';
  public const string DEFAULT_BG_EASING = self::EASING_EASE;

  /**
   * Scroll-effect values.
   */
  public const string SCROLL_EFFECT_NONE = 'none';
  public const string SCROLL_EFFECT_FADE = 'fade';
  public const string SCROLL_EFFECT_SCALE = 'scale';
  public const string SCROLL_EFFECT_ROTATE = 'rotate';
  public const string SCROLL_EFFECT_FADE_SCALE = 'fade-scale';
  public const string SCROLL_EFFECT_THREE_D = 'three-d';
  public const string SCROLL_EFFECT_SHADOW = 'shadow';
  public const string SCROLL_EFFECT_GLOW = 'glow';
  public const string SCROLL_EFFECT_SMOOTH = 'smooth';
  public const string SCROLL_EFFECT_HOVER = 'hover';
  public const string SCROLL_EFFECT_COMBINED = 'combined';
  public const string DEFAULT_SCROLL_EFFECT = self::SCROLL_EFFECT_FADE;

  /**
   * Defaults.
   */
  public const float DEFAULT_PARALLAX_SPEED = 0.3;
  public const float DEFAULT_BG_SPEED = 0.5;
  public const string DEFAULT_OVERLAY_COLOR = '#ffffff';
  public const float DEFAULT_OVERLAY_OPACITY = 0.7;
  public const int DEFAULT_MAX_WIDTH = 0;

  /**
   * Bounds.
   */
  public const float MIN_PARALLAX_SPEED = 0.1;
  public const float MAX_PARALLAX_SPEED = 2.0;
  public const float PARALLAX_SPEED_STEP = 0.1;
  public const float MIN_OVERLAY_OPACITY = 0.0;
  public const float MAX_OVERLAY_OPACITY = 1.0;
  public const float OVERLAY_OPACITY_STEP = 0.1;

  /**
   * Required formatter for the first field.
   */
  public const string REQUIRED_FIELD_TYPE = 'image_url';

  /**
   * Prevent instantiation.
   */
  private function __construct() {
  }

}
