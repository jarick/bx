<?php namespace BX\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerManager implements \BX\Logger\ILoggerManager
{
	/**
	 * Set log handler
	 * @param \Monolog\Logger $logger
	 */
	protected function setHandler(Logger $logger)
	{
		$logger->pushHandler(new StreamHandler(__DIR__.'/../../../error.log',Logger::WARNING));
	}
	/**
	 * @param string $name
	 * @return Logger
	 */
	public function get($name = 'default')
	{
		$logger = new Logger($name);
		$this->setHandler($logger);
		return $logger;
	}
}