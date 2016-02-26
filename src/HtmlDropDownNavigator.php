<?php
namespace Simphotonics\Dom;

use Simphotonics\Dom\HtmlNavigator;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Simphotonics\HtmlDropDownNavigator extends
 * HtmlNavigator adding support for php powered drop down
 * menus.
 * The order of navigator entries is changed such that the
 * entry containing an anchor with the current uri
 * is at the top.
 * Usage: See HtmlDropDownNavigatorTest.php located in
 * folder simphotonics/dom/tests.
 */
class HtmlDropDownNavigator extends HtmlNavigator
{
    /**
     * Constructs object
     * @method  __construct
     * @param   array        $input  Array containg node specs.
     */
    public function __construct($input = ['kind' => 'div'])
    {
        parent::__construct($input);
        $this->sortButtons();
    }
    
    private function sortButtons()
    {
        // Get drop down menu
        $dropDownMenu = $this->selfAnchor->getAncestor(2);
        if (!$dropDownMenu instanceof HtmlNode) {
            return;
        }
        if (!$dropDownMenu->hasAttrValue('class', 'dropDownMenu')) {
            return;
        }
      
        // Reorder list items
        $liFirst = $dropDownMenu[0];
        $liHere  = $this->selfAnchor->parent;
        if ($liHere !== $liFirst) {
            $dropDownMenu->removeChild($liHere);
            $dropDownMenu->insertBefore($liHere, $liFirst);
        }
    }
}
