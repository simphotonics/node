<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlNode;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests Simphotonics\HtmlNode methods.
 */
class HtmlNodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test instance of HtmlNode.
     * @var Simphotonics\Dom\HtmlNode
     */
    private static $n;

    /**
     * Test instance of HtmlNode.
     * @var Simphotonics\Dom\HtmlNode
     */
    private static $n0;

    /**
     * Test instance of HtmlNode.
     * @var Simphotonics\Dom\HtmlNode
     */
    private static $n1;

    public function __construct()
    {
        self::$n = new HtmlNode();
        self::$n0 = new HtmlNode(['attr' => ['class' => 'main']]);
        self::$n1 = new HtmlNode(['attr' => ['class' => 'main bold']]);
        self::$n->append([self::$n0,self::$n1]);
    }
           
    public function testRenderBlock()
    {
        self::$n->setCont('This is a block element!');
        $this->assertEquals(
            '<div>This is a block element!<div class="main"></div><div class="main bold"></div></div>',
            ''.self::$n
        );
    }
}
