<?php namespace BX\Validator\Manager;
use BX\Manager;
use BX\Validator\IValidator;
use Illuminate\Support\MessageBag;

class Setter extends Manager implements IValidator
{
	protected $new = 'all';
	protected $value;
	protected $function;
	protected $function_params = [];
	protected $validator = false;
	/**
	 * @var MessageBag
	 */
	private $error = null;
	/**
	 * Create
	 * @return Setter
	 */
	public static function create(array $params = [])
	{
		return static::getManager(false,$params);
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
	public function onAdd()
	{
		$this->new = true;
		return $this;
	}
	public function onUpdate()
	{
		$this->new = false;
		return $this;
	}
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
	public function setFunction($oFunction,$aFunctionParams = [])
	{
		$this->function = $oFunction;
		$this->function_params = $aFunctionParams;
		return $this;
	}
	/**
	 * @var IValidator
	 * */
	public function setValidator(IValidator $validator)
	{
		$this->validator = $validator;
		return $this;
	}
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
		} elseif (isset($this->function)){
			$value = call_user_func_array($this->function,$this->function_params);
		} else{
			throw new \LogicException("No value for set for param `$key`.");
		}
		if (!$this->isEmpty($value)){
			if ($this->validator !== false){
				if (!$this->validator->validate($key,$value,$label,$fields)){
					if ($this->validator->hasErrors()){
						$this->error->merge($this->validator->getErrors());
					}
					return false;
				}
			}
		}
		$fields[$key] = $value;
		return true;
	}
}