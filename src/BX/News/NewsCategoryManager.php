<?php namespace BX\News;
use BX\News\Store\TableNewsCategoryStore;
use BX\News\Entity\NewsCategoryEntity;
use BX\Event\Event;
use BX\User\User;

class NewsCategoryManager
{
	use \BX\Config\ConfigTrait,
	 \BX\String\StringTrait;
	/**
	 * @var TableNewsCategoryStore
	 */
	private $store;
	/**
	 * @return TableNewsCategoryStore
	 */
	private function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('news','category','store')){
				$store = $this->config()->get('news','category','store');
				switch ($store){
					case 'db': $this->store = new TableNewsCategoryStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableNewsCategoryStore();
			}
		}
		return $this->store;
	}
	/**
	 * Add news category
	 *
	 * @param array $category
	 * @return integer
	 */
	public function add(array $category)
	{
		$repo = $this->store()->getRepository('news_category');
		$repo->appendLockTables(['tbl_user']);
		if (isset($category['USER_ID'])){
			$user_id = intval($category['USER_ID']);
			$user = User::finder()->filter(['ID' => $user_id])->count();
			if ($user == 0){
				throw new \RuntimeException('User is not found');
			}
		}
		$entity = new NewsCategoryEntity();
		$entity->setData($category);
		return $this->store()->add($repo,$entity);
	}
	/**
	 * Update news category
	 *
	 * @param integer $id
	 * @param array $category
	 * @return integer
	 */
	public function update($id,$category)
	{
		$repo = $this->store()->getRepository('news_category');
		$repo->appendLockTables(['tbl_news']);
		if (isset($category['USER_ID'])){
			$user_id = intval($category['USER_ID']);
			$user = User::finder()->filter(['ID' => $user_id])->count();
			if ($user == 0){
				throw new \RuntimeException('User is not found');
			}
		}
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("Error news category is not found.");
		}
		$entity->setData($category);
		return $this->store()->update($repo,$entity);
	}
	/**
	 * Delete news category
	 *
	 * @param integer $id
	 * @return array
	 */
	public function delete($id)
	{
		$repo = $this->store()->getRepository('news_category');
		$repo->appendLockTables(['tbl_news','tbl_news_category_link']);
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("Error news category is not found.");
		}
		$event = $this->store()->getEvent();
		if ($this->string()->length($event) > 0){
			$event = 'OnPost'.$this->string()->ucwords($event).'Delete';
			Event::on($event,function($id){
				if (!NewsCategoryLink::deleteAllByCategoryId($id)){
					throw new \RuntimeException("Error delete link on news category.");
				}
			});
		}
		return $this->store()->delete($repo,$entity);
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