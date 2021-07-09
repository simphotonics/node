<?php

namespace Simphotonics\Dom\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlNode;
use Simphotonics\Dom\HtmlNavigator;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlNavigator using URI's with
 * different format.
 */
class HtmlNavigatorTest extends TestCase
{
    public function testHome()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $nav = self::initNavigator();
        $this->assertEquals('<div id="nav" class="has-shadow">' .
            '<ul id="mainMenu"><li class="here">' .
            '<a href="/">HOME</a></li><li>' .
            '<a href="/services">SERVICES</a></li></ul></div>', "$nav");
    }

    public function testServices()
    {
        $_SERVER['REQUEST_URI'] = '/services';
        $nav = self::initNavigator();
        $this->assertEquals(
            '<div id="nav" class="has-shadow">'
                . '<ul id="mainMenu"><li><a href="/">HOME</a></li>'
                . '<li class="here"><a href="/services">SERVICES</a>' .
                '</li></ul></div>',
            "$nav"
        );
    }

    /**
     * Initialises HtmlNavigator object.
     * @method  initNavigator
     * @return  void
     */
    private static function initNavigator()
    {
        // Templates
        $L = new HtmlLeaf(kind: 'a');
        $B = new HtmlNode(kind: 'li', childNodes: [$L]);
        // Home
        $B_home = clone $B;
        $B_home[0]->setAttributes(['href' => '/'])->setContent('HOME');
        // Services
        $B_services = clone $B;
        $B_services[0]->setAttributes(['href' => '/services'])
            ->setContent('SERVICES');

        $Menu = new HtmlNode(kind:'ul',
            attributes: ['id' => 'mainMenu'],
        );

        $Menu->append([$B_home, $B_services]);
        return new HtmlNavigator(
            kind:'div',
                attributes:['id' => 'nav', 'class' => 'has-shadow'],
                childNodes:[$Menu]
        );
    }
}
