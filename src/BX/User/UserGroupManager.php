<?php namespace BX\User;
use BX\User\Store\TableUserGroupStore;

class UserGroupManager
{
	use \BX\String\StringTrait,
	 \BX\Config\ConfigTrait;
	private $store = null;
	/**
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
	public function add(array $group)
	{
		return $this->store()->add($group);
	}
	public function update($id,$group)
	{
		return $this->store()->update($id,$group);
	}
	public function delete($id)
	{
		return $this->store()->delete($id);
	}
	public function finder()
	{
		return $this->store()->getFinder();
	}
}