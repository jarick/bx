<?php namespace BX\Engine;
use BX\Base\DI;
use BX\Engine\EngineManager;
use ZendSearch\Lucene\Exception\InvalidArgumentException;

trait EngineTrait
{
	/**
	 * Get engine
	 * @return \BX\Engine\Manager\IEngine
	 * @throws InvalidArgumentException
	 */
	public function engine()
	{
		$key = 'render';
		if (DI::get($key) === null){
			DI::set($key,new EngineManager());
		}
		return DI::get($key);
	}
}