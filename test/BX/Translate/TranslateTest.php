<?php namespace BX\Translate;

class TranslateManagerTest extends \BX\Test
{
	use \BX\Config\ConfigTrait;
	private $trans;
	private $reg;
	public function setUp()
	{
		$this->reg = $this->config()->all();
		$store = ['lang' => 'ru'];
		$this->config()->init('array',$store);
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
		$this->config()->init('array',$this->reg);
		parent::tearDown();
	}
}