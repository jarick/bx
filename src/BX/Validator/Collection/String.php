<?php namespace BX\Validator\Collection;

class String extends BaseValidator
{
	use \BX\String\StringTrait,
	 \BX\Translate\TranslateTrait;
	protected $min = null;
	protected $max = null;
	protected $is = null;
	protected $massage_invalid = false;
	/**
	 * @var string
	 */
	protected $message_empty = null;
	protected $massage_min = false;
	protected $massage_max = false;
	protected $massage_is = false;
	protected $words = false;
	public function setMessageInvalid($massage_invalid)
	{
		$this->massage_invalid = $massage_invalid;
		return $this;
	}
	protected function getMessageInvalid()
	{
		$message = $this->massage_invalid;
		if ($message === false){
			$message = $this->trans('validator.collection.string.invalid');
		}
		return $message;
	}
	/**
	 * Set empty message
	 * @param string $message
	 * @return \BX\Validator\Collection\String
	 */
	public function setMessageEmpty($message)
	{
		$this->message_empty = (string)$message;
		return $this;
	}
	/**
	 * Get empty message
	 * @return string
	 */
	protected function getMessageEmpty()
	{
		$message = $this->message_empty;
		if ($message === null){
			$message = $this->trans('validator.collection.string.empty');
		}
		return $message;
	}
	public function setMessageMin($massage_min)
	{
		$this->massage_min = $massage_min;
		return $this;
	}
	protected function getMessageMin()
	{
		$message = $this->massage_min;
		if ($message === false){
			$message = $this->trans('validator.collection.string.min');
		}
		return $message;
	}
	public function setMessageMax($massage_max)
	{
		$this->massage_max = $massage_max;
		return $this;
	}
	protected function getMessageMax()
	{
		$message = $this->massage_max;
		if ($message === false){
			$message = $this->trans('validator.collection.string.max');
		}
		return $message;
	}
	public function setMessageLength($massage_is)
	{
		$this->massage_is = $massage_is;
		return $this;
	}
	protected function getMessageLength()
	{
		$message = $this->massage_max;
		if ($message === false){
			$message = $this->trans('validator.collection.string.length');
		}
		return $message;
	}
	public function setWords($words)
	{
		$this->words = $words;
		return $this;
	}
	/**
	 * Get words
	 * @return array
	 */
	protected function getWords()
	{
		$words = $this->words;
		if ($words === false){
			$words = [
				$this->trans('validator.collection.string.words.one'),
				$this->trans('validator.collection.string.words.two'),
				$this->trans('validator.collection.string.words.many'),
			];
		}
		return $words;
	}
	/**
	 * Set min
	 * @param integer $min
	 * @return \BX\Validator\Manager\String
	 */
	public function setMin($min)
	{
		$this->min = $min;
		return $this;
	}
	/**
	 * Set max
	 * @param integer $max
	 * @return \BX\Validator\Manager\String
	 */
	public function setMax($max)
	{
		$this->max = $max;
		return $this;
	}
	/**
	 * Set length
	 * @param integer $is
	 * @return \BX\Validator\Manager\String
	 */
	public function setLength($is)
	{
		$this->is = $is;
		return $this;
	}
	/**
	 * Validate
	 * @param string $key
	 * @param string $value
	 * @param string $label
	 * @param string $fields
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
		$length = $this->string()->length($value);
		if ($this->min !== null && $length < $this->min){
			$this->addError($key,$this->getMessageMin(),[
				'#LABEL#'	 => $label,
				'#MIN#'		 => $this->min,
				'#WORD#'	 => $this->string()->convertNumber($this->min,$this->getWords()),
			]);
			return false;
		}
		if ($this->max !== null && $length > $this->max){
			$this->addError($key,$this->getMessageMax(),[
				'#LABEL#'	 => $label,
				'#MAX#'		 => $this->max,
				'#WORD#'	 => $this->string()->convertNumber($this->max,$this->getWords()),
			]);
			return false;
		}
		if ($this->is !== null && $length !== $this->is){
			$this->addError($key,$this->getMessageLength(),[
				'#LABEL#'	 => $label,
				'#LENGTH#'	 => $this->is,
				'#WORD#'	 => $this->string()->convertNumber($this->is,$this->getWords()),
			]);
			return false;
		}
		return true;
	}
}