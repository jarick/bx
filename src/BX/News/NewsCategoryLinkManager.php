<?php namespace BX\News;
use BX\News\Entity\NewsCategoryLinkEntity;
use BX\News\Store\TableNewsCategoryLinkStore;

class NewsCategoryLinkManager
{
	use \BX\Config\ConfigTrait;
	/**
	 * @var TableNewsCategoryStore
	 */
	private $store;
	/**
	 * @return TableNewsCategoryLinkStore
	 */
	private function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('news','category','store')){
				$store = $this->config()->get('news','category','store');
				switch ($store){
					case 'db': $this->store = new TableNewsCategoryLinkStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableNewsCategoryLinkStore();
			}
		}
		return $this->store;
	}
	/**
	 * Add link news and category
	 *
	 * @param integer $news_id
	 * @param integer $category_id
	 * @return boolean
	 */
	public function add($news_id,$category_id)
	{
		$repo = $this->store()->getRepository('news_category_link');
		$repo->appendLockTables(['tbl_news','tbl_news_category']);
		$filter = [
			NewsCategoryLinkEntity::C_NEWS_ID		 => $news_id,
			NewsCategoryLinkEntity::C_CATEGORY_ID	 => $category_id,
		];
		$find = $this->finder()->filter($filter)->count();
		if ($find > 0){
			$repo->rollback();
			throw new \RuntimeException("News category link already exists.");
		}
		$news = News::finder()->filter(['ID' => $news_id])->count();
		if ($news == 0){
			$repo->rollback();
			throw new \RuntimeException("News not found.");
		}
		$category = NewsCategory::finder()->filter(['ID' => $category_id])->count();
		if ($category == 0){
			$repo->rollback();
			throw new \RuntimeException("News category not found.");
		}
		$entity = new NewsCategoryLinkEntity();
		$entity->news_id = intval($news_id);
		$entity->category_id = intval($category_id);
		return $this->store()->add($repo,$entity);
	}
	/**
	 * Delete link by news id and category id
	 *
	 * @param integer $news_id
	 * @param integer $category_id
	 * @return boolean
	 */
	public function delete($news_id,$category_id)
	{
		$repo = $this->store()->getRepository('news_category_link');
		$repo->appendLockTables(['tbl_news','tbl_news_category']);
		$filter = [
			NewsCategoryLinkEntity::C_NEWS_ID		 => $news_id,
			NewsCategoryLinkEntity::C_CATEGORY_ID	 => $category_id,
		];
		$entity = $this->finder()->filter($filter)->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("News category link is not found.");
		}
		return $this->store()->delete($repo,$entity);
	}
	/**
	 * Delete link by news
	 *
	 * @param integer $news_id
	 * @param integer $category_id
	 * @return boolean
	 */
	public function deleteAllByNewsId($news_id)
	{
		$repo = $this->store()->getRepository('news_category_link');
		$repo->appendLockTables(['tbl_news']);
		$filter = [
			NewsCategoryLinkEntity::C_NEWS_ID => $news_id,
		];
		$entities = $this->finder()->filter($filter)->all();
		return $this->store()->deleteAll($repo,$entities);
	}
	/**
	 *
	 * @param type $category_id
	 * @return boolean
	 */
	public function deleteAllByCategoryId($category_id)
	{
		$repo = $this->store()->getRepository('news_category_link');
		$repo->appendLockTables(['tbl_news_category']);
		$filter = [
			NewsCategoryLinkEntity::C_CATEGORY_ID => $category_id,
		];
		$entities = $this->finder()->filter($filter)->all();
		return $this->store()->deleteAll($repo,$entities);
	}
	/**
	 * Return sql builder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function finder()
	{
		return $this->store()->getFinder();
	}
}