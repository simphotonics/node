<?php
namespace Simphotonics\Dom;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlNode;

use InvalidArgumentException;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Simphotonics\HtmlTable extends class HtmlNode adding
 * support for HTML tables.
 *
 */
class HtmlTable extends HtmlNode
{
    /**
     * Number of table columns.
     * @var  integer
     */
    private $nCols = 2;

    /**
     * Flag enabling table headers in first row.
     */
    const SET_TABLE_HEADERS = 1;

    /**
     * Flag disabling table headers in first row.
     * Instead table data 'td' elements will be used
     * in the first row.
     */
    const NO_TABLE_HEADERS = 0;

    /**
     * Add class attribute 'alt' to every $rowAlt row.
     * Useful for styling alternate rows.
     * @var  integer
     */
    private $rowAlt = 2;

    /**
     * Omit class attribute 'alt' for the first 'rowOffset'
     * rows.
     * @var  integer
     */
    private $rowOffset = 2;

    /**
     * Array containing original use input data.
     * Stored as class property to enable reformatting
     * of the table layout.
     * @var  array
     */
    private $inputData = [];
    
    /**
     * Constructs table object.
     * @method  __construct
     * @param   array        $inputData  User input data.
     * @param   integer      $nCols      Number of columns.
     * @param   integer      $headers    Flag enabling/disabling
     *                                   table headers.
     * @param   integer      $rowAlt     Add class alt to every
     *                                   $altRow row.
     * @param   integer      $rowOffset  Omit class alt for the first
     *                                   $rowOffset rows.
     */
    public function __construct(
        $inputData = [],
        $nCols = 2,
        $headersOnOff = self::SET_TABLE_HEADERS,
        $rowAlt = 2,
        $rowOffset = 2
    ) {
        parent::__construct(['kind' => 'table']);
        $this->nCols = $this->checkNcolsRange($nCols);
        $this->headersOnOff = $headersOnOff;
        $this->rowAlt = $this->checkRowAltRange($rowAlt);
        $this->rowOffset = $this->checkRowOffsetRange($rowOffset);
        // Store table data.
        $this->inputData = $inputData;
        // Append row nodes to table.
        $this->append($this->makeTableRows());
    }
  
    /**
     * Sets styling parameter $rowAlt.
     * @method  setRowAlt
     * @param   integer     $rowAlt  Enables styling of alternate rows.
     */
    public function setRowAlt($rowAlt)
    {
        $this->rowAlt = $this->checkRowAltRange($rowAlt);
        $this->childNodes = [];
        $this->append($this->makeTableRows());
    }
  
    /**
     * Sets parameter rowOffset used to omit the styling
     * of the first $rowOffset rows.
     * @method  setRowOffset
     * @param   integer        $rowOffset  Styling parameter.
     */
    public function setRowOffset($rowOffset)
    {
        $this->rowOffset = $this->checkRowOffsetRange($rowOffset);
        $this->childNodes = [];
        $this->append($this->makeTableRows());
    }
  
    /**
     * Returns the number of columns.
     * @method  getNumberOfColumns
     * @return  integer              No. of columns.
     */
    public function getNumberOfColumns()
    {
        return $this->nCols;
    }
  
    /**
     * Changes the table layout to $nCols columns.
     * @method  setNumberOfColumns
     * @param   void
     */
    public function setNumberOfColumns($nCols)
    {
        $this->nCols = $this->checkNcolsRange($nCols);
        $this->childNodes = [];
        $this->append($this->makeTableRows());
    }
  
    /**
     * Appends entries in $inputData to last row.
     * @method  appendToLastRow
     * @param   array      $newInputData  Array containing table data.
     * @return  void
     */
    public function appendToLastRow(array $newInputData)
    {
        $this->inputData = array_merge($this->inputData, $newInputData);
        $this->childNodes = [];
        $this->append($this->makeTableRows());
    }
  
    /**
     * Appends entries in $newInputData to new table row.
     * @method  appendRow
     * @param   array      $newInputData  Array containg table data.
     * @return  void
     */
    public function appendRow(array $newInputData)
    {
        // Add dummy entries to fill last row of
        // initial table data
        $this->addDummyEntries($this->inputData);
        $this->inputData = array_merge($this->inputData, $newInputData);
        $this->childNodes = [];
        $this->append($this->makeTableRows());
    }
  
    /**
     * Deletes first table row.
     * @method  deleteFirstRow
     * @return  void
     */
    public function deleteFirstRow()
    {
        array_shift($this->childNodes);
    }
  
    /**
     * Deletes row number $rowNumber.
     * Note: The first row is indexed with zero.
     * @method  deleteRow
     * @param   integer    $rowNumber  Valid row number.
     * @return  void
     */
    public function deleteRow($rowNumber = 0)
    {
        if ($rowNumber < 0) {
            return;
        }
        if (isset($this->childNodes[$rowNumber])) {
            unset($this->childNodes[$rowNumber]);
        }
        // Re-index array.
        $this->childNodes = array_values($this->childNodes);
    }

    /**
     * Deletes last table row.
     * @method  deleteLastRow
     * @return  void
     */
    public function deleteLastRow()
    {
        array_pop($this->childNodes);
    }
  
    /**
     * Deletes column $colNo and reformats table with
     * remaining table data entries.
     *
     * @method  deleteColumn
     * @param   integer       $colNo  Column to be deleted.
     * @return  void
     */
    public function deleteColumn($colNo = 0)
    {
        if ($colNo < 0) {
            return;
        }
        if ($colNo > $this->nCols) {
            return;
        }
        $delEntries = range($colNo, count($this->inputData), $this->nCols);
        foreach ($delEntries as $key) {
            unset($this->inputData[$key]);
        }
        // Reindex array!
        $this->inputData = array_values($this->inputData);
        // Build reduced table
        --$this->nCols;
        $this->childNodes = [];
        $this->append($this->makeTableRows());
    }
  
    /**
     * Wraps input nodes within table data 'td' or table header 'th'
     * elements. Input that is not an instance of HtmlLeaf set as
     * content of a span element.
     * If necessary, the function pads the last row with empty
     * span elements.
     *
     * @method  makeTableData
     * @param   array          $inputData  Input data.
     * @return  array          Array containing th and td nodes.
     */
    private function makeTableData(array $inputData)
    {
        // Empty template table elements.
        $td   = new HtmlNode(['kind' => 'td']);
        $th   = new HtmlNode(['kind' => 'th']);
        $span = new HtmlLeaf(['kind' => 'span']);
        // Add dummy entries to fill last row
        $this->addDummyEntries($inputData);
        // Wrap top level nodes with table data or table header elements.
        $colCount = 1;
        $nodeCount = 1;
        $tableData = [];
        foreach ($inputData as $node) {
            $td_tmp = ($nodeCount <= $this->nCols & $this->headersOnOff
                       ) ? clone $th : clone $td;
            ++$nodeCount;
            // Check if input is of type htmlLeaf
            if ($node instanceof HtmlLeaf) {
                $td_tmp->appendChild($node);
            } else {
                $td_tmp->appendChild($span)->setCont("$node");
            }
            // Reset $colCount at end of table row.
            $colCount = ($colCount == $this->nCols + 1) ? 1 : $colCount;
            // Style td and th
            $td_tmp->setAttr(['class' => 'col' . $colCount]);
            $tableData[] = $td_tmp;
            ++$colCount;
        }
        return $tableData;
    }
    
    /**
     * Returns an array containing table row 'tr' elements.
     * Note: The number of rows is determined by the number of nodes in
     *       $this->tableData and the number of columns.
     * @method  makeRows
     * @param   array         $rowAttr  (Optional) attributes of row nodes.
     * @return  array                   Array containg table row nodes.
     */
    private function makeTableRows($rowAttr = [])
    {
        // Reshape array $this->tableData such that each array entry contains
        // $this->nCols nodes. => Each array entry corresponds to one
        // row of table elements.
        $tableData = array_chunk(
            $this->makeTableData($this->inputData),
            $this->nCols
        );
        // Set up table content
        $rowCount = 0;
        $altRowCount = 0;
        $tr = new HtmlNode(['kind' => 'tr']);
        foreach ($tableData as $nodesPerRow) {
            $tr_tmp = clone $tr;
            // Set (imported) row attributes
            if (isset($rowAttr[$rowCount])) {
                $tr_tmp->setAttr($rowAttr[$rowCount]);
            }
            // Label alternative rows
            if (($rowCount >= $this->rowOffset )) {
                if (($altRowCount % $this->rowAlt) === 0) {
                    $tr_tmp->setAttr(['class' => 'alt'], 'add');
                }
                ++$altRowCount;
            }
            ++$rowCount;
            $tr_tmp->append($nodesPerRow);
            $tableRows[] = $tr_tmp;
        }
        return $tableRows;
    }

    /**
     * Returns integer $rowAlt within acceptable range.
     * @method  checkRowAltRange
     * @param   integer            $rowAlt
     * @return  integer            Used to style alternate rows.
     */
    private function checkRowAltRange($rowAlt)
    {
        return ($rowAlt > 0) ? $rowAlt : $this->rowAlt;
    }

    /**
     * Return integer $rowOffset within acceptable range.
     * @method  checkRowOffsetRange
     * @param   integer               $rowOffset
     * @return  integer               Omit styling of first rowOffset rows.
     */
    private function checkRowOffsetRange($rowOffset)
    {
        return ($rowOffset > 0) ? $rowOffset : $this->rowOffset;
    }

    /**
     * Ensures that number of columns is of type integer and
     * non-negative.
     * @method  checkNcolsRange
     * @param   integer           $nCols  Number of columns.
     * @return  integer
     */
    private function checkNcolsRange($nCols)
    {
        if (!is_int($nCols)) {
            throw new InvalidArgumentException('Integer input expected! Found: ' . "<$nCols>");
        }
        if ($nCols < 1) {
            throw new InvalidArgumentException('Integer >= 1 expected! Found: ' . $nCols);
        }
        return $nCols;
    }

    /**
     * Adds entries to fill last table row (with empty span elements).
     * @method  addDummyEntries
     * @param   void          &$inputData  Table input data.
     */
    private function addDummyEntries(&$inputData)
    {
        // Calculate no. of remaining nodes in last row.
        $nR = count($inputData) % $this->nCols;
        if ($nR === 0) {
            return;
        }
        // Add dummy entries to fill the last table row.
        $noDummyEntries = $this->nCols - $nR;
        for ($i=0; $i < $noDummyEntries; ++$i) {
            $inputData[] = '';
        }
    }
}
