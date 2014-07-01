<?php namespace BX\Form\Field;
use BX\Form\Field\BaseField;
use BX\Validator\Collection\StringValidator;

class TextField extends BaseField
{
	/**
	 * Init
	 */
	public function init()
	{
		$this->validator = new StringValidator();
	}
	/**
	 * @var integer
	 */
	protected $max;
	/**
	 * @var integer
	 */
	protected $min;
	/**
	 * Constructor
	 *
	 * @param string $label
	 * @param string $required
	 * @param integer $max
	 */
	public function __construct($label,$required = false,$max = null,$min = null)
	{
		parent::__construct($label,$required);
		$this->max = $max;
		$this->min = $min;
	}
	/**
	 * Set max lenght
	 *
	 * @param integer $max
	 * @return \BX\Form\Field\TextField
	 */
	public function setMax($max)
	{
		$this->max = intval($max);
		return $this;
	}
	/**
	 * Set min lenght
	 *
	 * @param integer $min
	 * @return \BX\Form\Field\TextField
	 */
	public function setMin($min)
	{
		$this->min = intval($min);
		return $this;
	}
	/**
	 * Print html code of field
	 *
	 * @return string
	 */
	public function renderSingle($css_class = '',$placeholder = '')
	{
		echo '<div class="form-group '.$css_class;
		if ($this->hasErrors()){
			echo ' has-error';
		}
		echo '">';
		echo '<label class="control-label" for="'.$this->getId().'">'
		.$this->string()->escape($this->label);
		if ($this->required){
			echo '<span class="text-red">*</span>';
		}
		echo '</label>'
		.'<input type="text" class="form-control"'
		.' name="'.$this->string()->escape($this->name).'"'
		.' id="'.$this->getId().'"'
		.' tabindex='.$this->tabindex
		.' placeholder="'.$placeholder.'"';
		if ($this->max > 0){
			echo ' maxlength="'.$this->max.'"';
		}
		echo ' value="'.$this->string()->escape($this->value).'"'
		.' />';
		echo '</div>';
	}
	/**
	 * Print html code of multy field
	 *
	 * @return string
	 */
	public function renderMulty()
	{

	}
	/**
	 * Validate value
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function validate(array &$data)
	{
		if ($this->max !== null){
			$this->validator->setMax($this->max);
		}
		if ($this->min !== null){
			$this->validator->setMin($this->min);
		}
		return parent::validate($data);
	}
}