<?php namespace BX\DB\UnitOfWork;
use BX\Validator\LazyValue;

class EntityAdd extends \BX\DB\UnitOfWork\EntityBase
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
	 * @var Repository
	 */
	private $repo;
	/**
	 * Set repository
	 * @param Repository $repo
	 * @return \BX\DB\UnitOfWork\EntityAdd
	 */
	public function setRepository($repo)
	{
		$this->repo = $repo;
		return $this;
	}
	/**
	 * Before validate fields
	 * @param array $fields
	 * @return boolean
	 */
	private function onStart(array &$fields)
	{
		$event = $this->table->getEvent();
		if ($this->string()->length($event) > 0){
			$event = 'OnStart'.$this->string()->ucwords($event).'Add';
			if ($this->fire($event,[&$fields]) === false){
				if (!$this->entity->hasErrors()){
					$trans = 'db.unitofwork.add_unknow_error';
					$this->entity->addError(false,$this->trans($trans));
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
	private function onBefore(array &$fields)
	{
		$event = $this->table->getEvent();
		if ($this->string()->length($event) > 0){
			if ($this->fire('OnBefore'.$this->string()->ucwords($event).'Add',[&$fields]) === false){
				if (!$this->hasErrors()){
					$this->entity->addError(false,$this->trans('db.unitofwork.add_unknow_error'));
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
	private function onAfter($id,array &$fields)
	{
		$event = $this->table->getEvent();
		if ($this->string()->length($event) > 0){
			$this->fire('OnAfter'.$this->string()->ucwords($event).'Add',[$id,&$fields]);
		}
	}
	/**
	 * Validate values
	 * @return boolean
	 */
	public function validate()
	{
		$fields = $this->entity->getData();
		if (!$this->onStart($fields)){
			return false;
		}
		if (!$this->entity->checkFields($fields,true)){
			return false;
		}
		if (!$this->onBefore($fields)){
			return false;
		}
		$this->fields = $this->prepareArrayToDb($fields);
		return true;
	}
	/**
	 * Save in db
	 * @return false|integer
	 */
	public function commit()
	{
		$context = [
			'table'	 => $this->table->getDbTable(),
			'fields' => $this->fields,
		];
		foreach($this->fields as $field){
			if ($field instanceof LazyValue){
				$this->log('db.unitofwork.entity_add')->error('Find lazy value',$context);
				$this->entity->addError(false,$this->trans('db.unitofwork.find_lazy_value'));
				return false;
			}
		}
		$id = $this->db()->add($this->table->getDbTable(),$this->fields);
		if ($id > 0){
			$this->id = $id;
			$this->entity->setValue($this->table->getPkColumn(),$id);
			if (!$this->repo->setLazy($this->entity,$id)){
				return false;
			}
			return $id;
		}else{
			$error = print_r($this->db()->adaptor()->pdo()->errorInfo(),1);
			$this->log('db.unitofwork.entity_add')->error('Error inset row. Error:'.$error);
			$this->entity->addError(false,$this->trans('db.unitofwork.add_unknow_error'));
			return false;
		}
	}
	/**
	 * Rollback
	 */
	public function rollback()
	{
		$pk = $this->table->getPkColumn();
		$pk_field = $this->string()->toLower($this->db()->esc($pk,true));
		$where = $this->db()->esc($pk)." = :$pk_field";
		$table = $this->table->getDbTable();
		if (!$this->db()->delete($table,$where,[$pk_field => $this->id])){
			$context = [
				'id'	 => $this->id,
				'where'	 => $where,
				'table'	 => $table,
			];
			$this->log('db.unitofwork.entity_add')->error('Error delete row',$context);
		}
	}
	/**
	 * Call after commit transaction
	 */
	public function onAfterCommit()
	{
		$this->entity->setData($this->fields,true);
		$this->onAfter($this->id,$this->fields);
		$this->addSearchIndex($this->id);
		$this->clearCache();
	}
}