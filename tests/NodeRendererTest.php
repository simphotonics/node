<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlNode;
use Simphotonics\Dom\Parser\NodeRenderer as Renderer;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\Dom\NodeRenderer.
 */
class NodeRendererTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        // Register 'empty' elements just in case they are not registered yet.
        HtmlLeaf::registerElements(['img' => 'empty']);
    }

    public function testRenderLeaf()
    {
        $img = new HtmlLeaf(['kind' => 'img',
            'attr' => ['id' => 'img1',
            'class' => 'wide',
            'href' => 'public/img/image1.jpg'
            ]
        ]);
 
        $expected='$'.$img->getID().' = new \Simphotonics\Dom\HtmlLeaf([
  \'kind\' => \'img\',
  \'attr\'=> [
    \'id\' => \'img1\',
    \'class\' => \'wide\',
    \'href\' => \'public/img/image1.jpg\'
  ],
]); 
';

        $this->assertEquals($expected, Renderer::render($img));
    }

    public function testRenderNode()
    {
        $img = new HtmlLeaf(['kind' => 'img',
            'attr' => ['id' => 'img1',
            'class' => 'wide',
            'href' => 'public/img/image1.jpg'
            ]
        ]);
        $div = new HtmlNode([
            'child' => [$img],
            'attr' => [
                'id' => 'div1'
            ]
        ]);
        $expected = '$'.$div->getID().' = new \Simphotonics\Dom\HtmlNode([
  \'kind\' => \'div\',
  \'attr\'=> [
    \'id\' => \'div1\'
  ],
  \'child\'=> [
    $'.$img->getID().'
  ]
]); 
';
        $this->assertEquals($expected, Renderer::render($div));
    }

    public function testRenderRecursive()
    {
        $img = new HtmlLeaf(['kind' => 'img',
            'attr' => ['id' => 'img1',
            'class' => 'wide',
            'href' => 'public/img/image1.jpg'
            ]
        ]);
        $div = new HtmlNode([
            'child' => [$img],
            'attr' => [
                'id' => 'div1'
            ]
        ]);

        $expected = '
$'.$img->getID().' = new \Simphotonics\Dom\HtmlLeaf([
  \'kind\' => \'img\',
  \'attr\'=> [
    \'id\' => \'img1\',
    \'class\' => \'wide\',
    \'href\' => \'public/img/image1.jpg\'
  ],
]); 

$'.$div->getID().' = new \Simphotonics\Dom\HtmlNode([
  \'kind\' => \'div\',
  \'attr\'=> [
    \'id\' => \'div1\'
  ],
  \'child\'=> [
    $'.$img->getID().'
  ]
]); 
';
        $this->assertEquals($expected, Renderer::renderRecursive($div));
    
    }
}
