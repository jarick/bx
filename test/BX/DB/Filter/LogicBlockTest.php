<?php namespace BX\DB\Filter;
use BX\DB\Manager\Database;
use BX\DB\Filter\SqlBuilder;
use Carbon\Carbon;

class LogicBlockTest extends \PHPUnit_Framework_TestCase
{
	private function db()
	{
		return Database::getManager();
	}
	public function testString()
	{
		$field = ['TEST' => 'T.TEST','TEST2' => 'T.TEST2'];
		$field_rules = ['TEST,TEST2' => 'string'];
		$builder = new SqlBuilder($this->db(),'tbl_test',$field,$field_rules);
		$builder->filter(['=TEST' => 'asd','TEST' => 'f','TEST2' => false,'%TEST' => 'a']);
		$filter = ['T.TEST = :test_0 AND T.TEST LIKE :test_1 AND (T.TEST2 IS NULL OR length(T.TEST2)=0) '
			.'AND upper(T.TEST) LIKE upper(:test_2)'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'filter_sql'),$filter);
		$vars = ['test_0' => 'asd','test_1' => 'f','test_2' => '%a%'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'vars'),$vars);
	}
	public function testStringNot()
	{
		$field = ['TEST' => 'T.TEST','TEST2' => 'T.TEST2'];
		$field_rules = ['TEST,TEST2' => 'string'];
		$builder = new SqlBuilder($this->db(),'tbl_test',$field,$field_rules);
		$builder->filter(['!=TEST' => 'asd','!TEST' => 'f','!TEST2' => false,'!%TEST' => 'a']);
		$filter = ['NOT(T.TEST = :test_0) AND NOT(T.TEST LIKE :test_1) '
			.'AND NOT((T.TEST2 IS NULL OR length(T.TEST2)=0)) AND NOT(upper(T.TEST) LIKE upper(:test_2))'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'filter_sql'),$filter);
		$vars = ['test_0' => 'asd','test_1' => 'f','test_2' => '%a%'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'vars'),$vars);
	}
	public function testNumber()
	{
		$field = ['TEST' => 'T.TEST','TEST2' => 'T.TEST2'];
		$field_rules = ['TEST,TEST2' => 'number'];
		$builder = new SqlBuilder($this->db(),'tbl_test',$field,$field_rules);
		$builder->filter(['TEST' => 1,'>TEST' => 2,'<TEST' => 3,'TEST2' => false,'>=TEST' => 4,'<=TEST' => 5]);
		$filter = ['T.TEST = :test_0 AND T.TEST > :test_1 AND T.TEST < :test_2 AND (T.TEST2 IS NULL OR length(T.TEST2)=0) '
			.'AND T.TEST >= :test_3 AND T.TEST <= :test_4'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'filter_sql'),$filter);
		$vars = ['test_0' => 1,'test_1' => 2,'test_2' => 3,'test_3' => 4,'test_4' => 5];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'vars'),$vars);
	}
	public function testNumberNot()
	{
		$field = ['TEST' => 'T.TEST','TEST2' => 'T.TEST2'];
		$field_rules = ['TEST,TEST2' => 'number'];
		$builder = new SqlBuilder($this->db(),'tbl_test',$field,$field_rules);
		$builder->filter(['!TEST' => 1,'!>TEST' => 2,'!<TEST' => 3,'!TEST2' => false,'!>=TEST' => 4,'!<=TEST' => 5]);
		$filter = ['NOT(T.TEST = :test_0) AND NOT(T.TEST > :test_1) AND NOT(T.TEST < :test_2) '
			.'AND NOT((T.TEST2 IS NULL OR length(T.TEST2)=0)) AND NOT(T.TEST >= :test_3) AND NOT(T.TEST <= :test_4)'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'filter_sql'),$filter);
		$vars = ['test_0' => 1,'test_1' => 2,'test_2' => 3,'test_3' => 4,'test_4' => 5];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'vars'),$vars);
	}
	public function testDateTime()
	{
		$field = ['TEST' => 'T.TEST','TEST2' => 'T.TEST2'];
		$field_rules = ['TEST,TEST2' => 'date'];
		$builder = new SqlBuilder($this->db(),'tbl_test',$field,$field_rules);
		$date = Carbon::createFromDate(2014,3,8,'GMT');
		$tmp = $date->format('d.m.Y');
		$timestamp = ''.$date->getTimestamp();
		$builder->filter(['TEST' => $tmp,'>TEST' => $tmp,'<TEST' => $tmp,'TEST2' => false,'>=TEST' => $tmp,'<=TEST' => $tmp]);
		$filter = ['T.TEST = :test_0 AND T.TEST > :test_1 AND T.TEST < :test_2 '
			.'AND (T.TEST2 IS NULL OR length(T.TEST2)=0) AND T.TEST >= :test_3 AND T.TEST <= :test_4'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'filter_sql'),$filter);
		$vars = ['test_0' => $timestamp,'test_1' => $timestamp,'test_2' => $timestamp,'test_3' => $timestamp,'test_4' => $timestamp];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'vars'),$vars);
	}
	public function testDateTimeNot()
	{
		$field = ['TEST' => 'T.TEST','TEST2' => 'T.TEST2'];
		$field_rules = ['TEST,TEST2' => 'date'];
		$builder = new SqlBuilder($this->db(),'tbl_test',$field,$field_rules);
		$date = Carbon::createFromDate(2014,3,8,'GMT');
		$tmp = $date->format('d.m.Y');
		$timestamp = ''.$date->getTimestamp();
		$builder->filter(['!TEST' => $tmp,'!>TEST' => $tmp,'!<TEST' => $tmp,'!TEST2' => false,'!>=TEST' => $tmp,'!<=TEST' => $tmp]);
		$filter = ['NOT(T.TEST = :test_0) AND NOT(T.TEST > :test_1) AND NOT(T.TEST < :test_2) '
			.'AND NOT((T.TEST2 IS NULL OR length(T.TEST2)=0)) AND NOT(T.TEST >= :test_3) AND NOT(T.TEST <= :test_4)'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'filter_sql'),$filter);
		$vars = ['test_0' => $timestamp,'test_1' => $timestamp,'test_2' => $timestamp,'test_3' => $timestamp,'test_4' => $timestamp];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'vars'),$vars);
	}
	public function testBoolean()
	{
		$field = ['TEST' => 'T.TEST','TEST2' => 'T.TEST2'];
		$field_rules = ['TEST,TEST2' => 'boolean'];
		$builder = new SqlBuilder($this->db(),'tbl_test',$field,$field_rules);
		$builder->filter(['TEST' => 'Y','!TEST' => 'N']);
		$filter = ['T.TEST = 1 AND NOT(T.TEST = 0)'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'filter_sql'),$filter);
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'vars'),[]);
	}
	public function testBlock()
	{
		$field = ['TEST' => 'T.TEST','TEST2' => 'T.TEST2'];
		$field_rules = ['TEST,TEST2' => 'boolean'];
		$builder = new SqlBuilder($this->db(),'tbl_test',$field,$field_rules);
		$builder->filter(['TEST' => 'Y',['LOGIC' => 'OR','TEST2' => 'Y','!TEST' => 'N']]);
		$filter = ['T.TEST = 1 AND (T.TEST2 = 1 OR NOT(T.TEST = 0))'];
		$this->assertEquals(\PHPUnit_Framework_Assert::readAttribute($builder,'filter_sql'),$filter);
	}
}