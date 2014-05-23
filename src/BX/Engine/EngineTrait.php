<?php namespace BX\Engine;
use BX\Config\DICService;
use BX\Engine\EngineManager;

trait EngineTrait
{
	/**
	 * Get engine manager
	 *
	 * @return EngineManager
	 */
	protected function engine()
	{
		$name = 'render';
		if (DICService::get($name) === null){
			$manager = function(){
				return new EngineManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
}