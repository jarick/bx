<?php namespace BX\Validator\Manager;

class Number extends BaseValidator
{
	protected $empty = true;
	protected $integer_only = false;
	protected $integer_pattern = '/^\s*[+-]?\d+\s*$/';
	protected $number_pattern = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';
	protected $min = null;
	protected $max = null;
	protected $message_invalid = false;
	protected $message_empty = false;
	protected $message_integer = false;
	protected $message_number = false;
	protected $message_min = false;
	protected $message_max = false;

	public function setMessageInvalid($message_invalid)
	{
		$this->message_invalid = $message_invalid;
		return $this;
	}

	public function getMessageInvalid()
	{
		$sMessage = $this->message_invalid;
		if ($sMessage === false) {
			$sMessage = $this->trans('validator.manager.number.invalid');
		}
		return $sMessage;
	}

	public function setMessageInteger($sMessage)
	{
		$this->message_integer = $sMessage;
		return $this;
	}

	public function getMessageInteger()
	{
		$sMessage = $this->message_integer;
		if ($sMessage === false) {
			$sMessage = $this->trans('validator.manager.number.integer');
		}
		return $sMessage;
	}

	public function setMessageNumber($sMessage)
	{
		$this->message_number = $sMessage;
		return $this;
	}

	public function getMessageNumber()
	{
		$sMessage = $this->message_number;
		if ($sMessage === false) {
			$sMessage = $this->trans('validator.manager.number.number');
		}
		return $sMessage;
	}

	public function setMessageEmpty($message_empty)
	{
		$this->message_empty = $message_empty;
		return $this;
	}

	public function getMessageEmpty()
	{
		$sMessage = $this->message_empty;
		if ($sMessage === false) {
			$sMessage = $this->trans('validator.manager.number.empty');
		}
		return $sMessage;
	}

	public function setMessageMin($message_min)
	{
		$this->message_min = $message_min;
		return $this;
	}

	public function getMessageMin()
	{
		$sMessage = $this->message_min;
		if ($sMessage === false) {
			$sMessage = $this->trans('validator.manager.number.min');
		}
		return $sMessage;
	}

	public function setMessageMax($message_max)
	{
		$this->message_max = $message_max;
		return $this;
	}

	public function getMessageMax()
	{
		$sMessage = $this->message_max;
		if ($sMessage === false) {
			$sMessage = $this->trans('validator.manager.number.max');
		}
		return $sMessage;
	}

	public function notEmpty()
	{
		$this->empty = false;
		return $this;
	}

	public function setMin($min)
	{
		$this->min = $min;
		return $this;
	}
	public function setMax($max)
	{
		$this->max = $max;
		return $this;
	}

	public function integer()
	{
		$this->integer_only = true;
		return $this;
	}

	public function setIntegerPattern($sPattern)
	{
		$this->integer_pattern = $sPattern;
		return $this;
	}

	public function setNumberPattern($sPattern)
	{
		$this->number_pattern = $sPattern;
		return $this;
	}
	public function validate($key, $value, $label, &$fields)
	{
		if (!$this->empty && $this->isEmpty($value)) {
			$this->addError($this->getMessageEmpty(), [
				'#LABEL#' => $label,
			]);
			return false;
		}
		if (!is_numeric($value)) {
			$this->addError($this->getMessageInvalid(), [
				'#LABEL#' => $label,
			]);
			return false;
		}
		if ($this->integer_only) {
			if (!preg_match($this->integer_pattern, "$value")) {
				$this->addError($this->getMessageInteger(),
					[
					'#LABEL#' => $label,
				]);
				return false;
			}
		} else {
			if (!preg_match($this->number_pattern, "$value")) {
				$this->addError($this->getMessageNumber(), [
					'#LABEL#' => $label,
				]);
				return false;
			}
		}
		if ($this->min !== null && $value < $this->min) {
			$this->addError($this->getMessageMin(),
				   [
				'#LABEL#'	 => $label,
				'#MIN#'		 => $this->min,
			]);
			return false;
		}
		if ($this->max !== null && $value > $this->max) {
			$this->addError($this->getMessageMax(),
				   [
				'#LABEL#'	 => $label,
				'#MAX#'		 => $this->max,
			]);
			return false;
		}
		return true;
	}
}