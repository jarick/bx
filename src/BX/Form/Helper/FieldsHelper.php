<?php namespace BX\Form\Helper;
use BX\Form\Field\TextField;
use BX\Form\Field\NumberField;
use BX\Form\Field\SessidField;
use BX\Form\Field\DateField;
use BX\Form\Field\CheckboxField;
use BX\Form\Field\SelectboxField;
use BX\Form\Field\HiddenField;

class FieldsHelper
{
	/**
	 * Return text input
	 *
	 * @param boolean $required
	 * @param integer $min
	 * @param integer $max
	 */
	public function text($required = false,$min = null,$max = null)
	{
		return new TextField(null,$required,$max,$min);
	}
	/**
	 * Return number input
	 *
	 * @param boolean $required
	 * @param float $min
	 * @param float $max
	 */
	public function number($required = false,$min = null,$max = null)
	{
		return new NumberField(null,$required);
	}
	/**
	 * Return datetime input
	 *
	 * @param type $required
	 * @param type $format
	 * @return \BX\Form\Field\DateField
	 */
	public function data($required = false,$format = 'full')
	{
		return new DateField(null,$required,$format);
	}
	/**
	 * Return checkbox input
	 *
	 * @param boolean $required
	 * @return CheckboxField
	 */
	public function checkbox($required = false)
	{
		return new CheckboxField(null,$required);
	}
	/**
	 *
	 * @param type $enums
	 * @param type $required
	 * @return \BX\Form\Field\SelectboxField
	 */
	public function selectbox($enums,$required = false)
	{
		return new SelectboxField(null,$required,$enums);
	}
	/**
	 * Return hidden input
	 *
	 * @param boolean $required
	 * @return \BX\Form\Field\HiddenField
	 */
	public function hidden($required = false)
	{
		return new HiddenField(null,$required);
	}
	/**
	 * Return sessid input
	 *
	 * @return \BX\Form\Field\SessidField
	 */
	public function sessid()
	{
		return new SessidField();
	}
}