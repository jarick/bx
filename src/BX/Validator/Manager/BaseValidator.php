<?php namespace BX\Validator\Manager;
use BX\Validator\IValidator;
use BX\Manager;

abstract class BaseValidator extends Manager implements IValidator
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
	 * @var array
	 */
	private $error = [];
	/**
	 * Set default value
	 * @param string $default
	 */
	public function setDefault($default)
	{
		$this->default = $default;
	}
	/**
	 * @return self
	 * */
	static public function create()
	{
		return static::getManager();
	}
	/**
	 * Validate if add value
	 * @return \BX\Validator\Manager\BaseValidator
	 */
	public function onAdd()
	{
		$this->new = true;
		return $this;
	}
	/**
	 * Validate if update value
	 * @return \BX\Validator\Manager\BaseValidator
	 */
	public function onUpdate()
	{
		$this->new = false;
		return $this;
	}
	/**
	 * Disable empty value
	 * @return \BX\Validator\Manager\BaseValidator
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
	 * Reset errors
	 */
	public function resetErrors()
	{
		$this->error = [];
	}
	/**
	 * Get errors
	 * @return array
	 */
	public function getErrors()
	{
		return $this->error;
	}
	/**
	 * Add error
	 * @param string $message
	 * @param array $params
	 */
	protected function addError($message,$params = [])
	{
		$this->error[] = (!empty($params)) ? strtr($message,$params) : $message;
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
		$value = $this->getValueByKey($fields,$key);
		if ($this->isEmpty($value)){
			$fields[$key] = $this->default;
			if ($this->empty){
				return true;
			} else{
				$value = $fields[$key];
			}
		}
		return $this->validate($key,$value,$label,$fields);
	}
	abstract public function validate($key,$value,$label,&$fields);
}