<?php

declare(strict_types=1);

namespace Drupal\Tests\vvjp\FunctionalJavascript;

use Drupal\Tests\vvj_core\FunctionalJavascript\VvjPatternJavascriptTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Browser smoke tests for the vvjp example view custom element.
 */
#[Group('vvjp')]
#[Group('vvj_javascript')]
final class VvjpJavascriptSmokeTest extends VvjPatternJavascriptTestBase {

  /**
   * {@inheritdoc}
   *
   * @var list<string>
   */
  protected static $modules = [
    'node',
    'block',
    'views',
    'field',
    'file',
    'filter',
    'text',
    'image',
    'vvj_core',
    'vvjp',
  ];

  /**
   * {@inheritdoc}
   */
  protected function vvjExampleViewId(): string {
    return 'vvjp_example';
  }

  /**
   * {@inheritdoc}
   */
  protected function vvjCustomElementTag(): string {
    return 'vvjp-parallax';
  }

  /**
   * {@inheritdoc}
   */
  protected function vvjRequiresArticleImageField(): bool {
    return TRUE;
  }

}
