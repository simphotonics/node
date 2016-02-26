<?php
namespace Simphotonics\Dom;

use Simphotonics\Dom\HtmlNode;
use Simphotonics\Utils\WebUtils;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Simphotonics\HtmlNavigator is an external node
 * and can be used to create a web site navigator with pure php.
 * During object creation descendant anchor nodes are scanned to
 * find a hyper-reference that points to the current URI.
 * If such node is found its parent node is added to the class 'here'.
 * The element (typically of kind 'li') can then be styled using CSS to
 * highlight the current position.
 * For a working example see: HtmlNavigatorTest.php
 * located in the folder dom\tests.
 */
class HtmlNavigator extends HtmlNode
{
    /**
     * Anchor pointing to the current uri.
     * @var  Simphotonics\Dom\HtmlLeaf|null
     */
    protected $selfAnchor = null;
    
    /**
     * Callback funtion used to determine the current uri.
     * @var  callback function
     */
    protected $getURI;
    
    /**
     * Constructs object.
     * @method __construct
     * @param  array       $input
     * @param  [type]      $framework
     */
    public function __construct(array $input = ['kind' => 'div'], callable $getURI = null)
    {
        parent::__construct($input);
        $this->getURI = (func_num_args() > 1) ? $getURI :
        'Simphotonics\Utils\WebUtils::getURI';
        $this->selfAnchor = $this->extendAttributes();
    }
    
    /**
     * Searches all descendant nodes for anchors pointing to the current uri. The first
     * such anchor to be found is returned. The parent node of the anchor is added to the
     * CSS class 'here' (to enable styling).
     * @method  extendAttributes
     * @return  Simphotonics\Dom\HtmlLeaf|null  Returns the anchor node pointing to the
     *                                          current uri. Null is returned if no such
     *                                          anchor is found.
     */
    protected function extendAttributes()
    {
        $selfAnchor = $this->getSelfAnchor();
        if ($selfAnchor) {
            // Set class attribute of parent node (usually a list item
            //     element).
            $selfAnchor->parent->setAttr(['class' => 'here'], 'add');
        }
        return $selfAnchor;
    }
    
    /**
     * Searches descendant nodes of $this for an anchor with 'href'
     * attribute pointing to the current uri.
     * @method  getSelfAnchor
     * @return  HtmlLeaf|null       Returns an anchor node or null.
     */
    protected function getSelfAnchor()
    {
        $anchors = $this->getNodesByKind('a');
        foreach ($anchors as $a) {
            if (isset($a->attr['href'])) {
                $self = call_user_func($this->getURI);
                $href = $a->attr['href'];
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
