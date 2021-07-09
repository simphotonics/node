<?php

namespace Simphotonics\Dom\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Dom\HtmlSelect;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlTitle using URI's with
 * different format.
 */
class HtmlSelectTest extends TestCase
{

    private static HtmlSelect $select;

    public function testInitOptions()
    {
        $select = new HtmlSelect(
            name: 'country',
            options: [
                'GB' => 'Great Britain',
                'USA' => 'United States of America'
            ],
            defaultOption: 'GB'
        );
        $this->assertEquals(
            '<select name="country" id="country">'.
                '<option value="GB" selected="selected">Great Britain</option>'.
                '<option value="USA">United States of America</option></select>',
            $select . ''
        );
    }

    public function testClearDefaultOption()
    {
        $select = new HtmlSelect(
            'country',
            [
                'GB' => 'Great Britain',
                'USA' => 'United States of America'
            ],
            'GB'
        );
        $select->clearDefaultOption();
        $this->assertEquals(
            '<select name="country" id="country">' .
                '<option value="GB">Great Britain</option>'
                . '<option value="USA">United States of America</option></select>',
            $select . ''
        );
    }

    public function testSetDefaultOption()
    {
        $select = new HtmlSelect(
            'country',
            [
                'GB' => 'Great Britain',
                'USA' => 'United States of America'
            ],
            'GB'
        );
        $select->setDefaultOption('USA');
        $this->assertEquals(
            '<select name="country" id="country">' .
                '<option value="GB">Great Britain</option>' .
                '<option value="USA" selected="selected">'
                . 'United States of America</option></select>',
            $select . ''
        );
    }
}
