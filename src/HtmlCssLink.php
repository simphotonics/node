<?php
namespace Simphotonics\Dom;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Utils\WebUtils;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Extends class HtmlLeaf by adding dynamic css links.
 * The css file name is generated using the calling script name
 * and the default css folder name.
 */
class HtmlCssLink extends HtmlLeaf
{

    /**
     * Constructs object.
     * @method __construct
     * @param  string   $cssFolder   Folder containing css style files.
     * @param  string   $cssFile     Optional css filename.
     * Note: For Laravel applications $cssFile could be set to:
     *       Route::currentRouteName().'css' .
     */
    
    public $path = '';
    public $cssFolder = '/style';
    public $cssFilename = '';

    public function __construct($cssFile = 'Index')
    {
        $this->cssFilename = (func_num_args() > 0 ) ?
                  func_get_arg(0) : WebUtils::baseURI();
        if (trim($this->cssFilename) == false) {
            $this->cssFilename = 'Index';
        }
        parent::__construct([
            'kind' => 'link',
            'attr' => [
                'rel' => 'stylesheet',
                'type' => 'text/css',
                'href' => &$this->path,
                'media' => 'all'
                ]
        ]);
    }

    /**
     * Sets the Css folder.
     * @method  setCssFolder
     * @param   string        $cssFolder
     */
    public function setCssFolder($cssFolder = '')
    {
        $this->cssFolder = $cssFolder;
    }

    /**
     * Returns string containing Css link element.
     * Note: The path to the Css files is generated in this function!
     * @method  __toString
     * @return  string
     */
    public function __toString()
    {
        $this->path = $this->cssFolder. '/'. $this->cssFilename.'.css';
        return parent::__toString();
    }
}
