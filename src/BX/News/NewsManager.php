<?php namespace BX\News;
use BX\User\User;
use BX\News\Store\TableNewsStore;

class NewsManager
{
	use \BX\Config\ConfigTrait;
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
	 * Check user exists
	 *
	 * @param integer $user_id
	 * @return boolean
	 */
	protected function checkUserExists($user_id)
	{
		$filter = ['ID' => $user_id];
		return User::finder()->filter($filter)->count() > 0;
	}
	/**
	 * Check fields
	 *
	 * @param array $values
	 * @throws \RuntimeException
	 * @return boolean
	 */
	protected function checkFields(array $values)
	{
		if (isset($values['USER_ID'])){
			$user_id = intval($values['USER_ID']);
			if (!$this->checkUserExists($user_id)){
				throw new \RuntimeException('User is not found');
			}
		}
		return true;
	}
	/**
	 * Add news
	 *
	 * @param array $news
	 * @return integer
	 */
	public function add(array $news)
	{
		if ($this->checkFields($news)){
			return $this->store()->add($news);
		}
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
		if ($this->checkFields($news)){
			return $this->store()->update($id,$news);
		}
	}
	/**
	 * Delete news
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		return $this->store()->delete($id);
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