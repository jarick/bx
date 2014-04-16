<?php namespace spec\BX\DB\UnitOfWork;
use BX\Base\DI;
use BX\Cache\CacheManager;
use BX\DB\Database;
use BX\DB\Test\TestTable;
use BX\Event\EventManager;
use BX\ZendSearch\ZendSearchManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EntityUpdateSpec extends ObjectBehavior
{
	/**
	 * @var TestTable
	 */
	private $entity;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\UnitOfWork\EntityUpdate');
	}
	function let()
	{
		\BX\DB\Schema::loadFromYamlFile();
		$entity = TestTable::finder()->filter(['ID' => 1])->get();
		$this->beConstructedWith($entity);
		$this->entity = $entity;
	}
	function it_commit()
	{
		$this->entity->test = 'TEST2';
		$this->validate()->shouldBe(true);
		$this->commit()->shouldBe('1');
		$return = ['ID' => '1','TEST' => 'TEST2'];
		$this->db()->query('SELECT * FROM tbl_test')->fetch()->shouldBe($return);
	}
	function it_rollback(Database $db)
	{
		$this->id = 1;
		$this->old_fields = ['TEST' => 'TEST'];
		$this->rollback();
		$return = ['ID' => '1','TEST' => 'TEST'];
		$this->db()->query('SELECT * FROM tbl_test')->fetch()->shouldBe($return);
	}
	function it_validateAndAfter(EventManager $event,ZendSearchManager $zendsearch,CacheManager $cache)
	{
		$zendsearch->delete(Argument::any())->shouldBeCalled();
		$zendsearch->add(Argument::any(),Argument::any())->shouldBeCalled();
		$event->fire('OnStartTestUpdate',Argument::any(),true)->shouldBeCalled()->willReturn(true);
		$event->fire('OnBeforeTestUpdate',Argument::any(),true)->shouldBeCalled()->willReturn(true);
		$event->fire('OnAfterTestUpdate',Argument::any(),true)->shouldBeCalled()->willReturn(true);
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