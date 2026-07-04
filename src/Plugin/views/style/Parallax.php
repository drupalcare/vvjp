<?php

declare(strict_types=1);

namespace Drupal\vvjp\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\views\Attribute\ViewsStyle;
use Drupal\vvj_core\Plugin\views\style\VvjStylePluginBase;
use Drupal\vvjp\VvjpConstants;

/**
 * Renders Views rows as a vanilla-JavaScript parallax section.
 *
 * @ingroup views_style_plugins
 */
#[ViewsStyle(
  id: 'views_vvjp',
  title: new TranslatableMarkup('Views Vanilla JavaScript Parallax'),
  help: new TranslatableMarkup('Render items in Parallax using vanilla JavaScript.'),
  theme: 'views_view_vvjp',
  display_types: ['normal'],
)]
class Parallax extends VvjStylePluginBase {

  /**
   * {@inheritdoc}
   */
  public function getModuleSlug(): string {
    return 'vvjp';
  }

  /**
   * {@inheritdoc}
   */
  public function getCustomElementTag(): string {
    return 'vvjp-parallax';
  }

  /**
   * {@inheritdoc}
   *
   * Restore v1's parallax-specific enable-CSS wording.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The description.
   */
  protected function getEnableCssDescription(): TranslatableMarkup {
    return $this->t('Check this box to include the CSS library for styling the parallax.');
  }

  /**
   * {@inheritdoc}
   *
   * Parallax is a scroll-driven decorative effect — no panels/slides
   * to deep-link to.
   */
  protected function supportsDeeplinking(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function definePatternOptions(array $options): array {
    $options['parallax_speed'] = ['default' => VvjpConstants::DEFAULT_PARALLAX_SPEED];
    $options['background_position'] = ['default' => VvjpConstants::DEFAULT_BG_POSITION];
    $options['overlay_color'] = ['default' => VvjpConstants::DEFAULT_OVERLAY_COLOR];
    $options['overlay_opacity'] = ['default' => VvjpConstants::DEFAULT_OVERLAY_OPACITY];
    $options['section_height'] = [
      'default' => [
        'value' => VvjpConstants::DEFAULT_HEIGHT_VALUE,
        'unit' => VvjpConstants::DEFAULT_HEIGHT_UNIT,
      ],
    ];
    $options['available_breakpoints'] = ['default' => VvjpConstants::DEFAULT_BREAKPOINT];
    $options['bg_animation_easing'] = ['default' => VvjpConstants::DEFAULT_BG_EASING];
    $options['bg_animation_speed'] = ['default' => VvjpConstants::DEFAULT_BG_SPEED];
    $options['max_width'] = ['default' => VvjpConstants::DEFAULT_MAX_WIDTH];
    $options['scroll_effect'] = ['default' => VvjpConstants::DEFAULT_SCROLL_EFFECT];
    $options['disable_overlay'] = ['default' => FALSE];
    $options['over_content_only'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildPatternSections(array &$form): void {
    $this->buildResponsiveSection($form);
    $this->buildDimensionsSection($form);
    $this->buildParallaxSection($form);
    $this->buildBackgroundSection($form);
    $this->buildEffectsSection($form);
    $this->buildOverlaySection($form);
  }

  /**
   * Responsive section.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildResponsiveSection(array &$form): void {
    $form['responsive'] = [
      '#type' => 'details',
      '#title' => $this->t('Responsive Settings'),
      '#open' => TRUE,
      '#weight' => -40,
    ];
    $form['responsive']['available_breakpoints'] = [
      '#type' => 'select',
      '#title' => $this->t('Available Breakpoints for Parallax'),
      '#options' => [
        VvjpConstants::BREAKPOINT_ALL => $this->t('Active on all screens'),
        '576' => $this->t('576 px / 36 rem'),
        '768' => $this->t('768 px / 48 rem'),
        '992' => $this->t('992 px / 62 rem'),
        '1200' => $this->t('1200 px / 75 rem'),
        '1400' => $this->t('1400 px / 87.5 rem'),
      ],
      '#default_value' => $this->options['available_breakpoints'],
      '#description' => $this->t('Select the maximum screen width (in pixels) at which the parallax effect should be disabled. Selecting "all" will keep the parallax effect active on all screen sizes.'),
    ];
  }

  /**
   * Dimensions section — section height (value + unit) and max width.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildDimensionsSection(array &$form): void {
    $form['dimensions'] = [
      '#type' => 'details',
      '#title' => $this->t('Dimensions'),
      '#open' => TRUE,
      '#weight' => -30,
    ];

    $section_height = $this->options['section_height'] ?? NULL;
    if (!is_array($section_height)) {
      $section_height = [
        'value' => VvjpConstants::DEFAULT_HEIGHT_VALUE,
        'unit' => VvjpConstants::DEFAULT_HEIGHT_UNIT,
      ];
    }

    $form['dimensions']['section_height'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Section Height'),
      '#description' => $this->t('Define the height of the parallax section by entering a value and selecting a unit.'),
      '#attributes' => ['class' => ['section-height-wrapper']],
    ];

    $form['dimensions']['section_height']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Height Value'),
      '#default_value' => $section_height['value'] ?? VvjpConstants::DEFAULT_HEIGHT_VALUE,
      '#description' => $this->t('Enter the numeric value for the height.'),
      '#attributes' => ['style' => 'width: 150px; display: inline-block;'],
    ];

    $form['dimensions']['section_height']['unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Height Unit (vw, vh, px, %, em, rem)'),
      '#default_value' => $section_height['unit'] ?? VvjpConstants::DEFAULT_HEIGHT_UNIT,
      '#options' => [
        VvjpConstants::UNIT_VW => $this->t('Viewport Width (vw)'),
        VvjpConstants::UNIT_VH => $this->t('Viewport Height (vh)'),
        VvjpConstants::UNIT_PX => $this->t('Pixels (px)'),
        VvjpConstants::UNIT_PERCENT => $this->t('Percentage (%)'),
        VvjpConstants::UNIT_EM => $this->t('Ems (em)'),
        VvjpConstants::UNIT_REM => $this->t('Root Ems (rem)'),
      ],
      '#attributes' => ['style' => 'width: 150px; display: inline-block;'],
      '#description' => $this->t('Choose from the following units:
        <ul>
          <li><strong>Viewport Width (vw):</strong> 1vw is equal to 1 percent of the viewport\'s width. For example, if the viewport is 1000px wide, 10vw will equal 100px.</li>
          <li><strong>Viewport Height (vh):</strong> 1vh is equal to 1 percent of the viewport\'s height. For example, if the viewport is 1000px high, 10vh will equal 100px.</li>
          <li><strong>Pixels (px):</strong> Specifies an exact number of pixels for the height, regardless of the viewport size.</li>
          <li><strong>Percentage (%):</strong> Specifies a percentage of the parent container\'s height.</li>
          <li><strong>Ems (em):</strong> Relative to the font size of the element. For example, if the font size is 16px, 1em equals 16px.</li>
          <li><strong>Root Ems (rem):</strong> Relative to the font size of the root element (typically <code>&lt;html&gt;</code>). For example, if the root font size is 16px, 1rem equals 16px.</li>
        </ul>'
      ),
    ];

    $form['dimensions']['max_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Width (px)'),
      '#default_value' => $this->options['max_width'],
      '#description' => $this->t('Defines the maximum width for the parallax content or insert zero allows it to stretch to 100 percent.'),
      '#step' => 1,
      '#min' => 0,
    ];
  }

  /**
   * Parallax section — scroll speed.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildParallaxSection(array &$form): void {
    $form['parallax'] = [
      '#type' => 'details',
      '#title' => $this->t('Parallax Settings'),
      '#open' => TRUE,
      '#weight' => -20,
    ];
    $form['parallax']['parallax_speed'] = [
      '#type' => 'number',
      '#title' => $this->t('Parallax Speed'),
      '#default_value' => $this->options['parallax_speed'],
      '#description' => $this->t('Set the speed of the parallax effect (e.g., 0.1 for slow, 2 for fast). Range: @min - @max.', [
        '@min' => VvjpConstants::MIN_PARALLAX_SPEED,
        '@max' => VvjpConstants::MAX_PARALLAX_SPEED,
      ]),
      '#step' => VvjpConstants::PARALLAX_SPEED_STEP,
      '#min' => VvjpConstants::MIN_PARALLAX_SPEED,
      '#max' => VvjpConstants::MAX_PARALLAX_SPEED,
    ];
  }

  /**
   * Background section — position, animation speed, easing.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildBackgroundSection(array &$form): void {
    $form['background'] = [
      '#type' => 'details',
      '#title' => $this->t('Background Settings'),
      '#open' => FALSE,
      '#weight' => -10,
    ];

    $form['background']['background_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Background Position'),
      '#options' => [
        VvjpConstants::BG_POSITION_CENTER => $this->t('Center'),
        VvjpConstants::BG_POSITION_TOP => $this->t('Top'),
        VvjpConstants::BG_POSITION_BOTTOM => $this->t('Bottom'),
        VvjpConstants::BG_POSITION_LEFT => $this->t('Left'),
        VvjpConstants::BG_POSITION_RIGHT => $this->t('Right'),
        VvjpConstants::BG_POSITION_TOP_LEFT => $this->t('Top Left'),
        VvjpConstants::BG_POSITION_TOP_RIGHT => $this->t('Top Right'),
        VvjpConstants::BG_POSITION_BOTTOM_LEFT => $this->t('Bottom Left'),
        VvjpConstants::BG_POSITION_BOTTOM_RIGHT => $this->t('Bottom Right'),
        VvjpConstants::BG_POSITION_CENTER_TOP => $this->t('Center Top'),
        VvjpConstants::BG_POSITION_CENTER_BOTTOM => $this->t('Center Bottom'),
        VvjpConstants::BG_POSITION_50_50 => $this->t('50% 50% (Center)'),
        VvjpConstants::BG_POSITION_0_0 => $this->t('0% 0% (Top Left)'),
        VvjpConstants::BG_POSITION_100_100 => $this->t('100% 100% (Bottom Right)'),
        VvjpConstants::BG_POSITION_30_70 => $this->t('30% 70%'),
        VvjpConstants::BG_POSITION_10PX_20PX => $this->t('10px 20px'),
        VvjpConstants::BG_POSITION_100PX_50 => $this->t('100px 50%'),
        VvjpConstants::BG_POSITION_LEFT_10 => $this->t('Left 10%'),
      ],
      '#default_value' => $this->options['background_position'],
      '#description' => $this->t('Set the position of the background image.'),
    ];

    $form['background']['bg_animation_speed'] = [
      '#type' => 'number',
      '#title' => $this->t('Background Animation Speed'),
      '#default_value' => $this->options['bg_animation_speed'],
      '#description' => $this->t('Set the speed of the Background Animation Speed (e.g., 0.1 for slow, 2 for fast). Range: @min - @max.', [
        '@min' => VvjpConstants::MIN_PARALLAX_SPEED,
        '@max' => VvjpConstants::MAX_PARALLAX_SPEED,
      ]),
      '#step' => VvjpConstants::PARALLAX_SPEED_STEP,
      '#min' => VvjpConstants::MIN_PARALLAX_SPEED,
      '#max' => VvjpConstants::MAX_PARALLAX_SPEED,
    ];

    $form['background']['bg_animation_easing'] = [
      '#type' => 'select',
      '#title' => $this->t('Background Animation Easing'),
      '#options' => [
        VvjpConstants::EASING_EASE => $this->t('Ease'),
        VvjpConstants::EASING_EASE_IN => $this->t('Ease-In'),
        VvjpConstants::EASING_EASE_OUT => $this->t('Ease-Out'),
        VvjpConstants::EASING_EASE_IN_OUT => $this->t('Ease-In-Out'),
        VvjpConstants::EASING_LINEAR => $this->t('Linear'),
      ],
      '#default_value' => $this->options['bg_animation_easing'],
      '#description' => $this->t('Choose the easing function for the animation.'),
    ];
  }

  /**
   * Effects section — scroll effect.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildEffectsSection(array &$form): void {
    $form['effects'] = [
      '#type' => 'details',
      '#title' => $this->t('Scroll Effects'),
      '#open' => FALSE,
      '#weight' => 0,
    ];
    $form['effects']['scroll_effect'] = [
      '#type' => 'select',
      '#title' => $this->t('Scroll Effect'),
      '#options' => [
        VvjpConstants::SCROLL_EFFECT_NONE => $this->t('None'),
        VvjpConstants::SCROLL_EFFECT_FADE => $this->t('Fade In'),
        VvjpConstants::SCROLL_EFFECT_SCALE => $this->t('Scale Up'),
        VvjpConstants::SCROLL_EFFECT_ROTATE => $this->t('Rotate'),
        VvjpConstants::SCROLL_EFFECT_FADE_SCALE => $this->t('Fade In and Scale Up'),
        VvjpConstants::SCROLL_EFFECT_THREE_D => $this->t('3D transformations'),
        VvjpConstants::SCROLL_EFFECT_SHADOW => $this->t('Shadow Effects'),
        VvjpConstants::SCROLL_EFFECT_GLOW => $this->t('Glow Effects'),
        VvjpConstants::SCROLL_EFFECT_SMOOTH => $this->t('Smooth Transition and Timing Functions'),
        VvjpConstants::SCROLL_EFFECT_HOVER => $this->t('Hover Interactions'),
        VvjpConstants::SCROLL_EFFECT_COMBINED => $this->t('Combined Effects'),
      ],
      '#default_value' => $this->options['scroll_effect'],
      '#description' => $this->t('Choose the scroll effect for the parallax content.'),
    ];
  }

  /**
   * Overlay section — color, opacity, scoping.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildOverlaySection(array &$form): void {
    $form['overlay'] = [
      '#type' => 'details',
      '#title' => $this->t('Overlay Settings'),
      '#open' => FALSE,
      '#weight' => 10,
    ];

    $form['overlay']['overlay_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Overlay Color'),
      '#default_value' => $this->options['overlay_color'],
      '#description' => $this->t('Choose a color overlay for the parallax background.'),
    ];

    $overlay_opacity = $this->options['overlay_opacity'] ?? VvjpConstants::DEFAULT_OVERLAY_OPACITY;
    $overlay_opacity_string = is_scalar($overlay_opacity)
      ? (string) $overlay_opacity : '';
    $form['overlay']['overlay_opacity'] = [
      '#type' => 'range',
      '#title' => $this->t('Overlay Opacity'),
      '#default_value' => $this->options['overlay_opacity'],
      '#min' => VvjpConstants::MIN_OVERLAY_OPACITY,
      '#max' => VvjpConstants::MAX_OVERLAY_OPACITY,
      '#step' => VvjpConstants::OVERLAY_OPACITY_STEP,
      '#description' => $this->t('Set the opacity level for the overlay. (0 = transparent, 1 = opaque).'),
      '#suffix' => '<span id="overlay-opacity-value">' . $overlay_opacity_string . '</span>',
      '#attributes' => [
        'oninput' => 'document.getElementById("overlay-opacity-value").innerText = this.value;',
      ],
    ];

    $form['overlay']['over_content_only'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Apply Overlay Background Under Content Only'),
      '#default_value' => $this->options['over_content_only'],
      '#description' => $this->t('Enable this option to apply the overlay background color only beneath the parallax content, rather than covering the entire row.'),
    ];

    $form['overlay']['disable_overlay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable Overlay Color'),
      '#default_value' => $this->options['disable_overlay'],
      '#description' => $this->t('Check this to disable overlay color completely.'),
    ];
  }

  /**
   * {@inheritdoc}
   *
   * Add the breakpoint-specific responsive library.
   *
   * @return list<string>
   *   Library identifiers.
   */
  protected function buildLibraryList(): array {
    $libraries = parent::buildLibraryList();
    $breakpoint = $this->options['available_breakpoints'] ?? VvjpConstants::DEFAULT_BREAKPOINT;
    $breakpoint_string = is_scalar($breakpoint)
      ? (string) $breakpoint : VvjpConstants::DEFAULT_BREAKPOINT;
    $libraries[] = 'vvjp/vvjp__' . $breakpoint_string;
    return $libraries;
  }

  /**
   * {@inheritdoc}
   *
   * @param array<int|string, mixed> $form
   *   The Views style options form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state): void {
    $values = $form_state->getValue('style_options');
    if (!is_array($values)) {
      $values = [];
    }
    $form_state->setValue('style_options', $this->flattenFormValues($values));
    parent::submitOptionsForm($form, $form_state);
  }

  /**
   * Flatten the sectioned form values back into the persisted shape.
   *
   * @param array<int|string, mixed> $values
   *   Submitted form values keyed by section then by option name.
   *
   * @return array<string, mixed>
   *   Flattened option array.
   */
  protected function flattenFormValues(array $values): array {
    $flattened = [];

    foreach (['responsive', 'dimensions', 'parallax', 'background', 'effects', 'overlay'] as $section) {
      $section_values = $values[$section] ?? NULL;
      if (!is_array($section_values)) {
        continue;
      }
      foreach ($section_values as $key => $value) {
        $flattened[(string) $key] = $value;
      }
    }

    $advanced = $values['advanced_section'] ?? NULL;
    if (is_array($advanced)) {
      $flattened['enable_css'] = $advanced['enable_css'] ?? TRUE;
    }

    $flattened['unique_id'] = $this->options['unique_id'] ?? $this->generateUniqueId();

    return $flattened;
  }

  /**
   * {@inheritdoc}
   *
   * Parallax requires Fields row style AND the first field's formatter
   * must be `image_url`.
   *
   * @return list<string|\Drupal\Core\StringTranslation\TranslatableMarkup>
   *   Validation errors. Empty list = valid.
   */
  public function validate(): array {
    $errors = parent::validate();

    if (!$this->usesFields()) {
      return $errors;
    }

    $fields = $this->view->display_handler->getOption('fields');
    if (!is_array($fields) || empty($fields)) {
      $errors[] = (string) $this->t('Views Parallax requires at least one field.');
      return $errors;
    }

    $first_field = reset($fields);
    if (!is_array($first_field) || ($first_field['type'] ?? '') !== VvjpConstants::REQUIRED_FIELD_TYPE) {
      $errors[] = (string) $this->t('Views Parallax requires the first field to be an image with the "URL to image" formatter.');
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   *
   * Parallax needs the first field to use the "URL to image" formatter; show
   * that image-specific guidance (v1 parity) instead of the base's generic
   * Fields-row note, and only when the requirement is not yet met.
   */
  protected function buildWarningMessage(array &$form): void {
    $example_id = $this->getModuleSlug() . '_example';
    if ($this->view->storage->id() === $example_id || $this->isFirstFieldUrlToImage()) {
      return;
    }

    $form['warning_message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="messages messages--error">' . $this->t(
        'Note: The first field must be an image, and you should choose the "URL to image" option under Formatter in the field configuration. To see an example, check the @example view by clicking <a href="@url">here</a> to edit it.',
        [
          '@example' => $example_id,
          '@url' => Url::fromRoute('entity.view.edit_form', ['view' => $example_id])->toString(),
        ],
      ) . '</div>',
      '#weight' => -50,
    ];
  }

  /**
   * Whether the first field uses the "URL to image" formatter.
   *
   * @return bool
   *   TRUE when the first field's formatter is the required image_url type.
   */
  private function isFirstFieldUrlToImage(): bool {
    $fields = $this->view->display_handler->getOption('fields');
    if (!is_array($fields) || $fields === []) {
      return FALSE;
    }
    $first_field = reset($fields);
    return is_array($first_field)
      && ($first_field['type'] ?? '') === VvjpConstants::REQUIRED_FIELD_TYPE;
  }

}
