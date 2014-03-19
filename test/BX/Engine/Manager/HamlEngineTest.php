<?php namespace BX\Engine\Manager;
use BX\MVC\Manager\View;
use BX\Registry;
use BX\Http\Manager\Request;
use BX\DI;

class HamlEngineTest extends \PHPUnit_Framework_TestCase
{
	public function testGetRealPath()
	{
		$dr = dirname(__DIR__);
		$request = Request::getManager(false,['server' => ['DOCUMENT_ROOT' => $dr]]);
		DI::set('request',$request);
		$engine = HamlEngine::getManager();
		$this->assertEquals($dr.'/data',$engine->getRealPath('~/../Engine/data'));
		DI::set('request',null);
	}
	public function test()
	{
		$reg = [
			'templating' => [
				'php'	 => dirname(__DIR__).'/cache',
				'haml'	 => dirname(__DIR__).'/data',
			],
			'sites'		 => [
				'test_site' => [
					'meta' => 'test',
				]
			]
		];
		Registry::init($reg,Registry::FORMAT_ARRAY);
		$engine = new HamlEngine();
		$engine->init();
		$view = View::getManager(false,[])->loadMeta('sites','test_site');
		$view->buffer()->start();
		$this->assertTrue($engine->render($view,'data',['meta' => 'test','test' => $this]));
		$this->assertEquals('test',trim($view->buffer()->end()));
	}
}