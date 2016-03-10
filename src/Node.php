<?php

namespace Simphotonics\Dom;

use Simphotonics\Utils\ArrayUtils;
use Simphotonics\Dom\NodeAccess;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Simphotonics\Node is a dom-node.
 */
class Node extends Leaf implements \ArrayAccess, NodeAccess, \RecursiveIterator
{
    use NodeMethods;

    /**
     * Child nodes.
     * @var Array
     */
    protected $childNodes = [];

    /**
     * Prints node hierarchy.
     * @return string
     */
    public function __toString()
    {
        return $this->tree();
    }
}
