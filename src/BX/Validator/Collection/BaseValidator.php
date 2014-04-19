<?php namespace BX\Validator\Collection;
use BX\Validator\IValidator;
use Illuminate\Support\MessageBag;

abstract class BaseValidator implements IValidator
{
	/**
	 * @var string
	 */
	protected $default = null;
	/**
	 * @var string|array
	 */
	protected $new = 'all';
	/**
	 * @var boolean
	 */
	protected $empty = true;
	/**
	 * @var MessageBag
	 */
	private $error = null;
	/**
	 * Set default value
	 * @param string $default
	 * @return \BX\Validator\Collection\BaseValidator
	 */
	public function setDefault($default)
	{
		$this->default = $default;
		return $this;
	}
	/**
	 * Create validator
	 * @return self
	 * */
	public static function create()
	{
		return new static();
	}
	/**
	 * Validate if add value
	 * @return \BX\Validator\Collection\BaseValidator
	 */
	public function onAdd()
	{
		$this->new = true;
		return $this;
	}
	/**
	 * Validate if update value
	 * @return \BX\Validator\Collection\BaseValidator
	 */
	public function onUpdate()
	{
		$this->new = false;
		return $this;
	}
	/**
	 * Disable empty value
	 * @return \BX\Validator\Collection\BaseValidator
	 */
	public function notEmpty()
	{
		$this->empty = false;
		return $this;
	}
	/**
	 * Get value by key
	 * @param string $value
	 * @param string $key
	 * @return null|string
	 */
	public function getValueByKey($value,$key)
	{
		return (isset($value[$key])) ? $value[$key] : null;
	}
	/**
	 * Is empty
	 * @param string $value
	 * @param boolean $trim
	 * @return bollean
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
	 * Validate field
	 * @param string $key
	 * @param array $fields
	 * @param string $label
	 * @return boolean
	 */
	public function validateField($key,&$fields,$label)
	{
		$this->error = new MessageBag();
		$value = $this->getValueByKey($fields,$key);
		if ($this->isEmpty($value)){
			$fields[$key] = $this->default;
			if ($this->empty){
				return true;
			}else{
				$value = $fields[$key];
			}
		}
		return $this->validate($key,$value,$label,$fields);
	}
	abstract public function validate($key,$value,$label,&$fields);
}