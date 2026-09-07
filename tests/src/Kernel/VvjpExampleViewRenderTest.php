<?php

declare(strict_types=1);

namespace Drupal\Tests\vvjp\Kernel;

use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\vvj_core\Kernel\VvjArticleImageExampleKernelTrait;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\views\Views;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Renders the shipped example view all the way to HTML.
 *
 * WHY THIS EXISTS. Every other test in this module stops short of rendering:
 * the install test checks the module installs, the style plugin test checks
 * the options and the build array. Neither runs the Twig template, so a style
 * option whose stored shape the template cannot print produced a working
 * install, a passing suite and HTTP 500 on the page — released in 2.0.0 and
 * unnoticed until 2026-09-07.
 *
 * A render is the cheapest place to catch that: it needs no browser, and it
 * goes red on exactly the class of defect this project keeps hitting — a
 * setting you can save and then watch take the page down.
 *
 * @see vvjp_update_10002()
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 */
#[RunTestsInSeparateProcesses]
class VvjpExampleViewRenderTest extends KernelTestBase {

  use VvjArticleImageExampleKernelTrait;

  /**
   * {@inheritdoc}
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
   * The shipped example view renders to HTML without a render-array error.
   */
  public function testExampleViewRenders(): void {
    $this->createArticlesWithImages(2);

    $view = Views::getView('vvjp_example');
    $this->assertNotNull($view, 'The example view ships as optional config.');
    $view->setDisplay('page_1');

    $build = $view->preview('page_1');
    $this->assertIsArray($build, 'The view produced a render array.');

    $renderer = $this->container->get('renderer');
    $html = (string) $renderer->renderInIsolation($build);

    $this->assertNotEmpty($view->result, 'The view returned rows.');
    $this->assertStringContainsString('vvjp-inner', $html, 'The parallax wrapper rendered.');
    $this->assertStringContainsString('parallax-row', $html, 'A parallax row rendered.');
  }

  /**
   * The breakpoint option is a scalar, because a single select produces one.
   *
   * Guards the shipped example view against regressing to the list form that
   * 2.0.0 released, which the template cannot print.
   */
  public function testShippedBreakpointOptionIsScalar(): void {
    $displays = $this->config('views.view.vvjp_example')->get('display');
    $this->assertIsArray($displays);

    $checked = 0;
    foreach ($displays as $display_id => $display) {
      if (!is_array($display)) {
        continue;
      }
      $display_options = $display['display_options'] ?? NULL;
      if (!is_array($display_options)) {
        continue;
      }
      $style = $display_options['style'] ?? NULL;
      if (!is_array($style) || ($style['type'] ?? NULL) !== 'views_vvjp') {
        continue;
      }
      $options = $style['options'] ?? NULL;
      if (!is_array($options) || !array_key_exists('available_breakpoints', $options)) {
        continue;
      }
      $this->assertIsString(
        $options['available_breakpoints'],
        sprintf('Display "%s" stores available_breakpoints as a string.', (string) $display_id),
      );
      $checked++;
    }

    $this->assertGreaterThan(0, $checked, 'At least one display sets the option.');
  }

  /**
   * The update hook repairs a view already carrying the 2.0.0 list value.
   *
   * Writes the broken shape straight to config storage — the config factory
   * would reject it now that the schema says string, which is the point — then
   * runs the update and proves the page renders again.
   */
  public function testUpdateNormalisesStoredList(): void {
    $name = 'views.view.vvjp_example';
    $storage = $this->container->get('config.storage');

    $data = $storage->read($name);
    $this->assertIsArray($data, 'The example view is in config storage.');
    $displays = $data['display'];
    $this->assertIsArray($displays);
    $default = $displays['default'];
    $this->assertIsArray($default);
    $display_options = $default['display_options'];
    $this->assertIsArray($display_options);
    $style = $display_options['style'];
    $this->assertIsArray($style);
    $options = $style['options'];
    $this->assertIsArray($options);

    $options['available_breakpoints'] = ['576'];
    $style['options'] = $options;
    $display_options['style'] = $style;
    $default['display_options'] = $display_options;
    $displays['default'] = $default;
    $data['display'] = $displays;
    $storage->write($name, $data);
    $this->container->get('config.factory')->reset($name);

    $this->container->get('module_handler')->loadInclude('vvjp', 'install');
    $message = vvjp_update_10002();
    $this->assertStringContainsString($name, $message, 'The update reported the view it fixed.');

    $breakpoints = $this->config($name)
      ->get('display.default.display_options.style.options.available_breakpoints');
    $this->assertSame('576', $breakpoints, 'The list collapsed to the string it held.');

    // The crash the list caused is gone.
    $this->createArticlesWithImages(1);
    $view = Views::getView('vvjp_example');
    $this->assertNotNull($view, 'The example view ships as optional config.');
    $view->setDisplay('page_1');
    $build = $view->preview('page_1');
    $this->assertIsArray($build, 'The view produced a render array.');
    $html = (string) $this->container->get('renderer')->renderInIsolation($build);
    $this->assertStringContainsString('parallax-row', $html, 'The repaired view renders.');
  }

  /**
   * Creates published articles carrying an image, so the view has rows.
   *
   * @param int $count
   *   How many nodes to create.
   */
  private function createArticlesWithImages(int $count): void {
    $file_system = $this->container->get('file_system');
    $directory = 'public://vvjp-render-test';
    $file_system->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
    $uri = $file_system->copy(
      DRUPAL_ROOT . '/core/tests/fixtures/files/image-test.png',
      $directory . '/image-test.png',
      FileExists::Replace,
    );

    $file = File::create(['uri' => $uri, 'status' => 1]);
    $file->save();

    for ($i = 0; $i < $count; $i++) {
      Node::create([
        'type' => 'article',
        'title' => 'Render probe ' . $i,
        'status' => 1,
        'body' => ['value' => '<p>Body ' . $i . '</p>', 'format' => 'plain_text'],
        'field_image' => ['target_id' => $file->id(), 'alt' => 'Render probe'],
      ])->save();
    }
  }

}
