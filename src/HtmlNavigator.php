<?php

declare(strict_types=1);

namespace Simphotonics\Node;

use Simphotonics\Node\HtmlNode;

/**
 * Description: Simphotonics\HtmlNavigator is an external node
 * and can be used to create a web site navigator with pure php.
 * During object creation descendant anchor nodes are scanned to
 * find a hyper-reference that points to the current URI.
 * If such node is found its parent node is added to the class 'here'.
 * The element (typically of kind 'li') can then be styled using CSS to
 * highlight the current position.
 * For a working example see: HtmlNavigatorTest.php
 * located in the folder node\tests.
 */
class HtmlNavigator extends HtmlNode
{
    /**
     * Anchor pointing to the current uri.
     * @var  Simphotonics\Node\HtmlLeaf|null
     */
    protected HtmlLeaf|HtmlNode|null $selfAnchor = null;

    /**
     * Constructs object.
     * @method __construct
     * @param  array       $input
     * @param  [type]      $framework
     */
    public function __construct(
        string $kind = 'div',
        array $attributes = [],
        string $content = '',
        array $childNodes = [],

    ) {
        parent::__construct(
            kind: $kind,
            attributes: $attributes,
            content: $content,
            childNodes: $childNodes,
        );
        $this->selfAnchor = $this->extendAttributes();
    }

    /**
     * Searches all descendant nodes for anchors pointing to the current uri.
     * The first such anchor to be found is returned.
     * The parent node of the anchor is
     * added to the CSS class 'here' (to enable styling).
     *
     * @method  extendAttributes
     *
     * @return  Simphotonics\Node\HtmlLeaf|null  Returns the anchor node
     *                                          pointing to the
     *                                          current uri.
     *                                          Null is returned if no such
     *                                          anchor is found.
     */
    protected function extendAttributes(): HtmlLeaf|HtmlNode|null
    {
        $selfAnchor = $this->getSelfAnchor();
        if ($selfAnchor) {
            // Set class attribute of parent node (usually a list item
            //     element).
            $selfAnchor->parent->setAttributes(['class' => 'here'], 'add');
        }
        return $selfAnchor;
    }

    /**
     * Searches descendant nodes of $this for an anchor with 'href'
     * attribute pointing to the current uri.
     * @method  getSelfAnchor
     * @return  HtmlLeaf|null       Returns an anchor node or null.
     */
    protected function getSelfAnchor(): HtmlLeaf|HtmlNode|null
    {
        $anchors = $this->getNodesByKind('a');
        foreach ($anchors as $a) {
            if (array_key_exists('href', $a->attributes)) {
                $self = $_SERVER['REQUEST_URI'];
                $href = $a->attributes['href'];
                // Compare filename
                //print "? $href == $self "."\n>";
                if ($href == $self) {
                    return $a;
                }
            }
        }
        return null;
    }
}
