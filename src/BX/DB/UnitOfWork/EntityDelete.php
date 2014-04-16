<?php namespace BX\DB\UnitOfWork;

class EntityDelete extends EntityBase
{
	/**
	 * @var integer|string
	 */
	public $id;
	/**
	 * @var array
	 */
	public $old_fields;
	/**
	 * On before delete
	 * @return boolean
	 */
	protected function onBeforeDelete()
	{
		$event = $this->table->getEvent();
		if ($this->string()->length($event) > 0){
			$event = 'OnBefore'.$this->string()->ucwords($event).'Delete';
			if ($this->fire($event,[$this->entity]) === false){
				if (!$this->entity->hasErrors()){
					$trans = 'db.unitofwork.delete_unknow_error';
					$this->entity->addError(false,$this->trans($trans));
				}
				return false;
			}
		}
		return true;
	}
	/**
	 * On after delete
	 * @return boolean
	 */
	protected function onAfterDelete()
	{
		$event = $this->table->getEvent();
		if ($this->string()->length($event) > 0){
			$event = 'OnAfter'.$this->string()->ucwords($event).'Delete';
			$this->fire($event,[$this]);
		}
	}
	/**
	 * Commit
	 * @return boolean
	 */
	public function commit()
	{
		$id = $this->id;
		$pk = $this->table->getPkColumn();
		$pk_field = $this->string()->toLower($this->db()->esc($pk,true));
		$where = $this->db()->esc($pk)."=:$pk_field";
		$table = $this->table->getDbTable();
		$sql = "SELECT * FROM $table WHERE $where";
		$fetch = $this->db()->query($sql,[$pk_field => $id]);
		if ($fetch->count() === 1){
			$this->old_fields = $fetch->fetch();
		}else{
			$mess = 'db.unitofwork.delete_row_not_found';
			$this->entity->addError(false,$this->trans($mess));
			return false;
		}
		if (!$this->db()->delete($table,$where,[$pk_field => $id])){
			$trans = 'db.unitofwork.delete_unknow_error';
			$this->entity->addError(false,$this->trans($trans));
			$context = [
				'table'	 => $table,
				'id'	 => $id,
			];
			$event = 'db.unitofwork.entity_delete';
			$this->log($event)->error('Error delete row',$context);
			return false;
		}
		return $id;
	}
	/**
	 * On after commit
	 */
	public function onAfterCommit()
	{
		$this->entity->setData([],true);
		$this->onAfterDelete();
		$this->deleteSearchIndex($this->id);
		$this->clearCache();
	}
	/**
	 * Rollback
	 */
	public function rollback()
	{
		$table = $this->table->getDbTable();
		if (!$this->db()->add($table,$this->old_fields)){
			$context = [
				'table'	 => $table,
				'fields' => $this->old_fields,
			];
			$event = 'db.unitofwork.entity_delete';
			$this->log($event)->error('Error insert row',$context);
		}
	}
	/**
	 * Validate entity
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function validate()
	{
		$pk = $this->table->getPkColumn();
		$id = $this->entity->getValue($pk);
		if ($this->string()->length($id) === 0){
			throw new \InvalidArgumentException('PK field is not set');
		}
		$this->id = $id;
		if ($this->onBeforeDelete()){
			return true;
		}
		return false;
	}
}