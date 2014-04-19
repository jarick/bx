<?php namespace BX\Engine;
use BX\Base\Registry;
use BX\Engine\Render\IRender;
use BX\Engine\Render\HamlRender;
use BX\Engine\Render\PhpRender;

class EngineManager implements IEngineManager
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
			if ($engine instanceof IRender){
				$this->render = $engine;
			}
			switch ($engine){
				case 'haml': $this->render = new HamlRender();
					break;
				case 'php': $this->render = new PhpRender();
					break;
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
	/**
	 * Is page exists
	 * @param string $path
	 * @return type
	 */
	public function exists($path)
	{
		return $this->getRender()->exists($path);
	}
}