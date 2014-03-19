<?php namespace BX\Migration\Command;
use BX\Console\Command\Console;
use BX\Migration\Manager\Migrate as MigrateManager;

class Migrate extends Console
{
	public function run(array $args)
	{
		if (!isset($aArgv[0])){
			throw new \InvalidArgumentException('Action is not set.');
		}
		$sAction = $aArgv[0];
		if (!isset($aArgv[1])){
			throw new \InvalidArgumentException("`$aArgv[1]` service is not set.");
		}
		$sService = $aArgv[1];
		if (substr_count($sService,':') > 0){
			list($sPackage,$sService) = explode(':',$sService);
		} else{
			$sPackage = self::getPackage();
		}
		$oMigrate = MigrateManager::getManager(false,[
				'package'	 => $sPackage,
				'service'	 => $sService,
		]);
		switch ($sAction){
			case 'up': $oMigrate->up();break;
			case 'redo': $oMigrate->redo();break;
			case 'down': $oMigrate->down();break;
			default: throw new \InvalidArgumentException("Action `$sAction` is not found, allow action: `up,redo,down`.");
		}
		if (!$oMigrate->isFound()){
			$this->getWriter()->error('Next migrate is not found.');
		} else{
			$this->getWriter()->success('Success.');
		}
	}
	public function command()
	{
		return 'migrate';
	}
}