<?php namespace BX\Config;

interface IConfigManager
{
	public function init($store,$format);
	public function exists(array $key);
	public function get(array $key);
	public function all();
}