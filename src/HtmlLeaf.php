<?php

declare(strict_types=1);

namespace Simphotonics\Dom;

use Simphotonics\Utils\FileUtils;
use InvalidArgumentException;

/**
  * Description: Simphotonics\HtmlLeaf is an external node (leaf),
 * and can be used for XHTML elements like br, img, span,
 * etc. that do not have child nodes.
 *
 * Notation:
 * The element 'kind' denotes the element tag without the brackets.
 * E.g.: <br/> => 'br', <span> </span> => 'span',
 * The element 'format' refers to the formatting of the xhtml element.
 * E.g.: <br/>              => 'inline' (elements without content),
 *       <span> ... </span> => 'block' (element with content),
 *       <!-- ... -->       => 'comment',
 *       <DOCTYPE! ... >    => 'dtd'.
 */

class HtmlLeaf extends Leaf
{
    /**
     * Available renderMethods
     * @var array of the form ['element format' => 'renderMethod'].
     */
    private static $renderMethods = [
        'block'  => 0,
        'inline'  => 1,
        'dtd'    => 2,
        'comment' => 3,
    ];

    /**
     * (Pre) Defined html elements
     * @var array of the form [element kind => renderMethod]
     */
    private static $elements = [
        '!--' => 'comment',
        '!DOCTYPE' => 'dtd',
        'base' => 'inline',
        'meta' => 'inline',
        'link' => 'inline',
        'hr' => 'inline',
        'br' => 'inline',
        'param' => 'inline',
        'img' => 'inline',
        'area' => 'inline',
        'input' => 'inline',
        'col' => 'inline'
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
    public function __construct(
        string $kind = 'div',
        array $attributes = [],
        mixed $content = '',
    ) {
        parent::__construct(
            kind: $kind,
            attributes: $attributes,
            content: $content,
        );
    }

    /**
     * Initialise xml element types.
     * @return void
     */
    public static function readElements($filename = 'elements.php'): void
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
     * Registers new element kind. Supporte formats are: inline, dtd, comment.
     * Block elements do not need to be registered. This is the default format.
     *
     * @param  array  $spec Array of the form: ['element kind' => 'format', ...]
     * @return void
     */
    public static function registerElements(
        array $elements = ['br' => 'inline']
    ): void {
        foreach ($elements as $kind => $type) {
            if (array_key_exists($type, self::$renderMethods)) {
                self::$elements[$kind] = $type;
            } else {
                $list    = implode(',', array_keys(self::$renderMethods));
                $message = "Cannot register element kind: '$kind'.
                There is no render method for elements of format type $type!
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
        self::$dtd = $dtd;
    }

    /**
     * Returns datatype description string.
     * @return [type] [description]
     */
    public static function getDTD()
    {
        return self::$dtd;
    }

    /**
     * Return elements array.
     * @return array
     */
    public static function getElements()
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

        if (array_key_exists($this->kind, self::$elements)) {
            //Calls methods: 'dtd()',
            //               'inline()',
            //               'comment()'.
            $varfunc = self::$elements[$this->kind];
            return $this->$varfunc();
        } else {
            //Calls default render method: block().
            return $this->block();
        }
    }

    /**
     * Renders nested xml elements:  <tag ... /tag> .
     * @return string
     */
    protected function block()
    {
        return "<$this->kind" . $this->attr2str()
            . '>' . $this->content . "</$this->kind>";
    }

    /**
     * Renders an inline xml element of the form <tag ... /> .
     * @return string
     */
    protected function inline()
    {
        return "<$this->kind" . $this->attr2str()
            . '/>';
    }

    /**
     * Renders a doctype declaration <!DOCTYPE ... > .
     * @return string
     */
    protected function dtd()
    {
        if ($this->hasContent()) {
            return '<!DOCTYPE' . $this->attr2str()
                . ' ' . $this->content . ' >';
        } else {
            return '<!DOCTYPE' . $this->attr2str()
                . ' ' . self::$dtd . ' >';
        }
    }

    /**
     * Renders a XML comment element <!-- ... --> .
     * @return string
     */
    protected function comment()
    {
        return '<!--' . $this->attr2str()
            . ' ' . $this->content . ' -->';
    }
}
