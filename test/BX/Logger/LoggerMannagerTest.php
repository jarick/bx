<?php namespace BX\Logger;
use BX\Base\DI;
use BX\Test;

class LoggerManagerTest extends Test
{
	use LoggerTrait;
	public function test()
	{
		$manager = $this->getMock('BX\Logger\LoggerManager',['setHandler']);
		$manager->expects($this->once())->method('setHandler')
			->will($this->returnCallback(function($logger){
					$handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractProcessingHandler');
					$handler->expects($this->once())->method('write');
					$logger->pushHandler($handler);
				})
		);
		DI::set('logger',$manager);
		$this->log()->warn('12345');
	}
	public function tearDown()
	{
		DI::set('logger',null);
	}
}