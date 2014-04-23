<?php namespace BX\DB;
use BX\Base\DI;
use BX\Base\Dictionary;
use BX\DB\Filter\SqlBuilder;
use BX\DB\Helper\ColumnHelper;
use BX\DB\Helper\RelationHelper;
use BX\DB\UnitOfWork\Cacher;

trait TableTrait
{
	/**
	 * Get primary key column
	 * @return string
	 */
	public function getPkColumn()
	{
		return 'ID';
	}
	/**
	 * Get settings
	 * @return array
	 */
	abstract protected function settings();
	/**
	 * Get settings
	 * @return array
	 */
	protected function getSettings($key)
	{
		return (array_key_exists($key,$this->settings())) ? $this->settings()[$key] : null;
	}
	/**
	 * Get DB table
	 * @return string
	 */
	public function getDbTable()
	{
		return $this->getSettings('db_table');
	}
	/**
	 * Get cache tag
	 * @return string|boolean
	 */
	public function getCacheTag()
	{
		return $this->getSettings('cache_tag');
	}
	/**
	 * Get event
	 * @return string|boolean
	 */
	public function getEvent()
	{
		return $this->getSettings('event');
	}
	/**
	 * Get entity name for user field
	 * @return string|boolean
	 */
	public function getUfEntity()
	{
		return $this->getSettings('uf_entity');
	}
	/**
	 * Get permission binding
	 * @return string|boolean
	 */
	public function getPermissionBinding()
	{
		return $this->getSettings('permission_binding');
	}
	/**
	 *
	 * @return string|boolean
	 */
	public function getPermissionTable()
	{
		return $this->getSettings('permission_table');
	}
	/**
	 * Get acl rules
	 * @return array
	 */
	protected function operations()
	{
		return [];
	}
	/**
	 * Get operations
	 * @return type
	 */
	public function getOperations()
	{
		return $this->operations();
	}
	/**
	 * Get relation helper
	 * @return RelationHelper
	 */
	protected function relation()
	{
		if (DI::get('relation_helper') === null){
			DI::set('relation_helper',new RelationHelper());
		}
		return DI::get('relation_helper');
	}
	/**
	 * Get relation
	 * @return array
	 */
	protected function relations()
	{
		return [];
	}
	/**
	 * Get relation
	 * @return array
	 */
	public function getRelations()
	{
		return $this->relations();
	}
	/**
	 * Get column helper
	 * @return ColumnHelper
	 */
	protected function column()
	{
		if (DI::get('column_helper') === null){
			DI::set('column_helper',new ColumnHelper());
		}
		return DI::get('column_helper');
	}
	/**
	 * Get columns
	 * @return array
	 */
	abstract protected function columns();
	/**
	 * Get columns
	 * @return Dictionary
	 */
	public function getColumns()
	{
		$dictionary = new Dictionary('BX\DB\Column\IColumn');
		$data = $this->columns();
		$dictionary->setData($data);
		return $dictionary;
	}
	/**
	 * Clear cache
	 */
	public function clearCache()
	{
		if (DI::get('uow_cache') === null){
			DI::set('uow_cache',new Cacher());
		}
		return DI::get('uow_cache')->clear($this);
	}
	/**
	 * Filter
	 * @param string $entity_class
	 * @return SqlBuilder
	 */
	public static function finder($entity_class = null)
	{
		if ($entity_class === null){
			$entity_class = get_called_class();
		}
		return new SqlBuilder(new static(),$entity_class);
	}
}