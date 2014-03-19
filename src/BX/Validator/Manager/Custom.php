<?php namespace BX\Validator\Manager;
use BX\Validator\IValidator;
use BX\Validator\Manager\BaseValidator;

class Custom extends BaseValidator implements IValidator
{
	use \BX\String\StringTrait;
	/**
	 * @var string
	 */
	private $message;
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
	public static function create($function,$message)
	{
		$validator = static::getManager();
		if (!is_callable($function)){
			throw new \InvalidArgumentException('Function must be collable');
		}
		$validator->message = $message;
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
		if ($this->string()->length($this->message) === 0){
			throw new \InvalidArgumentException('Is not set message');
		}
		if (call_user_func_array($this->function,[$value]) !== true){
			$this->addError($this->message);
			return false;
		}
		return true;
	}
}
