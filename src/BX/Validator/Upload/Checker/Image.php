<?php namespace BX\Validator\Upload\Checker;
use BX\Validator\Upload\Checker\IUploadFileChecker;
use Imagine\Gd\Imagine;

class Image implements IUploadFileChecker
{
	use \BX\Logger\LoggerTrait;
	/**
	 * @var array
	 */
	protected $exts = [
		'gif','jpeg','png','ico','bmp',
	];
	/**
	 * @var \Imagine\Image\AbstractImage
	 */
	protected $image;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var Imagine
	 */
	protected $imagine = null;
	/**
	 * Get imagine
	 *
	 * @return \Imagine\Image\AbstractImagine
	 */
	protected function getImagine()
	{
		if ($this->imagine === null){
			$this->imagine = new Imagine();
		}
		return $this->imagine;
	}
	/**
	 * Set imagine
	 *
	 * @param \Imagine\Image\AbstractImagine $imagine
	 * @return Image
	 */
	public function setImagine($imagine)
	{
		$this->imagine = $imagine;
		return $this;
	}
	/**
	 * load image
	 *
	 * @param string $file
	 * @return boolean
	 */
	public function analize($file)
	{
		$pathinfo = pathinfo($file);
		if (isset($pathinfo['extension']) && in_array($pathinfo['extension'],$this->exts)){
			try{
				$this->image = $this->getImagine()->open($file);
				$this->name = sha1_file($file).'.png';
				return true;
			}catch (\Exception $ex){
				$error = 'Error load image: '.$ex->getTraceAsString();
				$this->log('validation.upload.checker.image')->err($error);
			}
		}
		return false;
	}
	/**
	 * Get file name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	/**
	 * Resave file
	 *
	 * @param string $file
	 * @return boolean
	 */
	public function save($file)
	{
		$this->getImagine()->open($file)->save($file,array('png_compression_level' => 9));
		return true;
	}
}