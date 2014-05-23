<?php namespace BX\Config;

interface IConfigManager
{
	public function init($store,$format);
	public function exists();
	public function get();
	public function all();
	public function getCharset();
	public function isDevMode();
}