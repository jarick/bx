<?php namespace spec\BX\DB\Filter;
use BX\Config\DICService;
use BX\DB\DatabaseManager;
use BX\DB\DBResult;
use BX\DB\Test\TestTable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SqlBuilderSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\Filter\SqlBuilder');
	}
	function let()
	{
		$this->beConstructedWith(new TestTable(),TestTable::getClass());
	}
	function it_find(DatabaseManager $db)
	{
		$return = ['ID' => 1,'TEST' => 'TEST'];
		$result = new DBResult($return);
		$db_ptr = function() use($db,$result){
			$db->esc(Argument::any(),Argument::any())->willReturnArgument();
			$sql = 'SELECT T.ID as ID,T.TEST as TEST FROM tbl_test T '
				.'WHERE T.TEST = :test_0 AND T.TEST = :test '
				.'GROUP BY T.ID '
				.'ORDER BY T.ID DESC'
			;
			$params = ['test_0' => 'TEST','test' => 'TEST2'];
			$db->query($sql,$params)->shouldBeCalled()->willReturn($result);
			return $db->getWrappedObject();
		};
		DICService::update('db',$db_ptr);
		$this->filter(['=TEST' => 'TEST'])
			->select('ID','TEST')
			->sort(['ID' => 'desc'])
			->group('ID')
			->where('T.TEST = :test',['test' => 'TEST2'])
			->find()->shouldBe($result);
	}
	function letgo()
	{
		DICService::delete('db');
	}
}