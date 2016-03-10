<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlNode;
use Simphotonics\Dom\HtmlParser;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlTitle using URI's with
 * different format.
 */
class HtmlParserTest extends \PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $img = '<img class="img" href="https://simphotonics.com/image/img1.jpg"/>';
        $source = '<br/><br/>'.$img;
        $p = new HtmlParser($source);
        $this->assertEquals(3, count($p->getNodes()));
        $this->assertEquals($img, ''.$p->getNodes()[2]);
        $this->assertEquals('empty', $p->getFormatInfo()['img']);
    }

    public function testDTD()
    {
        $dtd = '<!DOCTYPE html5 >';
        $source = $dtd.'<html><body><p><br/></p></body></html>';
        $p = new HtmlParser($source);
        $this->assertEquals(2, count($p->getNodes()));
        $this->assertEquals($dtd, ''.$p->getNodes()[0]);
        $this->assertEquals('empty', $p->getFormatInfo()['br']);
    }

    public function testComment()
    {
        $comment = '<!-- This is comment <<<>> <div/  <> /> -->';
        $source = $comment.'<html><body><p>
        <!-- This is comment within a paragraph! --><br/></p></body></html>';
        $p = new HtmlParser($source);
        $this->assertEquals(2, count($p->getNodes()));
        $this->assertEquals($comment, ''.$p->getNodes()[0]);
    }

    public function testBlock()
    {
        $source = '<html><body><p>Content of paragraph!<br/></p></body></html>';
        $p = new HtmlParser($source);
        $this->assertEquals(1, count($p->getNodes()));
        // The notation below works since HtmlNode implements the ArrayAccess interface!
        $this->assertEquals(
            'Content of paragraph!',
            $p->getNodes()[0][0][0]->getCont()
        );
       
    }
}
