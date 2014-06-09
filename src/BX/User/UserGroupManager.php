<?php namespace BX\User;
use BX\User\Store\TableUserGroupStore;
use BX\User\Entity\UserGroupEntity;

class UserGroupManager
{
	use \BX\String\StringTrait,
	 \BX\Config\ConfigTrait;
	private $store = null;
	/**
	 * Return user group store
	 *
	 * @return TableUserGroupStore
	 */
	private function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('user','group','store')){
				$store = $this->config()->get('user','group','store');
				switch ($store){
					case 'db': $this->store = new TableUserGroupStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableUserGroupStore();
			}
		}
		return $this->store;
	}
	/**
	 * Add user group
	 *
	 * @param array $group
	 * @return integer
	 */
	public function add(array $group)
	{
		$entity = new UserGroupEntity();
		$entity->setData($group);
		$repo = $this->store()->getRepository('user_group');
		return $this->store()->add($repo,$entity);
	}
	/**
	 * Update user group
	 *
	 * @param integer $id
	 * @param array $group
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function update($id,array $group)
	{
		$repo = $this->store()->getRepository('user_group');
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			throw new \RuntimeException("Error user group is not found.");
		}
		$entity->setData($group);
		return $this->store()->update($repo,$entity);
	}
	/**
	 * Delete user group
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$repo = $this->store()->getRepository('user_group');
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			throw new \RuntimeException("Error user group is not found.");
		}
		return $this->store()->delete($repo,$entity);
	}
	/**
	 * Return Sql Builder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function finder()
	{
		return $this->store()->getFinder();
	}
}