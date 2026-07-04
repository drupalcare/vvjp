/**
 * @file
 * VVJP compat shim. Behavior lives in `<vvjp-parallax>` custom element.
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 */

// phpcs:disable Drupal.NamingConventions.UpperCaseConstant
// phpcs:disable Generic.PHP.UpperCaseConstant
// phpcs:disable Squiz.WhiteSpace.OperatorSpacing

((Drupal, once) => {
  'use strict';

  Drupal.behaviors.VVJParallax = {
    attach(context) {
      once('vvjp-parallax-marker', 'vvjp-parallax', context);
    },
    detach() {},
  };
})(Drupal, once);
