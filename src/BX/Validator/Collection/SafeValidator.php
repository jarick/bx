<?php namespace BX\Validator\Collection;

class SafeValidator extends BaseValidator
{
	private $security = false;
	public function setSecurity($security = true)
	{
		$this->security = (bool)$security;
		return this;
	}
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
		if ($this->security){
			$fields['~'.$key] = $value;
			unset($fields[$key]);
		}else{
			return true;
		}
	}
}