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
	public function __construct($file,$type = 'image',$dir = '',$from_db = false)
	{
		$this->debut_file = $file;
		$this->from_db = $from_db;
		$this->file = $file;
		$this->dir = $dir;
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
		$file = $this->getUploadDir().$ds.$this->dir.$ds.$this->file;
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
		if ($this->form_db){
			return true;
		}
		if ($file === null){
			return true;
		}
		$ds = DIRECTORY_SEPARATOR;
		$path = $this->getUploadDir().$ds.$this->dir.$ds.$this->getName();
		return $this->type->save($path);
	}
}