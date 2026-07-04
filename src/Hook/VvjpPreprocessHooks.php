<?php

declare(strict_types=1);

namespace Drupal\vvjp\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\views\Hook\ViewsThemeHooks;
use Drupal\views\ViewExecutable;
use Drupal\vvjp\Plugin\views\style\Parallax;
use Drupal\vvjp\VvjpConstants;

/**
 * Preprocess hooks for VVJP parallax.
 *
 * Derives the rgba overlay from hex+opacity, formats section_height
 * as `{value}{unit}`, rewrites row theme suggestions, and adds the
 * legacy `vvj-parallax` class on the views_view wrapper for v1
 * contract preservation.
 */
final class VvjpPreprocessHooks {

  /**
   * Constructor.
   *
   * @param \Drupal\views\Hook\ViewsThemeHooks $viewsThemeHooks
   *   Views row / field preprocess (replaces removed procedural
   *   template_preprocess_* in Drupal 12).
   */
  public function __construct(
    private readonly ViewsThemeHooks $viewsThemeHooks,
  ) {}

  /**
   * Implements hook_preprocess_HOOK() for views_view_vvjp.
   *
   * @param array<string, mixed> $variables
   *   Template variables.
   */
  #[Hook('preprocess_views_view_vvjp')]
  public function preprocessViewsViewVvjp(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!($view instanceof ViewExecutable)) {
      return;
    }
    $stylePlugin = $view->style_plugin;
    if (!($stylePlugin instanceof Parallax)) {
      return;
    }
    $options = $stylePlugin->options;
    $variables['options'] = $options;

    // Derive RGBA overlay from hex color + opacity.
    if (!empty($options['overlay_color'])) {
      $overlayColor = is_scalar($options['overlay_color'])
        ? (string) $options['overlay_color'] : '';
      $rgb = self::hexToRgb($overlayColor);
      $opacityRaw = $options['overlay_opacity'] ?? VvjpConstants::MAX_OVERLAY_OPACITY;
      $opacity = is_numeric($opacityRaw)
        ? (float) $opacityRaw : VvjpConstants::MAX_OVERLAY_OPACITY;
      $variables['background_rgb'] = sprintf('rgba(%d, %d, %d, %s)', $rgb['r'], $rgb['g'], $rgb['b'], $opacity);
    }
    else {
      $variables['background_rgb'] = NULL;
    }

    // Format section_height as `{value}{unit}`.
    $section_height = $options['section_height'] ?? NULL;
    if (!is_array($section_height)) {
      $section_height = [];
    }
    $height_value = $section_height['value'] ?? VvjpConstants::DEFAULT_HEIGHT_VALUE;
    $height_unit = $section_height['unit'] ?? VvjpConstants::DEFAULT_HEIGHT_UNIT;
    $value_string = is_scalar($height_value) ? (string) $height_value : '';
    $unit_string = is_scalar($height_unit) ? (string) $height_unit : '';
    $variables['section_height'] = $value_string . $unit_string;

    // Rewrite row theme suggestions.
    $rows = $variables['rows'] ?? NULL;
    if (is_array($rows)) {
      foreach ($rows as $key => $row) {
        if (!is_array($row) || !isset($row['#theme']) || !is_array($row['#theme'])) {
          continue;
        }
        foreach ($row['#theme'] as $idx => $hook) {
          if (is_string($hook)) {
            $row['#theme'][$idx] = str_replace(
              'views_view_fields',
              'views_view_vvjp_fields',
              $hook,
            );
          }
        }
        $rows[$key] = $row;
      }
      $variables['rows'] = $rows;
    }

    $this->viewsThemeHooks->preprocessViewsViewUnformatted($variables);
  }

  /**
   * Implements hook_preprocess_HOOK() for views_view_vvjp_fields.
   *
   * @param array<string, mixed> $variables
   *   Template variables.
   */
  #[Hook('preprocess_views_view_vvjp_fields')]
  public function preprocessViewsViewVvjpFields(array &$variables): void {
    $this->viewsThemeHooks->preprocessViewsViewFields($variables);
  }

  /**
   * Implements hook_preprocess_HOOK() for views_view.
   *
   * Adds the legacy `vvj-parallax` class for v1 contract preservation.
   *
   * @param array<string, mixed> $variables
   *   Template variables.
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!($view instanceof ViewExecutable)) {
      return;
    }
    if (!($view->style_plugin instanceof Parallax)) {
      return;
    }
    $attributes = $variables['attributes'] ?? NULL;
    if (is_array($attributes)) {
      $classes = $attributes['class'] ?? [];
      if (!is_array($classes)) {
        $classes = [];
      }
      $classes[] = 'vvj-parallax';
      $attributes['class'] = $classes;
      $variables['attributes'] = $attributes;
    }
  }

  /**
   * Convert a hex color to an RGB triplet.
   *
   * @param string $hex
   *   Hex color, with or without leading `#`. Accepts 3 or 6 chars.
   *
   * @return array{r: int, g: int, b: int}
   *   Decimal RGB components.
   */
  private static function hexToRgb(string $hex): array {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
      $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    return [
      'r' => (int) hexdec(substr($hex, 0, 2)),
      'g' => (int) hexdec(substr($hex, 2, 2)),
      'b' => (int) hexdec(substr($hex, 4, 2)),
    ];
  }

}
