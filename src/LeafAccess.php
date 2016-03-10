<?php

namespace Simphotonics\Dom;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Includes basic access methods required by
 *              classes working with external nodes of
 *              type Simphotonics\Dom\Leaf.
 */
interface LeafAccess
{
             
    public function getID();

    /**
     * Returns node kind.
     * @return string
     */
    public function getKind();
    
    /**
     * Returns attribute array.
     * @return array
     */
    public function getAttr();

    /**
     * Returns true if attribute array is
     * not empty.
     * @return boolean
     */
    public function hasAttr();

    /**
     * Checks if $this has content.
     * @return boolean
     */
    public function hasCont();

    /**
     * Returns content of node.
     * @return string
     */
    public function getCont();

    /**
     * Returns parent of current node.
     * @return Simphotonics\Node|NULL
     */
    public function getParent();

    /**
     * Always return false since leaves (external
     * nodes) have no child nodes by definition.
     * @method  hasChildNodes
     * @return  boolean
     */
    public function hasChildNodes();
}
