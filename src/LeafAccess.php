<?php

declare(strict_types=1);

namespace Simphotonics\Dom;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Includes basic access methods required by
 *              classes working with external nodes of
 *              type Simphotonics\Dom\Leaf.
 */
interface LeafAccess
{
    public function id(): string;

    /**
     * Returns node kind.
     * @return string
     */
    public function kind(): string;

    /**
     * Sets the given attributes.
     *
     * @param array $attributes: An array of attributes.
     * @param string $mode: 'reset','add', 'replace'.
     *
     * @return self
     */
    public function setAttributes(
        array $attributes,
        string $mode = 'add'
    ): self;

    /**
     * Returns the attributes array.
     * @return array
     */
    public function attributes(): array;

    /**
     * Returns true if $this has the attributes
     * listed in the input array.
     *
     * @param array $attributes
     * @return bool
     */
    public function hasAttributes(array $attributes): bool;

    /**
     * Returns true if the attributes array of $this is not empty.
     *
     * @return bool
     */

    public function attributesIsNotEmpty(): bool;

    /**
     * Returns true if the attributes array of $this is empty.
     *
     * @return bool
     */

    public function attributesIsEmpty(): bool;

    /**
     * Return true the content of this is not empty and false otherwise.
     * @return boolean
     */
    public function hasContent(): bool;

    /**
     * Returns the content of $this converted to string.
     * @return string
     */
    public function content(): string;

    /**
     * Returns the parent of $this.
     * @return Simphotonics\Node|NULL
     */
    public function parent();

    /**
     * Always return false since leaves (external
     * nodes) have no child nodes by definition.
     *
     * @method  hasChildNodes
     * @return  boolean
     */
    public function hasChildNodes(): bool;
}
