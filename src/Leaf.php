<?php

declare(strict_types=1);

namespace Simphotonics\Dom;

use Simphotonics\Dom\LeafAccess;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2021 Simphotonics
 * Description: Implements methods available to external
 * nodes (leaves). It is the base class of Simphotonics\Node.
 */
class Leaf implements LeafAccess
{
    /**
     * Object count (used to set the node id).
     * @var integer
     */
    protected static int $count = 0;

    /**
     * Node id.
     * @var string
     */
    protected string $id;

    /**
     * Stores node attributes.
     * @var array
     */
    protected $attributes = [];

    /**
     * Node content.
     * @var string
     */
    protected $content = '';

    /**
     * Element kind
     * @var string
     */
    protected string $kind = 'div';

    /**
     * Parent node
     * @var Simphotonics/Node
     */
    protected $parent = null;


    /**
     * Constructs node object. Expected input is an array of the
     * form $input = ['kind' => 'node-kind','attr' => [attributes],
     * 'cont' => 'node-content','child' => child-node];
     *
     * @param Array $input
     *
     */
    public function __construct(
        string $kind = 'default',
        array $attributes = [],
        string $content = ''
    ) {
        $this->kind = $kind;
        $this->attributes = $attributes;
        $this->content = $content;
        // Set id
        $this->id = $this->kind . ++self::$count;
    }

    /**
     * Creates a deep copy of $this.
     * @return void
     */
    public function __clone()
    {
        // Reset parent node
        $this->parent = null;
        // Set id
        $this->id = $this->kind . ++self::$count;
    }

    /**
     * Sets node kind.
     * @param string $kind
     * @return Simphotonics\Node  Enables chaining of commands!
     */
    public function setKind(string $kind): self
    {
        $this->kind = $kind;
        return $this;
    }

    /**
     * Returns node kind.
     * @return string
     */
    public function kind(): string
    {
        return $this->kind;
    }

    /**
     * Sets or modifies attributes array.
     * @param Array  $attr Expected form: ['attr' => 'attrValue'].
     * @param string $mode Valid modes are: reset,add,replace.
     * @return Simphotonics\Node
     */
    public function setAttributes(array $attr, $mode = 'add'): self
    {
        switch ($mode) {
            case 'add':
                // New and old attributes are merged.
                foreach ($attr as $key => $var) {
                    if (array_key_exists($key, $this->attributes)) {
                        $this->attributes[$key] .= ' ' . $var;
                    } else {
                        $this->attributes[$key] = $var;
                    }
                }
                return $this;
                break;
            case 'replace':
                // Old attributes are replaced by new ones.
                foreach ($attr as $key => $var) {
                    $this->attributes[$key] = $var;
                }
                return $this;
                break;
            default:
        }
        return $this;
    }

    /**
     * Resets attributes array.
     * @return void
     */
    public function resetAttributes(): self
    {
        $this->attributes = [];
        return $this;
    }

    /**
     * Returns attribute array.
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * Returns true if the attributes array is
     * empty.
     *
     * @return boolean
     */
    public function attributesIsEmpty(): bool
    {
        return empty($this->attributes);
    }

    /**
     * Returns true if the attributes array is not
     * empty.
     *
     * @return boolean
     */
    public function attributesIsNotEmpty(): bool
    {
        return !empty($this->attributes);
    }


    /**
     * Checks is attribute[$key] is set to $value.
     * N.B. For entry: ['class' => 'main border']
     * $this->hasAttrValue('class','main') will return true.
     * @param  string  $key
     * @param  string  $value
     * @return boolean
     */
    public function hasAttribute($key, $value): bool
    {
        if (!isset($this->attributes[$key])) {
            return false;
        }
        if (substr_count($this->attributes[$key], $value)) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if the attributes in the input array match attributes
     * of $this.
     *
     * @param  array   $attributes
     *
     * @return boolean
     */
    public function hasAttributes(array $attributes): bool
    {
        if (array_intersect_assoc($attributes, $this->attributes) === $attributes) {
            return true;
        }
        return false;
    }

    /**
     * Unsets entry $this->attributes[$key].
     * @param  int $key
     * @return Simphotonics\Leaf
     */
    public function removeAttribute($key): self
    {
        if (isset($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }
        return $this;
    }

    /**
     * Sets content of leaf. The argument $content is first converted to string.
     *
     * @param string $cont
     * @return $this
     */
    public function setContent(mixed $content): self
    {
        $this->content = "$content";
        return $this;
    }

    /**
     * Returns content of node.
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * Checks if $this has content.
     * @return boolean
     */
    public function hasContent(): bool
    {
        return !empty($this->content);
    }

    /**
     * Returns node id.
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Returns a string containing node information.
     * @param  bool $mode 1 => 'SHOW_NODE_UID'
     * @param  int $length Number of digits in node UID.
     * @return string
     */
    public function showID(): string
    {
        $newline = (PHP_SAPI == 'cli') ? "\n" : "<br/>";
        $parentID = " | parent: ";
        $parentID .= ($this->parent() == null) ? "NULL" :
            $this->parent()->id();
        return  $this->id() . $parentID . $newline;
    }

    /**
     * Returns parent of current node.
     * @return Simphotonics\Node|NULL
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * Returns the n-th ancestor of current node.
     * @param  int $n
     * @return \Simphotonics\Node|NULL
     */
    public function getAncestor($n = 1)
    {
        if ($n < 1) {
            return null;
        }
        $current = $this;
        for ($i = 0; $i < $n; ++$i) {
            if (!$current->parent) {
                return null;
            }
            $current = $current->parent;
        }
        return $current;
    }

    /**
     * Checks if node has child nodes.
     * Since leaf is an external node this function
     * always returns false.
     * It is used in trait:
     * Simphotonics\Dom\NodeMethods::recursion.
     *
     * @return boolean
     */
    public function hasChildNodes(): bool
    {
        return false;
    }

    public function __toString()
    {
        // Discriminate between CLI and HTML
        $blank = (PHP_SAPI == 'cli') ? "   " : " &nbsp &nbsp &nbsp";
        $newline = (PHP_SAPI == 'cli') ? "\n" : "<br/>";
        $parentPre = " | parent: ";
        $parentID = ($this->parent() == null) ? "NULL" : $this->parent()->id();
        $out = $newline . $this->id() . $parentPre . $parentID . $newline;
        return $out;
    }

    /**
     * Implodes the attributes array of a node.
     * @param  string $glue
     * @return string
     */
    protected function attr2str($glue = " "): string
    {
        $string = '';
        foreach ($this->attributes as $key => $var) {
            //Enclose with double quote characters
            $var = self::quote($var);
            $string .= $glue . $key . "=" . $var;
        }
        return rtrim($string);
    }

    /**
     * Encloses an input string with double quote characters.
     * @param  string $val
     * @param  string $q
     *
     * @return
     */
    private static function quote($val = "", $q = "\"")
    {
        if (is_string($val)) {
            return $q . $val . $q;
        } elseif (is_numeric($val)) {
            return $val;
        } elseif (is_array($val)) {
            return "Leaf::quote returned 'array'! ";
        } else {
            return $q . $val . $q;
        }
    }
}
