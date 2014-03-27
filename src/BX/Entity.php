<?php namespace BX;

class Entity extends Object
{
	use \BX\Validator\ValidatorTrait;
	/**
	 * @var Validator\Manager\Validator
	 */
	protected $validator = null;
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
	 * Constructor
	 */
	private function completeLabelsAndValues()
	{
		foreach ($this->getLabels() as $key => $value){
			$key = $this->string()->toUpper($key);
			$this->labels[$key] = $value;
			$this->value[$key] = null;
		}
		foreach ($this->getRules() as $rule){
			$keys = (is_array($rule[0])) ? $rule[0] : explode(',',$rule[0]);
			foreach ($keys as $key){
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
	 * Get regex for class
	 * @return string
	 */
	static protected function getRegexClass()
	{
		return "/(\w+)\\\\(\w+)\\\\Entity\\\\(\w+)/";
	}
	/**
	 * Get class name with namespace
	 * @param string $sPackage
	 * @param string $sService
	 * @param string $sEntity
	 * @return string
	 */
	static protected function getClass($sPackage,$sService,$sEntity)
	{
		return $sPackage."\\".$sService."\\Entity\\".ucwords($sEntity);
	}
	/**
	 * Get self
	 * @param string|boolean $entity
	 * @param array $params
	 * @return self
	 */
	static public function getEntity($entity = false,$params = [])
	{
		$instance = static::autoload($entity,'entities',$params);
		$instance->init();
		return $instance;
	}
	/**
	 * Get labels
	 * @return array()
	 */
	protected function labels()
	{
		return [];
	}
	/**
	 * Get labels
	 * @return array
	 */
	protected function getLabels()
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
		} else{
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
	 * Get settings
	 * @return array
	 */
	protected function settings()
	{
		return [];
	}
	/**
	 * Get settings
	 * @return array
	 */
	protected function getSettings($key)
	{
		return (array_key_exists($key,$this->settings())) ? $this->settings()[$key] : false;
	}
	/**
	 * Get rules
	 * @return array
	 */
	protected function rules()
	{
		return [];
	}
	/**
	 * Get rules
	 * @return array
	 */
	protected function getRules()
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
		echo htmlspecialcharsbx($this->getValue($key));
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
		} else{
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
		} else{
			$fields = ['#KEY#' => $key,'#MODEL#' => get_class($this)];
			throw new \InvalidArgumentException(strtr("`#KEY#` is not field #MODEL#",$fields));
		}
		return $this;
	}
	/**
	 * Get data
	 * @return array|null
	 */
	public function getData()
	{
		$result = null;
		foreach ($this->value as $key => $value){
			if ($value !== null){
				$result[$key] = $value;
			}
		}
		return $result;
	}
	/**
	 * Set data
	 * @param array $values
	 * @return self
	 */
	public function setData(array $values)
	{
		foreach ($values as $key => $value){
			$key = $this->string()->toUpper($key);
			if (array_key_exists($key,$this->value)){
				$this->value[$key] = $value;
			}
		}
		return $this;
	}
	/**
	 * Prepare $_FILES
	 * @param array $files
	 * @return array
	 */
	public function prepareFiles($files)
	{
		$arrayForFill = [];
		foreach ($files as $firstNameKey => $arFileDescriptions){
			foreach (array_keys($arFileDescriptions) as $fileDescriptionParam){
				$this->rRestructuringFilesArray($arrayForFill,$firstNameKey,$files[$firstNameKey][$fileDescriptionParam],$fileDescriptionParam);
			}
		}
		return $arrayForFill;
	}
	private function rRestructuringFilesArray(&$arrayForFill,$currentKey,$currentMixedValue,$fileDescriptionParam)
	{
		if (is_array($currentMixedValue)){
			foreach ($currentMixedValue as $nameKey => $mixedValue){
				$this->rRestructuringFilesArray($arrayForFill[$currentKey],$nameKey,$mixedValue,$fileDescriptionParam);
			}
		} else{
			$arrayForFill[$currentKey][$fileDescriptionParam] = $currentMixedValue;
		}
	}
	/**
	 * Load validator
	 * @return Validator\Manager\Validator
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
	 * @return \Illuminate\Support\MessageBag
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
	 * Get class name for behavior
	 * @param string $sPackage
	 * @param string $sService
	 * @param string $sBehavior
	 * @return string
	 */
	protected static function getBehaviorClass($sPackage,$sService,$sBehavior)
	{
		return $sPackage."\\".$sService."\\Behavior\\".ucwords($sBehavior);
	}
	/**
	 * Get template and params behavior
	 * @param string $path
	 * @param array $params
	 * @return array
	 */
	private function getBehaviorArray($path,array $params = [])
	{
		$package = static::getPackage();
		$service = static::getService();
		$count = substr_count($path,':');
		if ($count === 1){
			$path = $package.':'.$path;
		}
		if ($count === 0){
			$path = $package.':'.$service.':'.$path;
		}
		if (Registry::exists('behaviors',$path)){
			$reg_array = Registry::get('behaviors',$path);
			if (array_key_exists('class',$reg_array)){
				$behavior = $reg_array['class'];
			}
			if (array_key_exists('params',$reg_array)){
				$params = array_merge($params,$reg_array['params']);
			}
		}
		return [$behavior,$params];
	}
	/**
	 * Set params behavior
	 * @param \BX\Behavior $behavior
	 * @param array $params
	 */
	private function setParamsBehavior(Behavior $behavior,array $params)
	{
		foreach ($params as $key => $value){
			$func = 'set';
			foreach (explode('_',$key) as $item){
				$func .= $this->string()->ucwords($item);
			}
			$behavior->$func($value);
		}
		return $this;
	}
	/**
	 * Set behavior
	 * @throws \InvalidArgumentException
	 * @return self
	 */
	private function processingBehavior()
	{
		foreach ($this->behaviors() as $name => $behavior){
			if (is_array($behavior)){
				list($class_str,$params) = $this->getBehaviorArray($behavior[0],(array) $behavior[1]);
			} else{
				list($class_str,$params) = $this->getBehaviorArray($behavior);
			}
			list($package,$module,$class) = explode(':',$class_str);
			$c = static::getBehaviorClass($package,$module,$class);
			if (class_exists($c)){
				$instance = new $c();
			} else{
				$message = strtr('Behavior `#CLASS#` is not found',['#CLASS#' => $c]);
				throw new \InvalidArgumentException($message);
			}
			if (!empty($params)){
				$this->setParamsBehavior($instance,$params);
			}
			$this->attachBehavior($name,$instance);
		}
		return $this;
	}
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$package = static::getPackage();
		$service = static::getService();
		if (strlen($package) > 0 && strlen($service) > 0){
			$this->processingBehavior();
		} else{
			$this->log()->warning('package and service is not parsing');
		}
		$this->rules = $this->getRules();
		$this->completeLabelsAndValues();
	}
	protected $behaviors = array();
	/**
	 * Get behaviors
	 * return array
	 * */
	public function behaviors()
	{
		return array();
	}
	/**
	 * Attach behavior
	 * @param string $name
	 * @param Behavior $behavior
	 * @return Behavior
	 * */
	public function attachBehavior($name,Behavior $behavior)
	{
		$behavior->attach($this);
		$behavior->init();
		return $this->behaviors[$name] = $behavior;
	}
	/**
	 * Detach behavior
	 * @param string $name
	 * @return Behavior
	 * */
	public function detachBehavior($name)
	{
		if (!isset($this->behaviors[$name])){
			$this->behaviors[$name]->detach();
			$behavior = $this->behaviors[$name];
			unset($this->behaviors[$name]);
			return $behavior;
		}
	}
	/**
	 * Get behavior
	 * @param type $name
	 * @return type
	 * @throws \LogicException
	 */
	public function getBehavior($name)
	{
		if (!empty($this->behaviors[$name])){
			return $this->behaviors[$name];
		}
		$message = get_class($this).' and its behaviors do not have a method or closure named '.$name;
		throw new \LogicException($message);
	}
	/**
	 * Get all behavior
	 * @param type $name
	 * @return type
	 * @throws \LogicException
	 */
	public function getBehaviorAll()
	{
		return $this->behaviors;
	}
}