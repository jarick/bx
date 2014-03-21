<?php namespace BX\Validator\Manager;

class Safe extends BaseValidator
{
	/**
	 * Validate
	 * @param string $key
	 * @param string $value
	 * @param string $label
	 * @param string $fields
	 * @return boolean
	 */
	public function validate($key,$value,$label,&$fields)
	{
		return true;
	}
}