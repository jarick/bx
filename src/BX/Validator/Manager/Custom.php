<?php namespace BX\Validator\Manager;
use BX\Validator\IValidator;
use BX\Validator\Manager\BaseValidator;

class Custom extends BaseValidator implements IValidator
{
	use \BX\String\StringTrait;
	/**
	 * @var \Closure
	 */
	private $function;
	/**
	 * Create
	 * @param \Closure $function
	 * @param string $message
	 * @return self
	 * @throws \InvalidArgumentException
	 */
	public static function create($function)
	{
		$validator = static::getManager();
		if (!is_callable($function)){
			throw new \InvalidArgumentException('Function must be collable');
		}
		$validator->function = $function;
		return $validator;
	}
	/**
	 * Validate
	 * @param string $key
	 * @param string $value
	 * @param string $label
	 * @param array $fields
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function validate($key,$value,$label,&$fields)
	{
		$return = call_user_func_array($this->function,[$value]);
		if ($this->string()->length($return) > 1){
			$this->addError($return);
			return false;
		}
		$fields[$key] = $value;
		return true;
	}
}