<?php namespace BX\FileSystem;
use BX\Base\DI;
use BX\FileSystem\FileSystemManager;

trait FileSystemTrait
{
	/**
	 * Get filesystem manager
	 * @return FileSystemManager
	 */
	public function filesystem()
	{
		$key = 'filesystem';
		if (DI::get($key) === null){
			DI::set($key,new FileSystemManager());
		}
		return DI::get($key);
	}
}