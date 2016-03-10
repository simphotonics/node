<?php

namespace Simphotonics\Dom;

use Simphotonics\Dom\LeafAccess;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Includes basic access methods required by
 *              classes working with Simphotonics\Dom\Node.
 */
interface NodeAccess extends LeafAccess
{
    /**
     * Constant used to select only
     * child nodes (direct descendants).
     */
    const CHILD_NODES = 1;

    /**
     * Constant used to select all
     * descendant nodes (accessed recursively).
     */
    const ALL_NODES = 0;

    /**
     * Constant used in @see $this->getDescendants()
     * to return only external nodes.
     */
    const LEAVES_ONLY = 0;

    /**
     * Constant used in @see $this->getDescendants()
     * to return all node, $this first.
     */
    const SELF_FIRST = 1;

    /**
     * Constant used in @see $this->getDescendants()
     * to return all node, external nodes (leaves) first.
     */
    const CHILD_FIRST = 2;
    
    /**
     * Returns an array containing the child nodes.
     * @return array
     */
    public function getChildNodes();
    
    /**
     * Returns an array containing all descendant nodes/leaves.
     * @param  int $mode @see \RecursiveIteratorIterator takes
     * values: LEAVES_ONLY, SELF_FIRST, CHILD_FIRST
     * @return array
     */
    public function getDescendants($mode = self::SELF_FIRST);
}
