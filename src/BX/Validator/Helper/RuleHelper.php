<?php namespace BX\Validator\Helper;
use BX\Validator\Collection\Boolean;
use BX\Validator\Collection\Custom;
use BX\Validator\Collection\DateTime;
use BX\Validator\Collection\File;
use BX\Validator\Collection\Multy;
use BX\Validator\Collection\Number;
use BX\Validator\Collection\Safe;
use BX\Validator\Collection\Setter;
use BX\Validator\Collection\String;
use BX\Validator\IValidator;

class RuleHelper
{
	/**
	 * Get boolean validator
	 *
	 * @return Boolean
	 */
	public function boolean()
	{
		return Boolean::create();
	}
	/**
	 * Get custom validator
	 *
	 * @param string $function
	 * @return Custom
	 */
	public function custom($function)
	{
		return Custom::create($function);
	}
	/**
	 * Get date validator
	 *
	 * @return DateTime
	 */
	public function date()
	{
		return DateTime::create();
	}
	/**
	 * Get datetime validator
	 *
	 * @return DateTime
	 */
	public function datetime()
	{
		return DateTime::create()->withTime();
	}
	/**
	 * Get multy validator
	 *
	 * @param IValidator $validator
	 * @return Multy
	 */
	public function multy(IValidator $validator)
	{
		return Multy::create($validator);
	}
	/**
	 * Get number validator
	 *
	 * @return Number
	 */
	public function number()
	{
		return Number::create();
	}
	/**
	 * Get safe validator
	 *
	 * @return Safe
	 */
	public function safe()
	{
		return Safe::create();
	}
	/**
	 * Get setter
	 *
	 * @return Setter
	 */
	public function setter()
	{
		return Setter::create();
	}
	/**
	 * Get string validator
	 *
	 * @return String
	 */
	public function string()
	{
		return String::create();
	}
	/**
	 * Get file validator
	 *
	 * @return File
	 */
	public function file()
	{
		return File::create();
	}
}