<?php

/**
 * @file
 * Contains the hook implementations for the custom Views style plugin.
 */

/**
 * Implements hook_views_data_alter().
 */
function vvjp_views_data_alter(array &$data): void {
  // Check if the views_style_plugin table is defined in Views.
  if (!isset($data['views_style_plugin'])) {
    $data['views_style_plugin'] = [];
  }

  $data['views_style_plugin']['views_vvjp'] = [
    'group' => t('Style plugins'),
    'title' => t('Views Vanilla JavaScript Parallax'),
    'help' => t('Render items in Parallax using vanilla JavaScript.'),
    'style' => [
      'animation' => [
        'title' => t('Animation Type'),
        'help' => t('Select the animation type for the parallax effect.'),
        'field' => [
          'id' => 'string',
        ],
      ],
      'parallax_speed' => [
        'title' => t('Parallax Speed'),
        'help' => t('Set the speed of the parallax effect.'),
        'field' => [
          'id' => 'numeric',
        ],
      ],
      'background_position' => [
        'title' => t('Background Position'),
        'help' => t('Position of the background image.'),
        'field' => [
          'id' => 'string',
        ],
      ],
      'overlay_color' => [
        'title' => t('Overlay Color'),
        'help' => t('Select the overlay color for the parallax background.'),
        'field' => [
          'id' => 'string',
        ],
      ],
      'overlay_opacity' => [
        'title' => t('Overlay Opacity'),
        'help' => t('Set the opacity level for the overlay.'),
        'field' => [
          'id' => 'numeric',
        ],
      ],
      'section_height' => [
        'title' => t('Section Height'),
        'help' => t('Define the height of the parallax section in viewport height units.'),
        'field' => [
          'id' => 'numeric',
        ],
      ],
      'available_breakpoints' => [
        'title' => t('Responsive Breakpoints'),
        'help' => t('Define breakpoints for responsive design.'),
        'field' => [
          'id' => 'string',
        ],
      ],
      'bg_animation_easing' => [
        'title' => t('Background Animation Easing'),
        'help' => t('Choose the easing function for animations.'),
        'field' => [
          'id' => 'string',
        ],
      ],
      'bg_animation_speed' => [
        'title' => t('Background Animation Speed'),
        'help' => t('Set the speed of the background animation.'),
        'field' => [
          'id' => 'numeric',
        ],
      ],
      'scroll_effect' => [
        'title' => t('Scroll Effect'),
        'help' => t('Choose the scroll effect for the parallax content.'),
        'field' => [
          'id' => 'string',
        ],
      ],
      'enable_css' => [
        'title' => t('Enable CSS Library'),
        'help' => t('Enable or disable the CSS library for styling the parallax.'),
        'field' => [
          'id' => 'boolean',
        ],
      ],

    ],
  ];
}
