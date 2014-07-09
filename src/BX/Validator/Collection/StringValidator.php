<?php namespace BX\Validator\Collection;

class StringValidator extends BaseValidator
{
	use \BX\String\StringTrait,
	 \BX\Translate\TranslateTrait;
	/**
	 * @var integer
	 */
	protected $min = null;
	/**
	 * @var integer
	 */
	protected $max = null;
	/**
	 * @var integer
	 */
	protected $is = null;
	/**
	 * @var string
	 */
	protected $massage_invalid = null;
	/**
	 * @var string
	 */
	protected $message_empty = null;
	/**
	 * @var string
	 */
	protected $massage_min = null;
	/**
	 * @var string
	 */
	protected $massage_max = null;
	/**
	 * @var string
	 */
	protected $massage_is = null;
	/**
	 * @var array
	 */
	protected $words = null;
	/**
	 * Set message invalid
	 *
	 * @param string $massage_invalid
	 * @return \BX\Validator\Collection\StringValidator
	 */
	public function setMessageInvalid($massage_invalid)
	{
		$this->massage_invalid = $massage_invalid;
		return $this;
	}
	/**
	 * Return message invalid
	 *
	 * @return string
	 */
	protected function getMessageInvalid()
	{
		$message = $this->massage_invalid;
		if ($message === null){
			$message = $this->trans('validator.collection.string.invalid');
		}
		return $message;
	}
	/**
	 * Set empty message
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\String
	 */
	public function setMessageEmpty($message)
	{
		$this->message_empty = (string)$message;
		return $this;
	}
	/**
	 * Return message if value is empty
	 *
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
	/**
	 * Set min length error
	 *
	 * @param string $massage_min
	 * @return \BX\Validator\Collection\StringValidator
	 */
	public function setMessageMin($massage_min)
	{
		$this->massage_min = $massage_min;
		return $this;
	}
	/**
	 * Return min length error
	 *
	 * @return integer
	 */
	protected function getMessageMin()
	{
		$message = $this->massage_min;
		if ($message === null){
			$message = $this->trans('validator.collection.string.min');
		}
		return $message;
	}
	/**
	 * Set max length error
	 *
	 * @param type $massage_max
	 * @return \BX\Validator\Collection\StringValidator
	 */
	public function setMessageMax($massage_max)
	{
		$this->massage_max = $massage_max;
		return $this;
	}
	/**
	 * Return max length error
	 *
	 * @return string
	 */
	protected function getMessageMax()
	{
		$message = $this->massage_max;
		if ($message === null){
			$message = $this->trans('validator.collection.string.max');
		}
		return $message;
	}
	/**
	 * Set length error
	 *
	 * @param string $massage_is
	 * @return \BX\Validator\Collection\StringValidator
	 */
	public function setMessageLength($massage_is)
	{
		$this->massage_is = $massage_is;
		return $this;
	}
	/**
	 * Return length error
	 *
	 * @return string
	 */
	protected function getMessageLength()
	{
		$message = $this->massage_max;
		if ($message === null){
			$message = $this->trans('validator.collection.string.length');
		}
		return $message;
	}
	/**
	 * Set array of wordds
	 *
	 * @param array $words
	 * @return \BX\Validator\Collection\StringValidator
	 */
	public function setWords($words)
	{
		$this->words = $words;
		return $this;
	}
	/**
	 * Return array of words
	 *
	 * @return array
	 */
	protected function getWords()
	{
		$words = $this->words;
		if ($words === null){
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
	 * Validate field
	 *
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