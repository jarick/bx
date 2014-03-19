<?php
namespace BX\Validator\Manager;
use BX\Manager;
use BX\Validator\IValidator;

class Setter extends Manager implements IValidator
{
	protected $new = 'all';
	protected $value;
	protected $function;
	protected $function_params = [];
	protected $validator = false;

	public static function create()
	{
		return new self();
	}
	public function getValueByKey($value,$key)
	{
		return (isset($value[$key])) ? $value[$key] : null;
	}

	public function isEmpty($value,$trim=false)
	{
		return $value===null || $value===array() || $value==='' || $trim && is_scalar($value) && trim($value)==='';
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
	protected function addError($message, $params = [])
	{
		$this->error[] = (!empty($params)) ? strtr($message, $params) : $message;
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

	public function setValue($sValue)
	{
		$this->value = $sValue;
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
	 **/
	public function setValidator(IValidator $validator)
	{
		$this->validator = $validator;
		return $this;
	}
	
	public function validateField($key,&$fields,$label)
	{
		return empty($this->error);
	}

	public function set($key,&$fields,$label)
	{
		$value = $this->getValueByKey($fields,$key);
		if(isset($this->value)){
			$value = $this->value;
		} elseif(isset($this->function)){
			$value = call_user_func_array($this->function, $this->function_params);
		} else{
			throw new \LogicException("No value for set for param `$key`.");
		}
		if(!$this->isEmpty($value)){
			if($this->validator !== false){
				if(!$this->validator->validate($key, $value, $label, $fields)){
					foreach($this->validator->getErrors() as $error){
						$this->addError($error);
					}
					return false;
				}
			}
		}
		$fields[$key] = $value;
		return true;
	}
}