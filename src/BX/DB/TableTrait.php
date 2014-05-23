<?php namespace BX\DB;
use BX\Config\DICService;
use BX\Base\Dictionary;
use BX\DB\Filter\SqlBuilder;
use BX\DB\Helper\ColumnHelper;
use BX\DB\Helper\RelationHelper;

trait TableTrait
{
	/**
	 * Return primary key column
	 *
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
	 * Return settings
	 *
	 * @return array
	 */
	protected function getSettings($key)
	{
		return (array_key_exists($key,$this->settings())) ? $this->settings()[$key] : null;
	}
	/**
	 * Return table name
	 *
	 * @return string
	 */
	public function getDbTable()
	{
		return $this->getSettings('db_table');
	}
	/**
	 * Return cache tag
	 *
	 * @return string|boolean
	 */
	public function getCacheTag()
	{
		return $this->getSettings('cache_tag');
	}
	/**
	 * Return event
	 *
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
	#public function getUfEntity()
	#{
	#	return $this->getSettings('uf_entity');
	#}
	/**
	 * Return permission binding
	 *
	 * @return string|boolean
	 */
	public function getPermissionBinding()
	{
		return $this->getSettings('permission_binding');
	}
	/**
	 * Return permission table
	 *
	 * @return string|boolean
	 */
	public function getPermissionTable()
	{
		return $this->getSettings('permission_table');
	}
	/**
	 * Return acl rules
	 *
	 * @return array
	 */
	protected function operations()
	{
		return [];
	}
	/**
	 * Return operations
	 *
	 * @return type
	 */
	public function getOperations()
	{
		return $this->operations();
	}
	/**
	 * Return relation helper
	 *
	 * @return RelationHelper
	 */
	protected function relation()
	{
		$key = 'relation_helper';
		if (DICService::get($key) === null){
			$manager = function(){
				return new RelationHelper();
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
	/**
	 * Return relation
	 *
	 * @return array
	 */
	protected function relations()
	{
		return [];
	}
	/**
	 * Return relation
	 *
	 * @return array
	 */
	public function getRelations()
	{
		return $this->relations();
	}
	/**
	 * Return column helper
	 *
	 * @return ColumnHelper
	 */
	protected function column()
	{
		$key = 'column_helper';
		if (DICService::get($key) === null){
			$manager = function(){
				return new ColumnHelper();
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
	/**
	 * Return columns
	 *
	 * @return array
	 */
	abstract protected function columns();
	/**
	 * Return columns
	 *
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
	 * Filter
	 *
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