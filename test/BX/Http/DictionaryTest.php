<?php namespace BX\Http;
use BX\Http\Dictionary;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
	public function testGet()
	{
		$dictionary = new Dictionary(['TEST' => 'TEST']);
		$this->assertEquals($dictionary->get('TEST'),'TEST');
		$this->assertEquals($dictionary['TEST'],'TEST');
	}
	public function testHas()
	{
		$dictionary = new Dictionary(['TEST' => 'TEST']);
		$this->assertTrue($dictionary->has('TEST'));
		$this->assertTrue(isset($dictionary['TEST']));
	}
}