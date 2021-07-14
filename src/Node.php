<?php

namespace Simphotonics\Node;

use Simphotonics\Utils\ArrayUtils;
use Simphotonics\Node\NodeAccess;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Simphotonics\Node is a represents a node with
 * a kind tag, an attributes array, and child nodes.
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
     * Creates a deep copy of $this.
     * @return void
     */
    public function __clone()
    {
        // Reset parent node
        $this->parent = null;
        // Set id
        $this->id = $this->kind . ++self::$count;
        // Clone the child nodes and set the parent node.
        foreach ($this->childNodes as $key => $node) {
            $newNode = clone $node;
            $newNode->parent = $this;
            $this->childNodes[$key] = $newNode;
        }
    }

    /**
     * Prints node hierarchy.
     * @return string
     */
    public function __toString()
    {
        return $this->tree();
    }
}
