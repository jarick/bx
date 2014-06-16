<?php namespace BX\Validator;
use BX\Validator\Collection\Setter;
use Illuminate\Support\MessageBag;

class Validator
{
	use \BX\String\StringTrait;
	/**
	 * @var MessageBag
	 */
	private $error;
	/**
	 * @var labels
	 */
	private $labels = [];
	/**
	 * @var array
	 */
	private $rules = [];
	/**
	 * @var boolean
	 */
	protected $new = true;
	/**
	 * Constructor
	 *
	 * @param array $labels
	 * @param array $rules
	 * @param boolean $new
	 */
	public function __construct(array $labels,array $rules,$new = true)
	{
		$this->labels = $labels;
		$this->rules = $rules;
		$this->new = $new;
	}
	/**
	 * Set labels
	 *
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
	 *
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
	/**
	 * Set rules
	 *
	 * @param type $rules
	 * @return \BX\Validator\Manager\Validator
	 */
	public function setRules($rules)
	{
		$this->rules = $rules;
		return $this;
	}
	/**
	 * Get rules
	 *
	 * @return type
	 * @throws \LogicException
	 */
	private function getRules()
	{
		if (empty($this->rules)){
			throw new \LogicException("Rules for validation is not set");
		}
		return $this->rules;
	}
	/**
	 * Set is new
	 *
	 * @param boolean $new
	 * @return \BX\Validator\Manager\Validator
	 */
	public function setNew($new)
	{
		$this->new = $new;
		return $this;
	}
	/**
	 * Get is new
	 * @return type
	 */
	private function isNew()
	{
		return $this->new;
	}
	/**
	 * Check fields
	 *
	 * @param array $fields
	 * @return boolean
	 */
	public function check(array &$fields)
	{
		return $this->clear($fields)->validate($fields);
	}
	/**
	 * Clear fields
	 *
	 * @param array $fields
	 * @return \BX\Validator\Manager\Validator
	 */
	protected function clear(array &$fields)
	{
		$result = array();
		$new = $this->new;
		$rules = $this->getRules();
		foreach($rules as $rule){
			$rules_array = (is_array($rule[0])) ? $rule[0] : explode(',',$rule[0]);
			foreach($rules_array as $key){
				if ($this->string()->length($key) > 0){
					if ($rule[1] instanceof Setter){
						continue;
					}
					if ($rule[1]->isNew() !== 'all'){
						if ($rule[1]->isNew() !== $new){
							continue;
						}
					}
					$result[$key][] = $rule[1];
				}else{
					$this->log()->warning('Empty name field');
				}
			}
		}
		foreach(array_keys($fields) as $key){
			if (!array_key_exists($key,$result)){
				unset($fields[$key]);
			}
		}
		return $this;
	}
	/**
	 * Has errors
	 *
	 * @return array
	 */
	public function hasErrors()
	{
		if ($this->error === null){
			return false;
		}
		return count($this->error->all()) > 0;
	}
	/**
	 * Get errors
	 *
	 * @return MessageBag
	 */
	public function getErrors()
	{
		if ($this->error === null){
			throw new \LogicException('Get error before validate');
		}
		return $this->error;
	}
	/**
	 * Add error
	 *
	 * @param string $key
	 * @param string $message
	 * @param array $params
	 */
	public function addError($key,$message,$params = [])
	{
		if ($this->error === null){
			$this->error = new MessageBag();
		}
		if ($key === false){
			$key = 'UNKNOW_COLUMN';
		}
		$this->error->add($this->string()->toUpper($key),(!empty($params)) ? strtr($message,$params) : $message);
	}
	/**
	 * Validate fields
	 *
	 * @param array $fields
	 * @return boolean
	 */
	protected function validate(array &$fields)
	{
		$this->error = new MessageBag();
		$result = [];
		$new = $this->new;
		$rules = $this->getRules();
		$labels = $this->getLabels();
		foreach($rules as $rule){
			$rules_array = (is_array($rule[0])) ? $rule[0] : explode(',',$rule[0]);
			foreach($rules_array as $key){
				if ($this->string()->length($key) > 0){
					if ($rule[1]->isNew() !== 'all'){
						if ($rule[1]->isNew() !== $new){
							continue;
						}
					}
					if ($rule[1] instanceof Setter){
						$rule[1]->set($key,$fields,$labels[$key]);
					}
					$result[$key][] = $rule[1];
				}else{
					$this->log()->warning('Empty name field');
				}
			}
		}
		foreach($result as $key => $field){
			foreach($field as $action){
				if (isset($fields[$key]) && $fields[$key] instanceof LazyValue){
					$fields[$key]->setParameters($key,$fields,$labels);
					$fields[$key]->add($action);
				}else{
					$stop = $action->validateField($key,$fields,$labels[$key]);
					if ($action->hasErrors()){
						$this->error->merge($action->getErrors());
					}
					if ($stop !== true){
						break;
					}
				}
			}
		}
		return !$this->hasErrors();
	}
}