<?php namespace BX;
use BX\MVC\Entity\Site;

class MockObjectTest extends \BX\Entity
{
	private $test = false;
	public function setTest($test)
	{
		$this->test = $test;
		return $this;
	}
	public function getTest()
	{
		return $this->test;
	}
}

class ObjectTest extends \PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		Registry::init([
			'entities' => [
				'BX:MVC:Site' => [
					'class'	 => 'BX\MockObjectTest',
					'params' => ['test' => 'test'],
				],
			],
			],Registry::FORMAT_ARRAY
		);
	}
	public function testAutoload()
	{
		$entity = Site::getEntity();
		$this->assertEquals('test',$entity->getTest());
	}
}