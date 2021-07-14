<?php

namespace Simphotonics\Node;

use Simphotonics\Node\HtmlLeaf;
use Simphotonics\Node\HtmlNode;
use Simphotonics\Utils\ArrayUtils;

/**
 * Creates an HtmlNode representing an html select element.
 */
class HtmlSelect extends HtmlNode
{
    /**
     * Preselected option node
     * @var null
     */
    private $defaultOption = null;

    /**
     *
     * @param  string      $name    Input element name.
     * @param  array       $options Array of the form: ['option1',...,'selected' => 'option10',...].
     */
    public function __construct(
        $name = 'name',
        array $options = [
            'value1' => 'displayedContent1',
            'value2' => 'displayedContent2'
        ],
        string|null $defaultOption = null
    ) {
        parent::__construct(
            kind: 'select',
            attributes: ['name' => $name, 'id' => $name],
        );
        $this->initOptions($options, $defaultOption);
    }

    /**
     * Initialised option nodes
     * @method initOptions
     * @param  array   $options        Array of the form:
     *                                    ['value' => 'displayedValue', ...]
     * @param  string  $defaultOption  Key of default value in array $options.
     * @return void
     */
    private function initOptions(array $options, $defaultOption)
    {
        foreach ($options as $value => $displayedContent) {
            if ($defaultOption == $value) {
                $this->appendChild(new HtmlLeaf(
                    kind: 'option',
                    attributes: ['value' => $value, 'selected' => 'selected'],
                    content: $displayedContent,
                ));
                $this->defaultOption = $this->last();
            } else {
                $this->appendChild(new HtmlLeaf(
                    kind: 'option',
                    attributes: [
                        'value' => $value,

                    ],
                    content: $displayedContent,
                ));
            }
        }
    }

    /**
     * Clear default option.
     * @method clearDefaultOption
     * @return void
     */
    public function clearDefaultOption()
    {
        if (
            $this->defaultOption != null
            and isset($this->defaultOption->attributes['selected'])
        ) {
            unset($this->defaultOption->attributes['selected']);
            $this->defaultOption = null;
        }
    }

    /**
     * Sets default option.
     * @method setDefaultOption
     * @param  string       $value  Value of default option.
     */
    public function setDefaultOption($value)
    {
        // Clear default option
        $this->clearDefaultOption();
        // Find new default option
        $nodes = $this->getNodesByAttributeValue(['value' => $value]);
        if (empty($nodes)) {
            return;
        }
        $this->defaultOption = $nodes[0];
        $this->defaultOption->attributes['selected'] = 'selected';
    }
}
