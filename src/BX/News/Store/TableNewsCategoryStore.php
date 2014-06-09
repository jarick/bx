<?php namespace BX\News\Store;
use BX\News\Entity\NewsCategoryEntity;
use BX\DB\UnitOfWork\Repository;

class TableNewsCategoryStore implements \BX\DB\ITable
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
			'db_table'	 => 'tbl_news_category',
			'event'		 => 'NewsCategory',
			'cache_tag'	 => 'NewsCategory',
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
			NewsCategoryEntity::C_ID			 => $this->column()->int('T.ID'),
			NewsCategoryEntity::C_GUID			 => $this->column()->string('T.GUID'),
			NewsCategoryEntity::C_NAME			 => $this->column()->string('T.NAME'),
			NewsCategoryEntity::C_CODE			 => $this->column()->string('T.CODE'),
			NewsCategoryEntity::C_ACTIVE		 => $this->column()->bool('T.ACTIVE'),
			NewsCategoryEntity::C_TEXT			 => $this->column()->string('T.TEXT'),
			NewsCategoryEntity::C_SORT			 => $this->column()->int('T.SORT'),
			NewsCategoryEntity::C_CREATE_DATE	 => $this->column()->datetime('T.CREATE_DATE'),
			NewsCategoryEntity::C_TIMESTAMP_X	 => $this->column()->datetime('TIMESTAMP_X'),
			NewsCategoryEntity::C_USER_ID		 => $this->column()->int('T.USER_ID'),
			NewsCategoryEntity::C_PARENT_ID		 => $this->column()->int('T.PARENT_ID'),
		];
	}
	/**
	 * Return relation
	 *
	 * @return array
	 */
	protected function relations()
	{
		return [
			[NewsCategoryEntity::C_USER_GUID,NewsCategoryEntity::C_USER_LOGIN],
			$this->relation()->left('tbl_user U','T.USER_ID = U.ID'),
			[NewsCategoryEntity::C_PARENT_GUID,NewsCategoryEntity::C_PARENT_NAME],
			$this->relation()->left('tbl_news_category T2','T.PARENT_ID = T2.ID'),
		];
	}
	/**
	 * Add news category
	 *
	 * @param Repository $repo
	 * @param NewsCategoryEntity $entity
	 * @return integer
	 * @throws \RuntimeException
	 */
	public function add(Repository $repo,NewsCategoryEntity $entity)
	{
		$repo->add($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error add news. Error: {$mess}.");
		}
		return $entity->id;
	}
	/**
	 * Update news category
	 *
	 * @param Repository $repo
	 * @param NewsCategoryEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function update(Repository $repo,NewsCategoryEntity $entity)
	{
		$repo->update($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error update category news. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Delete news
	 *
	 * @param Repository $repo
	 * @param NewsCategoryEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function delete(Repository $repo,NewsCategoryEntity $entity)
	{
		$repo->delete($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete category news. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Return finder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function getFinder()
	{
		return self::finder(NewsCategoryEntity::getClass());
	}
}