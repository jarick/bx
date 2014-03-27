<?php namespace BX\Translate\Manager;
use BX\Registry;

class TranslateTest extends \BX\Test
{
	private $trans;
	public static function setUpBeforeClass()
	{
		require_once dirname(__DIR__).'/Message/Ru.php';
		Registry::init(['lang' => 'ru'],Registry::FORMAT_ARRAY);
	}
	public function setUp()
	{
		$this->trans = Translate::getManager();
	}
	public function testTrans()
	{
		$this->assertEquals('TEST TEST',$this->trans->trans('test',['#TEST#' => 'TEST'],'ru','BX','Translate'));
	}
}