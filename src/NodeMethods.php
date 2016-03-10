<?php

namespace Simphotonics\Dom;

use Simphotonics\Utils\ArrayUtils;
use RecursiveIteratorIterator;
use InvalidArgumentException;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2015 Simphotonics
 * Description: Contains methods shared by Simphotonics\Node
 * and Simphotonics\HtmlNode.
 */
trait NodeMethods
{
    /**
     * Constructs node object.
     * @param array| $input Array of the form:
     *             $input = [
     *               'kind' => 'div',
     *               'attr' => [attributes],
     *               'cont' => 'text content',
     *               'child' => Node
     *             ];
     */
    public function __construct(array $input = [])
    {
        // Append child nodes
        if (isset($input['child'])) {
            $this->append($input['child']);
        }
        parent::__construct($input);
    }

    /**
     * Creates a deep copy of $this.
     * @return void
     */
    public function __clone()
    {
        // Reset parent node
        $this->parent = null;
        $this->id = ++self::$count;
        // Clone the child nodes and set the parent node.
        foreach ($this->childNodes as $key => $node) {
            $newNode = clone $node;
            $newNode->parent = $this;
            $this->childNodes[$key] = $newNode;
        }
    }

    /**
     * Appends child node.
     * @param  Simphotonics\Node $input
     * @return Simphotonics\Node Returns appended node.
     */
    public function appendChild(Leaf $node)
    {
        $this->childNodes[] = $this->adopt($node);
        return $this->last();
    }

    /**
     * Append array of child nodes.
     * @param  Array  $input
     * @return Simphotonics\Node
     */
    public function append(array $input)
    {
        foreach ($input as $node) {
            $this->childNodes[] = $this->adopt($node);
        }
        return $this;
    }

    /**
     * Prepend child node.
     * @param  Node   $input
     * @return Node
     */
    public function prependChild(Leaf $node)
    {
        array_unshift($this->Childnodes, $this->adopt($node));
        return $this->first();
    }

    /**
     * Prepend array of child nodes.
     * @param  Array  $input
     * @return Node
     */
    public function prepend(array $input)
    {
        $input = array_reverse($input);
        foreach ($input as $node) {
            array_unshift($this->Childnodes, $this->adopt($node));
        }
        return $this;
    }

    /**
     * Adopt child node/leaf. Input class is assumed leaf/node!
     * @param  Leaf       $node
     * @return void
     */
    private function adopt(Leaf $node)
    {
        $newNode = ($this->recursion($node)) ? clone $node : $node;
        $newNode->parent = $this;
        return $newNode;
    }

    /**
     * Checks if appending $node to $this would lead to recursion.
     * Detects recursion by following the anchestors of $this and
     * comparing them to the input node.
     * N.B. Also returns false is input node has a parent node!
     * @param  Node $node
     * @return bool
     */
    private function recursion(Leaf $node)
    {
        if ($node->parent) {
            return true;
        }
        if ($node === $this) {
            return true;
        }
        if (!$node->hasChildNodes()) {
            return false;
        }
        $parent = $this->parent;
        $count = 0;
        $max = 10000;
        while ($parent and $count <= $max) {
            if ($node === $parent) {
                return true;
            }
            $parent = $parent->parent;
            ++$count;
        }
        return ($count > $max) ? true : false;
    }

    /**
     * Removes node from child list and reset its parent node.
     * @param  Simphotonics\Node
     * @return bool  Returns true on success.
     */
    public function removeChild(Leaf $node)
    {
        $key = $this->getKey($node);
        // N.B. The key may be 0 or "0". We explicitly
        //      have to check that it is not 'FALSE'.
        if ($key === false) {
            return false;
        }
        unset($this->childNodes[$key]);
        // $node is now an orphan =>
        if (isset($node)) {
            $node->parent = null;
        }
        //Re-index childNodes
        $this->childNodes = array_values($this->childNodes);
        return true;
    }

    /**
     * Replace child node with a new node.
     * @param  Node $newNode
     * @param  Node $oldNode
     * @return bool  Return true on success.
     */
    public function replaceChild(Leaf $newNode, Leaf $oldNode)
    {
        $key = $this->getKey($oldNode);
        if ($key === false) {
            return false;
        }
        $this->childNodes[$key] = $this->adopt($newNode);
        if (isset($oldNode)) {
            $oldNode->parent = null;
        }
        return true;
    }

    /**
     * Replace $oldNode with $newNode if $oldNode a descendant node.
     * N.B. Function scans each child node recursively!
     * If the node is a direct child node use @see $this->replaceChild().
     * @param  Node   $newNode
     * @param  Node   $oldNode
     * @return bool   Return true on success.
     */
    public function replaceNode(Leaf $newNode, Leaf $oldNode)
    {
        // First scan direct descendants
        if ($this->replaceChild($newNode, $oldNode)) {
            return true;
        }
        // Recursively scan each direct descendant with child nodes
        foreach ($this->childNodes as $node) {
            if (!$node->hasChildNodes()) {
                continue;
            }
            if ($node->replaceNode($newNode, $oldNode)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Insert $newNode before $oldNode.
     * @param  Node   $newNode
     * @param  Node   $oldNode
     * @return bool   Returns true on success.
     */
    public function insertBefore(Leaf $newNode, Leaf $oldNode)
    {
        return $this->insert($this->adopt($newNode), $oldNode, 0);
    }
    
    /**
     * Insert $newNode after $oldNode.
     * @param  Node $newNode
     * @param  Node $oldNode
     * @return bool          Returns true on success.
     */
    public function insertAfter(Leaf $newNode, Leaf $oldNode)
    {
        return $this->insert($this->adopt($newNode), $oldNode, 1);
    }
    
    /**
     * Checks if node has child nodes.
     * @return boolean
     */
    public function hasChildNodes()
    {
        if (empty($this->childNodes)) {
            return false;
        }
        return true;
    }

    /**
     * Remove $oldNode if $oldNode a descentant node.
     * N.B. Function scans each child node recursively!
     * If the node is a direct child node use @see $this->removeChild().
     * @param  Node   $oldNode
     * @return bool   Return true on success.
     */
    public function removeNode(Leaf $oldNode)
    {
        // First scan direct descendants
        if ($this->removeChild($oldNode)) {
            return true;
        }
        // Recursively scan each direct descendant with child nodes
        foreach ($this->childNodes as $node) {
            if (!$node->hasChildNodes()) {
                continue;
            }
            if ($node->removeNode($oldNode)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns an array containing the child nodes.
     * @return Array
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * Returns an array containing all descendant nodes/leaves.
     * @param  int $mode @see \RecursiveIteratorIterator takes
     * values: LEAVES_ONLY, SELF_FIRST, CHILD_FIRST
     * @return array
     */
    public function getDescendants($mode = self::SELF_FIRST)
    {
        $nodes = [];
        if (!$this->hasChildNodes()) {
            return $nodes;
        }
        $childRIT = new RecursiveIteratorIterator($this, $mode);
        foreach ($childRIT as $childNode) {
            $nodes[] = $childNode;
        }
        return $nodes;
    }

    /**
    * Returns first child.
    * @return Node
    */
    public function first()
    {
        reset($this->childNodes);
        return current($this->childNodes);
    }
    
    /**
     * Returns last child.
     * @return Node
     */
    public function last()
    {
        $last = end($this->childNodes);
        reset($this->childNodes);
        return $last;
    }
    
    /**
     * Returns number of direct child nodes.
     * @return int
     */
    public function count()
    {
        return count($this->childNodes);
    }

    /**
     * Returns an array containing child/descendant nodes of a certain 'kind'.
     * @param  string $kind
     * @param  bool $flag self::ALL_DESCENDANT_NODES/self::ONLY_CHILD_NODES
     * @return Array
     */
    public function getNodesByKind($kind, $flag = self::ALL_NODES)
    {
        $nodes = ($flag) ? $this->childNodes : $this->getDescendants();
        $out = [];
        foreach ($nodes as $node) {
            if ($node->kind == $kind) {
                $out[] = $node;
            }
        }
        return $out;
    }

    /**
     * Returns an array of nodes/descendants with a given attribute.
     * @param  string  $attrKey
     * @param  bool $flag  self::ALL_DESCENDANT_NODES/self::ONLY_CHILD_NODES
     * @return Array
     */
    public function getNodesByAttrKey($attrKey, $flag = self::ALL_NODES)
    {
        if (is_array($attrKey) and !empty($attrKey)) {
            return $this->getNodesByAttrValue($attrKey, $flag);
        }
        $out = [];
        $nodes = ($flag) ? $this->childNodes : $this->getDescendants();
        foreach ($nodes as $node) {
            if (isset($node->attr[$attrKey])) {
                $out[] = $node;
            }
        }
        return $out;
    }

    /**
     * Returns an array of nodes/descendants with a given [attribute => value].
     * @param  Array  $inputAttr
     * @param  bool $flag  self::ALL_DESCENDANT_NODES/self::ONLY_CHILD_NODES
     * @return Array
     */
    public function getNodesByAttrValue(array $inputAttr, $flag = self::ALL_NODES)
    {
        $nodes = ($flag) ? $this->childNodes : $this->getDescendants();
        $out = [];
        foreach ($nodes as $node) {
            if (array_intersect_assoc($inputAttr, $node->attr) === $inputAttr) {
                $out[] = $node;
            }
        }
        return $out;
    }

    /**
     * Returns a child/descendant node with a certain id.
     * @param  string $id
     * @param  bool $flag
     * @return Node|NULL
     */
    public function getNodeByID($id, $flag = self::ALL_NODES)
    {
        $nodes = ($flag) ? $this->childNodes : $this->getDescendants();
        foreach ($nodes as $node) {
            if ($node->getID() === $id) {
                return $node;
            }
        }
        return null;
    }

    /**
     * Returns a string showing a tree node hierarchy.
     * @param  bool $mode
     * @return string
     */
    public function tree()
    {
        // Discriminate between CLI and HTML
        $blank = (PHP_SAPI == 'cli') ? "   " : " &nbsp &nbsp &nbsp";
        $newline = (PHP_SAPI == 'cli') ? "\n" : "<br/>";
        $parentPre = " | parent: ";
        $parentID = ($this->getParent() == null) ? "NULL" : $this->getParent()->getID();
        $out = $newline . $this->getID(). $parentPre. $parentID. $newline;
            // Create instance of RecursiveIteratorIterator with option SELF_FIRST.
        $nodeRIT = new RecursiveIteratorIterator($this, 1);
        foreach ($nodeRIT as $node) {
            $out .= str_repeat(
                $blank,
                $nodeRIT->getDepth() + 1
            ) . $node->getID(). $parentPre.
            $node->parent->getID(). $newline;
        }
        return $out;
    }
    
    /**
     * Rearranges the nodes stored in $this->childNodes according to
     * a suitable permutation of array offsets.
     * @param  Array $permutation
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function permute(array $permutation)
    {
        // Check if permutation is valid
        $normalOrder = $permutation;
        sort($normalOrder);
        if ($normalOrder !== array_keys($this->childNodes)) {
            $message = 'Input array does not contain valid permutation. 
            Found: '. print_r($permutation, true);
            if (PHP_SAPI != 'cli') {
                $message = str_replace("\n", '<br/>', $message);
            }
            throw new InvalidArgumentException($message);
        }
        $nodes = $this->childNodes;
        $this->childNodes = [];
        foreach ($permutation as $key) {
            $this->childNodes[] = $nodes[$key];
        }
    }

    // ================
    // Helper Functions
    // ================

    /**
     * Insert new node at a given offset.
     * Note: It is assumed that $newNode has been 'adopted' in
     * the function calling $this->insert().
     * @param  node  $newNode
     * @param  node  $oldNode
     * @param  integer $offset
     * @return bool  Returns true on success.
     */
    private function insert(Leaf $newNode, Leaf $oldNode, $offset = 0)
    {
        $key = $this->getKey($oldNode);

        if ($key === false) {
            return false;
        }
        
        $offset += ArrayUtils::key2offset($this->childNodes, $key);
        array_splice($this->childNodes, $offset, 0, [$newNode]);
        return true;
    }

    /**
     * Returns a valid key if $node is found in nodes.
     * @param  node $node
     * @return int        Valid element key or false.
     */
    private function getKey(Leaf $node)
    {
        return array_search($node, $this->childNodes, true);
    }

    // ===================================================
    // Functions Required by Interface: \RecursiveIterator
    // ===================================================
    
    /**
     * Returns current node.
     * @return node
     */
    public function current()
    {
        return current($this->childNodes);
    }
    
    /**
     * Returns the key to the current element.
     * @return int
     */
    public function key()
    {
        return key($this->childNodes);
    }

    /**
     * Moves internal pointer to next position.
     * @return void
     */
    public function next()
    {
        return next($this->childNodes);
    }

    /**
     * Moves to previous position.
     * @return void
     */
    public function prev()
    {
        prev($this->childNodes);
    }

    /**
     * Rewinds the iterator to the first node.
     * @return void
     */
    public function rewind()
    {
        reset($this->childNodes);
    }

    /**
     * Checks if the current key is valid.
     * @return bool
     */
    public function valid()
    {
        return !is_null(key($this->childNodes));
    }

    /**
     * Returns the NodeRecursiveIterator object of the child node.
     * @return Simphotonics\Node
     */
    public function getChildren()
    {
        return $this->current();
    }

    /**
     * Checks if current child node (pointed to by the internal pointer)
     * has child nodes.
     * @return boolean
     */
    public function hasChildren()
    {
        return $this->current()->hasChildNodes();
    }

    // =============================================
    // Functions Required by Interface: Array Access
    // =============================================
    /**
     * Appends node at a given offset.
     * @param  int $offset
     * @param  Node $node
     * @return void
     */
    public function offsetSet($offset, $node)
    {
        if (isset($this->childNodes[$offset])) {
            $this->childNodes[$offset] = $this->adopt($node);
        } else {
            $this->childNodes[] = $this->adopt($node);
        }
    }

    /**
     * Checks if given offset is set.
     * @param  int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->childNodes[$offset]);
    }

    /**
     * Unsets elements at given offset.
     * @param  int $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->childNodes[$offset]);
    }

    /**
     * Returns element at given offset.
     * @param  int $offset
     * @return node
     */
    public function offsetGet($offset)
    {
        return isset($this->childNodes[$offset]) ?
        $this->childNodes[$offset] : null;
    }
}
