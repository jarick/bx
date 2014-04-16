<?php namespace BX\Validator;
use BX\Base\DI;
use BX\Validator\Helper\RuleHelper;
use BX\Validator\Collection\Validator;
use Illuminate\Support\MessageBag;
use BX\ZendSearch\Helper\SearchHelper;
use BX\ZendSearch\SearchCollection;

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
	 * Set labels,values and rules
	 */
	private function complete()
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
		return $this;
	}
	/**
	 * Init
	 */
	public function init()
	{

	}
	/**
	 * Get labels
	 * @return array()
	 */
	abstract protected function labels();
	/**
	 * Get labels
	 * @return array
	 */
	public function getLabels()
	{
		return $this->labels();
	}
	/**
	 * Get label
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
			$message = strtr("`#KEY#` is not field #MODEL#",['#KEY#' => $key,'#MODEL#' => get_class($this)]);
			throw new \InvalidArgumentException($message);
		}
	}
	/**
	 * Print label
	 * @param type $key
	 */
	public function printLabel($key)
	{
		echo $this->string()->escape($this->getLabel($key));
	}
	/**
	 * Get rule helper
	 * @return RuleHelper
	 */
	protected function rule()
	{
		if (DI::get('column_rule') === null){
			DI::set('column_rule',new RuleHelper());
		}
		return DI::get('column_rule');
	}
	/**
	 * Get rules
	 * @return array
	 */
	abstract protected function rules();
	/**
	 * Get rules
	 * @return array
	 */
	public function getRules()
	{
		return $this->rules();
	}
	/**
	 * Get value
	 * @param string $key
	 * @return string
	 */
	public function __get($key)
	{
		return $this->getValue($this->string()->toUpper($key));
	}
	/**
	 * Set value
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
	 * @param type $key
	 */
	public function __isset($key)
	{
		$this->exists($key);
	}
	/**
	 * Is exists field
	 * @param type $key
	 */
	public function exists($key)
	{
		return array_key_exists($this->string()->toUpper($key),$this->value);
	}
	/**
	 * Print value
	 * @param type $key
	 */
	public function printValue($key)
	{
		echo $this->string()->escape($this->getValue($key));
	}
	/**
	 * Get value
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
			$message = strtr("`#KEY#` is not field #MODEL#",['#KEY#' => $key,'#MODEL#' => get_class($this)]);
			throw new \InvalidArgumentException($message);
		}
	}
	/**
	 * Set value
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
			$fields = ['#KEY#' => $key,'#MODEL#' => get_class($this)];
			throw new \InvalidArgumentException(strtr("`#KEY#` is not field #MODEL#",$fields));
		}
		return $this;
	}
	/**
	 * Get old data
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
	 * Get data
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
	 * @param array $values
	 * @param boolean $old
	 * @return self
	 */
	public function setData(array $values,$old = false)
	{
		foreach($this->value as &$value){
			$value = null;
		}
		if ($old){
			foreach($this->old_value as &$value){
				$value = null;
			}
		}
		foreach($values as $key => $value){
			$key = $this->string()->toUpper($key);
			if (array_key_exists($key,$this->value)){
				if (is_array($value)){
					$value = array_map('trim',$value);
				}else{
					$value = trim($value);
				}
				$this->value[$key] = $value;
				if ($old){
					$this->old_value[$key] = $value;
				}
			}
		}
		return $this;
	}
	/**
	 * Prepare files array
	 * @param array $files
	 * @return array
	 */
	public function prepareFiles($files)
	{
		$arrayForFill = [];
		foreach($files as $firstNameKey => $arFileDescriptions){
			foreach(array_keys($arFileDescriptions) as $fileDescriptionParam){
				$this->restructuringFilesArray($arrayForFill,$firstNameKey,$files[$firstNameKey][$fileDescriptionParam],$fileDescriptionParam);
			}
		}
		return $arrayForFill;
	}
	/**
	 * restructuring files array
	 * @param array $arrayForFill
	 * @param string $currentKey
	 * @param string $currentMixedValue
	 * @param array $fileDescriptionParam
	 */
	private function restructuringFilesArray(&$arrayForFill,$currentKey,$currentMixedValue,$fileDescriptionParam)
	{
		if (is_array($currentMixedValue)){
			foreach($currentMixedValue as $nameKey => $mixedValue){
				$this->restructuringFilesArray($arrayForFill[$currentKey],$nameKey,$mixedValue,$fileDescriptionParam);
			}
		}else{
			$arrayForFill[$currentKey][$fileDescriptionParam] = $currentMixedValue;
		}
	}
	/**
	 * Load validator
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
	 * @return boolean
	 */
	public function hasErrors()
	{
		return $this->getValidator()->hasErrors();
	}
	/**
	 * Get errors
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
	 * Get search helper
	 * @return SearchHelper
	 */
	protected function index()
	{
		if (DI::get('search_helper') === null){
			DI::set('search_helper',new SearchHelper($this));
		}
		return DI::get('search_helper');
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
	 * Get class name
	 */
	public static function getClass()
	{
		return get_called_class();
	}
}