<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\Node;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests Simphotonics\Node methods.
 */

class NodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test instance of Node.
     * @var Simphotonics\Dom\Node
     */
    private static $n;

    /**
     * Test instance of Node.
     * @var Simphotonics\Dom\Node
     */
    private static $n0;

    /**
     * Test instance of Node.
     * @var Simphotonics\Dom\Node
     */
    private static $n1;

    /**
     * Test instance of Node.
     * @var Simphotonics\Dom\Node
     */
    private static $n2;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        self::$n = new Node();
        self::$n0 = new Node($i = ['attr' => ['class' => 'main']]);
        self::$n1 = new Node($i = ['attr' => ['class' => 'main bold']]);
        self::$n2 = new Node($i = ['kind' => 't1']);
        self::$n->append([self::$n0,self::$n1,self::$n2]);
    }
    
    public function testAppend()
    {
        $n = new Node();
        $n->append([$n,$n,$n]);
        $this->assertEquals(3, $n->count());
    }

    /**
     * @depends testAppend
     */
    public function testRemoveChild()
    {
        $n = new Node();
        $child = new Node();
        $n->append([$child]);
        $n->removeChild($child);
        $this->assertEquals(0, $n->count());
    }


    public function testReplaceChild()
    {
        $n = new Node();
        $this->assertEquals(false, self::$n->replaceChild($n, $n));
        $this->assertEquals(true, self::$n->replaceChild($n, self::$n2));
        $this->assertEquals($n, self::$n[2]);
        $this->assertEquals(true, self::$n->replaceChild(self::$n2, $n));
    }

    public function testReplaceNode()
    {
        $oldNode = new Node();
        self::$n2->appendChild($oldNode);
        $newNode = new Node();
        self::$n->replaceNode($newNode, $oldNode);
        $this->assertEquals($newNode, self::$n2[0]);
        unset(self::$n2[0]);
    }

    public function testInsertBefore()
    {
        $n = new Node();
        $this->assertEquals(true, self::$n->insertBefore($n, self::$n2));
        $this->assertEquals($n, self::$n[2]);
        self::$n[2] = self::$n2;
    }

    public function testInsertAfter()
    {
        $n = new Node();
        $this->assertEquals(true, self::$n->insertAfter($n, self::$n2));
        $this->assertEquals($n, self::$n[3]);

    }

    public function testHasChildNodes()
    {
        $this->assertTrue(self::$n->hasChildNodes());
        $n = new Node();
        $this->assertFalse($n->hasChildNodes());
    }

    public function testRemoveNode()
    {
        self::init();
        $newNode = new Node();
        self::$n2->appendChild($newNode);
        $this->assertEquals($newNode, self::$n2[0]);
        self::$n->removeNode($newNode);
        $this->assertFalse(self::$n2->hasChildNodes());
    }

    public function testGetDescendants()
    {
        $n = new Node();
        self::$n1->appendChild($n);
        // Leaves only
        $this->assertEquals(
            [self::$n0,
            $n,
            self::$n2],
            self::$n->getDescendants(0)
        );
        // Self first
        $this->assertEquals(
            [self::$n0,
            self::$n1,
            $n,
            self::$n2],
            self::$n->getDescendants(1)
        );
        // Leaves first
        $this->assertEquals(
            [self::$n0,
            $n,
            self::$n1,
            self::$n2],
            self::$n->getDescendants(2)
        );
        unset(self::$n1[0]);
    }

    public function testGetNodesByKind()
    {
        $this->assertEquals([], self::$n->getNodesByKind('h1'));
        $this->assertEquals(
            [self::$n0,self::$n1],
            self::$n->getNodesByKind('div')
        );

        $n = new Node($i = ['kind' => 'p']);
        self::$n1->appendChild($n);
        $this->assertEquals([$n], self::$n->getNodesByKind('p'));
        unset(self::$n1[0]);
    }

    public function testGetNodesByAttrKey()
    {
        $this->assertEquals(
            [self::$n0,self::$n1],
            self::$n->getNodesByAttrKey('class')
        );
        $this->assertEquals(
            [self::$n0],
            self::$n->getNodesByAttrKey(['class'=>'main'])
        );
    }

    public function testGetNodesByAttrValue()
    {
        $this->assertEquals(
            [self::$n0],
            self::$n->getNodesByAttrValue(['class' => 'main'])
        );
        $this->assertEquals(
            [self::$n1],
            self::$n->getNodesByAttrValue(['class' => 'main bold'])
        );
        $this->assertEquals(
            [],
            self::$n->getNodesByAttrValue(['class'=>'whatever'])
        );
    }

    public function testGetNodeByID()
    {
        $this->assertEquals(
            self::$n2,
            self::$n->getNodeByID(self::$n2->getID())
        );
    }

    /**
     *
     */
    public function testPermuteException()
    {
        $this->expectException(InvalidArgumentException::class);
        self::$n->permute([0,2,1,6]);
    }

    /**
     * @depends testPermuteException
     */
    public function testPermute()
    {
        self::init();
        self::$n->permute([2,0,1]);
        $this->assertEquals(self::$n->last(), self::$n1);
        $this->assertEquals(self::$n->first(), self::$n2);
        $this->assertEquals(self::$n[1], self::$n0);
        // Restore node order
        self::$n->permute([1,2,0]);
        $this->assertEquals(self::$n2, self::$n->last());

    }


    public function testFirst()
    {
        $this->assertEquals(self::$n0, self::$n->first());
    }

    public function testLast()
    {
        $this->assertEquals(self::$n2, self::$n->last());
    }

    public function testCount()
    {
        $this->assertEquals(3, self::$n->count());
    }

    public function testNext()
    {
        $n1 = self::$n->next();
        $this->assertEquals(self::$n1, $n1);
    }

    /**
     * @depends testNext
     */
    public function testValid()
    {
        $this->assertEquals(self::$n->valid(), true);
        self::$n->next();
        self::$n->next();
        self::$n->next();
        $this->assertEquals(self::$n->valid(), false);
    }

    public function testOffsetSet()
    {
        $n = new Node();
        self::$n->offsetSet(1, $n);
        $this->assertEquals($n, self::$n[1]);
        $n1 = new Node();
        self::$n->offsetSet(100, $n1);
        $this->assertEquals($n1, self::$n->last());
        $n2 = new Node();
        self::$n->offsetSet(0, $n2);
        $this->assertEquals($n2, self::$n->first());

        foreach (self::$n as $key => $child) {
            $keys[] = $key;
            $nodes[] = $child;
        }

        $this->assertEquals([$n2, $n, self::$n2, $n1], $nodes);
        $this->assertEquals([0,1,2,3], $keys);
    }
}
