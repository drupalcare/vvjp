<?php

declare(strict_types=1);

namespace Drupal\Tests\vvjp\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\vvj_core\Kernel\VvjArticleImageExampleKernelTrait;
use Drupal\views\Views;
use Drupal\vvjp\Plugin\views\style\Parallax;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Verifies the views_vvjp style plugin loads + provides expected defaults.
 */
#[Group('vvjp')]
#[RunTestsInSeparateProcesses]
final class VvjpStylePluginTest extends KernelTestBase {

  use VvjArticleImageExampleKernelTrait;

  /**
   * {@inheritdoc}
   *
   * @var list<string>
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'file',
    'filter',
    'text',
    'image',
    'node',
    'views',
    'vvj_core',
    'vvjp',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->vvjKernelInstallArticleImageExampleEnvironment('vvjp');
  }

  /**
   * The style plugin is registered with the expected class and theme.
   */
  public function testStylePluginIsRegistered(): void {
    /** @var \Drupal\views\Plugin\ViewsPluginManager $manager */
    $manager = $this->container->get('plugin.manager.views.style');
    $definition = $manager->getDefinition('views_vvjp');
    $this->assertIsArray($definition);

    $this->assertSame(Parallax::class, $definition['class']);
    $this->assertSame('views_view_vvjp', $definition['theme']);
  }

  /**
   * The example view loads and initializes the style plugin.
   */
  public function testExampleViewLoadsWithStyle(): void {
    $view = Views::getView('vvjp_example');
    $this->assertNotNull($view, 'vvjp_example view ships as optional config.');

    $view->setDisplay('default');
    $view->initStyle();

    $this->assertInstanceOf(Parallax::class, $view->style_plugin);
    $this->assertSame('views_vvjp', $view->style_plugin->getPluginId());
  }

  /**
   * The style plugin defines the expected option defaults.
   */
  public function testStyleDefinesExpectedOptionDefaults(): void {
    $view = Views::getView('vvjp_example');
    $this->assertNotNull($view);
    $view->setDisplay('default');
    $view->initStyle();

    $options = $view->style_plugin->options;

    $this->assertArrayHasKey('parallax_speed', $options);
    $this->assertArrayHasKey('scroll_effect', $options);
    $this->assertArrayHasKey('available_breakpoints', $options);
    $this->assertArrayHasKey('unique_id', $options);
    // Parallax is a scroll-driven decorative effect with no panels/slides to
    // deep-link to, so it overrides supportsDeeplinking() to FALSE and the
    // base class therefore does not define the deeplink options.
    $this->assertArrayNotHasKey('enable_deeplink', $options);
    $this->assertArrayNotHasKey('deeplink_identifier', $options);
  }

  /**
   * Rendering produces cache metadata and attaches the pattern library.
   */
  public function testRenderProducesCacheableOutput(): void {
    $view = Views::getView('vvjp_example');
    $this->assertNotNull($view);
    $view->setDisplay('default');
    $view->execute();

    $build = $view->style_plugin->render();
    $this->assertIsArray($build);

    $this->assertArrayHasKey('#cache', $build);
    $cache = $build['#cache'];
    $this->assertIsArray($cache);
    $this->assertArrayHasKey('tags', $cache);
    $this->assertArrayHasKey('contexts', $cache);
    $this->assertArrayHasKey('max-age', $cache);

    $this->assertArrayHasKey('#attached', $build);
    $attached = $build['#attached'];
    $this->assertIsArray($attached);
    $this->assertArrayHasKey('library', $attached);
    $library = $attached['library'];
    $this->assertIsArray($library);
    $this->assertContains('vvjp/vvjp', $library);
  }

}
