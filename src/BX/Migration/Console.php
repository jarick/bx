<?php
namespace BX\Migration;
use BX\Console\Console;
use Bitrix\Main\DB\ConnectionException;

class Console extends Console
{
	public function run($aArgv)
	{
		if(!array_key_exists(0, $aArgv)){
			throw new ConnectionException('Action is not set');
		}
		if(!array_key_exists(1, $aArgv)){
			throw new ConnectionException('Console class is not set');
		}
		$sAction = $aArgv[0];
		explode(':', $aArgv[1]);
	}
}