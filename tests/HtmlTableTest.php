<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlTable;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests Simphotonics\HtmlNode methods.
 */
class HtmlTableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Table data
     * @var  array
     */
    private $data = [];

    public function __construct()
    {
        for ($i=1; $i < 16; $i++) {
            $this->data[] = 'Data'.$i;
        }
    }

    public function testConstructor()
    {
        $table = new HtmlTable(array_slice($this->data, 0, 4));
        $this->assertEquals('<table><tr><th class="col1"><span>Data1</span></th><th class="col2"><span>Data2</span></th></tr><tr><td class="col1"><span>Data3</span></td><td class="col2"><span>Data4</span></td></tr></table>', "$table");
    }

    public function testSetRowAlt()
    {
        $table = new HtmlTable($this->data);
        // Default rowOffset is 2 => Rows 0 and 1 are omitted.
        // Rows of class alt: 2,4,...
        $this->assertEquals(['class' => 'alt'], $table[4]->getAttr());
        $table->setRowAlt(3);
        // Rows of class alt: 2,5,...
        $this->assertEquals(['class' => 'alt'], $table[5]->getAttr());
    }

    public function testSetRowOffset()
    {
        $table = new HtmlTable($this->data);
        // Default rowOffset is 2 => Rows 0 and 1 are omitted.
        $table->setRowOffset(3);
        // Rows of class alt: 3,5,...
        $this->assertEquals([], $table[0]->getAttr());
        $this->assertEquals([], $table[1]->getAttr());
        $this->assertEquals([], $table[2]->getAttr());
        $this->assertEquals(['class' => 'alt'], $table[3]->getAttr());
        $this->assertEquals(['class' => 'alt'], $table[5]->getAttr());
    }

    public function testSetNumberOfColumns()
    {
        $table = new HtmlTable($this->data, 4);
        $this->assertEquals(4, $table->count());
        $this->assertEquals('Data13', $table[3][0][0]->getCont());
        // Change table layout
        $table->setNumberOfColumns(3);
        $this->assertEquals(5, $table->count());
        $this->assertEquals('Data13', $table[4][0][0]->getCont());
    }

    public function testAppendToLastRow()
    {
        $table = new HtmlTable($this->data, 7);
        $table->appendToLastRow(['NData1','NData2']);
        $this->assertEquals('NData1', $table[2][1][0]->getCont());
    }

    public function testAppendRow()
    {
        $table = new HtmlTable($this->data, 7);
        $table->appendRow(['NData1','NData2']);
        $this->assertEquals('NData1', $table[3][0][0]->getCont());
    }

    public function testDeleteFirstRow()
    {
        $table = new HtmlTable($this->data, 3);
        $table->deleteFirstRow();
        $this->assertEquals('Data4', $table[0][0][0]->getCont());
    }

    public function testDeleteRow()
    {
        $table = new HtmlTable($this->data, 3);
        $table->deleteRow(1);
        $this->assertEquals('Data7', $table[1][0][0]->getCont());
    }

    public function testDeleteLastRow()
    {
        $table = new HtmlTable($this->data, 3);
        $this->assertEquals(5, $table->count());
        $table->deleteLastRow();
        $this->assertEquals(4, $table->count());
    }

    public function testDeleteColumn()
    {
        $table = new HtmlTable($this->data, 5);
        $this->assertEquals('Data3', $table[0][2][0]->getCont());
        $table->deleteColumn(2);
        $this->assertEquals('Data4', $table[0][2][0]->getCont());
    }
}
