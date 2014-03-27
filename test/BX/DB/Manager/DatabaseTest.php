<?php namespace BX\DB\Manager;
use BX\DBTest;

class DatebaseTest extends DBTest
{
	use \BX\String\StringTrait;
	const TABLE = 'tbl_test';
	public function setUp()
	{
		parent::setUp();
		$this->db()->add(self::TABLE,['ID' => 1,'TEST' => "TEST \"'%!"]);
	}
	public function testInitTableVarsForEdit()
	{
		$vars = $this->db()->initTableVarsForEdit(self::TABLE);
		$this->assertEquals(['ID' => '1','TEST' => "TEST"],$vars);
	}
	public function testAdd()
	{
		$values = $this->db()->query('SELECT * FROM tbl_test')->getData();
		$this->assertEquals(1,intval($values[0]['ID']));
		$this->assertEquals("TEST \"'%!",$values[0]['TEST']);
		$this->assertTrue($this->string()->startsWith('~$key','~'));
	}
	public function testUpdate()
	{
		$this->db()->update(self::TABLE,['TEST' => "TEST 2"],'ID =:id',['id' => 1]);
		$values = $this->db()->query('SELECT * FROM tbl_test')->getData();
		$this->assertEquals(1,intval($values[0]['ID']));
		$this->assertEquals("TEST 2",$values[0]['TEST']);
	}
	public function testDelete()
	{
		$this->db()->delete(self::TABLE,'ID =:id',['id' => 1]);
		$values = $this->db()->query('SELECT * FROM tbl_test')->getData();
		$this->assertEquals([],$values);
	}
}