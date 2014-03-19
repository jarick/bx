<?php namespace BX\DB\Manager;
use BX\DB\Manager\Database;
use BX\DB\Filter\SqlBuilder;
use BX\DB\Manager\DBResult;

class SqlBuilderTest extends \PHPUnit_Framework_TestCase
{
	private function db()
	{
		return Database::getManager();
	}
	public function testSort()
	{
		$builder = new SqlBuilder($this->db(),'tbl_test',['TEST' => 'T.TEST','TEST2' => 'T.TEST2']
		);
		$builder->sort(['TEST' => 'ASC','TEST2' => 'DESC']);
		$return = ['T.TEST asc','T.TEST2 desc'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'sort_sql'),$return);
	}
	public function testGroup()
	{
		$builder = new SqlBuilder($this->db(),'tbl_test',['TEST' => 'T.TEST','TEST2' => 'T.TEST2']
		);
		$builder->group(['TEST','TEST2']);
		$return = ['T.TEST','T.TEST2'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'group_sql'),$return);
	}
	public function testSelect()
	{
		$builder = new SqlBuilder($this->db(),'tbl_test',['TEST' => 'T.TEST','TEST2' => 'T.TEST2']);
		$builder->select(['TEST','TEST2' => 'MAX']);
		$return = ['T.TEST as `TEST`','MAX(T.TEST2) as `MAX_TEST2`'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'select_sql'),$return);

		$builder2 = new SqlBuilder($this->db(),'tbl_test',['TEST' => 'T.TEST','TEST2' => 'T.TEST2']);
		$builder2->select(['*','TEST2' => 'MAX']);
		$return2 = ['T.TEST as `TEST`','T.TEST2 as `TEST2`','MAX(T.TEST2) as `MAX_TEST2`'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder2,'select_sql'),$return2);
	}
	public function testQuery()
	{
		$sql = 'SELECT T.TEST as `TEST`,T2.TEST2 as `TEST2`'.
			' FROM tbl_test T LEFT OUTER JOIN tbl_test2 T2 ON T2.TEST = T.TEST'.
			' WHERE T.TEST = :test_0 OR T2.TEST2 = :test2_0'.
			' GROUP BY T.TEST'.
			' SORT BY T.TEST asc'.
			' LIMIT 5 OFFSET 1'
		;
		$db = $this->getMock('BX\DB\Manager\Database',['query']);
		$db->expects($this->any())
			->method('query')
			->with($this->equalTo($sql),$this->equalTo(['test_0' => 'a','test2_0' => 'b']))
		;
		$fields = ['TEST' => 'T.TEST','TEST2' => 'T2.TEST2'];
		$fields_rules = ['TEST,TEST2' => 'string'];
		$relation = ['TEST2' => 'LEFT OUTER JOIN tbl_test2 T2 ON T2.TEST = T.TEST'];
		$builder = new SqlBuilder($db,'tbl_test',$fields,$fields_rules,$relation);
		$builder->sort(['TEST' => 'asc'])
			->filter(['LOGIC' => 'OR','=TEST' => 'a','=TEST2' => 'b'])
			->group(['TEST'])
			->limit(5)
			->offset(1)
			->all()
		;
	}
	public function testCache()
	{
		$sql = 'SELECT T.TEST as `TEST` FROM tbl_test T';
		$db = $this->getMock('BX\DB\Manager\Database',['query']);
		$aData = [['TEST' => 1],['TEST' => 2]];
		$db->expects($this->once())
			->method('query')
			->with($this->equalTo($sql),$this->equalTo([]))
			->will($this->returnValue(
					DBResult::getManager(false,['result' => $aData])
		));
		$fields = ['TEST' => 'T.TEST','TEST2' => 'T2.TEST2'];
		$fields_rules = ['TEST,TEST2' => 'string'];
		$relation = ['TEST2' => 'LEFT OUTER JOIN tbl_test2 T2 ON T2.TEST = T.TEST'];
		$builder = new SqlBuilder($db,'tbl_test',$fields,$fields_rules,$relation);
		$this->assertEquals($builder->cache()->select('TEST')->all()->getData(),$aData);
		$this->assertEquals($builder->select('TEST')->all()->getData(),$aData);
	}
}