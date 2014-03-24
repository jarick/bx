<?php namespace BX\FileSystem\Manager;
use BX\Manager;
use BX\Registry;

class FileSystemManager extends Manager
{
	const DEFAULT_FILE_PERMISSION = 0644;
	const DEFAULT_FOLDER_PERMISSION = 0755;
	public function removePathDir($dirPath)
	{
		if (!is_dir($dirPath)){
			throw new \InvalidArgumentException("$dirPath must be a directory");
		}
		if (substr($dirPath,strlen($dirPath) - 1,1) != '/'){
			$dirPath .= '/';
		}
		$files = glob($dirPath.'*',GLOB_MARK);
		foreach ($files as $file){
			if (is_dir($file)){
				$this->removePathDir($file);
			} else{
				unlink($file);
			}
		}
		rmdir($dirPath);
	}
	public function checkPathDir($path)
	{
		$temp = str_replace(array("\\","//"),"/",$path);
		if (substr($temp,-1) != "/"){
			$temp = substr($temp,0,strrpos($temp,"/"));
		}
		$folder = rtrim($temp,"/");
		if (!file_exists($folder)){
			return mkdir($folder,$this->getPermissionFolder(),true);
		}
	}
	public function getPermissionFolder()
	{
		if (Registry::exists('permission','folder')){
			return Registry::get('permission','folder');
		} else{
			return self::DEFAULT_FOLDER_PERMISSION;
		}
	}
	public function getPermissionFile()
	{
		if (Registry::exists('permission','file')){
			return Registry::get('permission','file');
		} else{
			return self::DEFAULT_FILE_PERMISSION;
		}
	}
}