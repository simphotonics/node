<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\Leaf;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests methods of class \Simphotonics\Dom\Leaf.
 */

class LeafTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test instance of leaf
     * @var Simphotonics\Dom\Leaf
     */
    private static $l;

    public function __construct()
    {
        self::$l = new Leaf([
            'kind' => 'div',
            'attr' => ['class' => 'main emph','id' => 'div1'],
            'cont' => 'This is a test!'
            ]);
    }

    public function testSetKind()
    {
        self::$l->setKind('span');
        $this->assertEquals('span', self::$l->getKind());
        self::$l->setKind('div');
    }

    public function testSetAttr()
    {
        // Test mode 'add'
        self::$l->setAttr(['class' => 'bold'], 'add');
        $this->assertEquals('main emph bold', self::$l->getAttr()['class']);
        
        // Test mode 'replace'
        self::$l->setAttr(['class' => 'main emph'], 'replace');
        $this->assertEquals('main emph', self::$l->getAttr()['class']);
    }

    public function testHasAttr()
    {
        $this->assertEquals(true, self::$l->hasAttr());
        self::$l->resetAttr();
        $this->assertEquals(false, self::$l->hasAttr());
        self::$l->setAttr(['class' => 'main emph','id' => 'div1']);
    }

    public function testHasAttrValue()
    {
        $this->assertEquals(true, self::$l->hasAttrValue('class', 'main'));
        $this->assertEquals(false, self::$l->hasAttrValue('class', 'bold'));
    }

    public function testHasAttrValues()
    {
        $this->assertEquals(true, self::$l->hasAttrValues(
            ['class' => 'main emph']
        ));
    }

    public function testShowID()
    {
        $newline = (PHP_SAPI == 'cli') ? "\n" : "<br/>";
        $expected = self::$l->getID(). " | parent: NULL". $newline;
        $this->assertEquals($expected, self::$l->showID());
    }
}
