<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlNode;
use Simphotonics\Dom\HtmlDropDownNavigator;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlNavigator using URI's with
 * different format.
 */
class HtmlDropDownNavigatorTest extends \PHPUnit_Framework_TestCase
{
    public function testEntrySorting()
    {
        $_SERVER['REQUEST_URI'] = '/services';
        $nav = self::initDropDownNavigator();
        $this->assertEquals('<div id="nav" class="has-shadow"><ul class="dropDownMenu"><li class="here"><a href="/services">SERVICES</a></li><li><a href="/">HOME</a></li><li><a href="/about-us">ABOUT-US</a></li></ul></div>', "$nav");
    }
    
    /**
     * Initialises HtmlNavigator object.
     * @method  initNavigator
     * @return  void
     */
    private static function initDropDownNavigator()
    {
        // Templates
        $L = new HtmlLeaf([
        'kind' => 'a'
        ]);
        $B = new HtmlNode([
        'kind' => 'li',
        'child' => [$L]
        ]);
        // Home
        $B_home = clone $B;
        $B_home[0]->setAttr(['href' => '/'])->setCont('HOME');
        // Services
        $B_services = clone $B;
        $B_services[0]->setAttr(['href' => '/services'])->setCont('SERVICES');
        // About-Us
        $B_about = clone $B;
        $B_about[0]->setAttr(['href' => '/about-us'])->setCont('ABOUT-US');
 
        $Menu = new HtmlNode([
        'kind' => 'ul',
        'attr' => ['class' => 'dropDownMenu'],
        ]);

        $Menu->append([$B_home, $B_services, $B_about]);
        return new HtmlDropDownNavigator([
        'kind' => 'div',
        'attr' => ['id' => 'nav','class' => 'has-shadow'],
        'child' => [$Menu]
        ]);
    }
}
