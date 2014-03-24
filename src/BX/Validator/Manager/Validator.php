<?php namespace BX\Validator\Manager;
use BX\Manager;

class Validator extends Manager
{
	use \BX\String\StringTrait;
	/**
	 * @var array
	 */
	private $errors = [];
	/**
	 * @var labels
	 */
	private $labels = [];
	/**
	 * @var array
	 */
	private $rules = [];
	/**
	 * Set labels
	 * @param array $labels
	 * @return \BX\Validator\Manager\Validator
	 */
	public function setLabels($labels)
	{
		$this->labels = $labels;
		return $this;
	}
	/**
	 * Get labels
	 * @return array
	 * @throws \LogicException
	 */
	private function getLabels()
	{
		if (empty($this->labels)){
			throw new \LogicException("Labels for validation field is not set");
		}
		return $this->labels;
	}
	public function setRules($rules)
	{
		$this->rules = $rules;
	}
	private function getRules()
	{
		if (empty($this->rules)){
			throw new \LogicException("Rules for validation is not set");
		}
		return $this->rules;
	}
	protected $bNew = true;
	public function setNew($bNew)
	{
		$this->bNew = $bNew;
		return $this;
	}
	private function isNew()
	{
		return $this->bNew;
	}
	public function check(array &$fields)
	{
		return $this->clear($fields)->validate($fields);
	}
	protected function clear(array &$fields)
	{
		$result = array();
		$bNew = $this->bNew;
		$rules = $this->getRules();
		foreach ($rules as $rule){
			$rulesArray = (is_array($rule[0])) ? $rule[0] : explode(',',$rule[0]);
			foreach ($rulesArray as $key){
				if ($this->string()->length($key) > 0){
					if ($rule[1] instanceof Setter){
						continue;
					}
					if ($rule[1]->isNew() !== 'all'){
						if ($rule[1]->isNew() !== $bNew){
							continue;
						}
					}
					$result[$key][] = $rule[1];
				} else{
					$this->log()->warning('Empty name field');
				}
			}
		}
		foreach (array_keys($fields) as $key){
			if (!array_key_exists($key,$result)){
				unset($fields[$key]);
			}
		}
		return $this;
	}
	public function hasError()
	{
		return !empty($this->errors);
	}
	public function getErrors()
	{
		return $this->errors;
	}
	public function addError($key,$error)
	{
		if ($key === false){
			$key = 'unknow';
		}
		$this->errors[$this->string()->toUpper($key)][] = $error;
	}
	public function resetErrors()
	{
		$this->errors = [];
	}
	protected function validate(&$arFields)
	{
		$this->errors = [];
		$result = [];
		$bNew = $this->bNew;
		$rules = $this->getRules();
		$labels = $this->getLabels();
		foreach ($rules as $rule){
			$rulesArray = (is_array($rule[0])) ? $rule[0] : explode(',',$rule[0]);
			foreach ($rulesArray as $key){
				if ($this->string()->length($key) > 0){
					if ($rule[1]->isNew() !== 'all'){
						if ($rule[1]->isNew() !== $bNew){
							continue;
						}
					}
					if ($rule[1] instanceof Setter){
						$rule[1]->set($key,$arFields,$labels[$key]);
					}
					$result[$key][] = $rule[1];
				} else{
					$this->log()->warning('Empty name field');
				}
			}
		}
		foreach ($result as $key => $field){
			foreach ($field as $action){
				$bStop = $action->validateField($key,$arFields,$labels[$key]);
				foreach ($action->getErrors() as $sError){
					$this->addError($key,$sError);
				}
				if ($bStop !== true){
					break;
				}
			}
		}
		return empty($this->getErrors());
	}
}