<?php namespace spec\BX\Counter;
use BX\DB\Schema;
use PhpSpec\ObjectBehavior;

class CounterManagerSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait;
	function let()
	{
		Schema::loadFromYamlFile();
		$this->beConstructedWith('qwerty');
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Counter\CounterManager');
	}
	function it_inc()
	{
		$this->inc('inc')->shouldBe(3);
		$sql = 'SELECT COUNTER FROM tbl_counter WHERE ENTITY = "qwerty" AND ENTITY_ID = "inc"';
		$data = $this->db()->query($sql)->fetch();
		$count = intval($data['COUNTER']);
		if ($count !== 3){
			throw new \RuntimeException("Test error. Get: $count");
		}
	}
	function it_clear()
	{
		$this->clear('inc')->shouldBe(true);
		$sql = 'SELECT COUNTER FROM tbl_counter WHERE ENTITY = "qwerty" AND ENTITY_ID = "inc"';
		$data = $this->db()->query($sql)->fetch();
		$count = intval($data['COUNTER']);
		if ($count !== 0){
			throw new \RuntimeException("Test error. Get: $count");
		}
	}
	function it_clear_old()
	{
		$this->clearOld(10)->shouldBe(true);
		$sql = 'SELECT COUNTER FROM tbl_counter WHERE ENTITY = "qwerty" AND ENTITY_ID = "inc"';
		$data = $this->db()->query($sql)->fetch();
		$count = intval($data['COUNTER']);
		if ($count !== 0){
			throw new \RuntimeException("Test error. Get: $count");
		}
	}
	function it_get()
	{
		$count = $this->getWrappedObject()->get('inc');
		if ($count->counter != 2){
			throw new \RuntimeException("Test error. Get: $count->counter");
		}
	}
}