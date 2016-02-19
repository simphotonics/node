<?php

namespace Simphotonics\Dom;

use Simphotonics\Utils\FileUtils;
use InvalidArgumentException;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Simphotonics\HtmlLeaf is an external node (leaf),
 *              and can be used for XHTML elements like br, img, span,
 *              etc. that do not have child nodes.
 *
 *              Notation:
 *              The element 'kind' denotes the element tag without the brackets.
 *              E.g.: <br/> => 'br', <span> </span> => 'span',
 *              The element 'type' refers to the formatting of the xhtml element.
 *              E.g.: <br/>                    => 'empty' (elements without content),
 *                    <span> ... </span>       => 'block' (element with content),
 *                    <!-- ... -->             => 'comment',
 *                    <DOCTYPE! ... >          => 'dtd'.
 */

class HtmlLeaf extends Leaf
{
    /**
     * Available renderMethods
     * @var array of the form ['element type' => 'renderMethod'].
     */
    private static $renderMethods = [
        'block'  => 'renderBlock',
        'empty'  => 'renderEmpty',
        'dtd'    => 'renderDTD',
        'comment'=> 'renderComment',
    ];

    /**
     * (Pre) Defined html elements
     * @var array of the form [element kind => renderMethod]
     */
    private static $elements = [
        '!--' => 'renderDTD',
        '!DOCTYPE' => 'renderDTD'
    ];
    /**
     * Data type declaration
     * @var string
     */
    private static $dtd      = 'html5';

    /**
     * Path to filename containing loaded element specs.
     * @var string
     */
    private static $filenameDTD = 'elements.php';

    /**
     * Constructs object
     * @param Array|array $input
     */
    public function __construct(array $input = [])
    {
        parent::__construct($input);
    }

    /**
     * Initialise xml element types.
     * @return void
     */
    public static function readElements($filename = 'elements.php')
    {
        FileUtils::assertFileReadable($filename);
        require($filename);
        if (isset($dtd)) {
            self::$dtd = $dtd;
            self::$filenameDTD = $filename;
        }
        if (isset($elements)) {
            self::registerElements($elements);
        }
    }

    /**
     * Registers new element kind. "renderFunc" has to be an existing class method.
     * @param  array  $spec Array of the form: ['element kind' => 'element type', ...]
     * @return void
     */
    public static function registerElements($elements = ['br'=>'empty'])
    {
        foreach ($elements as $kind => $type) {
            if (isset(self::$renderMethods[$type])) {
                self::$elements[$kind] = self::$renderMethods[$type];
            } else {
                $list    = implode(',', array_keys(self::$renderMethods));
                $message = "Cannot register element kind: '$kind'. 
                There is no render method for elements of type $type! 
                Available render methods are: $list.";
                throw new InvalidArgumentException($message);
            }
        }
    }

    /**
     * Set datatype description string.
     * @param [type] $dtd [description]
     */
    public static function setDTD($dtd = 'html5')
    {
        $this->dtd = $dtd;
    }
    
    /**
     * Returns datatype description string.
     * @return [type] [description]
     */
    public static function getDTD()
    {
        return $this->dtd;
    }

    /**
     * Return elements array.
     * @return array
     */
    public function getElements()
    {
        return self::$elements;
    }

    /**
     * Converts leaf to string output.
     * @return string
     */
    public function __toString()
    {
        // Check element format.
        if (isset(self::$elements[$this->kind])) {
            //Calls methods'renderDTD','renderEmpty','renderComment'
            $varfunc = self::$elements[$this->kind];
            return $this->$varfunc();
        }
        //Calls default render method: renderBlock().
        return $this->renderBlock();
    }
    
    /**
     * Renders an xml block element <tag ... /tag> .
     * @return string
     */
    protected function renderBlock()
    {
        return "<$this->kind" . $this->attr2str()
        . '>' . $this->cont . "</$this->kind>";
    }
    
    /**
     * Renders an empty xml element of the form <tag ... /> .
     * @return string
     */
    protected function renderEmpty()
    {
        return "<$this->kind" . $this->attr2str()
        . ' ' . $this->cont . '/>';
    }
    
    /**
     * Renders a doctype declaration <!DOCTYPE ... > .
     * @return string
     */
    protected function renderDTD()
    {
        if ($this->hasCont()) {
            return '<!DOCTYPE' . $this->attr2str()
            . ' ' . $this->cont . ' >';
        } else {
            return '<!DOCTYPE' . $this->attr2str()
            . ' ' . $this->dtd . ' >';
        }
    }
    
    /**
     * Renders a XML comment element <!-- ... --> .
     * @return string
     */
    protected function renderComment()
    {
        return '<!--' . $this->attr2str()
        . ' ' . $this->cont . ' -->';
    }
}
