<?php

namespace Drupal\vvjp\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

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
    $options['background_position'] = ['default' => 'center center'];
    $options['overlay_color'] = ['default' => '#000000'];
    $options['overlay_opacity'] = ['default' => 0.3];
    $options['section_height'] = ['default' => 75];
    $options['available_breakpoints'] = ['default' => ['none']];
    $options['bg_animation_easing'] = ['default' => 'ease'];
    $options['bg_animation_speed'] = ['default' => 0.5];
    $options['enable_css'] = ['default' => TRUE];
    $options['scroll_effect'] = ['default' => 'fade'];
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

    // Add a warning message at the top of the settings form.
    $form['warning_message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="messages messages--warning">' . $this->t('Note: The first field must be an image, and you should choose the "URL to image" option under Formatter in the field configuration. The module comes with an example view named vvjp_example that you can check for reference.') . '</div>',
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

    // Background position configuration.
    $form['background_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Background Position'),
      '#options' => [
        'center' => $this->t('Center'),
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
      ],
      '#default_value' => $this->options['background_position'],
      '#description' => $this->t('Set the position of the background image.'),
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

    // Section height configuration.
    $form['section_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Section Height (vh)'),
      '#default_value' => $this->options['section_height'],
      '#description' => $this->t('Define the height of the parallax section in viewport height units.'),
    ];

    // Enable CSS library configuration.
    $form['enable_css'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CSS Library'),
      '#default_value' => $this->options['enable_css'],
      '#description' => $this->t('Check this box to include the CSS library for styling the parallax.'),
    ];

    // Responsive breakpoints configuration.
    $form['available_breakpoints'] = [
      '#type' => 'select',
      '#title' => $this->t('Available Breakpoints for Parallax'),
      '#options' => [
        'none' => $this->t('Active on all screens'),
        '576' => $this->t('576px / 36rem'),
        '768' => $this->t('768px / 48rem'),
        '992' => $this->t('992px / 62rem'),
        '1200' => $this->t('1200px / 75rem'),
        '1400' => $this->t('1400px / 87.5rem'),
      ],
      '#default_value' => $this->options['available_breakpoints'],
      '#description' => $this->t('Select the maximum screen width (in pixels) at which the parallax effect should be disabled. Selecting "none" will keep the parallax effect active on all screen sizes.'),
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
    if ($this->options['available_breakpoints'] != 'none') {
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
    return $errors;
  }

}
