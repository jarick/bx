<?php namespace BX\News;
use BX\News\Entity\NewsCategoryLinkEntity;

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
}