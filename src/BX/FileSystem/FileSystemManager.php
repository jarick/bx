<?php namespace BX\FileSystem;
use BX\Base\Registry;

class FileSystemManager
{
	use \BX\String\StringTrait;
	const DEFAULT_FILE_PERMISSION = 0644;
	const DEFAULT_FOLDER_PERMISSION = 0755;
	/**
	 * Remove all files and folder in directory
	 * @param string $path
	 * @throws \InvalidArgumentException
	 */
	public function removePathDir($path)
	{
		if (!is_dir($path)){
			throw new \InvalidArgumentException("$path must be a directory");
		}
		if (substr($path,$this->string()->length($path) - 1,1) != '/'){
			$path .= '/';
		}
		$files = glob($path.'*',GLOB_MARK);
		foreach($files as $file){
			if (is_dir($file)){
				$this->removePathDir($file);
			} else{
				unlink($file);
			}
		}
		rmdir($path);
		return true;
	}
	/**
	 * Check path dir
	 * @param string $path
	 * @return boolean
	 */
	public function checkPathDir($path)
	{
		$temp = str_replace(array("\\","//"),"/",$path);
		$folder = rtrim($temp,"/");
		if (!file_exists($folder)){
			return mkdir($folder,$this->getPermissionFolder(),true);
		} else{
			return true;
		}
	}
	/**
	 * Get permission folder
	 * @return string
	 */
	public function getPermissionFolder()
	{
		if (Registry::exists('permission','folder')){
			return Registry::get('permission','folder');
		} else{
			return self::DEFAULT_FOLDER_PERMISSION;
		}
	}
	/**
	 * Get permission file
	 * @return string
	 */
	public function getPermissionFile()
	{
		if (Registry::exists('permission','file')){
			return Registry::get('permission','file');
		} else{
			return self::DEFAULT_FILE_PERMISSION;
		}
	}
}