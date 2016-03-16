<?php

namespace Simphotonics\Dom\Parser;

use Simphotonics\Dom\Leaf;
use Simphotonics\Utils\FileUtils;
use InvalidArgumentException;

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
 * E.g.: <br/>              => 'empty' (elements without content),
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
    public function __construct(array $input = [])
    {
        parent::__construct($input);
        if (isset($input['name'])) {
            $this->name = $input['name'];
        }
    }

    /**
     * Return elements array.
     * @return array
     */
    public static function getElements()
    {
        return self::$elements;
    }

    public function getName()
    {
        return $this->name;
    }
}
