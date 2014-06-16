<?php namespace BX\Validator\Collection;

class DateTimeFilter extends BaseValidator
{
	use \BX\Date\DateTrait,
	 \BX\String\StringTrait,
	 \BX\Translate\TranslateTrait;
	/**
	 * @var DateTime
	 */
	protected $first_field = null;
	/**
	 * @var DateTime
	 */
	protected $second_field = null;
	/**
	 * @var string
	 */
	protected $message_invalid = null;
	/**
	 * @var string
	 */
	protected $message_invalid_min = null;
	/**
	 * @var string
	 */
	protected $message_invalid_max = null;
	/**
	 * @var string
	 */
	protected $message_min_more_max = null;
	/**
	 * @var string
	 */
	protected $min_key = 'from';
	/**
	 * @var string
	 */
	protected $max_key = 'to';
	/**
	 * @var string
	 */
	protected $format_input = 'full';
	/**
	 * Return min validator
	 *
	 * @return DateTime
	 */
	public function min()
	{
		return DateTime::create();
	}
	/**
	 * Return max validator
	 *
	 * @return DateTime
	 */
	public function max()
	{
		return DateTime::create();
	}
	/**
	 * Set validators
	 *
	 * @param \BX\Validator\Collection\callable $func
	 */
	public function filter(callable $func)
	{
		list($this->first_field,$this->second_field) = call_user_func($func,$this);
		return $this;
	}
	/**
	 * Set postfix min
	 *
	 * @param string $str
	 * @return \BX\Validator\Collection\DateTimeFilter
	 */
	public function setMinKey($str)
	{
		$this->min_key = $str;
		return $this;
	}
	/**
	 * Set prefix max
	 *
	 * @param string $str
	 * @return \BX\Validator\Collection\DateTimeFilter
	 */
	public function setMaxKey($str)
	{
		$this->max_key = $str;
		return $this;
	}
	/**
	 * Return message invalid first field
	 *
	 * @return string
	 */
	public function getMessageInvalid()
	{
		$message = $this->message_invalid;
		if ($message === null){
			$message = $this->trans('validator.manager.date_filter.invalid');
		}
		return $message;
	}
	/**
	 * Set message invalid
	 *
	 * @param string $str
	 * @return \BX\Validator\Collection\DateTimeFilter
	 */
	public function setMessageInvalid($str)
	{
		$this->message_invalid = $str;
		return $this;
	}
	/**
	 * Return message invalid first field
	 *
	 * @return string
	 */
	public function getMessageInvalidMin()
	{
		$message = $this->message_invalid_min;
		if ($message === null){
			$message = $this->trans('validator.manager.date_filter.min');
		}
		return $message;
	}
	/**
	 * Set message invalid  first field
	 *
	 * @param string $str
	 * @return \BX\Validator\Collection\DateTimeFilter
	 */
	public function setMessageInvalidMin($str)
	{
		$this->message_invalid_min = $str;
		return $this;
	}
	/**
	 * Return message invalid second field
	 *
	 * @return string
	 */
	public function getMessageInvalidMax()
	{
		$message = $this->message_invalid_max;
		if ($message === null){
			$message = $this->trans('validator.manager.date_filter.max');
		}
		return $message;
	}
	/**
	 * Set message invlaid second field
	 *
	 * @param string $str
	 * @return \BX\Validator\Collection\DateTimeFilter
	 */
	public function setMessageInvalidMax($str)
	{
		$this->message_invalid_max = $str;
		return $this;
	}
	/**
	 * Return message error when min value more max value
	 *
	 * @return array
	 */
	public function getMessageMinMoreMax()
	{
		$message = $this->message_min_more_max;
		if ($message === null){
			$message = $this->trans('validator.manager.date_filter.min_more_max');
		}
		return $message;
	}
	/**
	 * Set message error when min value more max value
	 *
	 * @return array
	 */
	public function setMessageMinMoreMax($str)
	{
		$this->message_min_more_max = $str;
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
		unset($fields[$key]);
		$fields['>='.$key] = null;
		$fields['<='.$key] = null;
		if (!is_array($value)){
			$this->addError($key,$this->getMessageInvalid(),[
				'#LABEL#' => $label,
			]);
			return false;
		}
		if ($this->first_field === null){
			$this->first_field = $this->min();
		}
		if ($this->second_field === null){
			$this->second_field = $this->max();
		}
		$min_value = (array_key_exists($this->min_key,$value)) ? $value[$this->min_key] : null;
		if (!$this->first_field->validate($this->min_key,$min_value,$label,$fields)){
			$this->addError($key,$this->getMessageInvalidMin(),[
				'#LABEL#'	 => $label,
				'#VALUE#'	 => $min_value,
			]);
			return false;
		}
		$max_value = (array_key_exists($this->max_key,$value)) ? $value[$this->max_key] : null;
		if (!$this->second_field->validate($this->max_key,$max_value,$label,$fields)){
			$this->addError($key,$this->getMessageInvalidMax(),[
				'#LABEL#'	 => $label,
				'#VALUE#'	 => $max_value,
			]);
			return false;
		}
		if ($this->string()->length($max_value) > 0){
			$max_time = $this->date()->makeTimeStamp($max_value,$this->format_input);
			$fields['<='.$key] = $this->date()->convertTimeStamp($max_time);
		}
		if ($this->string()->length($min_value) > 0){
			$min_time = $this->date()->makeTimeStamp($min_value,$this->format_input);
			$fields['>='.$key] = $this->date()->convertTimeStamp($min_time);
		}
		if (isset($max_time) && isset($min_time)){
			if ($max_time < $min_time){
				$this->addError($key,$this->getMessageMinMoreMax(),[
					'#LABEL#'	 => $label,
					'#MIN#'		 => $min_value,
					'#MAX#'		 => $max_value,
				]);
				return false;
			}
		}

		return true;
	}
}