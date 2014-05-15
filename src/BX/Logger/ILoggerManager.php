<?php namespace BX\Logger;

interface ILoggerManager
{
	/**
	 * Return logger manager
	 *
	 * @param string $name
	 * @return \Monolog\Logger
	 */
	public function get($name = 'default');
}