<?php namespace BX\DB;
use BX\DB\Manager\Database;
use BX\DI;

trait DBTrait
{
	/**
	 * Get database manager
	 * @return Database
	 */
	public function db()
	{
		$key = 'db';
		if (DI::get($key) === null){
			DI::set($key,Database::getManager());
		}
		return DI::get($key);
	}
}