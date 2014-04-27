<?php namespace BX\FileSystem;
use BX\Base\Registry;
use BX\Test;

class FileSystemManagerTest extends Test
{
	private $manager;
	private $reg;
	public function setUp()
	{
		$this->reg = Registry::all();
		Registry::init([
			'permission' => [
				'folder' => 0777,
				'file'	 => 0755,
			]
			],Registry::FORMAT_ARRAY);
		$this->manager = new FileSystemManager();
	}
	public function testCheckPathDir()
	{
		$file = __DIR__.'/data/test/test.txt';
		$this->assertTrue($this->manager->checkPathDir($file));
		$this->assertTrue(is_dir(dirname($file)));
	}
	public function testRemovePathDir()
	{
		$this->assertTrue($this->manager->checkPathDir(__DIR__.'/data/test'));
		$file = __DIR__.'/data';
		$this->manager->removePathDir($file);
		$this->assertFalse(is_dir($file));
	}
	public function testGetPermissionFolder()
	{
		$this->assertEquals(0777,$this->manager->getPermissionFolder());
	}
	public function testGetPermissionFile()
	{
		$this->assertEquals(0755,$this->manager->getPermissionFile());
	}
	public function tearDown()
	{
		$file = __DIR__.'/data';
		if (is_dir($file)){
			$this->manager->removePathDir(__DIR__.'/data');
		}
		Registry::init($this->reg,Registry::FORMAT_ARRAY);
	}
}