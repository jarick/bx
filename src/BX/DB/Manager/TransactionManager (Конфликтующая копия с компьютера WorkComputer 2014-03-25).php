<?php namespace BX\DB\Manager;
use BX\Manager;

class TransactionManager extends Manager
{
	/**
	 * @var Database
	 */
	private $db = null;
	
	public function setDb($db)
	{
		$this->db = $db;
	}

	public function start()
	{
		return $this->db->execute($this->db->adaptor()->startTransaction());
	}
	
	public function commit()
	{
		return $this->db->execute($this->db->adaptor()->commitTransaction());
	}
	
	public function rollback()
	{
		return $this->db->execute($this->db->adaptor()->rollbackTransaction());
	}
}