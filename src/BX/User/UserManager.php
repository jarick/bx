<?php namespace BX\User;
use BX\DB\Filter\SqlBuilder;
use BX\User\Store\TableUserStore;
use BX\User\Entity\UserEntity;
use BX\Event\Event;

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
		$entity = new UserEntity();
		$entity->setData($user);
		$repo = $this->store()->getRepository('user');
		$filter = array(
			'LOGIC'				 => 'OR',
			UserEntity::C_LOGIN	 => $user[UserEntity::C_LOGIN],
			UserEntity::C_EMAIL	 => $user[UserEntity::C_EMAIL],
		);
		$copy = $this->finder()->filter($filter)->get();
		if ($copy !== false){
			$repo->rollback();
			if ($copy->getValue(UserEntity::C_LOGIN) === $user[UserEntity::C_LOGIN]){
				throw new \RuntimeException('Dublicate login');
			}
			if ($copy->getValue(UserEntity::C_EMAIL) === $user[UserEntity::C_EMAIL]){
				throw new \RuntimeException('Dublicate email');
			}
			throw new \RuntimeException('Dublicate login or email');
		}
		return $this->store()->add($repo,$entity);
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
		$repo = $this->store()->getRepository('user');
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("Error user is not found.");
		}
		$filter = array(
			array(
				'LOGIC'				 => 'OR',
				UserEntity::C_LOGIN	 => $user[UserEntity::C_LOGIN],
				UserEntity::C_EMAIL	 => $user[UserEntity::C_EMAIL],
			),
			'!ID' => $id,
		);
		$copy = $this->finder()->filter($filter)->get();
		if ($copy !== false){
			$repo->rollback();
			if ($copy->getValue(UserEntity::C_LOGIN) === $user[UserEntity::C_LOGIN]){
				throw new \RuntimeException('Dublicate login');
			}
			if ($copy->getValue(UserEntity::C_EMAIL) === $user[UserEntity::C_EMAIL]){
				throw new \RuntimeException('Dublicate email');
			}
			throw new \RuntimeException('Dublicate login or email');
		}
		$entity->setData($user);
		return $this->store()->update($repo,$entity);
	}
	/**
	 * Delete user
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$repo = $this->store()->getRepository('user');
		$entity = $this->finder()->filter(['ID' => $id])->get();
		if ($entity === false){
			$repo->rollback();
			throw new \RuntimeException("User is not found.");
		}
		$event = $this->store()->getEvent();
		if ($this->string()->length($event) > 0){
			$event = 'OnPost'.$this->string()->ucwords($event).'Delete';
			Event::on($event,function($id){
				if (!UserGroupMember::deleteAllByUserId($id)){
					throw new \RuntimeException("Error delete groups member.");
				}
			});
		}
		return $this->store()->delete($repo,$entity);
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
}