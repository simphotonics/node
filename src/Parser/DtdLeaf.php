<?php

namespace Simphotonics\Dom\Parser;

use Simphotonics\Dom\Leaf;


/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Simphotonics\HtmlLeaf is an external node (leaf),
 * and can be used for XHTML elements like br, img, span,
 * etc. that do not have child nodes.
 *
 * Notation:
 * The element 'kind' denotes the element tag without the brackets.
 * E.g.: <br/> => 'br', <span> </span> => 'span',
 * The element 'format' refers to the formatting of the xhtml element.
 * E.g.: <br/>              => 'inline' (elements without content),
 *       <span> ... </span> => 'block' (element with content),
 *       <!-- ... -->       => 'comment',
 *       <DOCTYPE! ... >    => 'dtd'.
 */

class DtdLeaf extends Leaf
{

    private $name = '';

    /**
     * Constructs object
     * @param Array|array $input
     */
    public function __construct(string $name = '', string $kind = 'default',
    array $attributes = [], string $content = '',  )
    {
        parent::__construct($kind, $attributes, $content);
            $this->name = $name;

    }

    // /**
    //  * Return elements array.
    //  * @return array
    //  */
    // public static function getElements()
    // {
    //     return self::$elements;
    // }

    public function getName()
    {
        return $this->name;
    }
}
