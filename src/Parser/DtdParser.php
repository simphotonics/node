<?php

declare(strict_types=1);

namespace Simphotonics\Node\Parser;

use Simphotonics\Node\Parser\DtdLeaf;
use Simphotonics\Utils\FileUtils;
use Simphotonics\Node\NodeAccess;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Parses text containing data type definitions
 * attempting to extract entities, attribute lists, and
 * elements.
 */
class DtdParser
{

    /**
     * Entities extracted from parsed DTD document.
     * @var  array
     */
    private array $entities  = [];

    /**
     * Array containing attribute lists.
     * Format: $attrLists['name'] => 'value';
     *
     * @var  array
     */
    private array $attrLists = [];

    /**
     * Array containing extracted elements:
     * Format: $elements['name'] => 'value';
     * @var  array
     */
    private array $elements  = [];

    /**
     * DTD source code
     * @var  string
     */
    private $source = '';

    /**
     * Constructs object. Parses DTD source code generating
     * nodes of kind !ENTITY, !--, !ATTLIST, and !ELEMENT.
     * @method  __construct
     * @param   string       $source  DTD source code.
     */
    public function __construct($source = '')
    {
        if (func_num_args() > 0) {
            $this->source = $source;
            $this->parse();
            $this->resolveEntities();
        }
    }

    /**
     * Returns an array containing DTD entities.
     * @method  getEntities
     * @return  array       Entities.
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * Returns an array containing DTD elements.
     * @method  getElements
     * @return  array        Elements.
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Returns an array containing DTD attribute lists.
     * @method  getAttrLists
     * @return  array        Attribute lists.
     */
    public function getAttrLists(): array
    {
        return $this->attrLists;
    }

    /**
     * Loads a DTD source file and parses string content.
     * @method  loadDtd
     * @param   string   $filename  Path to the DTD file.
     * @return  void
     */
    public function loadDtd($filename = 'xhtml.dtd'): void
    {
        $this->source = FileUtils::loadFile($filename);
        $this->parse();
        $this->resolveEntities();
    }

    /**
     * Writes PHP source code that generates element nodes
     * to file.
     * @method  exportNodes
     * @param   string       $filename  Path to file on file system.
     *
     * @return  int               Number of bits writen. Throws on failure.
     */
    public function exportNodes($filename = 'dtdNodes.php'): int
    {
        // Render top nodes
        $source = '';
        foreach ($this->topNodes as $node) {
            if ($node instanceof NodeAccess) {
                $source .= NodeRenderer::renderRecursive($node);
            } else {
                $source .= NodeRenderer::render($node);
            }
        }
        return FileUtils::write2file($source, $filename, FileUtils::FILE_NEW);
    }

    /**
     * Writes PHP source code that generates an array containig empty
     * elements.
     * @method  exportEmptyElements
     * @param   string               $filename  Valid path to file.
     * @return  int|false                       No. of bits written or false.
     */
    public function exportEmptyElements(string $filename = 'emptyElements.php')
    {
        $elements = $this->getEmptyElements();
        $source = NodeRenderer::renderArray($elements, 'elements');
        return FileUtils::write2file($source, $filename, FileUtils::FILE_NEW);
    }

    /**
     * Returns an array containing empty DTD elements.
     * @method  getElements
     * @return  array        Elements.
     */
    public function getEmptyElements()
    {
        $out = [];
        foreach ($this->elements as $name => $value) {
            if ($value == 'EMPTY') {
                $out[$name] = 'empty';
            }
        }
        return $out;
    }

    /**
     * Returns an array containing the extracted element
     * nodes.
     * @method  getElementNodes
     * @return  array           Array containing nodes of type
     *                                Simphotonics\Node\Parser\DtdLeaf.
     */
    public function getElementNodes()
    {
        foreach ($this->elements as $name => $value) {
            $nodes[$name] = new DtdLeaf(
                name: $name,
                kind: '!ELEMENT',
                content: $value,
                attributes: isset($this->attrLists[$name]) ?
                    $this->parseAttrList($this->attrLists[$name]) : []
            );
        }
        return $nodes;
    }

    /**
     * Processes DTD source extracting entities,
     * attributes, and elements.
     * @method  parse
     * @return  void
     */
    private function parse()
    {
        $matches = self::parseInput($this->source);
        foreach ($matches as $match) {
            // Get format
            switch ($match['kind']) {
                    // ENTITY node
                case '!ENTITY':
                    // Trim quotation marks and white space.
                    $this->entities['%' . $match['name'] . ';'] =
                        trim($match['value'], " \t\n\r\0\x0B\"");
                    break;
                    // ATTLIST node
                case '!ATTLIST':
                    $this->attrLists[$match['name']] = $match['value'];
                    break;
                    // ELEMENT node
                case '!ELEMENT':
                    $this->elements[$match['name']] = $match['value'];
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Parses string containig element attributes and returns
     * an array of the form:
     * ['attr-name' => ['attr-type'=>'type','def-value' => 'value'],
     *     ...].
     * @method  parseAttrList
     * @param   string   $attrString  Input string
     * @return  array                 Element attributes.
     */
    private static function parseAttrList($attrString = '')
    {
        $pattern = '@[\s]*(?<name>[\S]*)[\s]*'
            . '(?<dataType>[\S]*)[\s]*(?<defaultValue>[\S]*)@';
        $pattern = '@
            [\s]*
            (?<name>[\S]*)
            [\s]*
            (?<dataType>[\S]*)
            [\s]*
            (?<defaultValue>
            (?:\#FIXED\s[\S]*|[\S]*)
            )
            [\s]*@x';
        preg_match_all($pattern, $attrString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if ($match['name'] == '') {
                continue;
            }
            $attr[$match['name']] = [$match['dataType'], $match['defaultValue']];
        }
        return $attr;
    }

    /**
     * Parses DTD source code extracting matches for
     * comments, entities, elements, and attribute lists.
     * @method  parseInput
     * @param   string      $source  DTD source
     * @return  array                Array containig matches.
     */
    private static function parseInput($source = '')
    {
        $multiMatch = '[\s]*<
        #Capture tag kind (\1)
        (?<kind>!ENTITY|
                !ELEMENT|
                !ATTLIST|
                !--
        )
        (?(?<=!--)
        (?<cvalue>[\s\S]*?)
        (?=-->)-->|
        [\s]*\%?[\s]*(?<name>[\w\.]*)
        [\s]*(?<value>[^>]*?)>
        )';
        $pattern = '@' . $multiMatch . '@xm';
        preg_match_all($pattern, $source, $matches, PREG_SET_ORDER);
        return $matches;
    }

    /**
     * Attempts to replace entities of the form %entity-name;
     * with the corresponding value in all entities,
     * attributes, and elements.
     * Note: All entities are traversed only once, that is entities have to
     * be defined before they are used.
     * @method  resolve
     * @param   array    &$inputArr  Array of the form: ['name' => 'value', ...]
     * @return  void
     */
    private function resolve(array &$inputArr)
    {
        foreach ($inputArr as $name => $value) {
            $pattern = '@(%[\w\.]*;)@';
            $inputArr[$name] =
                preg_replace_callback($pattern, function ($matches) {
                    return (isset($this->entities[$matches[0]])) ?
                        $this->entities[$matches[0]] : $matches[0];
                }, $value);
        }
    }

    /**
     * Attempts to replace entities of the form %entity-name;
     * with the corresponding value in all entities,
     * attributes, and elements.
     * @method  resolveEntities
     * @return  void
     */
    private function resolveEntities()
    {
        $this->resolve($this->entities);
        $this->resolve($this->attrLists);
        $this->resolve($this->elements);
    }
}
