<?php namespace BX\DB\Column;

class FileColumn extends BaseColumn
{
	/**
	 * @var string
	 */
	private $dir = '';
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
	 * Create column
	 *
	 * @param string $column
	 * @param string|\BX\Validator\Upload\Checker\IUploadFileChecker $format
	 * @return FileColumn
	 */
	public static function create($column,$format = 'image',$dir = '')
	{
		return parent::create($column)->setFormat($format)->setDirectory($dir);
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
		return $value->getName();
	}
	/**
	 * Convert value from db
	 *
	 * @param string $value
	 * @return \BX\Validator\Upload\ExistsFile
	 */
	public function convertFromDB($value)
	{
		return new \BX\Validator\Upload\ExistsFile($value,$this->format,$this->dir,true);
	}
}