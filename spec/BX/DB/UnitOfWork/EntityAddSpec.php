<?php namespace spec\BX\DB\UnitOfWork;
use BX\Base\DI;
use BX\Cache\CacheManager;
use BX\DB\Database;
use BX\DB\Test\TestTable;
use BX\Event\EventManager;
use BX\ZendSearch\ZendSearchManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EntityAddSpec extends ObjectBehavior
{
	private $entity;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\UnitOfWork\EntityAdd');
	}
	function let()
	{
		\BX\DB\Schema::loadFromYamlFile();
		$entity = new TestTable();
		$entity->test = 'TEST';
		$this->beConstructedWith($entity);
		$this->entity = $entity;
	}
	function it_commit(\BX\DB\UnitOfWork\Repository $repo)
	{
		$repo->setLazy($this->entity,Argument::any())->willReturn(true);
		$this->setRepository($repo);
		$this->validate()->shouldBe(true);
		$this->commit()->shouldBe('2');
		$return = ['ID' => '2','TEST' => 'TEST'];
		$this->db()->query('SELECT * FROM tbl_test WHERE ID = 2')->fetch()->shouldBe($return);
	}
	function it_rollback()
	{
		$this->id = '2';
		$this->rollback();
		$this->db()->query('SELECT * FROM tbl_test')->count()->shouldBe(1);
	}
	function it_validateAndAfter(EventManager $event,ZendSearchManager $zendsearch,CacheManager $cache)
	{
		$zendsearch->add(Argument::any(),Argument::any())->shouldBeCalled()->willReturn(null);
		$event->fire('OnStartTestAdd',Argument::any(),true)->shouldBeCalled()->willReturn(true);
		$event->fire('OnBeforeTestAdd',Argument::any(),true)->shouldBeCalled()->willReturn(true);
		$event->fire('OnAfterTestAdd',Argument::any(),true)->shouldBeCalled()->willReturn(true);
		$cache->clearByTags('test')->shouldBeCalled()->willReturn(null);
		DI::set('cache',$cache->getWrappedObject());
		DI::set('zend_search',$zendsearch->getWrappedObject());
		DI::set('event',$event->getWrappedObject());
		$this->validate();
		$this->onAfterCommit();
		DI::set('event',null);
		DI::set('zend_search',null);
		DI::set('cache',null);
	}
}