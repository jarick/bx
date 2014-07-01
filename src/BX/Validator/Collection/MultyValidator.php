<?php namespace BX\Validator\Collection;
use BX\Validator\IValidator;

class MultyValidator extends BaseValidator
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
	 * @var IValidator
	 */
	protected $validator;
	/**
	 * @var string
	 */
	protected $message_empty = null;
	/**
	 * @var string
	 */
	protected $message_min = null;
	/**
	 * @var string
	 */
	protected $message_max = null;
	/**
	 * @var string
	 */
	protected $message_is = null;
	/**
	 * @var array
	 */
	protected $words = null;
	/**
	 * Set validator
	 * @param \BX\Validator\IValidator $validator
	 * @return \BX\Validator\Collection\Multy
	 */
	public function setValidator(IValidator $validator)
	{
		$this->validator = $validator;
		return $this;
	}
	/**
	 * Set message empty
	 * @param string $message_empty
	 * @return \BX\Validator\Collection\Multy
	 */
	public function setMessageEmpty($message_empty)
	{
		$this->message_empty = $message_empty;
		return $this;
	}
	/**
	 * Get message empty
	 * @return string
	 */
	protected function getMessageEmpty()
	{
		$message = $this->message_empty;
		if ($message === null){
			$message = $this->trans('validator.manager.multy.empty');
		}
		return $message;
	}
	/**
	 * Set message min
	 * @param string $message_min
	 * @return \BX\Validator\Collection\Multy
	 */
	public function setMessageMin($message_min)
	{
		$this->message_min = $message_min;
		return $this;
	}
	/**
	 * Get message min
	 * @return string
	 */
	protected function getMessageMin()
	{
		$message = $this->message_min;
		if ($message === null){
			$message = $this->trans('validator.manager.multy.min');
		}
		return $message;
	}
	/**
	 * Set message max
	 * @param string $message_max
	 * @return \BX\Validator\Collection\Multy
	 */
	public function setMessageMax($message_max)
	{
		$this->message_max = $message_max;
		return $this;
	}
	/**
	 * Get message max
	 * @return string
	 */
	protected function getMessageMax()
	{
		$message = $this->message_max;
		if ($message === null){
			$message = $this->trans('validator.manager.multy.max');
		}
		return $message;
	}
	/**
	 * Set message length
	 * @param string $message_is
	 * @return \BX\Validator\Collection\Multy
	 */
	public function setMessageLength($message_is)
	{
		$this->message_is = $message_is;
		return $this;
	}
	/**
	 * Get message length
	 * @return string
	 */
	protected function getMessageLength()
	{
		$message = $this->message_max;
		if ($message === null){
			$message = $this->trans('validator.manager.multy.length');
		}
		return $message;
	}
	/**
	 * Set message words
	 * @param array $words
	 * @return \BX\Validator\Collection\Multy
	 */
	public function setWords(array $words)
	{
		$this->words = $words;
		return $this;
	}
	/**
	 * Get message words
	 * @return array
	 */
	protected function getWords()
	{
		$words = $this->words;
		if ($words === null){
			$words = [
				$this->trans('validator.manager.multy.words.one'),
				$this->trans('validator.manager.multy.words.two'),
				$this->trans('validator.manager.multy.words.many'),
			];
		}
		return $words;
	}
	/**
	 * Set min count values
	 * @param integer $min
	 * @return \BX\Validator\Collection\Multy
	 */
	public function setMin($min)
	{
		$this->min = intval($min);
		return $this;
	}
	/**
	 * Set max count values
	 * @param integer $max
	 * @return \BX\Validator\Collection\Multy
	 */
	public function setMax($max)
	{
		$this->max = intval($max);
		return $this;
	}
	/**
	 * Set count values
	 * @param integer $is
	 * @return \BX\Validator\Collection\Multy
	 */
	public function setLength($is)
	{
		$this->is = intval($is);
		return $this;
	}
	/**
	 * Create validator
	 * @param \BX\Validator\IValidator $validator
	 * @return Multy
	 */
	public static function create(IValidator $validator)
	{
		$return = parent::create();
		$return->setValidator($validator);
		return $return;
	}
	/**
	 * Validate value
	 * @param string $key
	 * @param array $value
	 * @param string $label
	 * @return boolean
	 */
	private function validateValue($key,$value,$label)
	{
		if (!$this->empty && empty($value)){
			$this->addError($key,$this->getMessageEmpty(),['#LABEL#' => $label]);
			return false;
		}
		$length = count($value);
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
	/**
	 * Validate values
	 * @param string $key
	 * @param array $value
	 * @param string $label
	 * @param array $fields
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function validate($key,$value,$label,&$fields)
	{
		if (!isset($this->validator)){
			throw new \RuntimeException('Validator is not set');
		}
		$value = (array)$value;
		if (!$this->validateValue($key,$value,$label)){
			return false;
		}
		$has_error = false;
		foreach($value as $item){
			if (!$this->validator->validate($key,$item,$label,$fields)){
				$has_error = true;
			}
		}
		if ($has_error){
			foreach($this->validator->getErrors()->toArray() as $key => $messages){
				foreach($messages as $message){
					$this->addError($key,$message);
				}
			}
			return false;
		}
		$fields[$key] = serialize($value);
		return true;
	}
}