<?php

namespace Simphotonics\Dom;

use Simphotonics\Utils\ArrayUtils as ArrayUtils;
use UnexpectedValueException;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Simphotonics\Node is a dom-node.
 */
class Node extends Leaf implements \RecursiveIterator, \ArrayAccess
{
    use NodeMethods;

    /**
     * Child nodes.
     * @var Array
     */
    public $childNodes = [];

    /**
     * Constant used in @see $this->getDescendants()
     * to select only child nodes (direct descendants).
     */
    const CHILD_NODES = true;

    /**
     * Constant used in @see $this->getDescendants()
     * to select all descendant nodes.
     */
    const ALL_NODES = false;

    /**
     * Prints node hierarchy.
     * @return string
     */
    public function __toString()
    {
        return $this->tree();
    }
}
