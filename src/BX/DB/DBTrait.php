<?php namespace BX\DB;
use BX\DB\Manager\Database;
use BX\DB\Manager\TransactionManager;
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
	/**
	 * Get transaction manager
	 * @return TransactionManager
	 */
	public function transaction()
	{
		$key = 'transaction';
		if (DI::get($key) === null){
			DI::set($key,TransactionManager::getManager(false,['db' => $this->db()]));
		}
		return DI::get($key);
	}
}