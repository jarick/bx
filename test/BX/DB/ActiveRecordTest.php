<?php namespace BX\DB;
use BX\Validator\Manager\String;
use BX\DI;
use BX\DB\Column\StringColumn;

class MockActiveRecordTest extends ActiveRecord
{
	protected function settings()
	{
		return [
			ActiveRecord::DB_TABLE	 => 'tbl_test',
			ActiveRecord::CACHE_TAG	 => 'cache_test',
			ActiveRecord::EVENT		 => 'cache_event',
			ActiveRecord::UF_ENTITY	 => 'cache_uf_event',
		];
	}
	protected function rules()
	{
		return [
			['NAME,TITLE,CODE',String::create()->notEmpty()],
		];
	}
	protected function columns()
	{
		return [
			'NAME'	 => StringColumn::create('T.NAME'),
			'TITLE'	 => StringColumn::create('T.TITLE'),
			'CODE'	 => StringColumn::create('T.CODE'),
		];
	}
}

class ActiveRecordTest extends \BX_Test
{
	/**
	 * @var ActiveRecord
	 */
	private $ar;
	public function setUp()
	{
		$this->ar = new MockActiveRecordTest();
	}
	public function testGetDbTable()
	{
		$this->assertEquals('tbl_test',$this->ar->getDbTable());
	}
	public function testGetCacheTag()
	{
		$this->assertEquals('cache_test',$this->ar->getCacheTag());
	}
	public function testGetEvent()
	{
		$this->assertEquals('cache_event',$this->ar->getEvent());
	}
	public function testUfEvent()
	{
		$this->assertEquals('cache_uf_event',$this->ar->getUfEntity());
	}
	public function testAdd()
	{
		$sql = 'INSERT INTO `tbl_test`(`NAME`,`TITLE`,`CODE`) VALUES (:name,:title,:code)';
		$params = [
			'name'	 => 'name',
			'title'	 => 'title',
			'code'	 => 'code',
		];
		$db = $this->getMock('BX\DB\Manager\Database',['execute'],[]);
		$db->expects($this->once())->method('execute')->with($this->equalTo($sql),$this->equalTo($params));
		DI::set('db',$db);
		$this->ar->add([
			'NAME'	 => 'name',
			'TITLE'	 => 'title',
			'CODE'	 => 'code',
		]);
	}
	public function testUpdate()
	{
		$sql = 'UPDATE `tbl_test` SET `NAME`=:name,`TITLE`=:title,`CODE`=:code WHERE `ID`=:id';
		$params = [
			'name'	 => 'name',
			'title'	 => 'title',
			'code'	 => 'code',
			'id'	 => 10,
		];
		$db = $this->getMock('BX\DB\Manager\Database',['execute'],[]);
		$db->expects($this->once())->method('execute')->with($this->equalTo($sql),$this->equalTo($params));
		DI::set('db',$db);
		$this->ar->update(10,[
			'NAME'	 => 'name',
			'TITLE'	 => 'title',
			'CODE'	 => 'code',
		]);
	}
	public function testDelete()
	{
		$sql = 'DELETE FROM `tbl_test` WHERE `ID`=:id';
		$params = [
			'id' => 10,
		];
		$db = $this->getMock('BX\DB\Manager\Database',['execute'],[]);
		$db->expects($this->once())->method('execute')->with($this->equalTo($sql),$this->equalTo($params));
		DI::set('db',$db);
		$this->ar->delete(10);
	}
	public function testFilter()
	{
		$this->assertInstanceOf('BX\DB\Filter\SqlBuilder',$this->ar->getFilter());
	}
	public function testHasMany()
	{
		$must = 'LEFT OUTER JOIN tbl_test T2 ON T.ID=T2.ID';
		$actual = $this->invokeMethod($this->ar,'hasMany','T2',new MockActiveRecordTest());
		$this->assertEquals($must,$actual);
	}
}