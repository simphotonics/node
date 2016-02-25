<?php
namespace Simphotonics\Dom;

use Simphotonics\Dom\HtmlLeaf;
use Simphotonics\Dom\HtmlNode;
use Simphotonics\Utils\ArrayUtils;

class HtmlSelect extends HtmlNode
{
    /**
     * Preselected option node
     * @var null
     */
    private $defaultOption = null;
  
    /**
     * Constructs object.
     * @method __construct
     * @param  string      $name    Input element name.
     * @param  array       $options Array of the form: ['option1',...,'selected' => 'option10',...].
     */
    public function __construct(
        $name = 'name',
        array $options = ['value1' => 'displayedValue1',
            'value2' => 'displayedValue1'
         ],
        $defaultOption = 'value2'
    ) {
        parent::__construct([
        'kind' => 'select',
        'attr' => ['name' => $name, 'id' => $name]
        ]);
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
        $optionNode = new HtmlLeaf([
        'kind' => 'option'
        ]);
        foreach ($options as $value => $displayedValue) {
            $this->appendChild(clone $optionNode)->setAttr([
              'value' => $value])->setCont("$displayedValue");
        }

        if (isset($options[$defaultOption])) {
            $offset = ArrayUtils::key2offset($options, $defaultOption);
            $this->childNodes[$offset]->attr['selected'] = 'selected';
            $this->defaultOption = $this->childNodes[$offset];
        }
    }
  
    /**
     * Clear default option.
     * @method clearDefaultOption
     * @return void
     */
    public function clearDefaultOption()
    {
        if ($this->defaultOption and isset($this->defaultOption->attr['selected'])) {
            unset($this->defaultOption->attr['selected']);
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
        $nodes = $this->getNodesByAttrValue(['value' => $value]);
        if (empty($nodes)) {
            return;
        }
        $this->defaultOption = $nodes[0];
        $this->defaultOption->attr['selected'] = 'selected';
    }
}
