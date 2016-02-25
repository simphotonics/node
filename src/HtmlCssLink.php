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
    public function __construct($cssFolder = '/style', $cssFile = 'Index.css')
    {

        $cssFilename = (func_num_args() > 1 ) ?
                  func_get_arg(1) : WebUtils::baseURI();
        $cssFilename =  (empty($cssFilename)) ? 'Index' : $cssFilename;
        $path = $cssFolder. '/'. $cssFilename.'.css';
    
         parent::__construct([
        'kind' => 'link',
        'attr' => ['rel' => 'stylesheet',
        'type' => 'text/css',
        'href' => $path,
        'media' => 'all']
         ]);
    }
}
