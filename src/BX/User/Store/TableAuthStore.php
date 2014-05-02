<?php namespace BX\User\Store;
use BX\DB\ITable;
use BX\DB\UnitOfWork\Repository;
use BX\User\Entity\AccessEntity;

class TableAuthStore implements ITable, IAccessStore
{
	use \BX\DB\TableTrait,
	 \BX\Date\DateTrait;
	/**
	 * @var string
	 */
	protected $repository_name = 'auth';
	/**
	 * Get columns
	 * @return array
	 */
	protected function columns()
	{
		return [
			AccessEntity::C_ID			 => $this->column()->int('T.ID'),
			AccessEntity::C_USER_ID		 => $this->column()->int('T.USER_ID'),
			AccessEntity::C_GUID		 => $this->column()->string('T.GUID'),
			AccessEntity::C_TOKEN		 => $this->column()->string('T.TOKEN'),
			AccessEntity::C_TIMESTAMP_X	 => $this->column()->datetime('T.TIMESTAMP_X'),
		];
	}
	/**
	 * Settings
	 *
	 * @return array
	 */
	protected function settings()
	{
		return [
			'db_table' => 'tbl_auth',
		];
	}
	/**
	 * Create access token
	 *
	 * @param AccessEntity $entity
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function add(AccessEntity $entity)
	{
		$this->clear($entity->user_id);
		$repo = new Repository($this->repository_name);
		$repo->add($this,$entity);
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error create access token. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Get entity
	 *
	 * @param string $guid
	 * @param string $token
	 * @return false|AccessEntity
	 */
	public function get($guid,$token)
	{
		$filter = [
			'='.AccessEntity::C_GUID => $guid,
		];
		$entities = self::finder(AccessEntity::getClass())->filter($filter)->all();
		foreach($entities as $entity){
			if (password_verify($token,$entity->token)){
				return $entity;
			}
		}
		return false;
	}
	/**
	 * Delete access token
	 *
	 * @param integer $user_id
	 * @return true
	 * @throws \RuntimeException
	 */
	public function clear($user_id)
	{
		$filter = [
			AccessEntity::C_USER_ID => $user_id,
		];
		$repo = new Repository($this->repository_name);
		$entities = self::finder(AccessEntity::getClass())->filter($filter)->all();
		foreach($entities as $entity){
			$repo->delete($this,$entity);
		}
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete access token. Error: {$mess}.");
		}
		return true;
	}
	/**
	 * Clear olds token
	 *
	 * @param integer $day
	 * @return true
	 * @throws \RuntimeException
	 */
	public function clearOld($day)
	{
		$time = $this->date()->convertTimeStamp(time() - $day * 3600 * 24);
		$filter = [
			'<'.AccessEntity::C_TIMESTAMP_X => $time,
		];
		$repo = new Repository($this->repository_name);
		$entities = self::finder(AccessEntity::getClass())->filter($filter)->all();
		foreach($entities as $entity){
			$repo->delete($this,$entity);
		}
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException("Error delete old access token. Error: {$mess}.");
		}
		return true;
	}
}