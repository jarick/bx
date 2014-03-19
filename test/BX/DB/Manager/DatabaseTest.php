<?php namespace BX\DB\Manager;
use BX\DB\Helper\TableColumn;

class DatebaseTest extends \PHPUnit_Framework_TestCase
{
	use \BX\String\StringTrait;
	const TABLE = 'tbl_test';
	/**
	 * @var Database
	 */
	private $db;
	public static function setUpBeforeClass()
	{
		\BX\DI::set('pdo',new \PDO('sqlite::memory'));
	}
	public function setUp()
	{
		$this->db = Database::getManager();
		$this->db->createTable(self::TABLE,[
			TableColumn::getPK('ID')->toArray(),
			TableColumn::getString('TEST',100)->setNotNull()->setDefault("TEST \"'%!")->toArray(),
		]);
	}
	public function testInitTableVarsForEdit()
	{
		$this->assertEquals(['ID' => null,'TEST' => "TEST \"'%!"],$this->db->initTableVarsForEdit(self::TABLE));
	}
	public function testAdd()
	{
		$this->db->add(self::TABLE,['TEST' => "TEST \"'%!"]);
		$values = $this->db->query('SELECT * FROM tbl_test')->getData();
		$this->assertEquals(1,intval($values[0]['ID']));
		$this->assertEquals("TEST \"'%!",$values[0]['TEST']);
		$this->assertTrue($this->string()->startsWith('~$key','~'));
	}
	public function testUpdate()
	{
		$this->db->add(self::TABLE,['TEST' => "TEST \"'%!"]);
		$this->db->update(self::TABLE,['TEST' => "TEST 2"],'ID =:id',['id' => 1]);
		$values = $this->db->query('SELECT * FROM tbl_test')->getData();
		$this->assertEquals(1,intval($values[0]['ID']));
		$this->assertEquals("TEST 2",$values[0]['TEST']);
	}
	public function testDelete()
	{
		$this->db->add(self::TABLE,['TEST' => "TEST \"'%!"]);
		$this->db->delete(self::TABLE,'ID =:id',['id' => 1]);
		$values = $this->db->query('SELECT * FROM tbl_test')->getData();
		$this->assertEquals([],$values);
	}
	public function tearDown()
	{
		$this->db->dropTable(self::TABLE);
	}
}