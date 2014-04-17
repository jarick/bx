<?php namespace BX\Engine;
use BX\Base\Registry;
use BX\Engine\Render\IRender;
use BX\Engine\Render\HamlEngine;
use BX\Engine\Render\PhpEngine;

class EngineManager
{
	/**
	 * @var IRender
	 */
	private $render = null;
	/**
	 * Get render
	 * @return IRender
	 * @throws \InvalidArgumentException
	 */
	private function getRender()
	{
		if ($this->render === null){
			if (Registry::exists('templating','engine')){
				$engine = Registry::get('templating','engine');
			}else{
				$engine = 'php';
			}
			if ($engine instanceof IEngine){
				$this->render = $engine;
			}
			switch ($engine){
				case 'haml': $this->render = HamlEngine();
				case 'php': $this->render = PhpEngine();
				default: throw new \InvalidArgumentException("Templating `$engine` not found");
			}
		}
		return $this->render;
	}
	/**
	 * Get render
	 * @param View $view
	 * @param string $path
	 * @param array $params
	 * @return IRender
	 */
	public function render($view,$path,array $params = [])
	{
		return $this->getRender()->render($view,$path,$params);
	}
}