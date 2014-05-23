<?php namespace BX\FileSystem;
use BX\Config\DICService;

trait FileSystemTrait
{
	/**
	 * Get filesystem manager
	 *
	 * @return IFileSystemManager
	 */
	protected function filesystem()
	{
		$name = 'filesystem';
		if (DICService::get($name) === null){
			$manager = function(){
				return new FileSystemManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}