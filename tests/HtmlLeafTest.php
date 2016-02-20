<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlLeaf;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests Simphotonics\HtmlNode methods.
 */
class HtmlLeafTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterElement()
    {
        $element = ['br'=>'empty'];
        HtmlLeaf::registerElements($element);
        $this->assertArraySubset(HtmlLeaf::getElements(), ['br' => 'renderEmpty']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterElementException()
    {
        $this->expectException(\InvalidArgumentException::class);
        HtmlLeaf::registerElements(['img' => 'non existing type']);
    }

    public function testRenderBlock()
    {
        $l = new HtmlLeaf(['attr'=> ['class'=> 'main bold'],
            'cont'=>'Test Leaf!']);
        $this->assertEquals(
            '<div class="main bold">Test Leaf!</div>',
            "$l"
        );
    }

    public function testRenderEmpty()
    {
        HtmlLeaf::registerElements(['br'=>'empty']);
        $l = new HtmlLeaf(['kind' => 'br']);
        $this->assertEquals('<br />', "$l");
    }
   
    public function testRenderComment()
    {
        $l = new HtmlLeaf(['kind'=> '!--','cont' => 'This is a comment!']);
        $this->assertEquals('<!-- This is a comment! -->', "$l");
    }

    public function testRenderDTD()
    {
        HtmlLeaf::setDTD('html5');
        $dtd = new HtmlLeaf(['kind' => 'DOCTYPE!']);
        // Empty content => The static variable HtmlLeaf::$dtd
        //                  will be used as element content!
        $this->assertEquals('<DOCTYPE! html5 >', "$dtd");
        // If a content is set, it overwrites the default content.
        $dtd->setCont('html5-test');
        $this->assertEquals('<DOCTYPE! html5-test >', "$dtd");
    }
}
