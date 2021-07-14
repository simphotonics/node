<?php

declare(strict_types=1);

namespace Simphotonics\Node\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Node\HtmlNode;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests Simphotonics\HtmlNode methods.
 */
class HtmlNodeTest extends TestCase
{
    /**
     * Test instance of HtmlNode.
     * @var Simphotonics\Node\HtmlNode
     */
    private static $n;

    /**
     * Test instance of HtmlNode.
     * @var Simphotonics\Node\HtmlNode
     */
    private static $n0;

    /**
     * Test instance of HtmlNode.
     * @var Simphotonics\Node\HtmlNode
     */
    private static $n1;

    public static function setUpBeforeClass(): void
    {
        self::$n = new HtmlNode();
        self::$n0 = new HtmlNode(kind: 'p', attributes: ['class' => 'main']);
        self::$n1 = new HtmlNode(
            kind: 'tr',
            attributes: ['class' => 'main bold']
        );
        self::$n->append([self::$n0, self::$n1]);
    }

    public function testRenderBlock()
    {

        $n = new HtmlNode(
            attributes: ['class' => 'main'],
            content: ('This is a block element!')
        );
        $p = new HtmlNode(kind: 'p', content: 'Content of p');
        $n->appendChild($p);
        $this->assertEquals(
            '<div class="main">This is a block element!'
                . '<p>Content of p</p></div>',
            '' . $n
        );
    }

    public function testSimpleBlockElement()
    {
        $n = new HtmlNode(kind: 'div', content: 'node content');
        $this->assertEquals('<div>node content</div>', "$n");
    }

    public function testNestedBlockElement()
    {
        $n = new HtmlNode(
            kind: 'div',
            content: 'node content',
            attributes: ['class' => 'main']
        );
        $n->appendChild(new HtmlNode(kind: 'p', attributes: ['id' => 'id89']));
        $this->assertTrue($n->hasChildNodes());
        $this->assertEquals(
            '<div class="main">node content<p id="id89"></p></div>',
            "$n"
        );
    }
}
