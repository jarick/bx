<?php namespace BX\Engine\Manager;
use BX\MVC\Manager\View;

class PhpEngineTest extends \PHPUnit_Framework_TestCase
{
	public function test()
	{
		$reg = [
			'sites' => [
				'test_site' => [
					'meta' => 'test',
				]
			]
		];
		\BX\Registry::init($reg,\BX\Registry::FORMAT_ARRAY);
		$engine = new PhpEngine();
		$engine->init();
		$engine->setFolder(dirname(__DIR__).'/data');
		$view = View::getManager(false,[])->loadMeta('sites','test_site');
		$view->buffer()->start();
		$this->assertTrue($engine->render($view,'data',['meta' => 'test','test' => $this]));
		$this->assertEquals('test',$view->buffer()->end());
	}
}