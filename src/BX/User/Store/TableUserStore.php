<?php namespace BX\User\Store;
use BX\DB\ITable;
use BX\DB\UnitOfWork\Repository;
use BX\User\Entity\UserEntity;
use BX\Validator\Exception\ValidateException;

class TableUserStore implements ITable
{
	use \BX\DB\TableTrait,
	 \BX\DB\DBTrait;
	/**
	 * Return settings
	 *
	 * @return array
	 */
	protected function settings()
	{
		return [
			'db_table'	 => 'tbl_user',
			'cache_tag'	 => 'User',
			'event'		 => 'User',
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
			UserEntity::C_ID			 => $this->column()->int('T.ID'),
			UserEntity::C_GUID			 => $this->column()->string('T.GUID'),
			UserEntity::C_LOGIN			 => $this->column()->string('T.LOGIN'),
			UserEntity::C_EMAIL			 => $this->column()->string('T.EMAIL'),
			UserEntity::C_CODE			 => $this->column()->string('T.CODE'),
			UserEntity::C_CREATE_DATE	 => $this->column()->datetime('T.CREATE_DATE'),
			UserEntity::C_TIMESTAMP_X	 => $this->column()->datetime('T.TIMESTAMP_X'),
			UserEntity::C_DISPLAY_NAME	 => $this->column()->string('T.DISPLAY_NAME'),
			UserEntity::C_REGISTERED	 => $this->column()->bool('T.REGISTERED'),
			UserEntity::C_ACTIVE		 => $this->column()->bool('T.ACTIVE'),
		];
	}
	/**
	 * Return finder
	 *
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function getFinder()
	{
		return static::finder(UserEntity::getClass());
	}
	/**
	 * Return password for user
	 *
	 * @param integer $user_id
	 * @return string
	 * @throws \RuntimeException
	 */
	public function getPasswordByUserID($user_id)
	{
		$sql = 'SELECT T.PASSWORD as PASSWORD FROM '.$this->getDbTable().' T WHERE T.ID = :user_id LIMIT 1';
		$array = $this->db()->query($sql,['user_id' => $user_id])->fetch();
		if ($array === false){
			throw new \RuntimeException("User $user_id is not found");
		}
		return $array['PASSWORD'];
	}
	/**
	 * Update password
	 *
	 * @param integer $user_id
	 * @param string $password_hash
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function updatePassword(Repository $repo,UserEntity $entity,$password_hash)
	{
		$sql = 'UPDATE '.$this->getDbTable().' SET PASSWORD = :hash WHERE ID = :id';
		if (!$this->db()->execute($sql,['id' => $entity->id,'hash' => $password_hash])){
			throw new \RuntimeException("Error update password for {$entity->id}");
		}
		$repo->commit();
		return true;
	}
	/**
	 * Add user
	 *
	 * @param Repository $repo
	 * @param UserEntity $entity
	 * @return integer
	 * @throws \RuntimeException
	 */
	public function add(Repository $repo,UserEntity $entity)
	{
		$repo->add($this,$entity);
		if (!$repo->commit()){
			throw new ValidateException($repo->getErrorEntity()->getErrors());
		}
		return $entity->id;
	}
	/**
	 * Update user
	 *
	 * @param Repository $repo
	 * @param UserEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function update(Repository $repo,UserEntity $entity)
	{
		$repo->update($this,$entity);
		if (!$repo->commit()){
			throw new ValidateException($repo->getErrorEntity()->getErrors());
		}
		return true;
	}
	/**
	 * Delete user
	 *
	 * @param Repository $repo
	 * @param UserEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function delete(Repository $repo,UserEntity $entity)
	{
		$repo->delete($this,$entity);
		if (!$repo->commit()){
			throw new ValidateException($repo->getErrorEntity()->getErrors());
		}
		return true;
	}
}