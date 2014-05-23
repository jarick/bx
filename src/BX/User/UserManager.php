<?php namespace BX\User;
use BX\DB\Filter\SqlBuilder;
use BX\User\Store\TableUserStore;

class UserManager
{
	use \BX\String\StringTrait,
	 \BX\Config\ConfigTrait;
	private $store = null;
	/**
	 * @return TableUserStore
	 */
	private function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('user','store')){
				$store = $this->config()->get('user','store');
				switch ($store){
					case 'db': $this->store = new TableUserStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableUserStore();
			}
		}
		return $this->store;
	}
	/**
	 * Add user
	 *
	 * @param array $user
	 * @return boolean
	 */
	public function add(array $user)
	{
		return $this->store()->add($user);
	}
	/**
	 * Update user
	 *
	 * @param integer $id
	 * @param array $user
	 * @return boolean
	 */
	public function update($id,array $user)
	{
		return $this->store()->update($id,$user);
	}
	/**
	 * Get filter
	 *
	 * @return SqlBuilder
	 */
	public function finder()
	{
		return $this->store()->getFinder();
	}
	/**
	 * Delete user
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		return $this->store()->delete($id);
	}
}