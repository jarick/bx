<?php namespace BX\DB\UnitOfWork;
use BX\Validator\LazyValue;
use InvalidArgumentException;

class EntityUpdate extends \BX\DB\UnitOfWork\EntityBase
{
	/**
	 * @var integer|string
	 */
	public $id;
	/**
	 * @var array
	 */
	public $fields;
	/**
	 * @var array
	 */
	public $old_fields;
	/**
	 * Before validate fields
	 *
	 * @param array $fields
	 * @return boolean
	 */
	protected function onStartUpdate($id,array &$fields)
	{
		$event = $this->table->getEvent();
		if ($this->string()->length($event) > 0){
			if ($this->fire('OnStart'.$this->string()->ucwords($event).'Update',[$this,$id,&$fields]) === false){
				if (!$this->hasErrors()){
					$this->entity->addError(false,$this->trans('db.unitofwork.update_unknow_error'));
				}
				return false;
			}
		}
		return true;
	}
	/**
	 * After validate fields
	 *
	 * @param array $fields
	 * @return boolean
	 */
	protected function onBeforeUpdate($id,&$fields)
	{
		$event = $this->table->getEvent();
		if ($this->string()->length($event) > 0){
			if ($this->fire('OnBefore'.$this->string()->ucwords($event).'Update',[$this,$id,&$fields]) === false){
				if (!$this->hasErrors()){
					$this->entity->addError(false,$this->trans('db.unitofwork.update_unknow_error'));
				}
				return false;
			}
		}
		return true;
	}
	/**
	 * After update
	 *
	 * @param integer|string $id
	 * @param array $fields
	 */
	protected function onAfterUpdate($id,array $fields)
	{
		$event = $this->table->getEvent();
		if ($this->string()->length($event) > 0){
			$this->fire('OnAfter'.$this->string()->ucwords($event).'Update',[$this,$id,$fields]);
		}
	}
	/**
	 * Validate fields
	 *
	 * @return boolean
	 * @throws InvalidArgumentException
	 */
	public function validate()
	{
		$pk = $this->table->getPkColumn();
		$id = $this->entity->getValue($pk);
		if ($this->string()->length($id) === 0){
			throw new InvalidArgumentException('Primary key field is not set');
		}
		$fields = $this->entity->getData();
		unset($fields[$pk]);
		if (!$this->onStartUpdate($id,$fields)){
			return false;
		}
		if (!$this->entity->checkFields($fields,false)){
			return false;
		}
		if (!$this->onBeforeUpdate($id,$fields)){
			return false;
		}
		$old = $this->entity->getOldData();
		foreach($fields as $key => $value){
			if (array_key_exists($key,$old)){
				if ($old[$key] === $value){
					unset($fields[$key]);
				}
			}
		}
		if (!empty($fields)){
			$fields = $this->prepareArrayToDb($fields);
		}
		$this->fields = $fields;
		$this->id = $id;
		return true;
	}
	/**
	 * Update
	 *
	 * @return boolean
	 */
	public function commit()
	{
		$id = $this->id;
		$fields = $this->fields;
		if (empty($fields)){
			return $id;
		}
		$context = [
			'table'	 => $this->table->getDbTable(),
			'fields' => $fields,
			'id'	 => $id,
		];
		foreach($fields as $field){
			if ($field instanceof LazyValue){
				$this->log('db.unitofwork.entity_add')->error('Find lazy value',$context);
				$this->entity->addError(false,$this->trans('db.unitofwork.find_lazy_value'));
				return false;
			}
		}
		$pk = $this->table->getPkColumn();
		$pk_field = $this->string()->toLower($this->db()->esc($pk,true));
		$where = $this->db()->esc($pk)." = :$pk_field";
		$table = $this->table->getDbTable();
		$sql = "SELECT ".implode(',',array_keys($fields))." FROM $table WHERE $where";
		$fetch = $this->db()->query($sql,[$pk_field => $id]);
		if ($fetch->count() === 1){
			$this->old_fields = $fetch->fetch();
		}else{
			$this->entity->addError(false,$this->trans('db.unitofwork.update_row_not_found'));
			return false;
		}
		if ($this->db()->update($table,$fields,$where,[$pk_field => $id])){
			return $id;
		}else{
			$event = 'db.unitofwork.entity_update';
			$this->log($event)->error('Error update row',$context);
			$this->entity->addError(false,$this->trans('db.unitofwork.update_unknow_error'));
			return false;
		}
		return $id;
	}
	/**
	 * Rollback
	 */
	public function rollback()
	{
		$id = $this->id;
		$fields = $this->entity->getOldData();
		$pk = $this->table->getPkColumn();
		unset($fields[$pk]);
		$pk_field = $this->string()->toLower($this->db()->esc($pk,true));
		$where = $this->db()->esc($pk)." = :$pk_field";
		$table = $this->table->getDbTable();
		if (!$this->db()->update($table,$this->old_fields,$where,[$pk_field => $id])){
			$context = [
				'table'	 => $table,
				'fields' => $fields,
				'id'	 => $id,
			];
			$event = 'db.unitofwork.entity_update';
			$this->log($event)->error('Error update row',$context);
		}
	}
	/**
	 * Call after commit transaction
	 */
	public function onAfterCommit()
	{
		$this->entity->setData($this->prepareArrayFromDb($this->fields),true);
		$this->deleteSearchIndex($this->id);
		$this->addSearchIndex($this->id);
		$this->clearCache();
		$this->onAfterUpdate($this->id,$this->fields);
	}
}