<?php namespace BX\DB;
use BX\DB\Database;
use BX\DB\TransactionManager;
use BX\Base\DI;

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
			DI::set($key,new Database());
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
			DI::set($key,new TransactionManager($this->db()));
		}
		return DI::get($key);
	}
}