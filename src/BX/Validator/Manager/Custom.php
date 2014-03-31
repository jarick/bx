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
	 * @var string
	 */
	protected $message_empty = null;
	/**
	 * Set empty message
	 * @param string $message
	 * @return \BX\Validator\Manager\Custom
	 */
	public function setMessageEmpty($message)
	{
		$this->message_empty = (string)$message;
		return $this;
	}
	/**
	 * Get empty message
	 * @return string
	 */
	protected function getMessageEmpty()
	{
		$message = $this->message_empty;
		if ($message === null){
			$message = $this->trans('validator.manager.custom.empty');
		}
		return $message;
	}
	/**
	 * Create
	 * @param \Closure $function
	 * @param array $params
	 * @return Custom
	 * @throws \InvalidArgumentException
	 */
	public static function create($function,array $params = [])
	{
		$validator = static::getManager(false,$params);
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
		if (!$this->empty && $this->isEmpty($value)){
			$this->addError($key,$this->getMessageEmpty(),['#LABEL#' => $label]);
			return false;
		}
		$return = call_user_func_array($this->function,[&$value]);
		if ($this->string()->length($return) > 1){
			$this->addError($key,$return);
			return false;
		}
		$fields[$key] = $value;
		return true;
	}
}