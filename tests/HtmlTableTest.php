<?php

namespace Simphotonics\Dom\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlTable;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Tests Simphotonics\HtmlNode methods.
 */
class HtmlTableTest extends TestCase
{

    /**
     * Table data
     * @var  array
     */
    private static $data = [];

    public static function setUpBeforeClass(): void
    {
        for ($i = 1; $i < 16; $i++) {
            self::$data[] = 'Data' . $i;
        }
    }

    public function testConstructor()
    {
        $table = new HtmlTable(inputData: array_slice(self::$data, 0, 4));
        $this->assertEquals('<table><tr><th class="col1"><span>'.
        'Data1</span></th><th class="col2"><span>'.
        'Data2</span></th></tr><tr><td class="col1"><span>'.
        'Data3</span></td><td class="col2"><span>'.
        'Data4</span></td></tr></table>', "$table");
    }

    public function testSetRowAlt()
    {
        $table = new HtmlTable(self::$data);
        // Default rowOffset is 2 => Rows 0 and 1 are omitted.
        // Rows of class alt: 2,4,...
        $this->assertEquals(['class' => 'alt'], $table[4]->attributes());
        $table->setRowAlt(3);
        // Rows of class alt: 2,5,...
        $this->assertEquals(['class' => 'alt'], $table[5]->attributes());
    }

    public function testSetRowOffset()
    {
        $table = new HtmlTable(self::$data);
        // Default rowOffset is 2 => Rows 0 and 1 are omitted.
        $table->setRowOffset(3);
        // Rows of class alt: 3,5,...
        $this->assertEquals([], $table[0]->attributes());
        $this->assertEquals([], $table[1]->attributes());
        $this->assertEquals([], $table[2]->attributes());
        $this->assertEquals(['class' => 'alt'], $table[3]->attributes());
        $this->assertEquals(['class' => 'alt'], $table[5]->attributes());
    }

    public function testSetNumberOfColumns()
    {
        $table = new HtmlTable(self::$data, 4);
        $this->assertEquals(4, $table->count());
        $this->assertEquals('Data13', $table[3][0][0]->content());
        // Change table layout
        $table->setNumberOfColumns(3);
        $this->assertEquals(5, $table->count());
        $this->assertEquals('Data13', $table[4][0][0]->content());
    }

    public function testAppendToLastRow()
    {
        $table = new HtmlTable(self::$data, 7);
        $table->appendToLastRow(['NData1', 'NData2']);
        $this->assertEquals('NData1', $table[2][1][0]->content());
    }

    public function testAppendRow()
    {
        $table = new HtmlTable(self::$data, 7);
        $table->appendRow(['NData1', 'NData2']);
        $this->assertEquals('NData1', $table[3][0][0]->content());
    }

    public function testDeleteFirstRow()
    {
        $table = new HtmlTable(self::$data, 3);
        $table->deleteFirstRow();
        $this->assertEquals('Data4', $table[0][0][0]->content());
    }

    public function testDeleteRow()
    {
        $table = new HtmlTable(self::$data, 3);
        $table->deleteRow(1);
        $this->assertEquals('Data7', $table[1][0][0]->content());
    }

    public function testDeleteLastRow()
    {
        $table = new HtmlTable(self::$data, 3);
        $this->assertEquals(5, $table->count());
        $table->deleteLastRow();
        $this->assertEquals(4, $table->count());
    }

    public function testDeleteColumn()
    {
        $table = new HtmlTable(self::$data, 5);
        $this->assertEquals('Data3', $table[0][2][0]->content());
        $table->deleteColumn(2);
        $this->assertEquals('Data4', $table[0][2][0]->content());
    }
}
