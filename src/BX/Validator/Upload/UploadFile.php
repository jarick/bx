<?php namespace BX\Validator\Upload;
use BX\Validator\Collection\File;
use BX\Validator\Upload\Checker\IUploadFileChecker;
use BX\Validator\Upload\Checker\Image;

class UploadFile implements IUploadFile
{
	use \BX\String\StringTrait,
	 \BX\Translate\TranslateTrait,
	 \BX\Http\HttpTrait,
	 \BX\Config\ConfigTrait,
	 \BX\FileSystem\FileSystemTrait;
	const TYPE_IMAGE = 'image';
	/**
	 * @var string
	 */
	protected $dir = null;
	/**
	 * @var array
	 */
	protected $file = null;
	/**
	 * @var IUploadFileChecker
	 */
	protected $type = null;
	/**
	 * @var IUploadFileChecker[]
	 */
	protected $types = null;
	/**
	 * @var string
	 */
	protected $upload_dir = null;
	/**
	 * Constructor
	 *
	 * @param string $file
	 */
	public function __construct($file,$type)
	{
		$files = $this->request()->files();
		if ($files->has($file)){
			$this->file = $files->get($file);
		}
		$this->setTypes($type);
	}
	/**
	 * Prepare add types
	 *
	 * @param array|string|IUploadFileChecker $type
	 * @throws \InvalidArgumentException
	 */
	protected function setTypes($type)
	{
		$this->types = (array)$type;
		if (empty($this->types)){
			throw new \InvalidArgumentException('File type validators is not set');
		}
		foreach($this->types as &$checker){
			if (!($checker instanceof IUploadFileChecker)){
				switch ($checker){
					case self::TYPE_IMAGE:
						$checker = new Image();
						break;
					default :
						throw new \InvalidArgumentException('File type validator has invalid format');
				}
			}
		}
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
	 * Return is invalid file
	 *
	 * @return boolean
	 */
	public function checkInvalid()
	{
		$file = $this->file;
		if ($file !== null){
			if (!isset($file['error']) || is_array($file['error'])){
				return false;
			}
			if ($file['error'] !== UPLOAD_ERR_OK){
				return false;
			}
		}
		return true;
	}
	/**
	 * Return is empty input file
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		return $this->file === null;
	}
	/**
	 * Check size input file
	 * @param type $size
	 * @return boolean
	 */
	public function checkSize($size)
	{
		$file = $this->file;
		if ($file !== null){
			if ($file['size'] > $size){
				return false;
			}
		}
		return true;
	}
	/**
	 * Check content type file
	 *
	 * @return boolean
	 */
	public function checkType()
	{
		$file = $this->file;
		if ($file !== null){
			foreach($this->types as $checker){
				if ($checker->analyze($file['tmp_name'])){
					$this->type = $checker;
					return true;
				}
			}
			return false;
		}
		return true;
	}
	/**
	 * Set upload directory
	 *
	 * @param string $upload_dir
	 * @return File
	 */
	public function setUploadDir($upload_dir)
	{
		$this->upload_dir = $upload_dir;
		return $this;
	}
	/**
	 * Return path to upload directory
	 *
	 * @return string
	 */
	protected function getUploadDir()
	{
		if ($this->upload_dir === null){
			if ($this->config()->exists('upload_dir')){
				$this->upload_dir = $this->config()->get('upload_dir');
			}else{
				$this->upload_dir = '~/upload';
			}
			if ($this->config()->exists('upload_dir')){
				$doc_root = $this->config()->get('doc_root');
			}else{
				$doc_root = $this->request()->server()->get('DOCUMENT_ROOT');
			}
			if ($this->string()->strpos($this->upload_dir,'~') !== false){
				$this->upload_dir = str_replace('~',$doc_root,$this->upload_dir);
			}
		}
		return $this->upload_dir;
	}
	/**
	 * Get path to file
	 *
	 * @return string
	 */
	public function getFilePath()
	{
		$dir = '';
		if ($this->string()->length($this->dir) > 0){
			$dir = $this->dir.DIRECTORY_SEPARATOR;
		}
		return $dir.$this->getName();
	}
	/**
	 * Return name of save file
	 *
	 * @return null|string
	 * @throws \InvalidArgumentException
	 */
	public function getName()
	{
		if ($this->file === null){
			return null;
		}
		if ($this->type === null){
			throw new \InvalidArgumentException('File type validator is not set');
		}
		return $this->type->getName();
	}
	/**
	 * Save file
	 *
	 * @return boolean
	 * @param string $dir
	 * @throws \RuntimeException
	 */
	public function saveFile()
	{
		$file = $this->file;
		if ($file !== null){
			$ds = DIRECTORY_SEPARATOR;
			$path = $this->getUploadDir().$ds.$this->getFilePath();
			$this->filesystem()->checkPathDir(dirname($path));
			if (!copy($file['tmp_name'],$path)){
				throw new \RuntimeException('Error save file');
			}
			return $this->type->save($path);
		}
		return true;
	}
	/**
	 * Delete file
	 *
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function deleteFile()
	{
		$file = $this->file;
		if ($file !== null){
			$ds = DIRECTORY_SEPARATOR;
			$path = $this->getUploadDir().$ds.$file;
			if (!unlink($path)){
				throw new \RuntimeException('Error delete file');
			}
		}
		return true;
	}
}