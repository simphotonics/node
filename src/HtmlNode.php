<?php

namespace Simphotonics\Dom;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Simphotonics\HtmlNode can be used to
 * represent internal and external nodes.
 * Extends: @see \Simphotonics\Dom\HtmlLeaf
 *
 * Notation:
 * The element 'kind' denotes the element tag without the brackets.
 * E.g.: <span> </span> => 'span'.
 */

class HtmlNode extends HtmlLeaf
{
    use NodeMethods;

    // ===============
    // PRIVATE METHODS
    // ===============

    /**
     * Renders a block XML element with opening and closing tag.
     * @return string
     */
    protected function renderBlock()
    {
        return "<$this->kind" . $this->attr2str().
        '>' . $this->cont . $this->childObj2str() . "</$this->kind>";
    }

    /**
     * Returns a string representation of child nodes.
     * @param  string $glue The separator, defaults to " ".
     * @return string
     */
    private function childObj2str()
    {
        if (empty($this->childNodes)) {
            return '';
        }
        // Iterate over childList
        $string = '';
        foreach ($this->childNodes as $child) {
            $string.="$child";
        }
        return $string;
    }
}