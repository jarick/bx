<?php namespace BX\Migration\Command;
use BX\Console\Command\Console;

class Migrate extends Console
{
	use \BX\String\StringTrait,
	 \BX\Migration\MigrationTrait;
	/**
	 * Run
	 * @param array $args
	 * @throws \InvalidArgumentException
	 */
	public function run(array $args)
	{
		if (!isset($args[0])){
			throw new \InvalidArgumentException('Action is not set.');
		}
		$action = $args[0];
		if (!isset($args[1])){
			throw new \InvalidArgumentException("`$args[1]` service is not set.");
		}
		$service = $args[1];
		if ($this->string()->countSubstr($service,':') > 0){
			list($package,$service) = explode(':',$service);
		}else{
			$package = 'BX';
		}
		$migrate = $this->migrate($package,$service);
		switch ($action){
			case 'up': $migrate->up();
				break;
			case 'redo': $migrate->redo();
				break;
			case 'down': $migrate->down();
				break;
			default: throw new \InvalidArgumentException("Action `$action` is not found, allow action: `up,redo,down`.");
		}
		if (!$migrate->isFound()){
			$this->writer->error('Next migrate is not found.');
		}else{
			$this->writer->success('Success.');
		}
	}
	/**
	 * Get command
	 * @return string
	 */
	public function command()
	{
		return 'migrate';
	}
}