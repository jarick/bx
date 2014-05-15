<?php namespace BX\Validator\Collection;

class File extends BaseValidator
{
	use \BX\Date\DateTrait,
	 \BX\String\StringTrait,
	 \BX\Translate\TranslateTrait,
	 \BX\Http\HttpTrait;
	/**
	 * @var integer
	 */
	protected $size = 1000000;
	/**
	 * @var string
	 */
	protected $dir = '';
	/**
	 * @var array
	 */
	protected $types = null;
	/**
	 * @var string
	 */
	protected $message_invalid = null;
	/**
	 * @var string
	 */
	protected $message_empty = null;
	/**
	 * @var string
	 */
	protected $message_size = null;
	/**
	 * @var string
	 */
	protected $message_format = null;
	/**
	 * Set upload directory
	 *
	 * @param string $dir
	 * @return \BX\DB\Column\FileColumn
	 */
	public function setDirectory($dir)
	{
		$this->dir = $dir;
		return $this;
	}
	/**
	 * Set message invalid
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\File
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
		if ($message === null){
			$message = $this->trans('validator.collection.file.invalid');
		}
		return $message;
	}
	/**
	 * Set message empty
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\File
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
		if ($message === null){
			$message = $this->trans('validator.collection.file.empty');
		}
		return $message;
	}
	/**
	 * Set message filesize limit
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\File
	 */
	public function setMessageSize($message)
	{
		$this->message_size = $message;
		return $this;
	}
	/**
	 * Get message filesize limit
	 *
	 * @return string
	 */
	public function getMessageSize()
	{
		$message = $this->message_size;
		if ($message === null){
			$message = $this->trans('validator.collection.file.size');
		}
		return $message;
	}
	/**
	 * Set message invalid file format
	 *
	 * @param string $message
	 * @return \BX\Validator\Collection\File
	 */
	public function setMessageTypeFormat($message)
	{
		$this->message_format = $message;
		return $this;
	}
	/**
	 * Get message invalid file format
	 *
	 * @return string
	 */
	public function getMessageTypeFormat()
	{
		$message = $this->message_format;
		if ($message === false){
			$message = $this->trans('validator.collection.file.format');
		}
		return $message;
	}
	/**
	 * Set file size
	 *
	 * @param integer $size
	 * @return \BX\Validator\Collection\File
	 */
	public function setSize($size)
	{
		$this->size = $size;
		return $this;
	}
	/**
	 * Set file types
	 *
	 * @param array $types
	 * @return \BX\Validator\Collection\File
	 */
	public function setTypes(array $types)
	{
		$this->types = $types;
		return $this;
	}
	/**
	 * Validate
	 * @param \BX\Validator\Upload\IUploadFile $key
	 * @param string $value
	 * @param string $label
	 * @param array $fields
	 * @return boolean
	 */
	public function validate($key,$value,$label,&$fields)
	{
		if (!($value instanceof \BX\Validator\Upload\IUploadFile)){
			throw new \InvalidArgumentException('Value must be instance of IUploadFile');
		}
		unset($fields[$key]);
		if ($value->isEmpty()){
			if (!$this->empty){
				$this->addError($key,$this->getMessageEmpty(),[
					'#LABEL#' => $label,
				]);
				return false;
			}else{
				return true;
			}
		}
		if (!$value->checkInvalid()){
			$this->addError($key,$this->getMessageInvalid(),[
				'#LABEL#' => $label,
			]);
			return false;
		}
		if (!$value->checkSize()){
			$this->addError($key,$this->getMessageSize(),[
				'#LABEL#'	 => $label,
				'#SIZE#'	 => $this->size,
			]);
			return false;
		}
		if (!$value->checkType()){
			$this->addError($key,$this->getMessageTypeFormat(),[
				'#LABEL#' => $label,
			]);
			return false;
		}
		return true;
	}
}