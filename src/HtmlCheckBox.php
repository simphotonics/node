<?php

declare(strict_types=1);

namespace Simphotonics\Node;

use Simphotonics\Node\HtmlLeaf;
use Simphotonics\Node\HtmlNode;

/**
 * author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Simphotonics\HtmlCheckBox
 *
 */

class HtmlCheckBox extends HtmlLeaf
{
    /**
     * Constant used to enable the insertion of
     * a break element after each checkbox element.
     */
    const LINE_BREAK_ON     = 1;  // Input Flag

    /**
     * Constant used to disable the insertion of
     * a break element after each checkbox element.
     */
    const LINE_BREAK_OFF    = 0;  // Input Flag

    /**
     * Constructs object.
     * @method __construct
     * @param  array       $input Array of the form:
     *                     [checkBoxName => checkBoxValue]
     */
    public function __construct($name = 'name', $value = 'value')
    {
        parent::__construct(
            kind: 'input',
            attributes: [
                'name' => $name,
                'value' => $value,
                'type' => 'checkbox'
            ]
        );
    }

    /**
     * Returns an array of HtmlCheckBox objects.
     * Note: Each HtmlCheckBox is appended to a span element of
     * type HtmlNode. The user has the option to include a linebreak
     * element after each span element.
     * @method getCheckBoxes
     *
     * @param  array      $input: ['checkBoxName1'
     *                            => 'checkBoxValue1, ...]
     * @param  int        $lineBreak Flag enabling linebreak elements.
     *
     * @return array      Array of HtmlLeaf/HtmlNode objects.
     */
    public static function generateCheckBoxes(
        array $input,
        $lineBreak = self::LINE_BREAK_ON
    ) {
        $out = [];
        $span = new HtmlNode(kind: 'span');
        $br = new HtmlLeaf(kind: 'br');

        foreach ($input as $name => $value) {
            $box = new \Simphotonics\Node\HtmlCheckBox($name, $value);
            $out[] = clone $span;
            end($out)->appendChild($box);
            if ($lineBreak) {
                $out[] = $br;
            }
        }
        if ($lineBreak) {
            array_pop($out);
        }
        return $out;
    }
}
