<?php
namespace BX\Validator\Manager;

class DateTime extends BaseValidator
{
	use \BX\Date\DateTrait;
	/**
	 * Is empty
	 * @var boolean
	 */
	protected $empty = true;
	/**
	 * Format input
	 * @var string
	 */
	protected $format_input = 'short';
	/**
	 * Format rules
	 * @var string
	 */
	protected $format_rules = 'short';
	/**
	 * Min value for input date
	 * @var string
	 */
	protected $min = null;
	/**
	 * Max value for input date
	 * @var string
	 */
	protected $max = null;
	/**
	 * Message invalid
	 * @var string
	 */
	protected $message_invalid = false;
	/**
	 * Message empty
	 * @var string
	 */
	protected $message_empty = false;
	/**
	 * Message min
	 * @var string
	 */
	protected $message_min = false;
	/**
	 * Message max
	 * @var string
	 */
	protected $message_max = false;
	/**
	 * Set message invalid
	 * @param string $message
	 * @return \BX\Validator\Manager\DateTime
	 */
	public function setMessageInvalid($message)
	{
		$this->message_invalid = $message;
		return $this;
	}
	/**
	 * Get message invalid
	 * @return type
	 */
	public function getMessageInvalid()
	{
		$message = $this->message_invalid;
		if($message === false){
			$message = $this->trans('validator.manager.date.invalid');
		}
		return $message;
	}
	/**
	 * Set message empty
	 * @param string $message
	 * @return \BX\Validator\Manager\DateTime
	 */
	public function setMessageEmpty($message)
	{
		$this->message_empty = $message;
		return $this;
	}
	/**
	 * Get message empty
	 * @return string
	 */
	public function getMessageEmpty()
	{
		$message = $this->message_empty;
		if($message === false){
			$message = $this->trans('validator.manager.date.empty');
		}
		return $message;
	}
	/**
	 * Set message min
	 * @param string $message
	 * @return \BX\Validator\Manager\DateTime
	 */
	public function setMessageMin($message)
	{
		$this->message_min = $message;
		return $this;
	}
	/**
	 * Get message min
	 * @return string
	 */
	public function getMessageMin()
	{
		$message = $this->message_min;
		if($message === false){
			$message = $this->trans('validator.manager.date.min');
		}
		return $message;
	}
	/**
	 * Set message max
	 * @param string $message
	 * @return \BX\Validator\Manager\DateTime
	 */
	public function setMessageMax($message)
	{
		$this->message_max = $message;
		return $this;
	}
	/**
	 * Get message max
	 * @return string
	 */
	public function getMessageMax()
	{
		$message = $this->message_max;
		if($message === false){
			$message = $this->trans('validator.manager.date.max');
		}
		return $message;
	}
	/**
	 * Set not empty
	 * @return \BX\Validator\Manager\DateTime
	 */
	public function notEmpty()
	{
		$this->empty = false;
		return $this;
	}
	/**
	 * Set min value
	 * @param integer $min
	 * @return \BX\Validator\Manager\DateTime
	 */
	public function setMin($min)
	{
		$this->min = $min;
		return $this;
	}
	/**
	 * Set max value
	 * @param integer $max
	 * @return \BX\Validator\Manager\DateTime
	 */
	public function setMax($max)
	{
		$this->max = $max;
		return $this;
	}
	/**
	 * Is validate time
	 * @param boolean $time
	 * @return \BX\Validator\Manager\DateTime
	 */
	public function withTime($time = true)
	{
		$this->format_type = ($time) ? 'full' : 'short';
		return $this;
	}
	/**
	 * Set format of value
	 * @param type $format
	 */
	public function setFormat($format)
	{
		$this->format_input = $format;
	}
	/**
	 * Set format for rule date
	 * @param type $format
	 */
	public function setFormatRules($format)
	{
		$this->format_rules = $format;
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
		if(!$this->empty && $this->isEmpty($value)){
			$this->addError($this->getMessageEmpty(),[
				'#LABEL#' => $label,
				]);
			return false;
		}
		if(is_array($value)){
			$this->addError($this->getMessageInvalid(),[
				'#LABEL#' => $label,
			]);
			return false;
		}
		if(!$this->date()->checkDateTime($value,$this->format_input)){
			$this->addError($this->getMessageInvalid(),[
				'#LABEL#' => $label,
			]);
			return false;
		}
		$timestamp = $this->date()->makeTimeStamp($value,$this->format_input);
		if($this->min !==null && $timestamp < $this->date()->makeTimeStamp($this->min,$this->format_rules)){
			$this->addError($this->getMessageMin(),[
				'#LABEL#' => $label,
				'#MIN#' => $this->min,
			]);
			return false;
		}
		if($this->max !== null && $timestamp > $this->date()->makeTimeStamp($this->max,$this->format_rules)){
			$this->addError($this->getMessageMax(),[
				'#LABEL#' => $label,
				'#MAX#' => $this->max,
			]);
			return false;
		}
		return true;
	}
}