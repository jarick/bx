<?php namespace spec\BX\DB\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use BX\DB\Test\TestTable;
use BX\Event\EventManager;
use BX\ZendSearch\ZendSearchManager;
use BX\Cache\CacheManager;
use BX\Config\DICService;

class EntityDeleteSpec extends ObjectBehavior
{
	/**
	 * @var TestTable
	 */
	private $entity;
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\DB\UnitOfWork\EntityDelete');
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
		$this->validate()->shouldBe(true);
		$this->commit()->shouldBeLike(1);
		$this->db()->query('SELECT * FROM tbl_test')->fetch()->shouldBe(false);
	}
	function it_rollback()
	{
		$return = ['ID' => '1','TEST' => 'TEST'];
		$this->old_fields = $return;
		$this->rollback();
		$this->db()->query('SELECT * FROM tbl_test')->fetch()->shouldBe($return);
	}
	function it_validateAndAfter(EventManager $event,ZendSearchManager $zendsearch,CacheManager $cache)
	{
		$zendsearch->delete(Argument::any())->shouldBeCalled();
		$event->fire('OnBeforeTestDelete',Argument::any(),true)->shouldBeCalled()->willReturn(true);
		$event->fire('OnAfterTestDelete',Argument::any(),true)->shouldBeCalled()->willReturn(true);
		$cache->clearByTags('test')->shouldBeCalled()->willReturn(null);
		DICService::update('cache',function()use($cache){
			return $cache->getWrappedObject();
		});
		DICService::update('zend_search',function()use($zendsearch){
			return $zendsearch->getWrappedObject();
		});
		DICService::update('event',function()use($event){
			return $event->getWrappedObject();
		});
		$this->validate();
		$this->onAfterCommit();
		DICService::delete('event');
		DICService::delete('zend_search');
		DICService::delete('cache');
	}
}