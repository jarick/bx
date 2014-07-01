<?php namespace BX\Validator\Helper;
use BX\Validator\Collection\BooleanValidator;
use BX\Validator\Collection\CustomValidator;
use BX\Validator\Collection\DateTimeValidator;
use BX\Validator\Collection\DateTimeFilterValidator;
use BX\Validator\Collection\File;
use BX\Validator\Collection\MultyValidator;
use BX\Validator\Collection\NumberValidator;
use BX\Validator\Collection\SafeValidator;
use BX\Validator\Collection\Setter;
use BX\Validator\Collection\StringValidator;
use BX\Validator\IValidator;

class RuleHelper
{
	/**
	 * Return boolean validator
	 *
	 * @return BooleanValidator
	 */
	public function boolean()
	{
		return BooleanValidator::create();
	}
	/**
	 * Return custom validator
	 *
	 * @param string $function
	 * @return CustomValidator
	 */
	public function custom($function)
	{
		return CustomValidator::create($function);
	}
	/**
	 * Return date validator
	 *
	 * @return DateTimeValidator
	 */
	public function date()
	{
		return DateTimeValidator::create();
	}
	/**
	 * Return datetime filter validator
	 *
	 * @return DateTimeValidator
	 */
	public function datetime_filter()
	{
		return DateTimeFilterValidator::create();
	}
	/**
	 * Return datetime validator
	 *
	 * @return DateTimeValidator
	 */
	public function datetime()
	{
		return DateTimeValidator::create()->withTime();
	}
	/**
	 * Return multy validator
	 *
	 * @param IValidator $validator
	 * @return MultyValidator
	 */
	public function multy(IValidator $validator)
	{
		return MultyValidator::create($validator);
	}
	/**
	 * Get number validator
	 *
	 * @return NumberValidator
	 */
	public function number()
	{
		return NumberValidator::create();
	}
	/**
	 * Return safe validator
	 *
	 * @return SafeValidator
	 */
	public function safe()
	{
		return SafeValidator::create();
	}
	/**
	 * Return setter
	 *
	 * @return Setter
	 */
	public function setter()
	{
		return Setter::create();
	}
	/**
	 * Return string validator
	 *
	 * @return StringValidator
	 */
	public function string()
	{
		return StringValidator::create();
	}
	/**
	 * Return file validator
	 *
	 * @return File
	 */
	public function file()
	{
		return File::create();
	}
}