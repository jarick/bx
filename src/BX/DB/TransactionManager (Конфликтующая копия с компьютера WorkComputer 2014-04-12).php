<?php namespace BX\DB;

class TransactionManager
{
	/**
	 * @var DatabaseManager
	 */
	private $db = null;
	/**
	 * Construct
	 * @param type $db
	 */
	public function __construct($db)
	{
		$this->db = $db;
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