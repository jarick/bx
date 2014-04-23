<?php namespace BX\Counter\Store;

interface ICounterStore
{
	/**
	 * Increment counter
	 * @param string $entity
	 * @param string $entity_id
	 * @throws \RuntimeException
	 * @return integer
	 */
	public function inc($entity,$entity_id);
	/**
	 * Clear counter
	 * @param string $entity
	 * @param string $entity_id
	 * @throws \RuntimeException
	 * @return true
	 */
	public function clear($entity,$entity_id);
	/**
	 * Clear old counter
	 * @param integer $day
	 * @throws \RuntimeException
	 * @return true
	 */
	public function clearOld($day = 30);
	/**
	 * Get counter
	 * @param string $entity
	 * @param string $entity_id
	 * @return CounterEntity|false
	 */
	public function get($entity,$entity_id);
}