<?php

declare(strict_types=1);

namespace Drupal\vvjp\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\vvjp\VvjpConstants;

/**
 * Style plugin to render items in Parallax using vanilla JavaScript.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "views_vvjp",
 *   title = @Translation("Views Vanilla JavaScript Parallax"),
 *   help = @Translation("Render items in Parallax using vanilla JavaScript."),
 *   theme = "views_view_vvjp",
 *   display_types = { "normal" }
 * )
 */
class ViewsVanillaJavascriptParallax extends StylePluginBase {

  /**
   * Breakpoint value constants.
   */
  public const BREAKPOINT_ALL = 'all';
  public const BREAKPOINT_576 = '576';
  public const BREAKPOINT_768 = '768';
  public const BREAKPOINT_992 = '992';
  public const BREAKPOINT_1200 = '1200';
  public const BREAKPOINT_1400 = '1400';

  /**
   * Height unit constants.
   */
  public const HEIGHT_UNIT_VW = 'vw';
  public const HEIGHT_UNIT_VH = 'vh';
  public const HEIGHT_UNIT_PX = 'px';
  public const HEIGHT_UNIT_PERCENT = '%';
  public const HEIGHT_UNIT_EM = 'em';
  public const HEIGHT_UNIT_REM = 'rem';

  /**
   * Background position constants.
   */
  public const BG_POSITION_CENTER = 'center';
  public const BG_POSITION_TOP = 'top';
  public const BG_POSITION_BOTTOM = 'bottom';
  public const BG_POSITION_LEFT = 'left';
  public const BG_POSITION_RIGHT = 'right';
  public const BG_POSITION_TOP_LEFT = 'top left';
  public const BG_POSITION_TOP_RIGHT = 'top right';
  public const BG_POSITION_BOTTOM_LEFT = 'bottom left';
  public const BG_POSITION_BOTTOM_RIGHT = 'bottom right';
  public const BG_POSITION_CENTER_TOP = 'center top';
  public const BG_POSITION_CENTER_BOTTOM = 'center bottom';
  public const BG_POSITION_50_50 = '50% 50%';
  public const BG_POSITION_0_0 = '0% 0%';
  public const BG_POSITION_100_100 = '100% 100%';
  public const BG_POSITION_30_70 = '30% 70%';
  public const BG_POSITION_10PX_20PX = '10px 20px';
  public const BG_POSITION_100PX_50 = '100px 50%';
  public const BG_POSITION_LEFT_10 = 'left 10%';

  /**
   * Easing function constants.
   */
  public const EASING_EASE = 'ease';
  public const EASING_EASE_IN = 'ease-in';
  public const EASING_EASE_OUT = 'ease-out';
  public const EASING_EASE_IN_OUT = 'ease-in-out';
  public const EASING_LINEAR = 'linear';

  /**
   * Scroll effect constants.
   */
  public const SCROLL_EFFECT_NONE = 'none';
  public const SCROLL_EFFECT_FADE = 'fade';
  public const SCROLL_EFFECT_SCALE = 'scale';
  public const SCROLL_EFFECT_ROTATE = 'rotate';
  public const SCROLL_EFFECT_FADE_SCALE = 'fade-scale';
  public const SCROLL_EFFECT_THREE_D = 'three-d';
  public const SCROLL_EFFECT_SHADOW = 'shadow';
  public const SCROLL_EFFECT_GLOW = 'glow';
  public const SCROLL_EFFECT_SMOOTH = 'smooth';
  public const SCROLL_EFFECT_HOVER = 'hover';
  public const SCROLL_EFFECT_COMBINED = 'combined';

  /**
   * Does the style plugin use a row plugin.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the style plugin use row classes.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * Cached unique ID for this instance.
   *
   * @var int|null
   */
  protected ?int $cachedUniqueId = NULL;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();

    $options[VvjpConstants::OPTION_UNIQUE_ID] = ['default' => $this->generateUniqueId()];
    $options[VvjpConstants::OPTION_PARALLAX_SPEED] = ['default' => VvjpConstants::DEFAULT_PARALLAX_SPEED];
    $options[VvjpConstants::OPTION_BG_POSITION] = ['default' => VvjpConstants::DEFAULT_BG_POSITION];
    $options[VvjpConstants::OPTION_OVERLAY_COLOR] = ['default' => VvjpConstants::DEFAULT_OVERLAY_COLOR];
    $options[VvjpConstants::OPTION_OVERLAY_OPACITY] = ['default' => VvjpConstants::DEFAULT_OVERLAY_OPACITY];
    $options[VvjpConstants::OPTION_SECTION_HEIGHT] = [
      'default' => [
        'value' => VvjpConstants::DEFAULT_HEIGHT_VALUE,
        'unit' => VvjpConstants::DEFAULT_HEIGHT_UNIT,
      ],
    ];
    $options[VvjpConstants::OPTION_BREAKPOINTS] = ['default' => [VvjpConstants::DEFAULT_BREAKPOINT]];
    $options[VvjpConstants::OPTION_BG_EASING] = ['default' => VvjpConstants::DEFAULT_BG_EASING];
    $options[VvjpConstants::OPTION_BG_SPEED] = ['default' => VvjpConstants::DEFAULT_BG_SPEED];
    $options[VvjpConstants::OPTION_MAX_WIDTH] = ['default' => VvjpConstants::DEFAULT_MAX_WIDTH];
    $options[VvjpConstants::OPTION_ENABLE_CSS] = ['default' => TRUE];
    $options[VvjpConstants::OPTION_SCROLL_EFFECT] = ['default' => VvjpConstants::DEFAULT_SCROLL_EFFECT];
    $options[VvjpConstants::OPTION_DISABLE_OVERLAY] = ['default' => FALSE];
    $options[VvjpConstants::OPTION_OVER_CONTENT_ONLY] = ['default' => FALSE];

    return $options;
  }

  /**
   * Gets available breakpoint options.
   *
   * @return array
   *   Array of breakpoint options with translated labels.
   */
  protected function getBreakpointOptions(): array {
    return [
      self::BREAKPOINT_ALL => $this->t('Active on all screens'),
      self::BREAKPOINT_576 => $this->t('576px / 36rem'),
      self::BREAKPOINT_768 => $this->t('768px / 48rem'),
      self::BREAKPOINT_992 => $this->t('992px / 62rem'),
      self::BREAKPOINT_1200 => $this->t('1200px / 75rem'),
      self::BREAKPOINT_1400 => $this->t('1400px / 87.5rem'),
    ];
  }

  /**
   * Gets available height unit options.
   *
   * @return array
   *   Array of height unit options with translated labels.
   */
  protected function getHeightUnitOptions(): array {
    return [
      self::HEIGHT_UNIT_VW => $this->t('Viewport Width (vw)'),
      self::HEIGHT_UNIT_VH => $this->t('Viewport Height (vh)'),
      self::HEIGHT_UNIT_PX => $this->t('Pixels (px)'),
      self::HEIGHT_UNIT_PERCENT => $this->t('Percentage (%)'),
      self::HEIGHT_UNIT_EM => $this->t('Ems (em)'),
      self::HEIGHT_UNIT_REM => $this->t('Root Ems (rem)'),
    ];
  }

  /**
   * Gets available background position options.
   *
   * @return array
   *   Array of background position options with translated labels.
   */
  protected function getBackgroundPositionOptions(): array {
    return [
      self::BG_POSITION_CENTER => $this->t('Center'),
      self::BG_POSITION_TOP => $this->t('Top'),
      self::BG_POSITION_BOTTOM => $this->t('Bottom'),
      self::BG_POSITION_LEFT => $this->t('Left'),
      self::BG_POSITION_RIGHT => $this->t('Right'),
      self::BG_POSITION_TOP_LEFT => $this->t('Top Left'),
      self::BG_POSITION_TOP_RIGHT => $this->t('Top Right'),
      self::BG_POSITION_BOTTOM_LEFT => $this->t('Bottom Left'),
      self::BG_POSITION_BOTTOM_RIGHT => $this->t('Bottom Right'),
      self::BG_POSITION_CENTER_TOP => $this->t('Center Top'),
      self::BG_POSITION_CENTER_BOTTOM => $this->t('Center Bottom'),
      self::BG_POSITION_50_50 => $this->t('50% 50% (Center)'),
      self::BG_POSITION_0_0 => $this->t('0% 0% (Top Left)'),
      self::BG_POSITION_100_100 => $this->t('100% 100% (Bottom Right)'),
      self::BG_POSITION_30_70 => $this->t('30% 70%'),
      self::BG_POSITION_10PX_20PX => $this->t('10px 20px'),
      self::BG_POSITION_100PX_50 => $this->t('100px 50%'),
      self::BG_POSITION_LEFT_10 => $this->t('Left 10%'),
    ];
  }

  /**
   * Gets available easing function options.
   *
   * @return array
   *   Array of easing options with translated labels.
   */
  protected function getEasingOptions(): array {
    return [
      self::EASING_EASE => $this->t('Ease'),
      self::EASING_EASE_IN => $this->t('Ease-In'),
      self::EASING_EASE_OUT => $this->t('Ease-Out'),
      self::EASING_EASE_IN_OUT => $this->t('Ease-In-Out'),
      self::EASING_LINEAR => $this->t('Linear'),
    ];
  }

  /**
   * Gets available scroll effect options.
   *
   * @return array
   *   Array of scroll effect options with translated labels.
   */
  protected function getScrollEffectOptions(): array {
    return [
      self::SCROLL_EFFECT_NONE => $this->t('None'),
      self::SCROLL_EFFECT_FADE => $this->t('Fade In'),
      self::SCROLL_EFFECT_SCALE => $this->t('Scale Up'),
      self::SCROLL_EFFECT_ROTATE => $this->t('Rotate'),
      self::SCROLL_EFFECT_FADE_SCALE => $this->t('Fade In and Scale Up'),
      self::SCROLL_EFFECT_THREE_D => $this->t('3D transformations'),
      self::SCROLL_EFFECT_SHADOW => $this->t('Shadow Effects'),
      self::SCROLL_EFFECT_GLOW => $this->t('Glow Effects'),
      self::SCROLL_EFFECT_SMOOTH => $this->t('Smooth Transition and Timing Functions'),
      self::SCROLL_EFFECT_HOVER => $this->t('Hover Interactions'),
      self::SCROLL_EFFECT_COMBINED => $this->t('Combined Effects'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::buildOptionsForm($form, $form_state);

    $this->setDefaultElementWeights($form);
    $this->buildWarningMessage($form);
    $this->buildResponsiveSettings($form);
    $this->buildDimensionsSettings($form);
    $this->buildParallaxSettings($form);
    $this->buildBackgroundSettings($form);
    $this->buildEffectsSettings($form);
    $this->buildOverlaySettings($form);
    $this->buildLibrarySettings($form);
    $this->buildTokenInfo($form);
  }

  /**
   * Sets weights for default Drupal form elements.
   *
   * @param array $form
   *   The form array.
   */
  protected function setDefaultElementWeights(array &$form): void {
    $default_elements = [
      'grouping' => -100,
      'row_class' => -90,
      'default_row_class' => -85,
      'uses_fields' => -80,
      'class' => -75,
      'wrapper_class' => -70,
    ];

    foreach ($default_elements as $element_key => $weight) {
      if (isset($form[$element_key])) {
        $form[$element_key]['#weight'] = $weight;
      }
    }
  }

  /**
   * Builds warning message section.
   *
   * @param array $form
   *   The form array.
   */
  protected function buildWarningMessage(array &$form): void {
    if ($this->view->storage->id() === 'vvjp_example') {
      return;
    }
    if (!$this->isFirstFieldUrlToImage()) {
      $form['warning_message'] = [
        '#type' => 'markup',
        '#markup' => '<div class="messages messages--error">' .
        $this->t('Note: The first field must be an image, and you should choose the "URL to image" option under Formatter in the field configuration. To see an example, check the vvjp_example view by clicking <a href="/admin/structure/views/view/@view_id" style="display: inline;">here</a> to edit it.',
            ['@view_id' => VvjpConstants::EXAMPLE_VIEW_ID]
        ) . '</div>',
        '#weight' => -50,
      ];
    }
  }

  /**
   * Builds responsive settings section.
   *
   * @param array $form
   *   The form array.
   */
  protected function buildResponsiveSettings(array &$form): void {
    $form['responsive'] = [
      '#type' => 'details',
      '#title' => $this->t('Responsive Settings'),
      '#open' => TRUE,
      '#weight' => -40,
    ];

    $form['responsive'][VvjpConstants::OPTION_BREAKPOINTS] = [
      '#type' => 'select',
      '#title' => $this->t('Available Breakpoints for Parallax'),
      '#options' => $this->getBreakpointOptions(),
      '#default_value' => $this->options[VvjpConstants::OPTION_BREAKPOINTS],
      '#description' => $this->t('Select the maximum screen width (in pixels) at which the parallax effect should be disabled. Selecting "all" will keep the parallax effect active on all screen sizes.'),
    ];
  }

  /**
   * Builds dimensions settings section.
   *
   * @param array $form
   *   The form array.
   */
  protected function buildDimensionsSettings(array &$form): void {
    $form['dimensions'] = [
      '#type' => 'details',
      '#title' => $this->t('Dimensions'),
      '#open' => TRUE,
      '#weight' => -30,
    ];

    $section_height = $this->options[VvjpConstants::OPTION_SECTION_HEIGHT] ?? [
      'value' => VvjpConstants::DEFAULT_HEIGHT_VALUE,
      'unit' => VvjpConstants::DEFAULT_HEIGHT_UNIT,
    ];

    $form['dimensions'][VvjpConstants::OPTION_SECTION_HEIGHT] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Section Height'),
      '#description' => $this->t('Define the height of the parallax section by entering a value and selecting a unit.'),
      '#attributes' => [
        'class' => ['section-height-wrapper'],
      ],
    ];

    $form['dimensions'][VvjpConstants::OPTION_SECTION_HEIGHT]['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Height Value'),
      '#default_value' => $section_height['value'] ?? VvjpConstants::DEFAULT_HEIGHT_VALUE,
      '#description' => $this->t('Enter the numeric value for the height.'),
      '#attributes' => [
        'style' => 'width: ' . VvjpConstants::FORM_ELEMENT_WIDTH . '; display: inline-block;',
      ],
    ];

    $form['dimensions'][VvjpConstants::OPTION_SECTION_HEIGHT]['unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Height Unit (vw, vh, px, %, em, rem)'),
      '#default_value' => $section_height['unit'] ?? VvjpConstants::DEFAULT_HEIGHT_UNIT,
      '#options' => $this->getHeightUnitOptions(),
      '#attributes' => [
        'style' => 'width: ' . VvjpConstants::FORM_ELEMENT_WIDTH . '; display: inline-block;',
      ],
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

    $form['dimensions'][VvjpConstants::OPTION_MAX_WIDTH] = [
      '#type' => 'number',
      '#title' => $this->t('Max Width (px)'),
      '#default_value' => $this->options[VvjpConstants::OPTION_MAX_WIDTH],
      '#description' => $this->t('Defines the maximum width for the parallax content or insert zero allows it to stretch to 100 percent.'),
      '#step' => 1,
      '#min' => 0,
    ];
  }

  /**
   * Builds parallax settings section.
   *
   * @param array $form
   *   The form array.
   */
  protected function buildParallaxSettings(array &$form): void {
    $form['parallax'] = [
      '#type' => 'details',
      '#title' => $this->t('Parallax Settings'),
      '#open' => TRUE,
      '#weight' => -20,
    ];

    $form['parallax'][VvjpConstants::OPTION_PARALLAX_SPEED] = [
      '#type' => 'number',
      '#title' => $this->t('Parallax Speed'),
      '#default_value' => $this->options[VvjpConstants::OPTION_PARALLAX_SPEED],
      '#description' => $this->t('Set the speed of the parallax effect (e.g., 0.1 for slow, 2 for fast).'),
      '#step' => VvjpConstants::STEP_PARALLAX_SPEED,
      '#min' => VvjpConstants::MIN_PARALLAX_SPEED,
      '#max' => VvjpConstants::MAX_PARALLAX_SPEED,
    ];
  }

  /**
   * Builds background settings section.
   *
   * @param array $form
   *   The form array.
   */
  protected function buildBackgroundSettings(array &$form): void {
    $form['background'] = [
      '#type' => 'details',
      '#title' => $this->t('Background Settings'),
      '#open' => FALSE,
      '#weight' => -10,
    ];

    $form['background'][VvjpConstants::OPTION_BG_POSITION] = [
      '#type' => 'select',
      '#title' => $this->t('Background Position'),
      '#options' => $this->getBackgroundPositionOptions(),
      '#default_value' => $this->options[VvjpConstants::OPTION_BG_POSITION],
      '#description' => $this->t('Set the position of the background image.'),
    ];

    $form['background'][VvjpConstants::OPTION_BG_SPEED] = [
      '#type' => 'number',
      '#title' => $this->t('Background Animation Speed'),
      '#default_value' => $this->options[VvjpConstants::OPTION_BG_SPEED],
      '#description' => $this->t('Set the speed of the Background Animation Speed (e.g., 0.1 for slow, 2 for fast).'),
      '#step' => VvjpConstants::STEP_PARALLAX_SPEED,
      '#min' => VvjpConstants::MIN_PARALLAX_SPEED,
      '#max' => VvjpConstants::MAX_PARALLAX_SPEED,
    ];

    $form['background'][VvjpConstants::OPTION_BG_EASING] = [
      '#type' => 'select',
      '#title' => $this->t('Background Animation Easing'),
      '#options' => $this->getEasingOptions(),
      '#default_value' => $this->options[VvjpConstants::OPTION_BG_EASING],
      '#description' => $this->t('Choose the easing function for the animation.'),
    ];
  }

  /**
   * Builds effects settings section.
   *
   * @param array $form
   *   The form array.
   */
  protected function buildEffectsSettings(array &$form): void {
    $form['effects'] = [
      '#type' => 'details',
      '#title' => $this->t('Scroll Effects'),
      '#open' => FALSE,
      '#weight' => 0,
    ];

    $form['effects'][VvjpConstants::OPTION_SCROLL_EFFECT] = [
      '#type' => 'select',
      '#title' => $this->t('Scroll Effect'),
      '#options' => $this->getScrollEffectOptions(),
      '#default_value' => $this->options[VvjpConstants::OPTION_SCROLL_EFFECT],
      '#description' => $this->t('Choose the scroll effect for the parallax content.'),
    ];
  }

  /**
   * Builds overlay settings section.
   *
   * @param array $form
   *   The form array.
   */
  protected function buildOverlaySettings(array &$form): void {
    $form['overlay'] = [
      '#type' => 'details',
      '#title' => $this->t('Overlay Settings'),
      '#open' => FALSE,
      '#weight' => 10,
    ];

    $form['overlay'][VvjpConstants::OPTION_OVERLAY_COLOR] = [
      '#type' => 'color',
      '#title' => $this->t('Overlay Color'),
      '#default_value' => $this->options[VvjpConstants::OPTION_OVERLAY_COLOR],
      '#description' => $this->t('Choose a color overlay for the parallax background.'),
    ];

    $form['overlay'][VvjpConstants::OPTION_OVERLAY_OPACITY] = [
      '#type' => 'range',
      '#title' => $this->t('Overlay Opacity'),
      '#default_value' => $this->options[VvjpConstants::OPTION_OVERLAY_OPACITY],
      '#min' => VvjpConstants::MIN_OVERLAY_OPACITY,
      '#max' => VvjpConstants::MAX_OVERLAY_OPACITY,
      '#step' => VvjpConstants::STEP_OVERLAY_OPACITY,
      '#description' => $this->t('Set the opacity level for the overlay.'),
      '#suffix' => '<span id="overlay-opacity-value">' . $this->options[VvjpConstants::OPTION_OVERLAY_OPACITY] . '</span>',
      '#attributes' => [
        'oninput' => 'document.getElementById("overlay-opacity-value").innerText = this.value;',
      ],
    ];

    $form['overlay'][VvjpConstants::OPTION_OVER_CONTENT_ONLY] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Apply Overlay Background Under Content Only'),
      '#default_value' => $this->options[VvjpConstants::OPTION_OVER_CONTENT_ONLY],
      '#description' => $this->t('Enable this option to apply the overlay background color only beneath the parallax content, rather than covering the entire row.'),
    ];

    $form['overlay'][VvjpConstants::OPTION_DISABLE_OVERLAY] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable Overlay Color'),
      '#default_value' => $this->options[VvjpConstants::OPTION_DISABLE_OVERLAY],
      '#description' => $this->t('Check this to disable overlay color completely.'),
    ];
  }

  /**
   * Builds library settings section.
   *
   * @param array $form
   *   The form array.
   */
  protected function buildLibrarySettings(array &$form): void {
    $form['library'] = [
      '#type' => 'details',
      '#title' => $this->t('Library Settings'),
      '#open' => FALSE,
      '#weight' => 20,
    ];

    $form['library'][VvjpConstants::OPTION_ENABLE_CSS] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CSS Library'),
      '#default_value' => $this->options[VvjpConstants::OPTION_ENABLE_CSS],
      '#description' => $this->t('Check this box to include the CSS library for styling the parallax.'),
    ];
  }

  /**
   * Builds token information section.
   *
   * @param array $form
   *   The form array.
   */
  protected function buildTokenInfo(array &$form): void {
    $form['vvjp_token_info'] = [
      '#type' => 'details',
      '#title' => $this->t('VVJP Tokens'),
      '#open' => FALSE,
      '#weight' => 100,
    ];

    $form['vvjp_token_info']['description'] = [
      '#markup' => $this->t('<p>When using <em>Global: Text area</em> or <em>Global: Unfiltered text</em> in the Views header, footer, or empty text areas, the default Twig-style tokens (e.g., <code>{{ title }}</code>) will not work with the VVJP style plugin.</p>
        <p>Instead, use the custom VVJP token format to access field values from the <strong>first row</strong> of the View result:</p>
        <ul>
          <li><code>[vvjp:field_name]</code> – The rendered output of the field (e.g., linked title, image, formatted text).</li>
          <li><code>[vvjp:field_name:plain]</code> – A plain-text version of the field, with all HTML stripped.</li>
        </ul>
        <p>Examples:</p>
        <ul>
          <li><code>{{ title }}</code> ➜ <code>[vvjp:title]</code></li>
          <li><code>{{ field_image }}</code> ➜ <code>[vvjp:field_image]</code></li>
          <li><code>{{ body }}</code> ➜ <code>[vvjp:body:plain]</code></li>
        </ul>
        <p>These tokens offer safe and flexible field output for dynamic headings, summaries, and fallback messages in VVJP-enabled Views.</p>'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::submitOptionsForm($form, $form_state);
    $this->flattenFormValues($form_state);
  }

  /**
   * Flattens nested form values to match expected option structure.
   *
   * This ensures that values from nested fieldsets are properly stored
   * at the top level of the options array for backward compatibility.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  protected function flattenFormValues(FormStateInterface $form_state): void {
    $values = $form_state->getValue('style_options');

    if (!is_array($values)) {
      return;
    }

    // Flatten responsive settings.
    if (isset($values['responsive'])) {
      foreach ($values['responsive'] as $key => $value) {
        $values[$key] = $value;
      }
      unset($values['responsive']);
    }

    // Flatten dimensions settings.
    if (isset($values['dimensions'])) {
      foreach ($values['dimensions'] as $key => $value) {
        $values[$key] = $value;
      }
      unset($values['dimensions']);
    }

    // Flatten parallax settings.
    if (isset($values['parallax'])) {
      foreach ($values['parallax'] as $key => $value) {
        $values[$key] = $value;
      }
      unset($values['parallax']);
    }

    // Flatten background settings.
    if (isset($values['background'])) {
      foreach ($values['background'] as $key => $value) {
        $values[$key] = $value;
      }
      unset($values['background']);
    }

    // Flatten effects settings.
    if (isset($values['effects'])) {
      foreach ($values['effects'] as $key => $value) {
        $values[$key] = $value;
      }
      unset($values['effects']);
    }

    // Flatten overlay settings.
    if (isset($values['overlay'])) {
      foreach ($values['overlay'] as $key => $value) {
        $values[$key] = $value;
      }
      unset($values['overlay']);
    }

    // Flatten library settings.
    if (isset($values['library'])) {
      foreach ($values['library'] as $key => $value) {
        $values[$key] = $value;
      }
      unset($values['library']);
    }

    $form_state->setValue('style_options', $values);
  }

  /**
   * Generates a unique numeric ID for the view display.
   *
   * @return int
   *   A unique numeric ID.
   *
   * @throws \Random\RandomException
   *   Thrown if an appropriate source of randomness cannot be found.
   */
  protected function generateUniqueId(): int {
    if ($this->cachedUniqueId === NULL) {
      $this->cachedUniqueId = random_int(
        VvjpConstants::RANDOM_ID_MIN,
        VvjpConstants::RANDOM_ID_MAX
      );
    }
    return $this->cachedUniqueId;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(): array {
    $errors = parent::validate();

    if (!$this->usesFields()) {
      $errors[] = $this->t('Views Parallax requires Fields as row style');
    }

    if (!$this->isFirstFieldUrlToImage()) {
      $form['warning_message'] = [
        '#type' => 'markup',
        '#markup' => '<div class="messages messages--error">' .
        $this->t('Note: The first field must be an image, and you should choose the "URL to image" option under Formatter in the field configuration. To see an example, check the vvjp_example view by clicking <a href="/admin/structure/views/view/@view_id" style="display: inline;">here</a> to edit it.',
            ['@view_id' => VvjpConstants::EXAMPLE_VIEW_ID]
        ) . '</div>',
        '#weight' => -50,
      ];
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $rows = [];
    foreach ($this->view->result as $row) {
      $rows[] = $this->view->rowPlugin->render($row);
    }

    $libraries = [VvjpConstants::LIBRARY_JS];

    if ($this->options[VvjpConstants::OPTION_ENABLE_CSS]) {
      $libraries[] = VvjpConstants::LIBRARY_CSS;
    }

    if (!empty($this->options[VvjpConstants::OPTION_BREAKPOINTS])) {
      $libraries[] = VvjpConstants::LIBRARY_BREAKPOINT_PREFIX . $this->options[VvjpConstants::OPTION_BREAKPOINTS];
    }

    return [
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $this->options,
      '#rows' => $rows,
      '#unique_id' => $this->options[VvjpConstants::OPTION_UNIQUE_ID],
      '#attached' => [
        'library' => $libraries,
      ],
    ];
  }

  /**
   * Checks if the first field is set to "URL to image".
   *
   * @return bool
   *   TRUE if the first field is set to "URL to image", FALSE otherwise.
   */
  protected function isFirstFieldUrlToImage(): bool {
    $fields = $this->view->display_handler->getOption('fields');

    if (!is_array($fields) || empty($fields)) {
      return FALSE;
    }

    $first_field = reset($fields);

    return isset($first_field['type']) &&
           $first_field['type'] === VvjpConstants::REQUIRED_FIELD_TYPE;
  }

}
