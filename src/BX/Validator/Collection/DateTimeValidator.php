<?php namespace BX\Validator\Collection;

class DateTimeValidator extends BaseValidator
{
	use \BX\Date\DateTrait,
	 \BX\String\StringTrait,
	 \BX\Translate\TranslateTrait;
	/**
	 * @var boolean
	 */
	protected $empty = true;
	/**
	 * @var string
	 */
	protected $format_input = 'short';
	/**
	 * @var string
	 */
	protected $format_rules = 'short';
	/**
	 * @var string
	 */
	protected $min = null;
	/**
	 * @var string
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
	protected $message_min = false;
	/**
	 * @var string
	 */
	protected $message_max = false;
	/**
	 * Set message invalid
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\DateTime
	 */
	public function setMessageInvalid($message)
	{
		$this->message_invalid = $message;
		return $this;
	}
	/**
	 * Get message invalid
	 *
	 * @return string
	 */
	public function getMessageInvalid()
	{
		$message = $this->message_invalid;
		if ($message === false){
			$message = $this->trans('validator.manager.date.invalid');
		}
		return $message;
	}
	/**
	 * Set message empty
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\DateTime
	 */
	public function setMessageEmpty($message)
	{
		$this->message_empty = $message;
		return $this;
	}
	/**
	 * Get message empty
	 *
	 * @return string
	 */
	public function getMessageEmpty()
	{
		$message = $this->message_empty;
		if ($message === false){
			$message = $this->trans('validator.manager.date.empty');
		}
		return $message;
	}
	/**
	 * Set message min
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\DateTime
	 */
	public function setMessageMin($message)
	{
		$this->message_min = $message;
		return $this;
	}
	/**
	 * Get message min
	 *
	 * @return string
	 */
	public function getMessageMin()
	{
		$message = $this->message_min;
		if ($message === false){
			$message = $this->trans('validator.manager.date.min');
		}
		return $message;
	}
	/**
	 * Set message max
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\DateTime
	 */
	public function setMessageMax($message)
	{
		$this->message_max = $message;
		return $this;
	}
	/**
	 * Get message max
	 *
	 * @return string
	 */
	public function getMessageMax()
	{
		$message = $this->message_max;
		if ($message === false){
			$message = $this->trans('validator.manager.date.max');
		}
		return $message;
	}
	/**
	 * Set min value
	 *
	 * @param integer $min
	 * @return \BX\Validator\Collection\DateTime
	 */
	public function setMin($min)
	{
		$this->min = $min;
		return $this;
	}
	/**
	 * Set max value
	 *
	 * @param integer $max
	 * @return \BX\Validator\Collection\DateTime
	 */
	public function setMax($max)
	{
		$this->max = $max;
		return $this;
	}
	/**
	 * Is validate time
	 *
	 * @param boolean $time
	 * @return \BX\Validator\Collection\DateTime
	 */
	public function withTime($time = true)
	{
		$this->format_rules = $this->format_input = ($time) ? 'full' : 'short';
		return $this;
	}
	/**
	 * Set format of value
	 *
	 * @param string $format
	 */
	public function setFormat($format)
	{
		$this->format_input = $format;
		return $this;
	}
	/**
	 * Set format for rule date
	 *
	 * @param string $format
	 */
	public function setFormatRules($format)
	{
		$this->format_rules = $format;
		return $this;
	}
	/**
	 * Validate
	 *
	 * @param string $key
	 * @param string $value
	 * @param string $label
	 * @param array $fields
	 * @return boolean
	 */
	public function validate($key,$value,$label,&$fields)
	{
		if (is_array($value) || is_object($value)){
			$this->addError($key,$this->getMessageInvalid(),[
				'#LABEL#' => $label,
			]);
			return false;
		}
		if ($this->isEmpty($value)){
			if (!$this->empty){
				$this->addError($key,$this->getMessageEmpty(),[
					'#LABEL#' => $label,
				]);
				return false;
			}else{
				return true;
			}
		}
		if (!$this->date()->checkDateTime($value,$this->format_input)){
			$this->addError($key,$this->getMessageInvalid(),[
				'#LABEL#' => $label,
			]);
			return false;
		}
		$timestamp = $this->date()->makeTimeStamp($value,$this->format_input);
		if ($this->min !== null){
			$min_timestamp = $this->date()->makeTimeStamp($this->min,$this->format_rules);
			if ($timestamp < $min_timestamp){
				$this->addError($key,$this->getMessageMin(),[
					'#LABEL#'	 => $label,
					'#MIN#'		 => $this->min,
				]);
				return false;
			}
		}
		if ($this->max !== null){
			$max_timestamp = $this->date()->makeTimeStamp($this->max,$this->format_rules);
			if ($timestamp > $max_timestamp){
				$this->addError($key,$this->getMessageMax(),[
					'#LABEL#'	 => $label,
					'#MAX#'		 => $this->max,
				]);
				return false;
			}
		}
		return true;
	}
}