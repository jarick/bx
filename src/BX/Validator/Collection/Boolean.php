<?php namespace BX\Validator\Collection;

class Boolean extends BaseValidator
{
	/**
	 * @var mixed
	 */
	protected $true_value = 'Y';
	/**
	 * @var mixed
	 */
	protected $false_value = 'N';
	/**
	 * @var boolean
	 */
	protected $strict = false;
	/**
	 * @var string
	 */
	protected $message_invalid = false;
	/**
	 * Set message invalid
	 * @param string $message_invalid
	 * @return \BX\Validator\Collection\Boolean
	 */
	public function setMessageInvalid($message_invalid)
	{
		$this->message_invalid = $message_invalid;
		return $this;
	}
	/**
	 * Get message invalid
	 * @return string
	 */
	protected function getMessageInvalid()
	{
		$sMessage = $this->message_invalid;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.boolean.invalid');
		}
		return $sMessage;
	}
	/**
	 * Set value
	 * @param mixed $true_value
	 * @param mixed $false_value
	 * @return \BX\Validator\Collection\Boolean
	 */
	public function setValue($true_value,$false_value)
	{
		$this->true_value = $true_value;
		$this->false_value = $false_value;
		return $this;
	}
	/**
	 * Set strict
	 * @param boolean $strict
	 * @return \BX\Validator\Collection\Boolean
	 */
	public function strict($strict = true)
	{
		$this->strict = (bool)$strict;
		return $this;
	}
	/**
	 * Validate
	 * @param string $key
	 * @param string $value
	 * @param string $label
	 * @param array $fields
	 * @return boolean
	 */
	public function validate($key,$value,$label,&$fields)
	{
		$value = $this->getValueByKey($fields,$key);
		if ($this->empty && $this->isEmpty($value)){
			return true;
		}
		if ((!$this->strict && $value != $this->true_value && $value != $this->false_value) || ($this->strict && $value !== $this->true_value && $value !== $this->false_value)){
			$this->addError($key,$this->getMessageInvalid(),[
				'#LABEL#'	 => $label,
				'#TRUE#'	 => $this->true_value,
				'#FALSE#'	 => $this->false_value,
			]);
			return false;
		}
		return true;
	}
}