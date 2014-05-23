<?php namespace spec\BX\DB;
use BX\Config\DICService;
use BX\DB\Adaptor\Sqlite;
use PhpSpec\ObjectBehavior;

class SchemaSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\Schema');
	}
	function it_load(Sqlite $adaptor)
	{
		$adaptor_ptr = function() use($adaptor){
			$adaptor->resetAI('tbl_test')->shouldBeCalled();
			$adaptor->getLastId()->willReturn(5);
			$pdo = new Sqlite();
			$adaptor->pdo()->willReturn($pdo->pdo());
			$adaptor->getQuoteChar()->willReturn('`');
			$adaptor->execute('DELETE FROM `tbl_test` WHERE 1=1',[])->shouldBeCalled()->willReturn(true);
			$adaptor->execute('INSERT INTO `tbl_test`(`TEST`) VALUES(:test)',['test' => 'TEST'])
				->shouldBeCalled()->willReturn(true);
			return $adaptor->getWrappedObject();
		};
		DICService::update('db_adaptor',$adaptor_ptr);
		$schema = [
			'tbl_test' => [
				'TEST' => 'TEST',
			],
		];
		$this->load($schema)->shouldBe(true);
	}
	function letgo()
	{
		DICService::delete('db_adaptor');
	}
}