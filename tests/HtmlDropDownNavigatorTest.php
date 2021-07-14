<?php

declare(strict_types=1);

namespace Simphotonics\Node\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Node\HtmlLeaf;
use Simphotonics\Node\HtmlNode;
use Simphotonics\Node\HtmlDropDownNavigator;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlNavigator using URI's with
 * different format.
 */
class HtmlDropDownNavigatorTest extends TestCase
{
    public function testEntrySorting()
    {
        $_SERVER['REQUEST_URI'] = '/services';
        $nav = self::initDropDownNavigator();
        $this->assertEquals('<div id="nav" class="has-shadow">' .
            '<ul class="dropDownMenu"><li class="here">' .
            '<a href="/services">SERVICES</a></li><li><a href="/">HOME</a>' .
            '</li><li><a href="/about-us">ABOUT-US</a></li></ul></div>', "$nav");
    }

    /**
     * Initialises the HtmlDropDownNavigator object.
     *
     * @method  initNavigator
     *
     * @return  \Simphotonics\Node\HtmlDropDownNavigator
     */
    private static function initDropDownNavigator(): HtmlDropDownNavigator
    {
        // Templates
        $L = new HtmlLeaf(
            kind: 'a'
        );
        $B = new HtmlNode(
            kind: 'li',
            childNodes: [$L]
        );
        // Home
        $B_home = clone $B;
        $B_home[0]->setAttributes(['href' => '/'])->setContent('HOME');
        // Services
        $B_services = clone $B;
        $B_services[0]->setAttributes(['href' => '/services'])
            ->setContent('SERVICES');
        // About-Us
        $B_about = clone $B;
        $B_about[0]->setAttributes(['href' => '/about-us'])
            ->setContent('ABOUT-US');

        $Menu = new HtmlNode(
            kind: 'ul',
            attributes: ['class' => 'dropDownMenu'],
        );

        $Menu->append([$B_home, $B_services, $B_about]);
        return new HtmlDropDownNavigator(
            kind: 'div',
            attributes: ['id' => 'nav', 'class' => 'has-shadow'],
            childNodes: [$Menu]
        );
    }
}
