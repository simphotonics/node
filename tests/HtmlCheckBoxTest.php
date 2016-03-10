<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlCheckBox;
use Simphotonics\Dom\HtmlNode;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlTitle using URI's with
 * different format.
 */
class HtmlCheckBoxTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        // Register 'empty' elements just in case they are not registered yet.
        HtmlCheckBox::registerElements(['br' => 'empty','input' => 'empty']);
    }

    public function testCheckBox()
    {
        $checkBox = new HtmlCheckBox('checkBoxName', 'checkBoxValue');
        $this->assertEquals(
            '<input name="checkBoxName" value="checkBoxValue" type="checkbox"/>',
            "$checkBox"
        );
    }

    public function testGetCheckBoxes()
    {
        $div = new HtmlNode();
        $checkBoxes = HtmlCheckBox::getCheckBoxes(
            ['name1' => 'value1','name2' => 'value2']
        );
        $div->append($checkBoxes);
        $this->assertEquals('<div><span><input name="name1" value="value1" type="checkbox"/></span><br/><span><input name="name2" value="value2" type="checkbox"/></span></div>', "$div");
    }
}
