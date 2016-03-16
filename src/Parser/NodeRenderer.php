<?php

namespace Simphotonics\Dom\Parser;

use Simphotonics\Utils\ArrayUtils;
use Simphotonics\Dom\Leaf;
use Simphotonics\Dom\NodeAccess;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Used to render Simphotonics\Dom\Leaf and
 * Simphotonics\Dom\Node objects as php source code.
 * Usage:
 * 1) NodeRenderer::render($node) generates the source code
 *    used to create $node.
 * 2) NodeRenderer::renderRecursive($node) generated the source
 *    code used to create all descendants of $node and $node
 *    itself.
 *
 *
 */
class NodeRenderer
{
    public function __toString()
    {
        return "Object of type " . get_class(self).
        "used to render Simphotonics\Dom nodes as PHP source code.";
    }
    
    /**
     * Renders input $node as php source code.
     *
     * @param  string $varName Variable name (optional).
     * @return string          PHP code.
     */
    public static function render(Leaf $node, $varName = '')
    {
        // Variable name
        $source = ($varName) ? '$' . $varName : '$' . $node->getID();
        // Object kind
        $source .= ' = new \\' . get_class($node) ."([\n" . "  'kind' => '{$node->getKind()}'";
        // Object attributes
        if ($node->hasAttr()) {
            $source .= ",\n" . self::renderArray($node->getAttr(), 'attr', 1);
        }
        // Object child nodes
        if ($node->hasChildNodes()) {
            $source = rtrim($source);
            $source .= "\n". self::renderChildNodes($node->getChildNodes(), 'child', 1);
        }
        // Content
        $source = rtrim($source);
        if ($node->hasCont()) {
            $content = wordwrap($node->getCont(), 90, "\n");
            $source .= ",\n  'cont' => '$content'";
        }
        // Closing brackets;
        $source .= "\n]); \n";
        return $source;
    }

    /**
     *  Renders the input $node and all its descendants as
     *  php source code.
     *
     * @return string
     */
    public static function renderRecursive(NodeAccess $node)
    {
        // Initialize $source
        $source = "\n";
        // Get descendants (child nodes first);
        $descNodes = $node->getDescendants(NodeAccess::CHILD_FIRST);
        foreach ($descNodes as $descNode) {
            $source .= self::render($descNode) . "\n";
        }
        // Export $node
        $source .= self::render($node);
        return $source;
    }

    /**
     * Returns a string representing an array of child nodes.
     * Used in @see $this->export().
     *
     * @param  integer $indentlevel        [description]
     * @param  string  $indentString [description]
     * @return [type]                [description]
     */
    private static function renderChildNodes(
        array $childNodes,
        $name,
        $indentLevel = 0,
        $indentString = "  "
    ) {

        // Set indentation level
        $outerIndent = str_repeat($indentString, $indentLevel);
        $innerIndent = str_repeat($indentString, $indentLevel + 1);
        // Open bracket
        $out = $outerIndent . "'$name'=> [\n";
        // Body
        foreach ($childNodes as $node) {
            $out .= $innerIndent . '$' . $node->getID() . ",\n";
        }
        $out = rtrim($out, ",\n");   // Eliminate last comma and newline
        // Close bracket
        $out .= "\n$outerIndent] \n";
        return $out;
    }

    public static function renderArray(
        array $arr,
        $name = 'arr',
        $indentLevel = 0,
        $indentString = "  "
    ) {
        // Check input
        if (!is_array($arr)) {
            $arr = [$arr];
        }
        // Set indentation level
        $outerIndent = str_repeat($indentString, $indentLevel);
        $innerIndent = str_repeat($indentString, $indentLevel + 1);
        // Opening bracket
        $out = ($indentLevel) ? $outerIndent . "'$name'=> [\n" : '$' . $name . " = [\n";
        // Typeset inner content
        foreach ($arr as $key => $var) {
            if (is_array($var)) {
                $out .= self::typesetArrayRecursive($var, $key, $indentLevel + 1);
            } else {
                //$var = trim($var);
                // N.B. We enclose the values in single quotes => All single quotes occurring
                //      inside the string $var have to be escaped!!!
                $key = str_replace('\'', '\\\'', $key);
                $var = str_replace('\'', '\\\'', $var);
                if (strlen($var) > 90) {
                    $var = chunk_split($var, 90, "\n");
                    $var = rtrim($var);
                }
                $out .= $innerIndent . "'$key' => '$var',\n";
            }
        }
        // Eliminate last comma and newline
        $out = rtrim($out, "\n,");
        // Closing bracket
        if ($indentLevel === 0) {
            $out = rtrim($out, ", \n");
            $out .= "\n];";
        } else {
            $out .= "\n$outerIndent], \n";
        }
        return $out;
    }
}
