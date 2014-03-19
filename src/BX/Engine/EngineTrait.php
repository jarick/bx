<?php namespace BX\Engine;
use BX\Engine\Manager\HamlEngine;
use BX\Engine\IEngine;
use BX\Engine\Manager\PhpEngine;
use BX\Registry;
use InvalidArgumentException;

trait EngineTrait
{
	/**
	 * Get engine
	 * @return \BX\Engine\Manager\IEngine
	 * @throws InvalidArgumentException
	 */
	public function engine()
	{
		if (Registry::exists('templating','engine')){
			$engine = Registry::get('templating','engine');
		} else{
			$engine = 'php';
		}
		if ($engine instanceof IEngine){
			return $engine;
		}
		switch ($engine){
			case 'haml': return HamlEngine::getManager();
			case 'php': return PhpEngine::getManager();
			default: throw new InvalidArgumentException('Templating `$engine` not found');
		}
	}
}