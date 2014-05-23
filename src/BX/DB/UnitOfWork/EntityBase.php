<?php namespace BX\DB\UnitOfWork;

abstract class EntityBase
{
	use \BX\Cache\CacheTrait,
	 \BX\String\StringTrait,
	 \BX\ZendSearch\ZendSearchTrait,
	 \BX\Event\EventTrait,
	 \BX\DB\DBTrait,
	 \BX\Translate\TranslateTrait,
	 \BX\Logger\LoggerTrait;
	/**
	 * @var \BX\DB\ITable
	 */
	protected $table;
	/**
	 * @var \BX\Validator\IEntity
	 */
	protected $entity;
	/**
	 * Constructor
	 *
	 * @param \BX\DB\ITable $table
	 * @param \BX\Validator\IEntity $entity
	 */
	public function __construct($table,$entity = null)
	{
		$this->table = $table;
		if ($entity !== null){
			$this->entity = $entity;
		}else{
			$this->entity = $table;
		}
	}
	/**
	 * Clear cache
	 *
	 * @return boolean
	 */
	protected function clearCache()
	{
		$tag = $this->table->getCacheTag();
		if ($this->string()->length($tag) > 0){
			$this->cache()->clearByTags($tag);
			return true;
		}
		return false;
	}
	/**
	 * Return table
	 *
	 * @retrun \BX\DB\ITable
	 */
	public function getTable()
	{
		return $this->table;
	}
	/**
	 * Return entity
	 *
	 * @retrun \BX\DB\IEntity
	 */
	public function getEntity()
	{
		return $this->entity;
	}
	/**
	 * Add search index
	 *
	 * @param integer|string $id
	 * @return boolean
	 */
	protected function addSearchIndex($id)
	{
		$index = $this->entity->getSearch();
		if ($index->count() > 0){
			$id = get_class($this->table).'_'.$id;
			$this->zendsearch()->add($id,$index);
			return true;
		}
		return false;
	}
	/**
	 * Delete search index
	 *
	 * @param integer|string $id
	 * @return boolean
	 */
	protected function deleteSearchIndex($id)
	{
		$index = $this->entity->getSearch();
		if ($index->count() > 0){
			$id = get_class($this->table).'_'.$id;
			$this->zendsearch()->delete($id);
			return true;
		}
		return false;
	}
	/**
	 * Prepare array to db
	 *
	 * @param array $fields
	 * @return array
	 */
	protected function prepareArrayToDb(array $fields)
	{
		$columns = $this->table->getColumns();
		foreach($fields as $field => &$value){
			if ($this->string()->startsWith($field,'~')){
				$field = $this->string()->substr($field,1);
			}
			if ($columns->has($field)){
				$value = $columns->get($field)->convertToDB($value);
			}else{
				$this->log('db.unitofwork.entitybase')->error("Column `$field` is not found");
			}
		}
		unset($value);
		return $fields;
	}
	abstract public function validate();
	abstract public function commit();
	abstract public function rollback();
	abstract public function onAfterCommit();
}