<?php
namespace BX\FileSystem;
use BX\Registry;
use BX\FileSystem\Manager\FileSystemManager;

trait FileSystemTrait 
{
	/**
	 * @return FileSystemManager
	 **/
	public function filesystem()
	{
		return $this->getFileSystemManager();
	}
	
	private function getFileSystemManager()
	{
		static $oFileSystem;
		if(!isset($oFileSystem)){
			$oFileSystem = FileSystemManager::getManager();
		}
		return $oFileSystem;
	}
}