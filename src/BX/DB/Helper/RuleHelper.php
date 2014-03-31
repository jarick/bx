<?php namespace BX\DB\Helper;
use BX\Base;
use BX\Validator\IValidator;
use BX\Validator\Manager\Boolean;
use BX\Validator\Manager\Custom;
use BX\Validator\Manager\DateTime;
use BX\Validator\Manager\Multy;
use BX\Validator\Manager\Number;
use BX\Validator\Manager\Safe;
use BX\Validator\Manager\Setter;
use BX\Validator\Manager\String;

class RuleHelper extends Base
{
	/**
	 * Get boolean validator
	 * @param array $params
	 * @return Boolean
	 */
	public function boolean(array $params = [])
	{
		return Boolean::create($params);
	}
	/**
	 * Get custom validator
	 * @param string $function
	 * @param array $params
	 * @return Custom
	 */
	public function custom($function,array $params = [])
	{
		return Custom::create($function,$params);
	}
	/**
	 * Get datetime validator
	 * @param array $params
	 * @return DateTime
	 */
	public function datetime(array $params = [])
	{
		return DateTime::create($params);
	}
	/**
	 * Get multy validator
	 * @param IValidator $validator
	 * @param array $params
	 * @return Multy
	 */
	public function multy(IValidator $validator,array $params = [])
	{
		return Multy::create($validator,$params);
	}
	/**
	 * Get number validator
	 * @param array $params
	 * @return Number
	 */
	public function number(array $params = [])
	{
		return Number::create($params);
	}
	/**
	 * Get safe validator
	 * @param array $params
	 * @return Safe
	 */
	public function safe(array $params = [])
	{
		return Safe::create($params);
	}
	/**
	 * Get setter
	 * @param array $params
	 * @return Setter
	 */
	public function setter(array $params = [])
	{
		return Setter::create($params);
	}
	/**
	 * Get string validator
	 * @param array $params
	 * @return String
	 */
	public function string(array $params = [])
	{
		return String::create($params);
	}
}