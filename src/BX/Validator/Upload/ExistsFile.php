<?php namespace BX\Validator\Upload;

class ExistsFile extends UploadFile
{
	/**
	 * @var boolean
	 */
	protected $from_db;
	/**
	 * Constructor
	 *
	 * @param string $file
	 * @param string|Checker\IUploadFileChecker $type
	 * @param string $dir
	 * @param boolean $from_db
	 * @throws \RuntimeException
	 */
	public function __construct($file,$type = 'image',$from_db = false)
	{
		$this->from_db = $from_db;
		$this->file = $file;
		$this->setTypes($type);
	}
	/**
	 * Return is invalid file
	 *
	 * @return boolean
	 */
	public function checkInvalid()
	{
		$ds = DIRECTORY_SEPARATOR;
		$file = $this->getUploadDir().$ds.$this->file;
		if (!file_exists($file)){
			$this->file = null;
		}else{
			$this->file = [
				'size'		 => filesize($file),
				'tmp_name'	 => $file,
			];
		}
		return true;
	}
	/**
	 * Save file
	 *
	 * @return boolean
	 */
	public function saveFile()
	{
		$file = $this->file;
		if ($this->from_db){
			return true;
		}
		if ($file === null){
			return true;
		}
		$ds = DIRECTORY_SEPARATOR;
		$path = $this->getUploadDir().$ds.$this->getFilePath();
		return $this->type->save($path);
	}
	/**
	 * Magick method
	 *
	 * @return string
	 */
	public function __toString()
	{
		if ($this->file === null){
			return '';
		}
		return $this->file;
	}
}