<?php namespace BX\Validator\Manager;
use BX\Validator\IValidator;

class Multy extends BaseValidator
{
	use \BX\String\StringTrait;
	protected $min = null;
	protected $max = null;
	protected $is = null;
	/**
	 * @var IValidator
	 */
	protected $validator;
	protected $message_invalid = false;
	protected $message_empty = false;
	protected $message_min = false;
	protected $message_max = false;
	protected $message_is = false;
	protected $words = false;
	public function setValidator(IValidator $validator)
	{
		$this->validator = $validator;
	}
	public function setMessageEmpty($message_empty)
	{
		$this->message_empty = $message_empty;
		return $this;
	}
	protected function getMessageEmpty()
	{
		$sMessage = $this->message_empty;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.multy.empty');
		}
		return $sMessage;
	}
	public function setMessageMin($message_min)
	{
		$this->message_min = $message_min;
		return $this;
	}
	protected function getMessageMin()
	{
		$sMessage = $this->message_min;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.multy.min');
		}
		return $sMessage;
	}
	public function setMessageMax($message_max)
	{
		$this->message_max = $message_max;
		return $this;
	}
	protected function getMessageMax()
	{
		$sMessage = $this->message_max;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.multy.max');
		}
		return $sMessage;
	}
	public function setMessageLength($message_is)
	{
		$this->message_is = $message_is;
		return $this;
	}
	protected function getMessageLength()
	{
		$sMessage = $this->message_max;
		if ($sMessage === false){
			$sMessage = $this->trans('validator.manager.multy.length');
		}
		return $sMessage;
	}
	public function setWords($words)
	{
		$this->words = $words;
		return $this;
	}
	protected function getWords()
	{
		$words = $this->words;
		if ($words === false){
			$words = [
				$this->trans('validator.manager.multy.words.one'),
				$this->trans('validator.manager.multy.words.two'),
				$this->trans('validator.manager.multy.words.many'),
			];
		}
		return $words;
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
	public function setLength($is)
	{
		$this->is = $is;
		return $this;
	}
	public static function create(IValidator $validator)
	{
		$return = parent::create();
		$return->setValidator($validator);
		return $return;
	}
	public function validate($key,$value,$label,&$fields)
	{
		if (!isset($this->validator)){
			throw new \RuntimeException('Validator is not set');
		}
		$value = (array) $value;
		if (!$this->empty && empty($value)){
			$this->addError($this->getMessageEmpty(),['#LABEL#' => $label]);
			return false;
		}
		$iLength = count($value);
		if ($this->min !== null && $iLength < $this->min){
			$this->addError($this->getMessageMin(),[
				'#LABEL#'	 => $label,
				'#MIN#'		 => $this->min,
				'#WORD#'	 => $this->string()->convertNumber($this->min,$this->getWords()),
			]);
			return false;
		}
		if ($this->max !== null && $iLength > $this->max){
			$this->addError($this->getMessageMax(),[
				'#LABEL#'	 => $label,
				'#MAX#'		 => $this->max,
				'#WORD#'	 => $this->string()->convertNumber($this->max,$this->getWords()),
			]);
			return false;
		}
		if ($this->is !== null && $iLength !== $this->is){
			$this->addError($this->getMessageLength(),[
				'#LABEL#'	 => $label,
				'#LENGTH#'	 => $this->is,
				'#WORD#'	 => $this->string()->convertNumber($this->is,$this->getWords()),
			]);
			return false;
		}
		$bError = false;
		foreach ($value as $sItem){
			if (!$this->validator->validate($key,$sItem,$label,$fields)){
				$bError = true;
			}
		}
		if ($bError){
			foreach ($this->validator->getErrors() as $sError){
				$this->addError($sError);
			}
			return false;
		}
		$fields[$key] = serialize($value);
		return true;
	}
}