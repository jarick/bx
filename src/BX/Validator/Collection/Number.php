<?php namespace BX\Validator\Collection;

class Number extends BaseValidator
{
	use \BX\String\StringTrait,
	 \BX\Translate\TranslateTrait;
	/**
	 * @var boolean
	 */
	protected $integer_only = false;
	/**
	 * @var string
	 */
	protected $integer_pattern = '/^\s*[+-]?\d+\s*$/';
	/**
	 * @var string
	 */
	protected $number_pattern = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';
	/**
	 * @var float
	 */
	protected $min = null;
	/**
	 * @var float
	 */
	protected $max = null;
	/**
	 * @var string
	 */
	protected $message_invalid = false;
	/**
	 * @var string
	 */
	protected $message_empty = false;
	/**
	 * @var string
	 */
	protected $message_integer = false;
	/**
	 * @var string
	 */
	protected $message_number = false;
	/**
	 * @var string
	 */
	protected $message_min = false;
	/**
	 * @var string
	 */
	protected $message_max = false;
	/**
	 * Set message invalid
	 *
	 * @param string $message_invalid
	 * @return Number
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
	public function getMessageInvalid()
	{
		$message = $this->message_invalid;
		if ($message === false){
			$message = $this->trans('validator.manager.number.invalid');
		}
		return $message;
	}
	/**
	 * Set message integer
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\Number
	 */
	public function setMessageInteger($message)
	{
		$this->message_integer = $message;
		return $this;
	}
	/**
	 * Return message integer
	 *
	 * @return string
	 */
	public function getMessageInteger()
	{
		$message = $this->message_integer;
		if ($message === false){
			$message = $this->trans('validator.manager.number.integer');
		}
		return $message;
	}
	/**
	 * Set message number
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\Number
	 */
	public function setMessageNumber($message)
	{
		$this->message_number = $message;
		return $this;
	}
	/**
	 * Return message number
	 *
	 * @return string
	 */
	public function getMessageNumber()
	{
		$message = $this->message_number;
		if ($message === false){
			$message = $this->trans('validator.manager.number.number');
		}
		return $message;
	}
	/**
	 * Set message empty
	 *
	 * @param string $message_empty
	 * @return \BX\Validator\Collection\Number
	 */
	public function setMessageEmpty($message_empty)
	{
		$this->message_empty = $message_empty;
		return $this;
	}
	/**
	 * Return message empty
	 *
	 * @return string
	 */
	public function getMessageEmpty()
	{
		$message = $this->message_empty;
		if ($message === false){
			$message = $this->trans('validator.manager.number.empty');
		}
		return $message;
	}
	/**
	 * Set message min
	 *
	 * @param string $message_min
	 * @return \BX\Validator\Collection\Number
	 */
	public function setMessageMin($message_min)
	{
		$this->message_min = $message_min;
		return $this;
	}
	/**
	 * Return message min
	 *
	 * @return string
	 */
	public function getMessageMin()
	{
		$message = $this->message_min;
		if ($message === false){
			$message = $this->trans('validator.manager.number.min');
		}
		return $message;
	}
	/**
	 * Set message max
	 *
	 * @param string $message_max
	 * @return \BX\Validator\Collection\Number
	 */
	public function setMessageMax($message_max)
	{
		$this->message_max = $message_max;
		return $this;
	}
	/**
	 * Return message max
	 *
	 * @return string
	 */
	public function getMessageMax()
	{
		$message = $this->message_max;
		if ($message === false){
			$message = $this->trans('validator.manager.number.max');
		}
		return $message;
	}
	/**
	 * Set is not empty
	 *
	 * @return \BX\Validator\Collection\Number
	 */
	public function notEmpty()
	{
		$this->empty = false;
		return $this;
	}
	/**
	 * Set min value
	 *
	 * @param float $min
	 * @return \BX\Validator\Collection\Number
	 */
	public function setMin($min)
	{
		$this->min = $min;
		return $this;
	}
	/**
	 * Set max value
	 *
	 * @param float $max
	 * @return \BX\Validator\Collection\Number
	 */
	public function setMax($max)
	{
		$this->max = $max;
		return $this;
	}
	/**
	 * Set value is integer
	 *
	 * @return \BX\Validator\Collection\Number
	 */
	public function integer()
	{
		$this->integer_only = true;
		return $this;
	}
	/**
	 * Set regex for validate integer value
	 *
	 * @param string $pattern
	 * @return \BX\Validator\Collection\Number
	 */
	public function setIntegerPattern($pattern)
	{
		$this->integer_pattern = $pattern;
		return $this;
	}
	/**
	 * Set regex for validate float value
	 *
	 * @param string $pattern
	 * @return \BX\Validator\Collection\Number
	 */
	public function setNumberPattern($pattern)
	{
		$this->number_pattern = $pattern;
		return $this;
	}
	/**
	 * Validate formate value
	 *
	 * @param string $key
	 * @param string $value
	 * @param string $label
	 * @return boolean
	 */
	public function validateFormate($key,$value,$label)
	{
		if (!is_numeric($value)){
			$this->addError($key,$this->getMessageInvalid(),[
				'#LABEL#' => $label,
			]);
			return false;
		}
		if ($this->integer_only){
			if (!preg_match($this->integer_pattern,"$value")){
				$this->addError($key,$this->getMessageInteger(),[
					'#LABEL#' => $label,
				]);
				return false;
			}
		}else{
			if (!preg_match($this->number_pattern,"$value")){
				$this->addError($key,$this->getMessageNumber(),[
					'#LABEL#' => $label,
				]);
				return false;
			}
		}
		return true;
	}
	/**
	 * Validate value
	 *
	 * @param string $key
	 * @param string $value
	 * @param string $label
	 * @param array $fields
	 * @return boolean
	 */
	public function validate($key,$value,$label,&$fields)
	{
		if ($this->isEmpty($value)){
			if (!$this->empty){
				$this->addError($key,$this->getMessageEmpty(),[
					'#LABEL#' => $label,
				]);
			}else{
				return true;
			}
		}
		if (!$this->validateFormate($key,$value,$label)){
			return false;
		}
		if ($this->min !== null && $value < $this->min){
			$this->addError($key,$this->getMessageMin(),[
				'#LABEL#'	 => $label,
				'#MIN#'		 => $this->min,
			]);
			return false;
		}
		if ($this->max !== null && $value > $this->max){
			$this->addError($key,$this->getMessageMax(),[
				'#LABEL#'	 => $label,
				'#MAX#'		 => $this->max,
			]);
			return false;
		}
		return true;
	}
}