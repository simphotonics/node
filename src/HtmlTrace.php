<?php

namespace Simphotonics\Dom;

use Simphotonics\Dom\HtmlNode;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Simphotonics\HtmlTrace can be used to
 * create a list of links interspersed with '>' symbols.
 * Extends: @see \Simphotonics\Dom\HtmlNode
 */
class HtmlTrace extends HtmlNode
{
    /**
     * Constructs object.
     * @method  __construct
     * @param   array        $input     Objects specs.
     * @param   array        $hrefList  Array of the form:
     *                                  ['title' => 'href'].
     */
    public function __construct(
        array $anchors = ['Home' => 'http://simphotonics.com/']
    ) {
        parent::__construct(['kind' => 'div']);
        $this->setAnchors($anchors);
    }
  
    /**
     * Constructs child nodes.
     * @method  setAnchors
     * @param   array         $anchors  Array containing hrefs.
     */
    private function setAnchors(array $anchors)
    {
        $gt = new HtmlLeaf([
        'kind' => 'span',
        'attr' => ['class' => 'gt'],
        'cont' => ' &gt; '
        ]);
        $a = new HtmlLeaf([
        'kind' => 'a'
        ]);
    
        foreach ($anchors as $title => $href) {
            $this->appendChild($a)->setAttr(['href' => $href])->setCont($title);
            $this->appendChild($gt);
        }
        // Remove last '>' symbol.
        array_pop($this->childNodes);
    }
}
