<?php namespace BX\DB\UnitOfWork;
use BX\Base\Collection;
use BX\DB\ITable;
use BX\Validator\IEntity;
use BX\Validator\LazyValue;
use \BX\DB\UnitOfWork\EntityAdd;
use \BX\DB\UnitOfWork\EntityUpdate;
use \BX\DB\UnitOfWork\EntityDelete;

class Repository
{
	use \BX\Mutex\MutexTrait,
	 \BX\DB\DBTrait;
	/**
	 * @var Collection
	 */
	protected $updates = null;
	/**
	 * @var Collection
	 */
	protected $adds = null;
	/**
	 * @var Collection
	 */
	protected $deletes = null;
	/**
	 * @var array
	 */
	protected $tables = [];
	/**
	 * @var IEntity
	 */
	protected $bad_entity = null;
	/**
	 * @var Collection
	 */
	protected $success = null;
	/**
	 * @var string
	 */
	protected $key;
	/**
	 * @var boolean
	 */
	private static $lock = true;
	/**
	 * Constructor
	 * @param string $key
	 * @param integer $max_acquire
	 */
	public function __construct($key = null,$max_acquire = 1)
	{
		$this->updates = new Collection('BX\DB\UnitOfWork\EntityBase');
		$this->adds = new Collection('BX\DB\UnitOfWork\EntityBase');
		$this->deletes = new Collection('BX\DB\UnitOfWork\EntityBase');
		$this->success = new Collection('BX\DB\UnitOfWork\EntityBase');
		$this->key = $key;
		if (!self::$lock){
			throw new \RuntimeException('Nested transactions is not support');
		}
		self::$lock = false;
		if ($key !== null){
			$this->mutex()->acquire($key,$max_acquire);
		}
	}
	/**
	 * Add entity
	 * @param ITable $table
	 * @param IEntity $entity
	 * @return LazyValue
	 */
	public function add(ITable $table,$entity = null)
	{
		$entity_add = new EntityAdd($table,$entity);
		$entity_add->setRepository($this);
		$this->adds->add($entity_add);
		if ($entity !== null){
			return new LazyValue($entity);
		}else{
			return new LazyValue($table);
		}
	}
	/**
	 * Update entity
	 * @param ITable $table
	 * @param IEntity $entity
	 */
	public function update(ITable $table,$entity = null)
	{
		$this->updates->add(new EntityUpdate($table,$entity));
	}
	/**
	 * Delete entity
	 * @param ITable $table
	 * @param IEntity $entity
	 */
	public function delete(ITable $table,$entity = null)
	{
		$this->deletes->add(new EntityDelete($table,$entity));
	}
	/**
	 * Validate all operations
	 */
	private function prepareAll()
	{
		$this->prepare($this->adds);
		$this->prepare($this->updates);
		$this->prepare($this->deletes);
	}
	/**
	 * Validate operation
	 * @param Collection $collection
	 * @return boolean
	 */
	private function prepare(Collection $collection)
	{
		foreach($collection as $update){
			$table = $update->getTable()->getDbTable();
			if (!$update->validate()){
				$this->bad_entity = $update->getEntity();
				return false;
			}
			if (!in_array($table,$this->tables)){
				$this->tables[] = $table;
			}
		}
		return true;
	}
	/**
	 * Execute all sql query
	 * @return boolean
	 */
	private function sendAll()
	{
		$send = $this->send($this->adds);
		if ($send){
			$send = $this->send($this->updates);
			if ($send){
				$send = $this->send($this->deletes);
			}
		}
		return $send;
	}
	/**
	 * Execute sql query
	 * @param Collection $collection
	 * @return boolean
	 */
	private function send(Collection $collection)
	{
		foreach($collection as $update){
			if (!$update->commit()){
				if ($this->bad_entity === null){
					$this->bad_entity = $update->getEntity();
				}
				return false;
			}else{
				$this->success->add($update);
			}
		}
		return true;
	}
	/**
	 * Get entity with error
	 * @return IEntity
	 */
	public function getErrorEntity()
	{
		return $this->bad_entity;
	}
	/**
	 * Lock table
	 * @param array $tables
	 */
	private function lock($tables)
	{
		$this->db()->adaptor()->lock($tables);
	}
	/**
	 * Unlock tables
	 * @param array $tables
	 */
	private function unlock($tables)
	{
		$this->db()->adaptor()->unlock($tables);
	}
	/**
	 * Rollback transaction
	 */
	private function rollback()
	{
		while ($update = $this->success->pop()){
			$update->rollback();
		}
	}
	/**
	 * On after commit transaction
	 */
	private function onAfterCommitAll()
	{
		$this->onAfterCommit($this->adds);
		$this->onAfterCommit($this->updates);
		$this->onAfterCommit($this->deletes);
	}
	/**
	 * On after commit transaction
	 */
	private function onAfterCommit(Collection $collection)
	{
		foreach($collection as $update){
			$update->onAfterCommit();
		}
	}
	/**
	 * Set lazy value
	 * @param IEntity $entity
	 * @param string $value
	 * @return boolean
	 */
	public function setLazy(IEntity $entity,$value)
	{
		foreach([$this->adds,$this->updates] as $coll){
			$temp = clone $coll;
			foreach($temp as $update){
				foreach($update->fields as &$field){
					if ($field instanceof LazyValue){
						if ($field->getEntity() === $entity){
							if (!$field->check($value)){
								$this->bad_entity = $update->getEntity();
								return false;
							}else{
								$field = $value;
							}
						}
					}
				}
			}
		}
		return true;
	}
	/**
	 * Commit transaction
	 * @param boolean $lock
	 * @return boolean
	 * @throws \BX\DB\UnitOfWork\Exception
	 */
	public function commit($lock = true)
	{
		try{
			$this->prepareAll();
			if ($this->bad_entity === null){
				if ($lock){
					if (count($this->tables) <= 1){
						$lock = false;
					}
				}
				if ($lock){
					$this->lock($this->tables);
				}
				if (count($this->tables) === 0){
					return true;
				}
				$send = $this->sendAll();
				if (!$send){
					$this->rollback();
				}
				if ($lock){
					$this->unlock($this->tables);
				}
				if ($this->key !== null){
					$this->mutex()->release($this->key);
				}
				self::$lock = true;
				if (!$send){
					return false;
				}
				$this->onAfterCommitAll();
				return true;
			}
			return false;
		}catch (\Exception $e){
			$this->rollback();
			if ($lock){
				$this->unlock($this->tables);
			}
			if ($this->key !== null){
				$this->mutex()->release($this->key);
			}
			self::$lock = true;
			throw $e;
		}
	}
	/**
	 * Autocomit on destruct
	 */
	public function __destruct()
	{
		if (!self::$lock){
			if ($this->success !== null){
				$this->commit();
			}else{
				$this->mutex()->releaseAll();
			}
			self::$lock = true;
		}
	}
	/**
	 * Append addition locks table
	 *
	 * Tables will lock on commit
	 * @param string|array $tables
	 */
	public function appendLockTables($tables)
	{
		foreach((array)$tables as $table){
			if (!in_array($table,$this->tables)){
				$this->tables[] = $table;
			}
		}
	}
}