<?php namespace BX\Validator\Manager;

class String extends BaseValidator
{
	use \BX\String\StringTrait;
	protected $min = null;
	protected $max = null;
	protected $is = null;
	protected $massage_invalid = false;
	protected $massage_empty = false;
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
		$sMessage = $this->massage_invalid;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.string.invalid');
		}
		return $sMessage;
	}
	public function setMessageEmpty($massage_empty)
	{
		$this->massage_empty = $massage_empty;
		return $this;
	}
	protected function getMessageEmpty()
	{
		$sMessage = $this->massage_empty;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.string.empty');
		}
		return $sMessage;
	}
	public function setMessageMin($massage_min)
	{
		$this->massage_min = $massage_min;
		return $this;
	}
	protected function getMessageMin()
	{
		$sMessage = $this->massage_min;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.string.min');
		}
		return $sMessage;
	}
	public function setMessageMax($massage_max)
	{
		$this->massage_max = $massage_max;
		return $this;
	}
	protected function getMessageMax()
	{
		$sMessage = $this->massage_max;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.string.max');
		}
		return $sMessage;
	}
	public function setMessageLength($massage_is)
	{
		$this->massage_is = $massage_is;
		return $this;
	}
	protected function getMessageLength()
	{
		$sMessage = $this->massage_max;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.string.length');
		}
		return $sMessage;
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
				$this->trans('validator.manager.string.words.one'),
				$this->trans('validator.manager.string.words.two'),
				$this->trans('validator.manager.string.words.many'),
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
		if (is_array($value)){
			$this->addError($this->getMessageInvalid(),['#LABEL#' => $label]);
			return false;
		}
		if (!$this->empty && $this->isEmpty($value)){
			$this->addError($this->getMessageEmpty(),['#LABEL#' => $label]);
			return false;
		}
		$length = $this->string()->length($value);
		if ($this->min !== null && $length < $this->min){
			$this->addError($this->getMessageMin(),[
				'#LABEL#'	 => $label,
				'#MIN#'		 => $this->min,
				'#WORD#'	 => $this->string()->convertNumber($this->min,$this->getWords()),
			]);
			return false;
		}
		if ($this->max !== null && $length > $this->max){
			$this->addError($this->getMessageMax(),[
				'#LABEL#'	 => $label,
				'#MAX#'		 => $this->max,
				'#WORD#'	 => $this->string()->convertNumber($this->max,$this->getWords()),
			]);
			return false;
		}
		if ($this->is !== null && $length !== $this->is){
			$this->addError($this->getMessageLength(),[
				'#LABEL#'	 => $label,
				'#LENGTH#'	 => $this->is,
				'#WORD#'	 => $this->string()->convertNumber($this->is,$this->getWords()),
			]);
			return false;
		}
		return true;
	}
}