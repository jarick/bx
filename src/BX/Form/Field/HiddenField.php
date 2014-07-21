<?php namespace BX\Form\Field;
use BX\Form\Field\BaseField;
use BX\Validator\Collection\StringValidator;

class HiddenField extends BaseField
{
	/**
	 * Init
	 */
	public function init()
	{
		$this->validator = new StringValidator();
	}
	/**
	 * Constructor
	 *
	 * @param string $label
	 * @param string $required
	 */
	public function __construct($label,$required = false)
	{
		parent::__construct($label,$required);
	}
	/**
	 * Print html code of field
	 *
	 * @return string
	 */
	public function renderSingle()
	{
		echo '</label>'
		.'<input type="hidden"'
		.' name="'.$this->string()->escape($this->getFullName()).'"'
		.' id="'.$this->getId().'"'
		.' value="'.$this->string()->escape($this->value).'"'
		.' />';
	}
	/**
	 * Print html code of multy field
	 *
	 * @return string
	 */
	public function renderMulty()
	{
		throw new \RuntimeException('Field is not has multy values');
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