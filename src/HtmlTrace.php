<?php

namespace Simphotonics\Node;

use Simphotonics\Node\HtmlNode;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Simphotonics\HtmlTrace can be used to
 * create a div element containing a list of links
 * interspersed with '>' symbols.
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
        parent::__construct(kind: 'div');
        $this->setAnchors($anchors);
    }

    /**
     * Constructs child nodes.
     * @method  setAnchors
     * @param   array         $anchors  Array containing hrefs.
     */
    private function setAnchors(array $anchors)
    {
        $gt = new HtmlLeaf(
            kind: 'span',
            attributes: ['class' => 'gt'],
            content: ' &gt; '
        );
        $a = new HtmlLeaf(kind: 'a');

        foreach ($anchors as $title => $href) {
            $this->appendChild($a)->setAttributes(['href' => $href])
                ->setContent($title);
            $this->appendChild($gt);
        }
        // Remove last '>' symbol.
        array_pop($this->childNodes);
    }
}
