<?php namespace BX\Counter\Store;
use \BX\Counter\Entity\CounterEntity;
use \BX\DB\ITable;
use \BX\DB\UnitOfWork\Repository;

class TableCounterStore implements ITable, ICounterStore
{
	use \BX\DB\TableTrait,
	 \BX\Date\DateTrait;
	protected function settings()
	{
		return [
			'db_table' => 'tbl_counter',
		];
	}
	/**
	 * Coulmns
	 * @return array
	 */
	protected function columns()
	{
		return [
			CounterEntity::C_ID			 => $this->column()->int('T.ID'),
			CounterEntity::C_ENTITY		 => $this->column()->string('T.ENTITY'),
			CounterEntity::C_ENTITY_ID	 => $this->column()->string('T.ENTITY_ID'),
			CounterEntity::C_TIMESTAMP_X => $this->column()->datetime('T.TIMESTAMP_X'),
			CounterEntity::C_COUNTER	 => $this->column()->int('T.COUNTER'),
		];
	}
	/**
	 * Increment counter
	 * @param string $entity
	 * @param string $entity_id
	 * @throws \RuntimeException
	 * @return integer
	 */
	public function inc($entity,$entity_id)
	{
		$repo = new Repository('counter');
		$counter = $this->get($entity,$entity_id);
		if ($counter === false){
			$counter = new CounterEntity();
			$counter->entity = $entity;
			$counter->entity_id = $entity_id;
			$repo->add($this,$counter);
			$return = 1;
		}else{
			$counter->counter++;
			$repo->update($this,$counter);
			$return = $counter->counter;
		}
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException('Error increment counter. Message: '.$mess);
		}
		return $return;
	}
	/**
	 * Clear counter
	 * @param string $entity
	 * @param string $entity_id
	 * @throws \RuntimeException
	 * @return true
	 */
	public function clear($entity,$entity_id)
	{
		$repo = new Repository('counter');
		$counter = $this->get($entity,$entity_id);
		if ($counter !== false){
			$repo->delete($this,$counter);
		}
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException('Error delete counter. Message: '.$mess);
		}
		return true;
	}
	/**
	 * Clear old counter
	 * @param integer $day
	 * @throws \RuntimeException
	 * @return true
	 */
	public function clearOld($day = 30)
	{
		$time = $this->date()->convertTimeStamp(time() - $day * 3600 * 24);
		$filter = [
			'<'.CounterEntity::C_TIMESTAMP_X => $time,
		];
		$repo = new Repository('counter');
		$counters = self::finder(CounterEntity::getClass())->filter($filter)->all();
		foreach($counters as $counter){
			$repo->delete($this,$counter);
		}
		if (!$repo->commit()){
			$mess = print_r($repo->getErrorEntity()->getErrors()->all(),1);
			throw new \RuntimeException('Error delete old counter. Message: '.$mess);
		}
		return true;
	}
	/**
	 * Get counter
	 * @param string $entity
	 * @param string $entity_id
	 * @return CounterEntity|false
	 */
	public function get($entity,$entity_id)
	{
		$filter = [
			CounterEntity::C_ENTITY		 => $entity,
			CounterEntity::C_ENTITY_ID	 => $entity_id,
		];
		return self::finder(CounterEntity::getClass())->filter($filter)->get();
	}
}