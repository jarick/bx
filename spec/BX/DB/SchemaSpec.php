<?php namespace spec\BX\DB;
use BX\Base\DI;
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
		$schema = [
			'tbl_test' => [
				'TEST' => 'TEST',
			],
		];
		$adaptor->resetAI('tbl_test')->shouldBeCalled();
		$adaptor->getLastId()->willReturn(5);
		$adaptor->pdo()->willReturn($this->db()->adaptor()->pdo());
		$adaptor->getQuoteChar()->willReturn('`');
		$adaptor->execute('DELETE FROM `tbl_test` WHERE 1=1',[])->shouldBeCalled()->willReturn(true);
		$adaptor->execute('INSERT INTO `tbl_test`(`TEST`) VALUES(:test)',['test' => 'TEST'])
			->shouldBeCalled()->willReturn(true);
		DI::set('adaptor',$adaptor->getWrappedObject());
		$this->load($schema)->shouldBe(true);
	}
	function letgo()
	{
		DI::set('adaptor',null);
	}
}