<?php namespace BX\Validator\Collection;
use BX\Validator\IValidator;
use Illuminate\Support\MessageBag;

class Setter implements IValidator
{
	/**
	 * @var bool|string
	 */
	protected $new = 'all';
	/**
	 * @var mixed
	 */
	protected $value;
	/**
	 * @var calleble
	 */
	protected $function;
	/**
	 * @var array
	 */
	protected $function_params = [];
	/**
	 * @var array
	 */
	protected $validators = [];
	/**
	 * @var MessageBag
	 */
	private $error = null;
	/**
	 * Create validator
	 * @return self
	 * */
	static public function create()
	{
		return new static();
	}
	/**
	 * Get value by key
	 * @param string $value
	 * @param string $key
	 * @return string|null
	 */
	public function getValueByKey($value,$key)
	{
		return (isset($value[$key])) ? $value[$key] : null;
	}
	/**
	 * Is empty value
	 * @param string $value
	 * @param boolean $trim
	 * @return boolean
	 */
	public function isEmpty($value,$trim = false)
	{
		return $value === null || $value === array() || $value === '' || $trim && is_scalar($value) && trim($value) === '';
	}
	/**
	 * Is new
	 * @return boolean
	 */
	public function isNew()
	{
		return $this->new;
	}
	/**
	 * Has errors
	 * @return array
	 */
	public function hasErrors()
	{
		if ($this->error === null){
			return false;
		}
		return count($this->error->all()) > 0;
	}
	/**
	 * Get errors
	 * @return MessageBag
	 */
	public function getErrors()
	{
		if ($this->error === null){
			throw new \LogicException('Get error before validate');
		}
		return $this->error;
	}
	/**
	 * Add error
	 * @param string $key
	 * @param string $message
	 * @param array $params
	 */
	public function addError($key,$message,$params = [])
	{
		if ($this->error === null){
			$this->error = new MessageBag();
		}
		$this->error->add($this->string()->toUpper($key),(!empty($params)) ? strtr($message,$params) : $message);
	}
	/**
	 * On add
	 * @return \BX\Validator\Collection\Setter
	 */
	public function onAdd()
	{
		$this->new = true;
		return $this;
	}
	/**
	 * On update
	 * @return \BX\Validator\Collection\Setter
	 */
	public function onUpdate()
	{
		$this->new = false;
		return $this;
	}
	/**
	 * Set value
	 * @param string $value
	 * @return \BX\Validator\Collection\Setter
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
	/**
	 * Set function
	 * @param string $function
	 * @param array $function_params
	 * @return \BX\Validator\Collection\Setter
	 */
	public function setFunction($function,$function_params = [])
	{
		$this->function = $function;
		$this->function_params = $function_params;
		return $this;
	}
	/**
	 * Set validators
	 * @param array
	 * @return Setter
	 * */
	public function setValidators($validators)
	{
		foreach($validators as $validator){
			if (!($validator instanceof IValidator)){
				throw new \InvalidArgumentException('Validator must be instance of IValidator');
			}
		}
		$this->validators = $validators;
		return $this;
	}
	/**
	 * Validate field
	 * @param string $key
	 * @param array $fields
	 * @param string $label
	 * @return boolean
	 */
	public function validateField($key,&$fields,$label)
	{
		return $this->hasErrors();
	}
	/**
	 * Set value
	 * @param string $key
	 * @param array $fields
	 * @param string $label
	 * @return boolean
	 * @throws \LogicException
	 */
	public function set($key,&$fields,$label)
	{
		$value = $this->getValueByKey($fields,$key);
		if (isset($this->value)){
			$value = $this->value;
		}elseif (isset($this->function)){
			$value = call_user_func_array($this->function,$this->function_params);
		}else{
			throw new \LogicException("No value for set for param `$key`.");
		}
		if (!$this->isEmpty($value)){
			foreach($this->validators as $validator){
				$return = $validator->validate($key,$value,$label,$fields);
				if ($validator->hasErrors()){
					$this->error->merge($validator->getErrors());
				}
				if (!$return){
					return false;
				}
			}
		}
		$fields[$key] = $value;
		return true;
	}
}