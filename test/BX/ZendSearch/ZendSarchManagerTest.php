<?php namespace BX\ZendSearch;
use BX\Base\Registry;
use BX\Test;
use BX\ZendSearch\ZendSearchManager;
use ZendSearch\Lucene\Document\Field;

class ZendSarchManagerTest extends Test
{
	protected function setUp()
	{
		Registry::init([
			'zend_search' => [
				'morphy_dicts'	 => __DIR__.'/'.'search'.'/'.'dicts',
				'stop_words'	 => __DIR__.'/'.'search'.'/'.'stop-words'.'/'.'stop-words-ru.txt',
				'index'			 => __DIR__.'/'.'search'.'/'.'data',
			],
			],Registry::FORMAT_ARRAY);
	}
	public function test()
	{
		$search = new ZendSearchManager();
		$search->flush();
		$search->add(10,[
			Field::text('content','Соломенная шляпа'),
			Field::text('title','Сочный заголовок'),
		]);
		$this->assertEquals(current($search->findByQuery('id: 10'))->getDocument()->getFieldValue('id'),10);
		$this->assertEquals(current($search->find('соломенный'))->getDocument()->getFieldValue('id'),10);
		$this->assertEquals(current($search->find('шляпу'))->getDocument()->getFieldValue('id'),10);
		$this->assertEquals(current($search->find('сочная'))->getDocument()->getFieldValue('id'),10);
		$this->assertEquals(current($search->find('заголовки'))->getDocument()->getFieldValue('id'),10);
		$this->assertFalse(current($search->find('вечная')));
		$search->delete(10);
		$this->assertFalse(current($search->findByQuery('id: 10')));
		$search->flush();
	}
}