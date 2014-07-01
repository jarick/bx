<?php namespace BX\Validator\Collection;

class BooleanValidator extends BaseValidator
{
	use \BX\String\StringTrait,
	 \BX\Translate\TranslateTrait;
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
	protected $message_invalid = null;
	/**
	 * @var string
	 */
	protected $message_empty = null;
	/**
	 * Return message empty
	 *
	 * @return string
	 */
	protected function getMessageEmpty()
	{
		$message = $this->message_empty;
		if ($message === null){
			$message = $this->trans('validator.collection.boolean.empty');
		}
		return $message;
	}
	/**
	 * Set message empty
	 *
	 * @param string $message_empty
	 * @return \BX\Validator\Collection\BooleanValidator
	 */
	public function setMessageEmpty($message_empty)
	{
		$this->message_empty = $message_empty;
		return $this;
	}
	/**
	 * Set message invalid
	 *
	 * @param string $message_invalid
	 * @return \BX\Validator\Collection\Boolean
	 */
	public function setMessageInvalid($message_invalid)
	{
		$this->message_invalid = $message_invalid;
		return $this;
	}
	/**
	 * Return message invalid
	 *
	 * @return string
	 */
	protected function getMessageInvalid()
	{
		$message = $this->message_invalid;
		if ($message === null){
			$message = $this->trans('validator.collection.boolean.invalid');
		}
		return $message;
	}
	/**
	 * Set value
	 *
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
	 *
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
		if (is_array($value) || is_object($value)){
			$this->addError($key,$this->getMessageInvalid(),['#LABEL#' => $label]);
			return false;
		}
		if ($this->isEmpty($value)){
			if (!$this->empty){
				$this->addError($key,$this->getMessageEmpty(),['#LABEL#' => $label]);
				return false;
			}else{
				return true;
			}
		}
		if ($this->strict){
			if ($value !== $this->true_value && $value !== $this->false_value){
				$this->addError($key,$this->getMessageInvalid(),[
					'#LABEL#'	 => $label,
					'#TRUE#'	 => $this->true_value,
					'#FALSE#'	 => $this->false_value,
				]);
				return false;
			}
		}else{
			if ($value != $this->true_value && $value != $this->false_value){
				$this->addError($key,$this->getMessageInvalid(),[
					'#LABEL#'	 => $label,
					'#TRUE#'	 => $this->true_value,
					'#FALSE#'	 => $this->false_value,
				]);
				return false;
			}
		}
		return true;
	}
}