<?php

declare(strict_types=1);

namespace Simphotonics\Node;

use Simphotonics\Node\NodeAccess;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Simphotonics\HtmlNode can be used to
 * represent internal and external nodes.
 * Extends: @see \Simphotonics\Node\HtmlLeaf
 *
 * Notation:
 * The element 'kind' denotes the element tag without the brackets.
 * E.g.: <span> </span> => 'span'.
 */

class HtmlNode extends HtmlLeaf implements
    \ArrayAccess,
    NodeAccess,
    \RecursiveIterator
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
     * Renders a nested XML element with opening and closing tag.
     * @return string
     */
    public function __toString()
    {
        return "<$this->kind" . $this->attr2str() .
            '>' . $this->content . $this->childObj2str() . "</$this->kind>";
    }

    // ===============
    // PRIVATE METHODS
    // ===============
    /**
     * Returns a string representation of child nodes.
     * @param  string $glue The separator, defaults to " ".
     * @return string
     */
    private function childObj2str()
    {
        if ($this->hasChildNodes()) {
            // Iterate over childList
            $out = '';
            foreach ($this->childNodes as $child) {
                $out .= "$child";
            }
            return $out;
        } else {
            return '';
        }
    }
}
