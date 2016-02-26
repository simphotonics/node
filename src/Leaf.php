<?php

namespace Simphotonics\Dom;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Implements methods available to external
 * nodes (leaves). It is the base class of Simphotonics\Node.
 */
class Leaf
{
    /**
     * Object count (used to set the node id).
     * @var integer
     */
    protected static $count = 1;

    /**
     * Node id.
     * @var int
     */
    protected $id = 0;

    /**
     * Stores node attributes.
     * @var array
     */
    protected $attr = [];

    /**
     * Node content.
     * @var string
     */
    protected $cont = '';

    /**
     * Element kind
     * @var string
     */
    protected $kind = 'div';

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
    public function __construct(array $input = [])
    {
        // Assign kind
        if (isset($input['kind'])) {
            $this->kind = $input['kind'];
        }
        // Assign attributes
        if (isset($input['attr'])) {
            $this->attr = (is_array($input['attr'])) ? $input['attr'] : [];
        }
        // Assign content
        if (isset($input['cont'])) {
            $this->cont = $input['cont'];
        }
        // Set id
        $this->id = ++self::$count;
    }

    /**
     * Sets node kind.
     * @param string $kind
     * @return Simphotonics\Node  Enables chaining of commands!
     */
    public function setKind(string $kind)
    {
        $this->kind = $kind;
        return $this;
    }
    
    /**
     * Returns node kind.
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }
    
    /**
     * Sets or modifies attributes array.
     * @param Array  $attr Expected form: ['attr' => 'attrValue'].
     * @param string $mode Valid modes are: reset,add,replace.
     * @return Simphotonics\Node
     */
    public function setAttr(array $attr, $mode = 'add')
    {
        switch ($mode) {
            case 'add':
                // New and old attributes are merged.
                foreach ($attr as $key => $var) {
                    if (array_key_exists($key, $this->attr)) {
                        $this->attr[$key] .= ' ' . $var;
                    } else {
                        $this->attr[$key] = $var;
                    }
                }
                return $this;
            break;
            case 'replace':
                // Old attributes are replaced by new ones.
                foreach ($attr as $key => $var) {
                    $this->attr[$key] = $var;
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
    public function resetAttr()
    {
        $this->attr = [];
        return $this;
    }

    /**
     * Returns attribute array.
     * @return Array
     */
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     * Checks if attribute array is not empty.
     * @return boolean
     */
    public function hasAttr()
    {
        return !empty($this->attr);
    }

    /**
     * Checks is attribute[$key] is set to $value.
     * N.B. For entry: ['class' => 'main border']
     * $this->hasAttrValue('class','main') will return true.
     * @param  string  $key
     * @param  string  $value
     * @return boolean
     */
    public function hasAttrValue($key, $value)
    {
        if (!isset($this->attr[$key])) {
            return false;
        }
        if (substr_count($this->attr[$key], $value)) {
            return true;
        }
        return false;
    }
    
    /**
     * Checks for exact match of entries in attributes array.
     * @param  Array   $attrArr
     * @return boolean
     */
    public function hasAttrValues(array $attrArr)
    {
        if (array_intersect_assoc($attrArr, $this->attr) === $attrArr) {
            return true;
        }
        return false;
    }
    
    /**
     * Unsets entry $this->attr[$key].
     * @param  int $key
     * @return Simphotonics\Leaf
     */
    public function removeAttr($key)
    {
        if (isset($this->attr[$key])) {
            unset($this->attr[$key]);
            
        }
        return $this;
    }
    
    /**
     * Sets string content of node.
     * @param string $cont
     * @return $this
     */
    public function setCont($cont)
    {
        $this->cont = "$cont";
        return $this;
    }
    
    /**
     * Returns content of node.
     * @return string
     */
    public function getCont()
    {
        return $this->cont;
    }

    /**
     * Checks if $this has content.
     * @return boolean
     */
    public function hasCont()
    {
        return !empty($this->cont);
    }
    
    /**
     * Returns node id.
     * @return int
     */
    public function getID()
    {
        return $this->kind.$this->id;
    }

    /**
     * Returns a string containing node information.
     * @param  bool $mode 1 => 'SHOW_NODE_UID'
     * @param  int $length Number of digits in node UID.
     * @return string
     */
    public function showID()
    {
        $newline = (PHP_SAPI == 'cli') ? "\n" : "<br/>";
        $parentID = " | parent: ";
        $parentID .= ($this->getParent() == null) ? "NULL" :
        $this->getParent()->getID();
        return  $this->getID() . $parentID . $newline;
    }
    
    /**
     * Returns parent of current node.
     * @return Simphotonics\Node|NULL
     */
    public function getParent()
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
     * It is used trait Simphotonics\Dom\NodeMethods::recursion.
     *
     * @return boolean
     */
    public function hasChildNodes()
    {
        return false;
    }

    /**
     * Implodes the attributes array of a node.
     * @param  string $glue
     * @return string
     */
    protected function attr2str($glue = " ")
    {
        $string = '';
        foreach ($this->attr as $key => $var) {
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
     * @return string
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
