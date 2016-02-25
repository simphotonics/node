<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlSelect;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlTitle using URI's with
 * different format.
 */
class HtmlSelectTest extends \PHPUnit_Framework_TestCase
{

    private $select = null;

    public function __construct()
    {
        $this->select = new HtmlSelect(
            'country',
            ['GB' => 'Great Britain',
            'USA' => 'United States of America'],
            'GB'
        );
    }

    public function testInitOptions()
    {
         $this->assertEquals(
             '<select name="country" id="country"><option value="GB" selected="selected">Great Britain</option><option value="USA">United States of America</option></select>',
             "$this->select"
         );
    }

    public function testClearDefaultOption()
    {
        $this->select->clearDefaultOption();
        $this->assertEquals(
            '<select name="country" id="country"><option value="GB">Great Britain</option><option value="USA">United States of America</option></select>',
            "$this->select"
        );
    }

    public function testSetDefaultOption()
    {
       
        $this->select->setDefaultOption('USA');
        $this->assertEquals(
            '<select name="country" id="country"><option value="GB">Great Britain</option><option value="USA" selected="selected">United States of America</option></select>',
            "$this->select"
        );
    }
}
