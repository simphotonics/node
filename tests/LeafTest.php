<?php

namespace Simphotonics\Node\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Node\Leaf;
use Simphotonics\Node\Node;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests methods of class \Simphotonics\Node\Leaf.
 */

class LeafTest extends TestCase
{
    /**
     * Test instance of leaf
     * @var Simphotonics\Node\Leaf
     */
    private static $l;

    public static function setUpBeforeClass(): void
    {
        self::$l = new Leaf(...[
            'kind' => 'div',
            'attributes' => ['class' => 'main emph', 'id' => 'div1'],
            'content' => 'This is a test!'
        ]);
    }

    public function testSetKind()
    {
        self::$l->setKind('span');
        $this->assertEquals('span', self::$l->kind());
        self::$l->setKind('div');
    }

    public function testSetContent()
    {
        $l = new Leaf();
        $this->assertFalse($l->hasContent());
        $l->setContent('the content');
        $this->assertTrue($l->hasContent());
        $this->assertEquals($l->content(), 'the content');
    }

    public function testSetAttributes()
    {
        // Test mode 'add'
        self::$l->setAttributes(['class' => 'bold'], 'add');
        $this->assertEquals('main emph bold', self::$l->attributes()['class']);

        // Test mode 'replace'
        self::$l->setAttributes(['class' => 'main emph'], 'replace');
        $this->assertEquals('main emph', self::$l->attributes()['class']);
    }

    public function testAttributeIsEmpty()
    {
        $this->assertTrue(self::$l->attributesIsNotEmpty());
        self::$l->resetAttributes();
        $this->assertTrue(self::$l->attributesIsEmpty());
        self::$l->setAttributes(['class' => 'main emph', 'id' => 'div1']);
    }

    public function testHasAttributes()
    {
        $this->assertTrue(self::$l->hasAttributes(['class' => 'main emph']));
        $this->assertFalse(self::$l->hasAttributes(['class' => 'bold']));
    }

    public function testShowID()
    {
        $newline = (PHP_SAPI == 'cli') ? "\n" : "<br/>";
        $expected = self::$l->id() . " | parent: NULL" . $newline;
        $this->assertEquals($expected, self::$l->showID());
    }

    public function testGetAncestor()
    {
        $a = new Leaf(kind: 'a');
        $div = new Node();
        $div->appendChild($a);
        $body = new Node(kind: 'body');
        $body->appendChild($div)->appendChild($a);
        $ancestor = $a->getAncestor(2);
        $this->assertEquals($body, $ancestor);
    }
}
