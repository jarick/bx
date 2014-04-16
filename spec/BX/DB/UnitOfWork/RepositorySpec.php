<?php namespace spec\BX\DB\UnitOfWork;
use BX\DB\Schema;
use BX\DB\Test\TestTable;
use PhpSpec\ObjectBehavior;

class RepositorySpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\UnitOfWork\Repository');
	}
	function let()
	{
		Schema::loadFromYamlFile();
		$this->beConstructedWith('test');
	}
	function it_test()
	{
		$entity = TestTable::finder()->filter(['ID' => 1])->get();
		$entity->test = 'QWERTY';
		$id = $this->add($entity)->getWrappedObject();
		$entity2 = new TestTable();
		$entity2->test = $id;
		$this->add($entity2);
		$this->commit()->shouldReturn(true);
		$this->db()->query("SELECT COUNT(*) AS CNT FROM tbl_test")->getData()
			->shouldBe([['CNT' => '3']]);
	}
	function is_test2()
	{
		$entity = TestTable::finder()->filter(['ID' => 1])->get();
		$this->delete($entity);
		$entity->test = 'QWERTY';
		$this->update($entity);
		$this->commit()->shouldReturn(true);
		$this->db()->query("SELECT COUNT(*) AS CNT FROM tbl_test")->getData()
			->shouldBe([['CNT' => '0']]);
	}
	function is_test3()
	{
		$entity = TestTable::finder()->filter(['ID' => 1])->get();
		$entity->test = 'QWERTY';
		$this->update($entity);
		$this->commit()->shouldReturn(true);
		$this->db()->query("SELECT TEST FROM tbl_test")->getData()
			->shouldBe([['TEST' => 'QWERTY']]);
	}
}