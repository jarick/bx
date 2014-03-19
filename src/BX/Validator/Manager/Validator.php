<?php namespace BX\Validator\Manager;
use BX\Manager;

class Validator extends Manager
{
	use \BX\String\StringTrait;
	private $aError = [];
	private $aLabels = [];
	public function setLabels($aLabels)
	{
		$this->aLabels = $aLabels;
	}
	private function getLabels()
	{
		if (empty($this->aLabels)){
			throw new \LogicException("Labels for validation field is not set");
		}
		return $this->aLabels;
	}
	private $aRules = [];
	public function setRules($aRules)
	{
		$this->aRules = $aRules;
	}
	private function getRules()
	{
		if (empty($this->aRules)){
			throw new \LogicException("Rules for validation is not set");
		}
		return $this->aRules;
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
	public function check(&$aFields)
	{
		return $this->clear($aFields)->validate($aFields);
	}
	protected function clear(&$aFields)
	{
		$result = array();
		$bNew = $this->bNew;
		$rules = $this->getRules();
		foreach ($rules as $rule){
			$aRulesArray = (is_array($rule[0])) ? $rule[0] : explode(',',$rule[0]);
			foreach ($aRulesArray as $key){
				if ($rule[1] instanceof Setter){
					continue;
				}
				if ($rule[1]->isNew() !== 'all'){
					if ($rule[1]->isNew() !== $bNew){
						continue;
					}
				}
				$result[$key][] = $rule[1];
			}
		}
		foreach (array_keys($aFields) as $key){
			if (!array_key_exists($key,$result)){
				unset($aFields[$key]);
			}
		}
		return $this;
	}
	public function hasError()
	{
		return !empty($this->aError);
	}
	public function getErrors()
	{
		return $this->aError;
	}
	public function addError($key,$error)
	{
		if ($key === false){
			$key = 'unknow';
		}
		$this->aError[$this->string()->toUpper($key)][] = $error;
	}
	public function resetErrors()
	{
		$this->aError = [];
	}
	protected function validate(&$arFields)
	{
		$this->aError = [];
		$result = [];
		$bNew = $this->bNew;
		$rules = $this->getRules();
		$aLabels = $this->getLabels();
		foreach ($rules as $rule){
			$aRulesArray = (is_array($rule[0])) ? $rule[0] : explode(',',$rule[0]);
			foreach ($aRulesArray as $key){
				if ($rule[1]->isNew() !== 'all'){
					if ($rule[1]->isNew() !== $bNew){
						continue;
					}
				}
				if ($rule[1] instanceof Setter){
					$rule[1]->set($key,$arFields,$aLabels[$key]);
				}
				$result[$key][] = $rule[1];
			}
		}
		foreach ($result as $key => $field){
			foreach ($field as $action){
				$bStop = $action->validateField($key,$arFields,$aLabels[$key]);
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