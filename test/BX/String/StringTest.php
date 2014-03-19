<?php
use BX\String\Manager\StringManager;

class StringTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var StringManager
	 */
	private $string;
	public function setUp()
	{
		$this->string = StringManager::getManager();
	}
	public function testToUpper()
	{
		$this->assertEquals($this->string->toUpper('тест'),'ТЕСТ');
	}
	public function testToLower()
	{
		$this->assertEquals($this->string->toLower('ТЕСТ'),'тест');
	}
	public function testEscape()
	{
		$this->assertEquals($this->string->escape('<input type="text" value="значение">'),'&lt;input type=&quot;text&quot; value=&quot;значение&quot;&gt;');
	}
	public function testStartsWith()
	{
		$this->assertTrue($this->string->startsWith('asdasd','asd'));
		$this->assertFalse($this->string->startsWith('dasdasd','asd'));
	}
	public function testEndsWith()
	{
		$this->assertTrue($this->string->endsWith('asdasd','asd'));
		$this->assertFalse($this->string->endsWith('dasdasd','sasd'));
	}
}