<?php
namespace Simphotonics\Dom;

use Simphotonics\Dom\HtmlLeaf as Leaf;
use Simphotonics\Dom\HtmlNode as Node;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Simphotonics\HtmlForm is used to generate HTML forms.
 * Extends: @see \Simphotonics\Dom\HtmlLeaf
 */
class HtmlForm extends HtmlNode
{
    protected $model = null;  // Model used to populate forms.

    /**
     * Constructors an HtmlForm object.
     * @method  __construct
     * @param   array        $params  Array of the form:
     * ['action' => url, 'model' => model]
     */
    public function __construct($action = 'Warning: Form action not set!')
    {
        parent::__construct([
            'kind' => 'form',
            'attr' => [
                'id' => $this->id,
                'method' => 'post',
                'action' => $action,
            ],
        ]);
    }
       
    /**
     * Appends hidden input field containing CSRF token.
     * @method  setCsrfToken
     * @param   int        $token
     */
    public function setCsrfToken($token)
    {
      // Generate hidden input fields
        $nodes[] = new Leaf([
            'kind' => 'input',
            'attr' => [
            'type' => 'hidden',
            'name' => 'id',
            'value' => $this->id]
        ]);
        $nodes[] = new Leaf([
            'kind' => 'input',
            'attr' => [
            'type' => 'hidden',
            'name' => '_token',
            'id' => 'token'.$this->uid,
            'value' =>  $token]
            ]);
        $this->append($nodes);
    }
  

  /**
  * Get the model value that should be assigned to the field.
  *
  * @param  string  $name
  * @return string
  */
    protected function getModelValueAttr($name, $default = null)
    {

        return (is_object($this->model)
        and isset($this->model->{$name})) ? $this->model->{$name} : $default;

    }
}
