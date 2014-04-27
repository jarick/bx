<?php

class frameworkTest extends \BX\Test
{
	public function setUp()
	{
		Error::reset();
	}
	public function testFileSystem_true()
	{
		FileSystem::checkPathDir(__DIR__.'/test/test');
		$this->assertTrue(is_dir(__DIR__.'/test/test'));
		FileSystem::removePathDir(__DIR__.'/test/test');
		$this->assertFalse(is_dir(__DIR__.'/test/test'));
	}
	public function testFileSystem_false()
	{
		$this->assertFalse(FileSystem::checkPathDir('/:asasd:asdasdsad'));
		$this->assertInstanceOf('Exception',Error::get());
		Error::reset();
		$this->assertFalse(FileSystem::removePathDir('/:asasd:asdasdsad'));
		$this->assertInstanceOf('Exception',Error::get());
	}
	public function testString()
	{
		$this->assertTrue(Str::length(Str::getRandString(8)) === 8);
		$this->assertEquals('&lt;html&gt;',Str::escape('<html>'));
		$this->assertEquals('QWERTY',Str::toUpper('QwErTy'));
		$this->assertEquals('qwerty',Str::toLower('QwErTy'));
		$this->assertEquals('Qwerty',Str::ucwords('qwerty'));
	}
	public function testDate()
	{
		$this->assertTrue(Date::disableTimeZone());
		$this->assertTrue(Date::activeTimeZone());
		$this->assertTrue(Date::checkDateTime('10.10.2013 12:00:00','full'));
		$this->assertFalse(Date::checkDateTime('10.13.2013 122:00:00','full'));
		$this->assertEquals(1381392000,Date::makeTimeStamp('10.10.2013 12:00:00','full'));
		$this->assertEquals('10.10.2013 12:00:00',Date::convertTimeStamp(1381392000,'full'));
		$this->assertGreaterThan(0,Date::getOffset());
		$this->assertGreaterThan(0,Date::getUtc());
	}
	public function testEvent()
	{
		$func = function($test){
			$this->assertEquals('test',$test);
			return 5;
		};
		Event::on('test',$func);
		Event::on('test',$func);
		$this->assertEquals([5,5],Event::fire('test',['test']));
	}
	public function testCaptcha()
	{
		BX\DB\Schema::loadFromYamlFile();
		$guid = Captcha::getGuid();
		$this->assertGreaterThan(0,Str::length($guid));
		$code = Captcha::getCode($guid);
		$this->assertGreaterThan(0,Str::length($code));
		$this->assertTrue(Captcha::check($guid,$code));
		$reload = Captcha::reload($guid);
		$this->assertGreaterThan(0,Str::length($reload));
		$this->assertNotEquals($reload,$code);
		$this->assertTrue(Captcha::clear($guid));
		$this->assertTrue(Captcha::clearOld());
	}
}