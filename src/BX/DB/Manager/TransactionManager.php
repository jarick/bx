<?php namespace BX\DB\Manager;
use BX\Manager;

class TransactionManager extends Manager
{
	/**
	 * @var Database
	 */
	private $db = null;
	/**
	 * Set db
	 * @param type $db
	 */
	public function setDb($db)
	{
		$this->db = $db;
		return $this;
	}
	/**
	 * Start transaction
	 * @return boolean
	 */
	public function begin()
	{
		return $this->db->adaptor()->pdo()->beginTransaction();
	}
	/**
	 * Commit transaction
	 * @return boolean
	 */
	public function commit()
	{
		return $this->db->adaptor()->pdo()->commit();
	}
	/**
	 * Rollback transaction
	 * @return boolean
	 */
	public function rollback()
	{
		return $this->db->adaptor()->pdo()->rollback();
	}
}