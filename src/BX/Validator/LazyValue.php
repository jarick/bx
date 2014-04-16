<?php namespace BX\Validator;

class LazyValue extends \BX\Base\Collection
{
	/**
	 * @var IEntity
	 */
	private $entity;
	/**
	 * @var string
	 */
	private $key = null;
	/**
	 * @var array
	 */
	private $fields = null;
	/**
	 * @var array
	 */
	private $labels = null;
	/**
	 * Constructor
	 * @param \BX\Validator\IEntity $entity
	 */
	public function __construct(IEntity $entity)
	{
		$this->entity = $entity;
		parent::__construct('BX\Validator\Collection\BaseValidator');
	}
	/**
	 * Set parameters
	 * @param string $key
	 * @param array $fields
	 * @param array $labels
	 */
	public function setParameters($key,$fields,$labels)
	{
		$this->key = $key;
		$this->fields = $fields;
		$this->labels = $labels;
	}
	/**
	 * Get entity
	 * @retrun IEntity
	 */
	public function getEntity()
	{
		return $this->entity;
	}
	/**
	 * Check value
	 * @param string $value
	 * @return boolean
	 */
	public function check($value)
	{
		if ($this->key === null){
			throw new \RuntimeException('Is not set params before check');
		}
		$this->fields[$this->key] = $value;
		foreach($this->array as $validator){
			$label = $this->labels[$this->key];
			$stop = $validator->validateField($this->key,$this->fields,$label);
			if ($validator->hasErrors()){
				foreach($validator->getErrors()->all() as $error){
					$this->entity->addError($this->key,$error);
				}
			}
			if ($stop !== true){
				return false;
			}
		}
		return true;
	}
}