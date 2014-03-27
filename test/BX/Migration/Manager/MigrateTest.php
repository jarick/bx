<?php namespace BX\Migration\Manager;
use BX\DI;

class MigrateTest extends \BX\Test
{
	use \BX\Date\DateTrait;
	private $migrate;
	public static function setUpBeforeClass()
	{
		require_once dirname(__DIR__).'/Migration.php';
	}
	public function setUp()
	{
		$this->migrate = new Migrate();
		$this->migrate->init();
		$this->migrate->setPackage('BX')->setService('Migration');
	}
	public function testAdd()
	{
		$return = [
			'upFirst'	 => ['upSecond' => ['upThird_1' => false,'upThird_2' => false]],
			'upRoot'	 => false,
		];
		$this->assertEquals($return,$this->invokeMethod($this->migrate,'getTreeUp'));
	}
	public function testParseTree()
	{
		$sql = 'SELECT T.FUNCTION as `FUNCTION` FROM tbl_migrate T WHERE '
			.'T.SERVICE = :service_0 AND T.PACKAGE = :package_0';
		$params = ['service_0' => 'Migration','package_0' => 'BX'];
		$db_return = [
			['FUNCTION' => 'upFirst'],
			['FUNCTION' => 'upRoot'],
		];
		$db = $this->getMock('BX\DB\Manager\Database',['query','add'],[]);
		$db->expects($this->at(0))->method('query')->with($this->equalTo($sql),$this->equalTo($params))
			->will($this->returnValue($db_return));
		$add = [
			'PACKAGE'		 => 'BX',
			'SERVICE'		 => 'Migration',
			'FUNCTION'		 => 'upSecond',
			'GUID'			 => $this->migrate->getHash(),
			'TIMESTAMP_X'	 => $this->date()->getUtc(),
		];
		$db->expects($this->at(1))->method('add')->with($this->equalTo('tbl_migrate'),$this->equalTo($add))
			->will($this->returnValue(true));
		$add['FUNCTION'] = 'upThird_1';
		$db->expects($this->at(2))->method('add')->with($this->equalTo('tbl_migrate'),$this->equalTo($add))
			->will($this->returnValue(true));
		$add['FUNCTION'] = 'upThird_2';
		$db->expects($this->at(3))->method('add')->with($this->equalTo('tbl_migrate'),$this->equalTo($add))
			->will($this->returnValue(true));
		DI::set('db',$db);
		$return = [
			'upFirst'	 => ['upSecond' => ['upThird_1' => false,'upThird_2' => false]],
			'upRoot'	 => false,
		];
		$this->invokeMethod($this->migrate,'parseTree',$return);
		DI::set('db',null);
	}
	public function testGetLastFunctions()
	{
		$sql = 'SELECT T.FUNCTION as `FUNCTION`,T.GUID as `GUID`,T.ID as `ID` FROM tbl_migrate T WHERE '
			.'T.SERVICE = :service_0 AND T.PACKAGE = :package_0 SORT BY T.TIMESTAMP_X desc';
		$params = ['service_0' => 'Migration','package_0' => 'BX'];
		$db_return = [];
		$db = $this->getMock('BX\DB\Manager\Database',['query'],[]);
		$db->expects($this->once())->method('query')->with($this->equalTo($sql),$this->equalTo($params))
			->will($this->returnValue($db_return));
		DI::set('db',$db);
		$this->invokeMethod($this->migrate,'getLastFunctions');
		DI::set('db',null);
	}
}