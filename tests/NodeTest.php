<?php

declare(strict_types=1);

namespace Simphotonics\Dom\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Dom\Node;

use function PHPUnit\Framework\assertEquals;

/**
 * @author    D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests Simphotonics\Node methods.
 */

class NodeTest extends TestCase
{

    public function testAppend()
    {
        $n = new Node();
        $n->append([$n, $n, $n]);
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
        $n1 = new Node();
        $n2 = new Node();
        $n->appendChild($n1);
        $this->assertFalse($n->replaceChild($n, $n));
        $this->assertTrue($n->replaceChild($n1, $n2));
        $this->assertEquals($n2, $n[0]);
        $this->assertTrue($n->replaceChild($n2, $n1));
    }

    public function testReplaceNode()
    {
        $n = new Node();
        $n1 = new Node();
        $n2 = new Node();
        $n1->appendChild($n2);
        $n->appendChild($n1);
        $n3 = new Node();
        $n->replaceNode($n2, $n3);
        $this->assertEquals($n3, $n1[0]);
    }

    public function testInsertBefore()
    {
        $n = new Node();
        $n1 = new Node();
        $n2 = new Node();
        $n->appendChild($n1);
        $this->assertTrue($n->insertBefore($n1, $n2));
        $this->assertEquals($n1, $n[1]);
    }

    public function testInsertAfter()
    {
        $n = new Node();
        $n1 = new Node();
        $n2 = new Node();
        $n->appendChild($n1);
        $this->assertEquals(true, $n->insertAfter($n1, $n2));
        $this->assertEquals($n2, $n[1]);
    }

    public function testHasChildNodes()
    {
        $n = new Node();
        $n1 = new Node();
        $n2 = new Node();
        $n->appendChild($n1);
        $this->assertTrue($n->hasChildNodes());
        $this->assertFalse($n2->hasChildNodes());
    }

    public function testRemoveNode()
    {
        $n = new Node();
        $n1 = new Node();
        $n->appendChild($n1);
        $this->assertEquals($n1, $n[0]);
        $n->removeNode($n1);
        $this->assertFalse($n->hasChildNodes());
    }

    public function testGetDescendants()
    {

        $n = new Node();
        $n1 = new Node();
        $n2 = new Node();
        $n1->appendChild($n2);
        $n->appendChild($n1);
        // Leaves only
        $this->assertEquals(
            [
                $n2
            ],
            $n->getDescendants(0)
        );
        // Self first
        $this->assertEquals(
            [
                $n1, $n2,
            ],
            $n->getDescendants(1)
        );
        // Leaves first
        $this->assertEquals(
            [
                $n2, $n1
            ],
            $n->getDescendants(2)
        );
    }

    public function testGetNodesByKind()
    {
        $n = new Node();
        $n0 = new Node(kind: 'div', attributes: ['class' => 'main']);
        $n1 = new Node(kind: 'div', attributes: ['class' => 'main bold']);
        $n2 = new Node(kind: 't1');
        $n->append([$n0, $n1, $n2]);
        $this->assertEquals([], $n->getNodesByKind('h1'));
        $this->assertEquals(
            [$n0, $n1],
            $n->getNodesByKind('div')
        );

        $n3 = new Node(...['kind' => 'p']);
        $n->appendChild($n3);
        $this->assertEquals([$n3], $n->getNodesByKind('p'));
    }

    public function testGetNodesByAttributeKey()
    {
        $n = new Node();
        $n0 = new Node(attributes: ['class' => 'main']);
        $n1 = new Node(attributes: ['class' => 'main bold']);
        $n2 = new Node(...['kind' => 't1']);
        $n->append([$n0, $n1, $n2]);
        $nodes = $n->getNodesByAttributeKey('class');

        $this->assertEquals(
            [$n0, $n1],
            $nodes,
        );
    }

    public function testGetNodesByAttributeValue()
    {
        $n = new Node();
        $n0 = new Node(attributes: ['class' => 'main']);
        $n1 = new Node(attributes: ['class' => 'main bold']);
        $n2 = new Node(...['kind' => 't1']);
        $n->append([$n0, $n1, $n2]);
        $this->assertEquals(
            [$n0],
            $n->getNodesByAttributeValue(['class' => 'main'])
        );
        $this->assertEquals(
            [$n1],
            $n->getNodesByAttributeValue(['class' => 'main bold'])
        );
        $this->assertEquals(
            [],
            $n->getNodesByAttributeValue(['class' => 'whatever'])
        );
    }

    public function testGetNodeByID()
    {
        $n = new Node();
        $n0 = new Node(attributes: ['class' => 'main']);
        $n1 = new Node(attributes: ['class' => 'main bold']);
        $n2 = new Node(...['kind' => 't1']);
        $n->append([$n0, $n1, $n2]);
        $this->assertEquals(
            $n2,
            $n->getNodeByID($n2->id())
        );
    }

    public function testPermuteException()
    {
        $n = new Node();
        $this->expectException(\InvalidArgumentException::class);
        $n->permute([0, 2, 1, 6]);
    }

    /**
     * @depends testPermuteException
     */
    public function testPermute()
    {
        $n = new Node();
        $n0 = new Node(attributes: ['class' => 'main']);
        $n1 = new Node(attributes: ['class' => 'main bold']);
        $n2 = new Node(...['kind' => 't1']);
        $n->append([$n0, $n1, $n2]);

        $n->permute([2, 0, 1]);
        $this->assertEquals($n->last(), $n1);
        $this->assertEquals($n->first(), $n2);
        $this->assertEquals($n[1], $n0);
        // Restore node order
        $n->permute([1, 2, 0]);
        $this->assertEquals($n2, $n->last());
    }


    public function testFirst()
    {
        $n = new Node();
        $n0 = new Node(attributes: ['class' => 'main']);
        $n1 = new Node(attributes: ['class' => 'main bold']);
        $n2 = new Node(...['kind' => 't1']);
        $n->append([$n0, $n1, $n2]);
        $this->assertEquals($n0, $n->first());
    }

    public function testLast()
    {
        $n = new Node();
        $n0 = new Node(attributes: ['class' => 'main']);
        $n1 = new Node(attributes: ['class' => 'main bold']);
        $n2 = new Node(...['kind' => 't1']);
        $n->append([$n0, $n1, $n2]);
        $this->assertEquals($n2, $n->last());
    }

    public function testCount()
    {
        $n = new Node();
        $n1 = new Node();
        $n2 = new Node();
        $n1->appendChild($n2);
        $n->appendChild($n1);
        $this->assertEquals(1, $n->count());
    }

    public function testNext()
    {
        $n = new Node();
        $n0 = new Node();
        $n1 = new Node();
        $n2 = new Node();
        $n->append([$n0, $n1, $n2]);  // [$n1 ->[$n2]]
        $this->assertEquals($n->current(), $n0);
        $n->next();
        $this->assertEquals($n->current(), $n1);
    }

    /**
     * @depends testNext
     */
    public function testValid()
    {
        $n = new Node();
        $n1 = new Node();
        $n2 = new Node();
        $n1->appendChild($n2);
        $n->appendChild($n1);
        $this->assertEquals($n->valid(), true);
        $n->next();
        $n->next();
        $n->next();
        $this->assertEquals($n->valid(), false);
    }

    public function testOffsetSet()
    {
        $n = new Node();
        $n0 = new Node();
        $n1 = new Node();
        $n->append([$n0, $n1]); // [$n0, $n1]
        $this->assertEquals([$n0, $n1,], $n->childNodes());
        $n2 = new Node();
        $n->offsetSet(0, $n2); // [$n2, $n1]
        $this->assertEquals([$n2, $n1,], $n->childNodes());

        $n3 = new Node();
        $n->offsetSet(100, $n3); // [$n2, $n1, $n3]
        $this->assertEquals($n3, $n->last());
        $n5 = new Node();
        $n->offsetSet(0, $n5); // [$n5, $n1, $n3]
        $this->assertEquals($n5, $n->first());

        foreach ($n as $key => $child) {
            print($key);
            $keys[] = $key;
            $nodes[] = $child;
        }

        $this->assertEquals([0, 1, 2], $keys);
        $this->assertEquals([$n5, $n1, $n3], $n->childNodes());
    }
}
