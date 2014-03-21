<?php namespace BX\Logger\Manager;
use BX\Manager;
use Monolog\Logger as Monolog;
use Monolog\Handler\ChromePHPHandler;

class LoggerManager extends Manager
{
	use \Psr\Log\LoggerTrait;
	/**
	 * @var Monolog
	 */
	private $logger;
	/**
	 * Init
	 */
	public function init()
	{
		$this->logger = new Monolog('main');
		if ($this->isDevMode()){
			$this->logger->pushHandler(new ChromePHPHandler(Monolog::DEBUG));
		}
	}
	/**
	 * Log message
	 * @param string $level
	 * @param string $message
	 * @param array $context
	 */
	public function log($level,$message,array $context = [])
	{
		if ($this->fire('AddMessageToLog') === false){
			return;
		}
		$this->logger->log($level,$message,$context);
	}
}