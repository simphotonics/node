<?php

namespace Simphotonics\Dom\Parser;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlNode;
use Simphotonics\Utils\FileUtils;
use Simphotonics\Dom\NodeAccess;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Parses HTML source code and attempts
 * to extract an array of Simphotonics\Dom nodes.
 */

class HtmlParser
{

    /**
     * Top nodes of parsed HTML document.
     * @var  array
     */
    private $topNodes = [];

    /**
     * Array listing empty HTML elements found:
     * Example: ['br' => 'empty','img' => 'empty', ...].
     * @var  array
     */
    private $formatInfo = [];

    /**
     * HTML source code
     * @var  string
     */
    private $source = '';

    /**
     * Constructs object. Parses Html source code generating
     * top level nodes. The format of empty elements is
     * stored in $this->formatInfo.
     * @method  __construct
     * @param   string       $source  Html source code.
     */
    public function __construct($source = '')
    {
        if (func_num_args() > 0) {
            $this->source = $this->prepareSource($source);
            $this->topNodes = $this->parseNodes($this->source);
        }
    }

    /**
     * Loads a HTML source file and parses string content.
     * @method  loadDtd
     * @param   string   $filename  Path to the HTML source code file.
     * @return  void
     */
    public function loadHtml($filename = 'site.xhtml')
    {
        $this->source = $this->prepareSource(
            FileUtils::loadFile($filename)
        );
        $this->topNodes = $this->parseNodes($this->source);
    }

    /**
     * Return an array containg the 'top level' nodes.
     * (Typically <!DOCTYPE ... > and <html> ... </html>.)
     * @method  getNodes
     * @return  array    Array containing top level nodes.
     */
    public function getNodes()
    {
        return $this->topNodes;
    }

    /**
     * Write PHP source code that generates top nodes
     * to file.
     * @method  exportNodes
     * @param   string       $filename  Path to file on file system.
     * @return  int|false               Number of bits writen or false on failure.
     */
    public function exportNodes($filename = 'parsedNodes.php')
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
     * Return an array containing format information of
     * parsed empty elements.
     * @method  getFormatInfo
     * @return  array         Array of the form:
     *                        ['br' => 'empty', ...].
     */
    public function getFormatInfo()
    {
        return $this->formatInfo;
    }

    /**
     * Replaces closing tags of DTDs with '!>'.
     * (Makes it easier to parse the HTML source.)
     * @method  prepareSource
     * @return  void
     */
    private function prepareSource($source = '')
    {
        $pattern = '@[\s]*<!DOCTYPE([^>]*)>@';
        $out = preg_replace_callback($pattern, function ($matches) {
            return '<!DOCTYPE' . $matches[1] . '!>';
        }, $source);
        if ($out === null) {
            return '';
        } else {
            return $out;
        }
    }

    private function parseNodes($source = '')
    {
        $matches = self::getNodeInput($source);
        $nodes = [];
        foreach ($matches as $match) {
            // Get format
            switch ($match['ctag']) {
                    // EMPTY node
                case '/>':
                    $this->formatInfo[$match['kind']] = 'empty';
                    $nodes[] = new HtmlLeaf(
                        kind: $match['kind'],
                        attributes: self::attributes($match['attr'])
                    );
                    break;
                    // DOCTYPE node
                case '!>':
                    $nodes[] = new HtmlLeaf(
                        kind:'!DOCTYPE',
                        content: trim($match['attr'])
                    );
                    break;
                    // COMMENT node
                case '-->':
                    $nodes[] = new HtmlLeaf(
                        kind:$match['kind'],
                        content:trim($match['attr'])
                    );
                    break;
                    // BLOCK node
                    // Note: Calls parseNodes recursively
                default:
                    $nodes[] = new HtmlNode(
                        kind:$match['kind'],
                        attributes: self::attributes($match['attr']),
                        content:trim($match['text'])
                    );
                    if (strlen(trim($match['childNodes']))) {
                        $childNodes = $this->parseNodes($match['childNodes']);
                        if (count($childNodes)) {
                            end($nodes)->append($childNodes);
                        }
                    }

                    break;
            }
        }
        return $nodes;
    }

    /**
     * Parsed string containig element attributes.
     * @method  getAttr
     * @param   string   $attrString  Input string
     * @return  array                 Element attributes.
     */
    private static function attributes($attrString = '')
    {
        $attrString = str_replace('"', '', $attrString);
        $words = explode(' ', $attrString);
        $attrArr = [];
        foreach ($words as $var) {
            $tmp = explode('=', $var);
            if (isset($tmp[0]) & isset($tmp[1])) {
                $attrArr[$tmp[0]] = $tmp[1];
            }
        }
        return $attrArr;
    }

    /**
     * Parses Html source code and returns an array containg
     * the 'top' nodes. Nested nodes are appended to the top
     * nodes.
     * E.g. Given input of the form:
     * $source ='<!DOCTYPE ...><html> ...</html>' yields an
     * array containing 2 'top' nodes.
     * @method  getNodeInput
     * @param   string        $source  Html source code.
     * @return  array                  Top level nodes.
     */
    public static function getNodeInput($source = '')
    {
        /**
         * Pattern matches:
         *     COMMENT NODES <!-- ... -->
         *     EMPTY NODES   <br/>, <img ... />
         *     BLOCK NODES   <div class=...> ... </div>
         *     Note: Detects nested nodes.
         * @var  string
         */
        $multiMatch = '
        [\s]*
        < #Capture tag kind (\1)
        (?<kind>[a-z0-9]+|!--|!DOCTYPE)
        #Capture tag attributes (\2)
        #Note the positive lookbehind and lookahead to force
        #     capturing of comment elements.
        (?<attr>(?(?<=!--)[\s\S]*?(?=-->)|[\s\S]*?))
        (?<ctag>
            #Capture closing tag EMPTY elements (\3)
            \/>|
            #Capture closing tag COMMENT elements (\3)
            -->|
            #Capture closing tag of DOCTYPE elements (\3)
            #see self::prepareSource()
            !>|
            #Capture <tag{>...}<\tag> of BLOCK elements (\3)
            >
            #Capture text content of BLOCK element (\4)
            #  Note: Only text after the closing bracket and
            #         before any nested nodes is captured!
            (?<text>[^<]*)
            (?:
                #Capture nested nodes (\5)
                (?<childNodes>
                    (?:
                        #COND I
                        [^<]*?|
                        #COMMENT node nested within BLOCK element: COND II
                        <\!\-\-.*?\-\->|
                        #EMPTY node within BLOCK element. COND III
                        <[a-z]+[^>]*/>|
                        #Start recursion if COND I-III do not match.
                        (?R)
                    )*
                )
                # Closing tag of BLOCK elements
                </\1>
            )
        )';
        $pattern = '@' . $multiMatch . '@xm';
        preg_match_all($pattern, $source, $matches, PREG_SET_ORDER);
        return $matches;
    }
}
