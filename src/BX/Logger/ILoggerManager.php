<?php namespace BX\Logger;

interface ILoggerManager
{
	/**
	 * @param string $name
	 * @return Logger
	 */
	public function get($name = 'default');
}