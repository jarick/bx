<?php namespace BX\News\Store;
use BX\News\Entity\NewsCategoryLinkEntity;
use BX\DB\UnitOfWork\Repository;

class TableNewsCategoryLinkStore implements \BX\DB\ITable
{
	use \BX\DB\TableTrait;
	/**
	 * Return settings
	 *
	 * @return array
	 */
	protected function settings()
	{
		return [
			'db_table'	 => 'tbl_news_category_link',
			'event'		 => 'News',
			'cache_tag'	 => 'News',
		];
	}
	/**
	 * Return columns
	 *
	 * @return array
	 */
	protected function columns()
	{
		return [
			NewsCategoryLinkEntity::C_ID			 => $this->column()->int('T.ID'),
			NewsCategoryLinkEntity::C_NEWS_ID		 => $this->column()->int('T.NEWS_ID'),
			NewsCategoryLinkEntity::C_CATEGORY_ID	 => $this->column()->int('T.CATEGORY_ID'),
			NewsCategoryLinkEntity::C_TIMESTAMP_X	 => $this->column()->datetime('T.TIMESTAMP_X'),
			NewsCategoryLinkEntity::C_NEWS_GUID		 => $this->column()->string('N.GUID'),
			NewsCategoryLinkEntity::C_NEWS_NAME		 => $this->column()->string('N.NAME'),
			NewsCategoryLinkEntity::C_CATEGORY_GUID	 => $this->column()->string('C.GUID'),
			NewsCategoryLinkEntity::C_CATEGORY_NAME	 => $this->column()->string('C.NAME'),
		];
	}
	/**
	 * Return relations
	 *
	 * @return array
	 */
	protected function relations()
	{
		return [
			[NewsCategoryLinkEntity::C_NEWS_GUID,NewsCategoryLinkEntity::C_NEWS_NAME],
			$this->relation()->left('tbl_news N','T.NEWS_ID = N.ID'),
			[NewsCategoryLinkEntity::C_CATEGORY_GUID,NewsCategoryLinkEntity::C_CATEGORY_NAME],
			$this->relation()->left('tbl_news_category C','T.CATEGORY_ID = C.ID'),
		];
	}
	/**
	 * Add link news and category
	 *
	 * @param Repository $repo
	 * @param NewsCategoryLinkEntity $entity
	 * @return integer
	 */
	public function add(Repository $repo,NewsCategoryLinkEntity $entity)
	{
		$repo->add($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete category news link. Error: {$mess}.");
		}
		return $entity->id;
	}
	/**
	 * Delete link by news id and category id
	 *
	 * @param Repository $repo
	 * @param NewsCategoryLinkEntity $entity
	 * @return boolean
	 */
	public function delete(Repository $repo,NewsCategoryLinkEntity $entity)
	{
		$repo->delete($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete category news link. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Delete all links by news array
	 *
	 * @param Repository $repo
	 * @param NewsCategoryLinkEntity[] $entities
	 * @return boolean
	 */
	public function deleteAll(Repository $repo,$entities)
	{
		foreach($entities as $entity){
			$repo->delete($this,$entity);
		}
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete category news link. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Return sql builder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function getFinder()
	{
		return self::finder(NewsCategoryLinkEntity::getClass());
	}
}