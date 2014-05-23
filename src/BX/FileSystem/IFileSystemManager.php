<?php namespace BX\FileSystem;

interface IFileSystemManager
{
	public function removePathDir($path);
	public function checkPathDir($path);
}