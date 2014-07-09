<?php namespace BX\Form\Field;
use BX\Form\Field\BaseField;
use BX\Validator\Collection\BooleanValidator;

class CheckboxField extends BaseField
{
	/**
	 * @var string
	 */
	private $false_value = 'N';
	/**
	 * @var integer
	 */
	private $true_value = 'Y';
	/**
	 * @var boolean
	 */
	protected $strict = false;
	/**
	 * Set true value
	 *
	 * @param string $true_value
	 * @return \BX\Validator\Collection\BooleanValidator
	 */
	public function setTrueValue($true_value = 'Y')
	{
		$this->true_value = $true_value;
		return $this;
	}
	/**
	 * Return true value
	 *
	 * @return string
	 */
	public function getTrueValue()
	{
		return $this->true_value;
	}
	/**
	 * Set false value
	 *
	 * @param string $false_value
	 * @return \BX\Validator\Collection\BooleanValidator
	 */
	public function setFalseValue($false_value = 'N')
	{
		$this->false_value = $false_value;
		return $this;
	}
	/**
	 * Return false value
	 *
	 * @return string
	 */
	public function getFalseValue()
	{
		return $this->false_value;
	}
	/**
	 * Set is strict value
	 *
	 * @param boolean $strict
	 * @return \BX\Validator\Collection\Boolean
	 */
	public function strict($strict = true)
	{
		$this->strict = (bool)$strict;
		return $this;
	}
	/**
	 * Init
	 */
	public function init()
	{
		$this->validator = new BooleanValidator();
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
		echo '"><div class="checkbox">';
		echo '<label class="control-label" for="'.$this->getId().'">'
		.$this->string()->escape($this->label);
		if ($this->required){
			echo '<span class="text-red">*</span>';
		}
		echo '</label>'
		.'<input type="hidden"'
		.' name="'.$this->string()->escape($this->getFullName()).'"'
		.' value="'.$this->string()->escape($this->none_value).'"'
		.' />'
		.'<input type="checkbox" class="form-control"'
		.' name="'.$this->string()->escape($this->getFullName()).'"'
		.' id="'.$this->getId().'"'
		.' tabindex='.$this->tabindex
		.' placeholder="'.$placeholder.'"';
		if ($this->value == $this->check_value){
			'checked';
		}
		echo ' value="'.$this->string()->escape($this->check_value).'"'
		.' />'
		.'</div></div>';
	}
	/**
	 * Print html code of multy field
	 *
	 * @return string
	 */
	public function renderMulty()
	{
		throw new \RuntimeException('Function in development');
	}
	/**
	 * Validate value
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function validate(array &$data)
	{
		$this->validator->setValue($this->true_value,$this->false_value);
		$this->validator->strict($this->strict);
		return parent::validate($data);
	}
}