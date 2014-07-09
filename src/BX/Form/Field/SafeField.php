<?php namespace BX\Form\Field;
use BX\Form\Field\BaseField;
use \BX\Validator\Collection\Safe;

class SafeField extends BaseField
{
	/**
	 * Init
	 */
	public function init()
	{
		$this->validator = new Safe();
	}
	/**
	 * Constructor
	 *
	 * @param string $label
	 */
	public function __construct($label)
	{
		parent::__construct($label,false);
	}
	/**
	 * Print html code of field
	 *
	 * @return string
	 */
	public function renderSingle()
	{

	}
	/**
	 * Print html code of field
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
		return parent::validate($data);
	}
}