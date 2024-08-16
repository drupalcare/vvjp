<?php

namespace Drupal\vvjp\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Url;

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
   * Does the style plugin use a row plugin.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Set default options.
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();
    $options['unique_id'] = ['default' => $this->generateUniqueId()];
    $options['parallax_speed'] = ['default' => 0.3];
    $options['background_position'] = ['default' => 'center'];
    $options['overlay_color'] = ['default' => '#000000'];
    $options['overlay_opacity'] = ['default' => 0.3];
    $options['section_height'] = [
      'default' => [
        'value' => 75,
        'unit' => 'vh',
      ],
    ];
    $options['available_breakpoints'] = ['default' => ['none']];
    $options['bg_animation_easing'] = ['default' => 'ease'];
    $options['bg_animation_speed'] = ['default' => 0.5];
    $options['enable_css'] = ['default' => TRUE];
    $options['scroll_effect'] = ['default' => 'fade'];
    $options['disable_overlay'] = ['default' => FALSE];
    $options['over_content_only'] = ['default' => FALSE];
    return $options;
  }

  /**
   * Builds the options form for configuring the plugin.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    // Add a warning message if the first field is not set to "URL to image".
    if (!$this->isFirstFieldUrlToImage()) {
      $form['warning_message'] = [
        '#type' => 'markup',
        '#markup' => '<div class="messages messages--error">' . $this->t('Note: The first field must be an image, and you should choose the "URL to image" option under Formatter in the field configuration. To see an example, check the vvjp_example view by clicking <a href="/admin/structure/views/view/vvjp_example" style="display: inline;">here</a> to edit it.', ['@url' => Url::fromRoute('entity.view.edit_form', ['view' => 'vvjp_example'])->toString()]) . '</div>',
      ];
    }

    // Responsive breakpoints configuration.
    $form['available_breakpoints'] = [
      '#type' => 'select',
      '#title' => $this->t('Available Breakpoints for Parallax'),
      '#options' => [
        'all' => $this->t('Active on all screens'),
        '576' => $this->t('576px / 36rem'),
        '768' => $this->t('768px / 48rem'),
        '992' => $this->t('992px / 62rem'),
        '1200' => $this->t('1200px / 75rem'),
        '1400' => $this->t('1400px / 87.5rem'),
      ],
      '#default_value' => $this->options['available_breakpoints'],
      '#description' => $this->t('Select the maximum screen width (in pixels) at which the parallax effect should be disabled. Selecting "none" will keep the parallax effect active on all screen sizes.'),
    ];

    $form['section_height'] = [
    // Or 'fieldset' if you want a titled box around it.
      '#type' => 'container',
      '#title' => $this->t('Section Height'),
      '#description' => $this->t('Define the height of the parallax section by entering a value and selecting a unit.'),
      '#attributes' => [
    // Optional, for styling or JS targeting.
        'class' => ['section-height-wrapper'],
      ],
    ];

    $section_height = $this->options['section_height'] ??
    ['value' => 75, 'unit' => 'vh'];

    $form['section_height']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Height Value'),
      '#default_value' => $section_height['value'] ?? 75,
      '#description' => $this->t('Enter the numeric value for the height.'),
      '#attributes' => [
    // Optional, for inline display.
        'style' => 'width: 150px; display: inline-block;',
      ],
    ];

    $form['section_height']['unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Height Unit'),
      '#default_value' => $section_height['unit'] ?? 'vh',
      '#options' => [
        'vh' => $this->t('Viewport Height (vh)'),
        'px' => $this->t('Pixels (px)'),
        '%' => $this->t('Percentage (%)'),
        'em' => $this->t('Ems (em)'),
      ],
      '#attributes' => [
      // Optional, for inline display.
        'style' => 'width: 150px; display: inline-block;',
      ],
      '#description' => $this->t('Select the unit for the height.'),
    ];

    // Parallax speed configuration.
    $form['parallax_speed'] = [
      '#type' => 'number',
      '#title' => $this->t('Parallax Speed'),
      '#default_value' => $this->options['parallax_speed'],
      '#description' => $this->t('Set the speed of the parallax effect (e.g., 0.1 for slow, 2 for fast).'),
      '#step' => 0.1,
      '#min' => 0.1,
      '#max' => 2,
    ];

    // Background animation speed configuration.
    $form['bg_animation_speed'] = [
      '#type' => 'number',
      '#title' => $this->t('Background Animation Speed'),
      '#default_value' => $this->options['bg_animation_speed'],
      '#description' => $this->t('Set the speed of the Background Animation Speed (e.g., 0.1 for slow, 2 for fast).'),
      '#step' => 0.1,
      '#min' => 0.1,
      '#max' => 2,
    ];

    $form['background_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Background Position'),
      '#options' => [
        'center' => $this->t('Center'),
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
        'top left' => $this->t('Top Left'),
        'top right' => $this->t('Top Right'),
        'bottom left' => $this->t('Bottom Left'),
        'bottom right' => $this->t('Bottom Right'),
        'center top' => $this->t('Center Top'),
        'center bottom' => $this->t('Center Bottom'),
        '50% 50%' => $this->t('50% 50% (Center)'),
        '0% 0%' => $this->t('0% 0% (Top Left)'),
        '100% 100%' => $this->t('100% 100% (Bottom Right)'),
        '30% 70%' => $this->t('30% 70%'),
        '10px 20px' => $this->t('10px 20px'),
        '100px 50%' => $this->t('100px 50%'),
        'left 10%' => $this->t('Left 10%'),
      ],
      '#default_value' => $this->options['background_position'],
      '#description' => $this->t('Set the position of the background image.'),
    ];

    // Background animation easing configuration.
    $form['bg_animation_easing'] = [
      '#type' => 'select',
      '#title' => $this->t('Background Animation Easing'),
      '#options' => [
        'ease' => $this->t('Ease'),
        'ease-in' => $this->t('Ease-In'),
        'ease-out' => $this->t('Ease-Out'),
        'ease-in-out' => $this->t('Ease-In-Out'),
        'linear' => $this->t('Linear'),
      ],
      '#default_value' => $this->options['bg_animation_easing'],
      '#description' => $this->t('Choose the easing function for the animation.'),
    ];

    // Scroll effect configuration.
    $form['scroll_effect'] = [
      '#type' => 'select',
      '#title' => $this->t('Scroll Effect'),
      '#options' => [
        'none' => $this->t('None'),
        'fade' => $this->t('Fade In'),
        'scale' => $this->t('Scale Up'),
        'rotate' => $this->t('Rotate'),
        'fade-scale' => $this->t('Fade In and Scale Up'),
        'three-d' => $this->t('3D transformations'),
        'shadow' => $this->t('Shadow Effects'),
        'glow' => $this->t('Glow Effects'),
        'smooth' => $this->t('Smooth Transition and Timing Functions'),
        'hover' => $this->t('Hover Interactions'),
        'combined' => $this->t('Combined Effects'),
      ],
      '#default_value' => $this->options['scroll_effect'],
      '#description' => $this->t('Choose the scroll effect for the parallax content.'),
    ];

    // Overlay color configuration.
    $form['overlay_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Overlay Color'),
      '#default_value' => $this->options['overlay_color'],
      '#description' => $this->t('Choose a color overlay for the parallax background.'),
    ];

    // Overlay opacity configuration.
    $form['overlay_opacity'] = [
      '#type' => 'range',
      '#title' => $this->t('Overlay Opacity'),
      '#default_value' => $this->options['overlay_opacity'],
      '#min' => 0,
      '#max' => 1,
      '#step' => 0.1,
      '#description' => $this->t('Set the opacity level for the overlay.'),
      '#suffix' => '<span id="overlay-opacity-value">' . $this->options['overlay_opacity'] . '</span>',
      '#attributes' => [
        'oninput' => 'document.getElementById("overlay-opacity-value").innerText = this.value;',
      ],
    ];

    $form['over_content_only'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Apply Overlay Background Under Content Only'),
      '#default_value' => $this->options['over_content_only'],
      '#description' => $this->t('Enable this option to apply the overlay background color only beneath the parallax content, rather than covering the entire row.'),
    ];

    $form['disable_overlay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable Overlay Color'),
      '#default_value' => $this->options['disable_overlay'],
      '#description' => $this->t('Check this to disable overlay color completely.'),
    ];

    // Enable CSS library configuration.
    $form['enable_css'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CSS Library'),
      '#default_value' => $this->options['enable_css'],
      '#description' => $this->t('Check this box to include the CSS library for styling the parallax.'),
    ];

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
    // 8 digit unique ID
    return random_int(10000000, 99999999);
  }

  /**
   * Renders the view with the parallax style.
   *
   * @return array
   *   A render array for the parallax view.
   */
  public function render(): array {
    $rows = [];
    foreach ($this->view->result as $row) {
      // Render the row content.
      $rows[] = $this->view->rowPlugin->render($row);
    }

    $libraries = [
      'vvjp/vvjp',
    ];

    // Conditionally include the CSS library based on the option.
    if ($this->options['enable_css']) {
      // Include the vvjp-style library.
      $libraries[] = 'vvjp/vvjp-style';
    }

    // Conditionally include the CSS library based on the option.
    if ($this->options['available_breakpoints']) {
      // Include the vvjp-style library.
      $libraries[] = 'vvjp/vvjp__' . $this->options['available_breakpoints'];
    }

    return [
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $this->options,
      '#rows' => $rows,
      '#unique_id' => $this->options['unique_id'],
      '#attached' => [
        'library' => $libraries,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = parent::validate();
    if (!$this->usesFields()) {
      $errors[] = $this->t('Views Parallax requires Fields as row style');
    }

    // Check if the first field is not set to "URL to image".
    if (!$this->isFirstFieldUrlToImage()) {
      // Generate the URL to the view edit form.
      $link = Url::fromRoute('entity.view.edit_form', ['view' => $this->view->storage->id()])->toString();
      \Drupal::messenger()->addError($this->t('Note: The first field must be an image, and you should choose the "URL to image" option under Formatter in the field configuration. To see an example, check the view by clicking <a href="/admin/structure/views/view/vvjp_example" style="display: inline;">here</a> to edit it.', ['@url' => $link]));
    }
    return $errors;
  }

  /**
   * Check if the first field is set to "URL to image".
   *
   * @return bool
   *   TRUE if the first field is set to "URL to image", FALSE otherwise.
   */
  private function isFirstFieldUrlToImage() {
    // Get the fields in the display.
    $fields = $this->view->display_handler->getOption('fields');
    if (is_array($fields) && !empty($fields)) {
      // Get the first field.
      $first_field = reset($fields);
      // Check if the first field's type is "image_url".
      if (isset($first_field['type']) && $first_field['type'] === 'image_url') {
        return TRUE;
      }
    }
    return FALSE;
  }

}
