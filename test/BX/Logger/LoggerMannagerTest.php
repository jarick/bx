<?php namespace BX\Logger;
use BX\Config\DICService;
use BX\Test;

class LoggerManagerTest extends Test
{
	use LoggerTrait;
	public function test()
	{
		$func = function(){
			$manager = $this->getMock('BX\Logger\LoggerManager',['setHandler']);
			$manager->expects($this->once())->method('setHandler')
				->will($this->returnCallback(function($logger){
						$handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractProcessingHandler');
						$handler->expects($this->once())->method('write');
						$logger->pushHandler($handler);
					})
			);
			return $manager;
		};
		DICService::update('logger',$func);
		$this->log()->warn('12345');
	}
	public function tearDown()
	{
		DICService::delete('logger');
	}
}