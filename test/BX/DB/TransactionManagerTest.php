<?php namespace BX\DB;
use BX\DBTest;

class TransactionManagerTest extends DBTest
{
	use \BX\DB\DBTrait;
	public function testRollback()
	{
		$save = ['TEST' => 'TEST'];
		$this->transaction()->begin();
		$this->assertGreaterThan(0,$this->db()->add('tbl_test',$save));
		$this->transaction()->rollback();
		$this->assertTableRowCount('tbl_test',0);
	}
	public function testCommit()
	{
		$save = ['TEST' => 'TEST'];
		$this->transaction()->begin();
		$this->assertGreaterThan(0,$this->db()->add('tbl_test',$save));
		$this->transaction()->commit();
		$this->assertTableRowCount('tbl_test',1);
	}
}