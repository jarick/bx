<?php namespace BX\FileSystem;

class FileSystemTraitTest extends \BX\Test
{
	use FileSystemTrait;
	public function test()
	{
		$this->assertInstanceOf('BX\FileSystem\FileSystemManager',$this->filesystem());
	}
}