<?php namespace BX\News\Store;
use BX\DB\Filter\SqlBuilder;
use BX\DB\ITable;
use BX\DB\UnitOfWork\Repository;
use BX\News\Entity\NewsEntity;

class TableNewsStore implements ITable
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
			'db_table'	 => 'tbl_news',
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
			NewsEntity::C_ID			 => $this->column()->int('T.ID'),
			NewsEntity::C_GUID			 => $this->column()->string('T.GUID'),
			NewsEntity::C_NAME			 => $this->column()->string('T.NAME'),
			NewsEntity::C_CODE			 => $this->column()->string('T.CODE'),
			NewsEntity::C_ACTIVE		 => $this->column()->bool('T.ACTIVE'),
			NewsEntity::C_PREVIEW_TEXT	 => $this->column()->string('T.PREVIEW_TEXT'),
			NewsEntity::C_DETAIL_TEXT	 => $this->column()->string('T.DETAIL_TEXT'),
			NewsEntity::C_PICTURE		 => $this->column()->file('T.PICTURE','image'),
			NewsEntity::C_SORT			 => $this->column()->int('T.SORT'),
			NewsEntity::C_CREATE_DATE	 => $this->column()->datetime('T.CREATE_DATE'),
			NewsEntity::C_TIMESTAMP_X	 => $this->column()->datetime('T.TIMESTAMP_X'),
			NewsEntity::C_USER_ID		 => $this->column()->int('T.USER_ID'),
			NewsEntity::C_USER_GUID		 => $this->column()->string('U.GUID'),
			NewsEntity::C_USER_LOGIN	 => $this->column()->string('U.LOGIN'),
		];
	}
	/**
	 * Return relation
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			[NewsEntity::C_USER_GUID,NewsEntity::C_USER_LOGIN],
			$this->relation()->left('tbl_user U','T.USER_ID = U.ID'),
		];
	}
	/**
	 * Add news
	 *
	 * @param Repository $repo
	 * @return NewsEntity $entity
	 * @throws \RuntimeException
	 */
	public function add(Repository $repo,NewsEntity $entity)
	{
		$repo->add($this,$entity);
		$picture = $entity->picture;
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error add news. Error: {$mess}.");
		}
		if ($picture !== null){
			$picture->saveFile();
		}
		return $entity->id;
	}
	/**
	 * Update news
	 *
	 * @param Repository $repo
	 * @return NewsEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function update(Repository $repo,NewsEntity $entity)
	{
		$repo->update($this,$entity);
		$picture = $entity->picture;
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error update news. Error: {$mess}.");
		}
		if ($picture !== null){
			$picture->saveFile();
		}
		return true;
	}
	/**
	 * Delete news
	 *
	 * @param Repository $repo
	 * @return NewsEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function delete(Repository $repo,NewsEntity $entity)
	{
		$repo->delete($this,$entity);
		$picture = $entity->picture;
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete news. Error: {$mess}.");
		}
		if ($picture !== null){
			$picture->deleteFile();
		}
		return true;
	}
	/**
	 * Return finder
	 *
	 * @return SqlBuilder
	 */
	public function getFinder()
	{
		return self::finder(NewsEntity::getClass());
	}
}