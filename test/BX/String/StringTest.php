<?php namespace BX\String;

class StringTest extends \BX\Test
{
	/**
	 * @var StringManager
	 */
	private $string;
	public function setUp()
	{
		$this->string = new StringManager();
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
		$should = '&lt;input type=&quot;text&quot; value=&quot;значение&quot;&gt;';
		$be = '<input type="text" value="значение">';
		$this->assertEquals($this->string->escape($be),$should);
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