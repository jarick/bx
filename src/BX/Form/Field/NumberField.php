<?php namespace BX\Form\Field;
use BX\Form\Field\BaseField;
use BX\Validator\Collection\NumberValidator;

class NumberField extends BaseField
{
	/**
	 * @var float
	 */
	protected $min = null;
	/**
	 * @var float
	 */
	protected $max = null;
	/**
	 * Init
	 */
	public function init()
	{
		$this->validator = new NumberValidator();
	}
	/**
	 * Set max value
	 *
	 * @param float $max
	 * @return \BX\Form\Field\NumberField
	 */
	public function setMax($max)
	{
		$this->max = $max;
		return $this;
	}
	/**
	 * Return max value
	 *
	 * @return float
	 */
	public function getMax()
	{
		return $this->max;
	}
	/**
	 * Set min value
	 *
	 * @param float $min
	 * @return \BX\Form\Field\NumberField
	 */
	public function setMin($min)
	{
		$this->min = $min;
		return $this;
	}
	/**
	 * Return min value
	 *
	 * @return float
	 */
	public function getMin()
	{
		return $this->min;
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
		.'<input type="number" class="form-control"'
		.' name="'.$this->string()->escape($this->getFullName()).'"'
		.' id="'.$this->getId().'"'
		.' tabindex="'.$this->tabindex.'"'
		.' placeholder="'.$placeholder.'"'
		.' value="'.$this->string()->escape($this->value).'"'
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
		throw new \RuntimeException('Function in development');
	}
}