<?php
namespace BxProvider\LogHandler;
use Monolog\Handler\AbstractProcessingHandler;

class BitrixHandler extends AbstractProcessingHandler
{
	protected function write(array $record) 
	{	
		#\CEventLog::Add AddMessage2Log((string) $record['formatted']);
	}

}