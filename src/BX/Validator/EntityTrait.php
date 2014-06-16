<?php namespace BX\Validator;
use BX\Config\DICService;
use BX\Validator\Helper\RuleHelper;
use BX\Validator\Collection\Validator;
use Illuminate\Support\MessageBag;
use BX\ZendSearch\Helper\SearchHelper;
use BX\ZendSearch\SearchCollection;
use BX\Validator\Collection\Setter;

trait EntityTrait
{
	use \BX\Validator\ValidatorTrait,
	 \BX\String\StringTrait;
	/**
	 * @var Validator
	 */
	protected $validator = null;
	/**
	 * @var array
	 */
	protected $old_value = [];
	/**
	 * @var array
	 */
	protected $value = [];
	/**
	 * @var array
	 */
	protected $labels = [];
	/**
	 * @var array
	 */
	protected $rules = [];
	/**
	 * @var array
	 */
	protected $cache_rules = null;
	/**
	 * Set labels,values and rules
	 */
	protected function complete()
	{
		$rules = $this->getRules();
		for($i = 0; $i < count($rules); $i+=2){
			$this->rules[] = [$rules[$i],$rules[$i + 1]];
		}
		foreach($this->getLabels() as $key => $value){
			$key = $this->string()->toUpper($key);
			$this->labels[$key] = $value;
			$this->value[$key] = null;
		}
		foreach($this->rules as $rule){
			$keys = (is_array($rule[0])) ? $rule[0] : explode(',',$rule[0]);
			foreach($keys as $key){
				$key = $this->string()->toUpper($key);
				if (!array_key_exists($key,$this->labels)){
					$this->labels[$key] = '';
					$this->value[$key] = null;
				}
			}
		}
		$filters = $this->filter();
		for($i = 0; $i < count($filters); $i+=2){
			$this->filters[] = [$filters[$i],$filters[$i + 1]];
		}
		return $this;
	}
	/**
	 * Init
	 */
	public function init()
	{

	}
	/**
	 * Return labels
	 *
	 * @return array
	 */
	abstract protected function labels();
	/**
	 * Return labels
	 *
	 * @return array
	 */
	public function getLabels()
	{
		return $this->labels();
	}
	/**
	 * Return label
	 *
	 * @param string $key
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function getLabel($key)
	{
		$key = $this->string()->toUpper($key);
		if (array_key_exists($key,$this->labels)){
			return $this->labels[$key];
		}else{
			$params = [
				'#KEY#'		 => $key,
				'#MODEL#'	 => get_class($this)
			];
			$message = strtr("`#KEY#` is not field #MODEL#",$params);
			throw new \InvalidArgumentException($message);
		}
	}
	/**
	 * Print label
	 *
	 * @param type $key
	 */
	public function printLabel($key)
	{
		echo $this->string()->escape($this->getLabel($key));
	}
	/**
	 * Return rule helper
	 *
	 * @return RuleHelper
	 */
	protected function rule()
	{
		$key = 'column_rule';
		if (DICService::get($key) === null){
			$manager = function(){
				return new RuleHelper();
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
	/**
	 * Return rules
	 *
	 * @return array
	 */
	abstract protected function rules();
	/**
	 * Return rules
	 *
	 * @return array
	 */
	public function getRules()
	{
		return $this->rules();
	}
	/**
	 * Return value
	 *
	 * @param string $key
	 * @return string
	 */
	public function __get($key)
	{
		return $this->getValue($this->string()->toUpper($key));
	}
	/**
	 * Set value
	 *
	 * @param string $key
	 * @param string $value
	 * @return self
	 */
	public function __set($key,$value)
	{
		$this->setValue($this->string()->toUpper($key),$value);
		return $this;
	}
	/**
	 * Is exists field
	 *
	 * @param type $key
	 */
	public function __isset($key)
	{
		$this->exists($key);
	}
	/**
	 * Is exists field
	 *
	 * @param type $key
	 */
	public function exists($key)
	{
		return array_key_exists($this->string()->toUpper($key),$this->value);
	}
	/**
	 * Print value
	 *
	 * @param type $key
	 */
	public function printValue($key)
	{
		echo $this->string()->escape($this->getValue($key));
	}
	/**
	 * Return value
	 *
	 * @param string $key
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function getValue($key)
	{
		$key = $this->string()->toUpper($key);
		if (array_key_exists($key,$this->value)){
			return $this->value[$key];
		}else{
			$params = [
				'#KEY#'		 => $key,
				'#MODEL#'	 => get_class($this)
			];
			$message = strtr("`#KEY#` is not field #MODEL#",$params);
			throw new \InvalidArgumentException($message);
		}
	}
	/**
	 * Set value
	 *
	 * @param string $key
	 * @param string $value
	 * @throws \InvalidArgumentException
	 */
	public function setValue($key,$value)
	{
		$key = $this->string()->toUpper($key);
		if (array_key_exists($key,$this->value)){
			$this->value[$key] = $value;
		}else{
			$fields = [
				'#KEY#'		 => $key,
				'#MODEL#'	 => get_class($this)
			];
			throw new \InvalidArgumentException(strtr("`#KEY#` is not field #MODEL#",$fields));
		}
		return $this;
	}
	/**
	 * Return is required field
	 *
	 * @param string $key
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function isRequired($key)
	{
		if (!$this->exists($key)){
			$fields = [
				'#KEY#'		 => $key,
				'#MODEL#'	 => get_class($this)
			];
			throw new \InvalidArgumentException(strtr("`#KEY#` is not field #MODEL#",$fields));
		}
		if ($this->cache_rules === null){
			$this->cache_rules = [];
			foreach($this->rules as $rule){
				foreach($rule[0] as $field){
					$this->cache_rules[$field][] = $rule[1];
				}
			}
		}
		if (isset($this->cache_rules[$key])){
			foreach($this->cache_rules[$key] as $rule){
				if ($rule instanceof Setter){
					continue;
				}
				if ($rule->isRequired()){
					return true;
				}
			}
		}
		return false;
	}
	/**
	 * Return old data
	 *
	 * @return array|null
	 */
	public function getOldData()
	{
		$result = [];
		foreach($this->old_value as $key => $value){
			if ($value !== null){
				$result[$key] = $value;
			}
		}
		return $result;
	}
	/**
	 * Return data
	 *
	 * @return array|null
	 */
	public function getData()
	{
		$result = [];
		foreach($this->value as $key => $value){
			if ($value !== null){
				$result[$key] = $value;
			}
		}
		return $result;
	}
	/**
	 * Set data
	 *
	 * @param array $values
	 * @param boolean $old
	 * @return self
	 */
	public function setData(array $values,$old = false)
	{
		foreach($this->value as $key => &$value){
			if (isset($this->old_value[$key])){
				$value = $this->old_value[$key];
			}else{
				$value = null;
			}
		}
		unset($value);
		if ($old){
			foreach($this->old_value as &$value){
				$value = null;
			}
		}
		unset($value);
		foreach($values as $key => $value){
			$key = $this->string()->toUpper($key);
			if (array_key_exists($key,$this->value)){
				$this->value[$key] = $value;
				if ($old){
					$this->old_value[$key] = $value;
				}
			}
		}
		return $this;
	}
	/**
	 * Return filter
	 *
	 * @return array
	 */
	protected function filter()
	{
		return [];
	}
	/**
	 * Return filter array
	 *
	 * @param array $data
	 * @return array
	 */
	public function getFilter($data = null)
	{
		if ($data === null){
			$data = $this->getData();
		}
		$this->validator = $this->validator($this->filters,$this->labels);
		if ($this->validator->check($data)){
			foreach($data as $key => $value){
				if ($this->string()->length($value) === 0){
					unset($data[$key]);
				}
			}
			return $data;
		}else{
			return array();
		}
	}
	/**
	 * Load validator
	 *
	 * @return Validator
	 */
	protected function getValidator()
	{
		if ($this->validator === null){
			$this->validator = $this->validator($this->rules,$this->labels);
		}
		return $this->validator;
	}
	/**
	 * Check value
	 *
	 * @param array|null $data
	 * @param boolean $new
	 * @return type
	 */
	public function checkFields(&$data = null,$new = true)
	{
		if ($data === null){
			$data = $this->getData();
		}
		$this->validator = $this->validator($this->rules,$this->labels,$new);
		return $this->validator->check($data);
	}
	/**
	 * Is has error
	 *
	 * @return boolean
	 */
	public function hasErrors()
	{
		return $this->getValidator()->hasErrors();
	}
	/**
	 * Get errors
	 *
	 * @return MessageBag
	 */
	public function getErrors()
	{
		return $this->getValidator()->getErrors();
	}
	/**
	 * Add error
	 * @param string $key
	 * @param string $error
	 * @return self
	 */
	public function addError($key,$error)
	{
		$this->getValidator()->addError($key,$error);
		return $this;
	}
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->complete()->init();
	}
	/**
	 * Return search helper
	 *
	 * @return SearchHelper
	 */
	protected function index()
	{
		$key = 'search_helper';
		if (DICService::get($key) === null){
			$manager = function(){
				return new SearchHelper($this);
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
	/**
	 * Return search fileds
	 *
	 * @return array
	 */
	protected function search()
	{
		return [];
	}
	/**
	 * Return search fileds
	 *
	 * @return SearchCollection
	 */
	public function getSearch()
	{
		$collection = new SearchCollection();
		$data = $this->search();
		$collection->setData($data);
		return $collection;
	}
	/**
	 * Return class name
	 *
	 * @return string
	 */
	public static function getClass()
	{
		return get_called_class();
	}
}