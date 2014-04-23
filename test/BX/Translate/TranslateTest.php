<?php namespace BX\Translate;
use BX\Base\Registry;

class TranslateManagerTest extends \BX\Test
{
	private $trans;
	private $reg;
	public function setUp()
	{
		$this->reg = Registry::all();
		Registry::init(['lang' => 'ru'],Registry::FORMAT_ARRAY);
		$this->trans = new TranslateManager();
		$this->trans->addArrayResource(['test' => 'TEST #TEST#']);
		parent::setUp();
	}
	public function testTrans()
	{
		$message = $this->trans->trans('test',['#TEST#' => 'TEST'],'ru');
		$this->assertEquals('TEST TEST',$message);
	}
	public function tearDown()
	{
		Registry::init($this->reg,Registry::FORMAT_ARRAY);
		parent::tearDown();
	}
}