<?php namespace BX\DB;
use BX\Entity;
use BX\DB\Filter\SqlBuilder;
use BX\Collection;

class ActiveRecord extends Entity
{
	use \BX\ZendSearch\SearchTrait,
	 \BX\Cache\CacheTrait,
	 DBTrait;
	const CACHE_TAG = 'cache_tag';
	const EVENT = 'event';
	const DB_TABLE = 'db_table';
	const UF_ENTITY = 'uf_entity';
	const PERMISSION_BINDING = 'permission_binding';
	const PERMISSION_TABLE = 'permission_table';
	/**
	 * Get primary key column
	 * @return string
	 */
	public function getPkColumn()
	{
		return 'ID';
	}
	/**
	 * Get DB table
	 * @return string
	 */
	public function getDbTable()
	{
		return $this->getSettings(self::DB_TABLE);
	}
	/**
	 * Get cache tag
	 * @return string|boolean
	 */
	public function getCacheTag()
	{
		return $this->getSettings(self::CACHE_TAG);
	}
	/**
	 * Get event
	 * @return string|boolean
	 */
	public function getEvent()
	{
		return $this->getSettings(self::EVENT);
	}
	/**
	 * Get entity name for user field
	 * @return string|boolean
	 */
	public function getUfEntity()
	{
		return $this->getSettings(self::UF_ENTITY);
	}
	/**
	 * Get permission binding
	 * @return string|boolean
	 */
	public function getPermissionBinding()
	{
		return $this->getSettings(self::PERMISSION_BINDING);
	}
	/**
	 *
	 * @return string|boolean
	 */
	public function getPermissionTable()
	{
		return $this->getSettings(self::PERMISSION_TABLE);
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
	protected function getRelations()
	{
		return $this->relations();
	}
	/**
	 * Get search fileds
	 * @return array
	 */
	protected function search()
	{
		return [];
	}
	/**
	 * Get search fileds
	 * @return array
	 */
	protected function getSearch()
	{
		return $this->search();
	}
	/**
	 * Add search index
	 * @param string|integer $id
	 * @return boolean
	 */
	private function addIndex($id)
	{
		$index = $this->getSearch();
		if (!empty($index)){
			$this->zendsearch()->add($id,$index);
			return true;
		}
		return false;
	}
	/**
	 * Delete index
	 * @param integer $id
	 */
	private function deleteIndex($id)
	{
		$index = $this->getSearch();
		if (!empty($index)){
			$this->zendsearch()->delete($id);
			return true;
		}
		return false;
	}
	/**
	 * Get filter fields
	 * @return array
	 */
	protected function columns()
	{
		return [];
	}
	/**
	 * Get filter fields
	 * @return array
	 */
	protected function getColumns()
	{
		return $this->columns();
	}
	/**
	 * Clear cache
	 */
	private function clearCache()
	{
		$tag = $this->getCacheTag();
		if ($this->string()->length($tag) > 0){
			$this->cache()->clearByTags($tag);
		}
	}
	/**
	 * Before validate fields
	 * @param array $fields
	 * @return boolean
	 */
	protected function onStartAdd(array &$fields)
	{
		$event = $this->getEvent();
		if ($this->string()->length($event) > 0){
			if ($this->fire('OnStart'.$this->string()->ucwords($event).'Add',[$this,&$fields]) === false){
				if (!$this->hasErrors()){
					$this->addError(false,$this->trans('db.activerecord.add_unknow_error'));
				}
				return false;
			}
		}
		return true;
	}
	/**
	 * After validate fields
	 * @param array $fields
	 * @return boolean
	 */
	protected function onBeforeAdd(array &$fields)
	{
		$event = $this->getEvent();
		if ($this->string()->length($event) > 0){
			if ($this->fire('OnBefore'.$this->string()->ucwords($event).'Add',[$this,&$fields]) === false){
				if (!$this->hasErrors()){
					$this->addError(false,$this->trans('db.activerecord.add_unknow_error'));
				}
				return false;
			}
		}
		return true;
	}
	/**
	 * After add
	 * @param integer|string $id
	 * @param array $fields
	 */
	protected function onAfterAdd($id,array &$fields)
	{
		$event = $this->getEvent();
		if ($this->string()->length($event) > 0){
			$this->fire('OnAfter'.$this->string()->ucwords($event).'Add',[$this,$id,&$fields]);
		}
	}
	/**
	 * Execute insert sql
	 * @param string $table
	 * @param string $fields
	 * @return integer
	 */
	protected function executeAdd($table,$fields)
	{
		return $this->db()->add($table,$fields);
	}
	/**
	 * Add
	 * @param boolean|array $fields
	 * @return integer|boolean
	 */
	public function add($fields = false)
	{
		$this->resetErrors();
		$pk = $this->getPkColumn();
		$this->log()->debug('call '.self::getClassName().'::add');
		if ($fields === false){
			$fields = $this->getData();
		} else{
			$this->setData($fields);
		}
		unset($fields[$pk]);
		if (!$this->onStartAdd($fields)){
			return false;
		}
		if (!$this->checkFields($fields,true)){
			return false;
		}
		if (!$this->onBeforeAdd($fields)){
			return false;
		}
		$fields = $this->prepareArrayToDb($fields);
		$id = $this->executeAdd($this->getDbTable(),$fields);
		if ($id > 0){
			$this->addIndex($id);
			$this->onAfterAdd($id,$fields);
		} else{
			$this->addError(false,$this->trans('db.activerecord.add_unknow_error'));
			return false;
		}
		$this->clearCache();
		return $id;
	}
	/**
	 * Before validate fields
	 * @param array $fields
	 * @return boolean
	 */
	protected function onStartUpdate($id,array &$fields)
	{
		$event = $this->getEvent();
		if ($this->string()->length($event) > 0){
			if ($this->fire('OnStart'.$this->string()->ucwords($event).'Update',[$this,$id,&$fields]) === false){
				var_dump('OnStart'.$this->string()->ucwords($event).'Update');
				if (!$this->hasErrors()){
					$this->addError(false,$this->trans('db.activerecord.update_unknow_error'));
				}
				return false;
			}
		}
		return true;
	}
	/**
	 * After validate fields
	 * @param array $fields
	 * @return boolean
	 */
	protected function onBeforeUpdate($id,&$fields)
	{
		$event = $this->getEvent();
		if ($this->string()->length($event) > 0){
			if ($this->fire('OnBefore'.$this->string()->ucwords($event).'Update',[$this,$id,&$fields]) === false){
				if (!$this->hasErrors()){
					$this->addError(false,$this->trans('db.activerecord.update_unknow_error'));
				}
				return false;
			}
		}
		return true;
	}
	/**
	 * After update
	 * @param integer|string $id
	 * @param array $fields
	 */
	protected function onAfterUpdate($id,array $fields)
	{
		$event = $this->getEvent();
		if ($this->string()->length($event) > 0){
			$this->fire('OnAfter'.$this->string()->ucwords($event).'Update',[$this,$id,$fields]);
		}
	}
	/**
	 * Execute update sql
	 * @param string $table
	 * @param string $fields
	 * @param string $where
	 * @param string $where_params
	 * @return integer
	 */
	protected function executeUpdate($table,$fields,$where,$where_params)
	{
		return $this->db()->update($table,$fields,$where,$where_params);
	}
	/**
	 * Update
	 * @param integer|string $id
	 * @param array|boolean $fields
	 * @return boolean
	 */
	public function update($id,$fields = false)
	{
		$this->resetErrors();
		$pk = $this->getPkColumn();
		$this->log()->debug('call '.self::getClassName().'::update');
		if ($fields === false){
			$fields = $this->getData();
		} else{
			$this->setData($fields);
		}
		unset($fields[$pk]);
		if (!$this->onStartUpdate($id,$fields)){
			return false;
		}
		if (!$this->checkFields($fields,false)){
			return false;
		}
		if (!$this->onBeforeUpdate($id,$fields)){
			return false;
		}
		$fields = $this->prepareArrayToDb($fields);
		$pk_field = $this->string()->toLower($this->db()->esc($pk,true));
		$where = $this->db()->esc($pk)."=:$pk_field";
		if ($this->executeUpdate($this->getDbTable(),$fields,$where,[$pk_field => $id])){
			$this->deleteIndex($id);
			$this->addIndex($id);
			$this->onAfterUpdate($id,$fields);
		} else{
			$this->addError(false,$this->trans('db.activerecord.update_unknow_error'));
			return false;
		}
		$this->clearCache();
		return $id;
	}
	/**
	 * On before delete
	 * @param integer|string $id
	 * @return boolean
	 */
	protected function onBeforeDelete($id)
	{
		$event = $this->getEvent();
		if ($this->string()->length($event) > 0){
			if ($this->fire('OnBefore'.$this->string()->ucwords($event).'Delete',[$this,$id]) === false){
				if (!$this->hasErrors()){
					$this->addError(false,$this->trans('db.activerecord.delete_unknow_error'));
				}
				return false;
			}
		}
		return true;
	}
	/**
	 * On after delete
	 * @param integer|string $id
	 * @return boolean
	 */
	protected function onAfterDelete($id)
	{
		$event = $this->getEvent();
		if ($this->string()->length($event) > 0){
			$this->fire('OnAfter'.$this->string()->ucwords($event).'Delete',[$this,$id]);
		}
	}
	/**
	 * Delete
	 * @param integer|string $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$this->resetErrors();
		$pk = $this->getPkColumn();
		$this->log()->debug("call ".self::getClassName()."::delete($id)");
		if (!$this->onBeforeDelete($id)){
			return false;
		}
		$pk_field = $this->string()->toLower($this->db()->esc($pk,true));
		$where = $this->db()->esc($pk)."=:$pk_field";
		if (!$this->db()->delete($this->getDbTable(),$where,[$pk_field => $id])){
			$this->addError(false,$this->trans('db.activerecord.delete_unknow_error'));
			return false;
		}
		$this->onAfterDelete($id);
		$this->deleteIndex($id);
		$this->clearCache();
		return true;
	}
	/**
	 * Prepare array from db
	 * @param array $fields
	 * @return array
	 */
	private function prepareArrayFromDb(array $fields)
	{
		$columns = $this->getColumns();
		foreach ($fields as $field => &$value){
			if (array_key_exists($field,$columns)){
				$value = $columns[$field]->convertFromDB($field,$value,$fields);
			}
		}
		unset($value);
		return $fields;
	}
	/**
	 * Prepare array to db
	 * @param array $fields
	 * @return array
	 */
	private function prepareArrayToDb(array $fields)
	{
		$columns = $this->getColumns();
		foreach ($fields as $field => &$value){
			if ($this->string()->startsWith($field,'~')){
				$field = $this->string()->substr($field,1);
			}
			if (array_key_exists($field,$columns)){
				$value = $columns[$field]->convertToDB($field,$value,$fields);
			} else{
				$this->log()->error("Column `$field` is not found");
			}
		}
		unset($value);
		return $fields;
	}
	/**
	 * Convert values from db
	 * @param array $values
	 * @return \BX\Collection
	 */
	public function convertFromArray(array $values)
	{
		$collection = new Collection(self::getClassName());
		foreach ($values as $value){
			$entity = static::getEntity();
			$entity->setData($this->prepareArrayFromDb($value));
			$collection->add($entity);
		}
		return $collection;
	}
	/**
	 * Get filter
	 * @return SqlBuilder
	 */
	public static function filter()
	{
		return static::getEntity()->getFilter();
	}
	/**
	 * Get filter
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	public function getFilter()
	{
		$fields = [];
		$rules = [];
		foreach ($this->getColumns() as $field => $column){
			$fields[$field] = $column->getColumn();
			$rules[$field] = $column->getFilterRule();
		}
		return new SqlBuilder($this->db(),$this->getDbTable(),$fields,$rules,$this->getRelations(),[$this,'convertFromArray']);
	}
	/**
	 * Get join sql
	 * @param string $table
	 * @param string|ActiveRecord $pk
	 */
	public function hasMany($table,$db_table,$pk = null)
	{
		if ($db_table instanceof ActiveRecord){
			$pk = $db_table->getPkColumn();
			$db_table = $db_table->getDbTable();
		}
		return "LEFT OUTER JOIN $db_table $table ON T.".$this->getPkColumn()."=$table.$pk";
	}
}