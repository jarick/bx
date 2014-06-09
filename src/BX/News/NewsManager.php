<?php namespace BX\News;
use BX\User\User;
use BX\News\Store\TableNewsStore;
use BX\News\Entity\NewsEntity;
use BX\Event\Event;

class NewsManager
{
	use \BX\Config\ConfigTrait,
	 \BX\String\StringTrait;
	/**
	 * @var TableNewsStore
	 */
	private $store = null;
	/**
	 * @return TableNewsStore
	 */
	private function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('news','store')){
				$store = $this->config()->get('news','store');
				switch ($store){
					case 'db': $this->store = new TableNewsStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableNewsStore();
			}
		}
		return $this->store;
	}
	/**
	 * Add news
	 *
	 * @param array $news
	 * @return integer
	 */
	public function add(array $news)
	{
		$repo = $this->store()->getRepository('news');
		$repo->appendLockTables(['tbl_user']);
		if (isset($news['USER_ID'])){
			$user_id = intval($news['USER_ID']);
			$filter = ['ID' => $user_id];
			$user = User::finder()->filter($filter)->count();
			if ($user == 0){
				throw new \RuntimeException('User is not found');
			}
		}
		$entity = new NewsEntity();
		$entity->setData($news);
		return $this->store()->add($repo,$entity);
	}
	/**
	 * Update news
	 *
	 * @param integer $id
	 * @param array $news
	 * @return boolean
	 */
	public function update($id,array $news)
	{
		$repo = $this->store()->getRepository('news');
		$repo->appendLockTables(['tbl_user']);
		if (isset($news['USER_ID'])){
			$user_id = intval($news['USER_ID']);
			$filter = ['ID' => $user_id];
			$user = User::finder()->filter($filter)->count();
			if ($user == 0){
				throw new \RuntimeException('User is not found');
			}
		}
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("Error news is not found.");
		}
		$entity->setData($news);
		return $this->store()->update($repo,$entity);
	}
	/**
	 * Delete news
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$repo = $this->store()->getRepository('news');
		$repo->appendLockTables(['tbl_news_category_link']);
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("Error news is not found.");
		}
		$event = $this->store()->getEvent();
		if ($this->string()->length($event) > 0){
			$event = 'OnPost'.$this->string()->ucwords($event).'Delete';
			Event::on($event,function($id){
				if (!NewsCategoryLink::deleteAllByNewsId($id)){
					throw new \RuntimeException("Error delete link on news category.");
				}
			});
		}
		return $this->store()->delete($repo,$entity);
	}
	/**
	 * Return finder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function finder()
	{
		return $this->store()->getFinder();
	}
}