<?php

declare(strict_types=1);

namespace Simphotonics\Node\Parser;

// Simphotonics\Utils\ArrayUtils;
use Simphotonics\Node\Leaf;
use Simphotonics\Node\Node;
use Simphotonics\Node\NodeAccess;

/**
 * Description: Used to render Simphotonics\Node\Leaf and
 * Simphotonics\Node\Node objects as php source code.
 * Usage:
 * 1) NodeRenderer::render($node) generates the source code
 *    used to create $node.
 * 2) NodeRenderer::renderRecursive($node) generated the source
 *    code used to create all descendants of $node and $node
 *    itself.
 */
class NodeRenderer
{
    public function __toString(): string
    {
        return "Object of type " . __CLASS__ .
            "used to render Simphotonics\Node nodes as PHP source code.";
    }

    /**
     * Renders an input $node as php source code.
     *
     * @param         $node A node (or leaf).
     * @param  string $varName Variable name (optional).
     *
     * @return string          PHP code.
     */
    public static function render(Leaf|Node $node, $varName = ''): string
    {
        // Validate variable name
        $varName = ($varName) ? $varName : $node->id();
        $varName = self::generateVariableName($varName);
        // Object kind
        $source = '$' . $varName . ' = new \\' . get_class($node) . "(\n" .
            '  kind: ' . self::quote($node->kind()) . ",\n";

        // Object attributes
        if ($node->attributesIsNotEmpty()) {
            $source .= '  attributes: ' . self::renderArray(
                input: $node->attributes(),
                name: '',
                indentLevel: 1
            );
            $source .= "\n";
        }

        // Object child nodes
        if ($node instanceof NodeAccess && $node->hasChildNodes()) {
            $source = rtrim($source);
            $source .= "\n  childNodes: " . self::renderChildNodes(
                $node->childNodes(),
                'child',
                1
            );
        }
        // Content
        $source = rtrim($source);
        if ($node->hasContent()) {
            $content = wordwrap($node->content(), 80, "\n");
            $source .= "\n  'content' => '$content',";
        }
        // Closing brackets;
        $source .= "\n); \n";
        return $source;
    }

    /**
     *  Renders the input $node and all its descendants as
     *  php source code.
     *
     * @return string
     */
    public static function renderRecursive(NodeAccess $node): string
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
     * @param  integer $indentlevel  [description]
     * @param  string  $indentString [description]
     * @return [type]                [description]
     */
    private static function renderChildNodes(
        array $childNodes,
        string $name,
        int $indentLevel = 0,
        string $indentString = "  "
    ): string {

        // Set indentation level
        $outerIndent = str_repeat($indentString, $indentLevel);
        $innerIndent = str_repeat($indentString, $indentLevel + 1);
        // Open bracket
        $out = $outerIndent . "'$name'=> [\n";
        // Body
        foreach ($childNodes as $node) {
            $out .= $innerIndent . '$' .
                self::generateVariableName($node->id()) . ",\n";
        }
        $out = rtrim($out, "\n");   // Eliminate last comma and newline
        // Close bracket
        $out .= "\n$outerIndent] \n";
        return $out;
    }

    public static function renderArray(
        array $input,
        string|int $name = '',
        int $indentLevel = 0,
        string $indentString = "  ",
        string $closingCharacter = '',
    ): string {
        // Check input
        if (!is_array($input)) {
            $input = [$input];
        }
        // Set indentation level
        $outerIndent = str_repeat($indentString, $indentLevel);
        $innerIndent = str_repeat($indentString, $indentLevel + 1);

        if (!empty($name)) {
            if ($indentLevel == 0) {
                $name = "\$$name";
            } else {
                $name = self::quote($name);
            }
        }
        // $name
        $out = $outerIndent . $name;
        // Opening bracket
        if (empty($name)) {
            $out .= "[\n";
        } else {
            $out .= ($indentLevel == 0) ? " = [\n" : " => [\n";
        }

        // Typeset inner content
        foreach ($input as $key => $var) {
            if (is_array($var)) {
                $out .= self::renderArray(
                    input: $var,
                    name: $key,
                    indentLevel: $indentLevel + 2,
                    indentString: "   "
                );
            } else {
                //$var = trim($var);
                // N.B. We enclose the values in single quotes =>
                // All single quotes occurring
                //      inside the string $var have to be escaped!!!

                $key = is_string($key) ?
                    str_replace('\'', '\\\'', $key) : $key;
                $var = is_string($var) ?
                    str_replace('\'', '\\\'', $var) : $var;
                if (is_string($var) && strlen($var) > 79) {
                    $var = chunk_split($var, 79, "\n");
                    $var = rtrim($var);
                }
                $out .= $innerIndent .
                    self::quote($key) . ' => ' . self::quote($var) . ",\n";
            }
        }
        // Eliminate last comma and newline
        $out = rtrim($out, "\n");
        // Closing bracket
        if ($indentLevel === 0) {
            $out = rtrim($out, ", \n");
            $out .= "\n].$closingCharacter";
        } else {
            $out .= "\n$outerIndent], \n";
        }
        return $out;
    }

    /**
     * Encloses an input string with double quote characters.
     * @param  string $val
     * @param  string $q
     *
     * @return
     */
    private static function quote($val = "", $q = "'")
    {
        if (is_string($val)) {
            return $q . $val . $q;
        } elseif (is_numeric($val)) {
            return $val;
        } elseif (is_array($val)) {
            return " 'array' cant quote recursively! ";
        } else {
            return $q . $val . $q;
        }
    }

    /**
     * Check if variable name conforms to syntax constraints.
     *
     * @method checkVariableName
     * @param  string $name Variable name
     *
     * @return string      New name
     */
    private static function generateVariableName($name = ''): string
    {
        // Starts with a letter
        $pattern = '@^[a-zA-z]+[a-zA-z0-9]*@';
        if (preg_match($pattern, $name)) {
            return strtolower($name);
        }
        // Does not start with a letter
        $pattern = '@[a-zA-z]+[a-zA-z0-9]*@';
        if (preg_match($pattern, $name, $matches)) {
            return strtolower($matches[0]);
        }
        // Starts with !--
        if (substr($name, 0, 3) === '!--') {
            return 'comment' . substr($name, 3);
        }
        // Contains at least one number
        $pattern = '@[0-9]+@';
        if (preg_match($pattern, $name, $matches)) {
            return 'var' . $matches[0];
        }
        // If all else fails:
        return 'var' . md5($name);
    }
}
