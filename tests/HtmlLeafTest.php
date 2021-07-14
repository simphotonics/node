<?php

namespace Simphotonics\Node\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Node\HtmlLeaf;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests Simphotonics\HtmlNode methods.
 */
class HtmlLeafTest extends TestCase
{
    public function testRegisterElement()
    {
        $element = ['br' => 'inline'];
        HtmlLeaf::registerElements($element);
        $elements = HtmlLeaf::getElements();
        $this->assertArrayHasKey('br', $elements);
        $this->assertEquals($elements['br'], 'inline');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterElementException()
    {
        $this->expectException(\InvalidArgumentException::class);
        HtmlLeaf::registerElements(['img' => 'non existing render method']);
    }

    public function testSetContent()
    {
        $l = new HtmlLeaf();
        $this->assertFalse($l->hasContent());
        $l->setContent('the content');
        $this->assertTrue($l->hasContent());
        $this->assertEquals($l->content(), 'the content');
    }

    public function testRenderBlock()
    {
        $l = new HtmlLeaf(
            attributes: ['class' => 'main bold'],
            content: 'Test Leaf!'
        );
        $this->assertEquals(
            '<div class="main bold">Test Leaf!</div>',
            "$l"
        );
    }

    public function testRenderEmpty()
    {
        HtmlLeaf::registerElements(['br' => 'inline']);
        $l = new HtmlLeaf(...['kind' => 'br']);
        $this->assertEquals('<br/>', "$l");
    }

    public function testRenderComment()
    {
        $l = new HtmlLeaf(...[
            'kind' => '!--',
            'content' => 'This is a comment!'
        ]);
        $this->assertEquals('<!-- This is a comment! -->', "$l");
    }

    public function testRenderDTD()
    {
        HtmlLeaf::setDTD('html5');
        $dtd = new HtmlLeaf(...['kind' => '!DOCTYPE']);
        // Empty content => The static variable HtmlLeaf::$dtd
        //                  will be used as element content!
        $this->assertEquals('<!DOCTYPE html5 >', "$dtd");
        // If a content is set, it overwrites the default content.
        $dtd->setContent('html5-test');
        $this->assertEquals('<!DOCTYPE html5-test >', "$dtd");
    }
}
