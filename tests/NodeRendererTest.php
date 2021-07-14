<?php

namespace Simphotonics\Node\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Node\HtmlLeaf;
use Simphotonics\Node\HtmlNode;
use Simphotonics\Node\Parser\NodeRenderer as Renderer;

/**
 * @author    D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\Node\NodeRenderer.
 */
class NodeRendererTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    // Register 'empty' elements just in case they are not registered yet.
    HtmlLeaf::registerElements(['img' => 'inline']);
  }

  public function testRenderLeaf()
  {
    $img = new HtmlLeaf(
      kind: 'img',
      attributes: [
        'id' => 'img1',
        'class' => 'wide',
        'href' => 'public/img/image1.jpg'
      ]
    );

    $expected = "\${$img->id()} = new \Simphotonics\Node\HtmlLeaf(\n" .
      "  kind: 'img',\n" .
      "  attributes:   [\n" .
      "    'id' => 'img1',\n" .
      "    'class' => 'wide',\n" .
      "    'href' => 'public/img/image1.jpg',\n" .
      "  ],\n" .
      "); \n" .
      "";

    $this->assertEquals(
      $expected,
      Renderer::render($img)
    );
  }

  public function testRenderNode()
  {
    $img = new HtmlLeaf(
      kind: 'img',
      attributes: [
        'id' => 'img1',
        'class' => 'wide',
        'href' => 'public/img/image1.jpg'
      ]
    );
    $div = new HtmlNode(
      childNodes: [$img],
      attributes: [
        'id' => 'div1'
      ]
    );
    $expected = "\${$div->id()} = new \Simphotonics\Node\HtmlNode(\n" .
      "  kind: 'div',\n" .
      "  attributes:   [\n" .
      "    'id' => 'div1',\n" .
      "  ],\n" .
      "  childNodes:   'child'=> [\n" .
      "    \${$img->id()},\n" .
      "  ]\n" .
      "); \n";

    $this->assertEquals($expected, Renderer::render($div));
  }

  public function testRenderRecursive()
  {
    $img = new HtmlLeaf(
      kind: 'img',
      attributes: [
        'id' => 'img1',
        'class' => 'wide',
        'href' => 'public/img/image1.jpg'
      ]
    );
    $div = new HtmlNode(
      childNodes: [$img],
      attributes: [
        'id' => 'div1'
      ]
    );

    $expected =
      "\n" .
      "\${$img->id()} = new \Simphotonics\Node\HtmlLeaf(\n" .
      "  kind: 'img',\n" .
      "  attributes:   [\n" .
      "    'id' => 'img1',\n" .
      "    'class' => 'wide',\n" .
      "    'href' => 'public/img/image1.jpg',\n" .
      "  ],\n" .
      "); \n" .
      "\n" .
      "\${$div->id()} = new \Simphotonics\Node\HtmlNode(\n" .
      "  kind: 'div',\n" .
      "  attributes:   [\n" .
      "    'id' => 'div1',\n" .
      "  ],\n" .
      "  childNodes:   'child'=> [\n" .
      "    \${$img->id()},\n" .
      "  ]\n" .
      "); \n" .
      "";
    $this->assertEquals($expected, Renderer::renderRecursive($div));
  }
}
