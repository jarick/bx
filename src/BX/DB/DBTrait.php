<?php namespace BX\DB;
use BX\Config\DICService;

trait DBTrait
{
	/**
	 * Get database manager
	 *
	 * @return DatabaseManager
	 */
	public function db()
	{
		$key = 'db';
		if (DICService::get($key) === null){
			$manager = function(){
				return new DatabaseManager();
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
	/**
	 * Get transaction manager
	 *
	 * @return TransactionManager
	 */
	public function transaction()
	{
		$key = 'transaction';
		if (DICService::get($key) === null){
			$manager = function(){
				return new TransactionManager($this->db());
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
}