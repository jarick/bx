<?php namespace spec\BX\Mutex;
use PhpSpec\ObjectBehavior;

class MockMutex
{
	private $obj;
	public function testSet()
	{
		$this->obj = memcache_connect("localhost",11211);
		return memcache_set($this->obj,'var_key','',false,3000);
	}
	public function testGet()
	{
		return memcache_get($this->obj,'var_key');
	}
}

class MutexSpec extends ObjectBehavior
{
	private $run = false;
	function let()
	{
		$this->beAnInstanceOf('spec\BX\Mutex\MockMutex');
	}
	function it_lock()
	{
		if ($this->run){
			$this->testSet();
			exec("php ".__DIR__."/mutext.php >>buffer.log 2>&1 &");
			usleep(10000);
			exec("php ".__DIR__."/mutext2.php >>buffer.log 2>&1 &");
			sleep(1);
			$this->testGet()->shouldReturn('|1|2|a|b');
		}
	}
}