<?php namespace BX\DB\Column;
use BX\Validator\Upload\IUploadFile;

class FileColumn extends BaseColumn
{
	use \BX\String\StringTrait;
	/**
	 * @var string|\BX\Validator\Upload\Checker\IUploadFileChecker
	 */
	private $format;
	/**
	 * Set format
	 *
	 * @param string|\BX\Validator\Upload\Checker\IUploadFileChecker
	 * @return \BX\DB\Column\FileColumn
	 */
	public function setFormat($format)
	{
		$this->format = $format;
		return $this;
	}
	/**
	 * Create column
	 *
	 * @param string $column
	 * @param string|\BX\Validator\Upload\Checker\IUploadFileChecker $format
	 * @return FileColumn
	 */
	public static function create($column,$format = 'image')
	{
		return parent::create($column)->setFormat($format);
	}
	/**
	 * Get filter filter rule name
	 *
	 * @return string
	 */
	public function getFilterRule()
	{
		return 'file';
	}
	/**
	 * Convert value to db
	 *
	 * @param \BX\Validator\Upload\IUploadFile $value
	 * @return string
	 */
	public function convertToDB($value)
	{
		if ($value === null){
			return null;
		}
		return $value->getFilePath();
	}
	/**
	 * Convert value from db
	 *
	 * @param string|null $value
	 * @return \BX\Validator\Upload\ExistsFile|null
	 */
	public function convertFromDB($value)
	{
		if ($value === null){
			return null;
		}else{
			return new \BX\Validator\Upload\ExistsFile($value,$this->format,true);
		}
	}
}