<?php namespace spec\BX\DB\UnitOfWork;
use BX\Base\DI;
use BX\Cache\CacheManager;
use BX\DB\Test\TestTable;
use BX\DB\UnitOfWork\EntityBase;
use BX\ZendSearch\ZendSearchManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EntityBaseSpec extends ObjectBehavior
{
	function let()
	{
		$table = new TestTable();
		$this->beAnInstanceOf('spec\BX\DB\UnitOfWork\TestEntityBase');
		$this->beConstructedWith($table);
	}
	function it_clearCache(CacheManager $cache)
	{
		$cache->clearByTags('test')->shouldBeCalled()->willReturn(null);
		DI::set('cache',$cache->getWrappedObject());
		$this->clearCache();
		DI::set('cache',null);
	}
	function it_addSearchIndex(ZendSearchManager $zendsearch)
	{
		$str = Argument::type('string');
		$coll = Argument::type('BX\ZendSearch\SearchCollection');
		$zendsearch->add($str,$coll)->shouldBeCalled()->willReturn(null);
		DI::set('zend_search',$zendsearch->getWrappedObject());
		$this->addSearchIndex(1);
		DI::set('zend_search',null);
	}
	function it_deleteSearchIndex(ZendSearchManager $zendsearch)
	{
		$str = Argument::type('string');
		$zendsearch->delete($str)->shouldBeCalled()->willReturn(null);
		DI::set('zend_search',$zendsearch->getWrappedObject());
		$this->deleteSearchIndex(1);
		DI::set('zend_search',null);
	}
}

class TestEntityBase extends EntityBase
{
	public function clearCache()
	{
		parent::clearCache();
	}
	public function addSearchIndex($id)
	{
		parent::addSearchIndex($id);
	}
	public function deleteSearchIndex($id)
	{
		parent::deleteSearchIndex($id);
	}
	public function commit()
	{

	}
	public function onAfterCommit()
	{

	}
	public function rollback()
	{

	}
	public function validate()
	{

	}
}