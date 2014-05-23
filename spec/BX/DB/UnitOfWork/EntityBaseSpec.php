<?php namespace spec\BX\DB\UnitOfWork;
use BX\Config\DICService;
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
		$cache_ptr = function() use($cache){
			$cache->clearByTags('test')->shouldBeCalled()->willReturn(null);
			return $cache->getWrappedObject();
		};
		DICService::update('cache',$cache_ptr);
		$this->clearCache();
		DICService::delete('cache');
	}
	function it_addSearchIndex(ZendSearchManager $zendsearch)
	{
		$zendsearch_ptr = function() use($zendsearch){
			$str = Argument::type('string');
			$coll = Argument::type('BX\ZendSearch\SearchCollection');
			$zendsearch->add($str,$coll)->shouldBeCalled()->willReturn(null);
			return $zendsearch->getWrappedObject();
		};
		DICService::update('zend_search',$zendsearch_ptr);
		$this->addSearchIndex(1);
		DICService::delete('zend_search');
	}
	function it_deleteSearchIndex(ZendSearchManager $zendsearch)
	{
		$zendsearch_ptr = function() use($zendsearch){
			$str = Argument::type('string');
			$zendsearch->delete($str)->shouldBeCalled()->willReturn(null);
			return $zendsearch->getWrappedObject();
		};
		DICService::update('zend_search',$zendsearch_ptr);
		$this->deleteSearchIndex(1);
		DICService::delete('zend_search');
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