<?php namespace BX\Migration\Manager;
use BX\Manager;
use BX\Migration\Entity\Migration;

class Migrate extends Manager
{
	/**
	 * @var string
	 */
	private $package;
	/**
	 * @var string
	 */
	private $service;
	/**
	 * @var string
	 */
	private $hash;
	/**
	 * @var boolean
	 */
	protected $found = false;
	/**
	 * Set package
	 * @param string $package
	 */
	public function setPackage($package)
	{
		$this->package = $package;
		return $this;
	}
	/**
	 * Is found
	 * @return boolean
	 */
	public function isFound()
	{
		return $this->found;
	}
	/**
	 * Set service
	 * @param string $service
	 */
	public function setService($service)
	{
		$this->service = ucwords($service);
		return $this;
	}
	/**
	 * Get unique id
	 * @return string
	 */
	public function getHash()
	{
		return $this->hash;
	}
	/**
	 * Init
	 */
	public function init()
	{
		$this->hash = uniqid('migrate'.$this->package.$this->service);
	}
	/**
	 * Get up function
	 * @return array
	 * @throws \LogicException
	 */
	private function getTreeUp()
	{
		$class = new \ReflectionClass($this->package.'\\'.$this->service.'\\Migration');
		$func_array = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($func_array as $index => $function){
			if (substr($function->name,0,2) !== 'up'){
				unset($func_array[$index]);
			}
		}
		$hash_table = [];
		$stack = [];
		foreach ($func_array as $function){
			$doc_block = $function->getDocComment();
			$match = [];
			if (strpos($doc_block,'@root')){
				$stack[$function->name] = false;
			} elseif (preg_match("/@parent\s+(\S+)/",$doc_block,$match)){
				$hash_table[$function->name] = $match[1];
			}
		}
		if (empty($stack)){
			throw new \LogicException("Not found root function");
		}
		foreach ($stack as $func_name => &$node){
			$node = $this->buildTree($hash_table,$func_name);
		}
		return $stack;
	}
	/**
	 * Up command
	 * @return boolean
	 */
	public function up()
	{
		$this->parseTree($this->getTreeUp());
		return true;
	}
	/**
	 * Redo command
	 * @return boolean
	 */
	public function redo()
	{
		$entity = Migration::getEntity();
		$this->found = true;
		$class = $this->package.'\\'.$this->service.'\\Migration';
		if(!class_exists($class)){
			throw new \LogicException("Class `{$class}` is not found");
		}
		$instance = new $class();
		foreach ($this->getLastFunctions() as $func){
			call_user_func_array([$instance,$func[Migration::C_FUNCTION]],[true]);
		}
	}
	/**
	 * Add row in db table
	 * @param string $func
	 */
	private function add($func)
	{
		$this->found = true;
		$class = $this->package.'\\'.$this->service.'\\Migration';
		if(!class_exists($class)){
			throw new \LogicException("Class `{$class}` is not found");
		}
		$instance = new $class();
		call_user_func_array([$instance,$func],[true]);
		$entity = Migration::getEntity();
		$entity->setValue(Migration::C_PACKAGE,$this->package);
		$entity->setValue(Migration::C_SERVICE,$this->service);
		$entity->setValue(Migration::C_FUNCTION,$func);
		$entity->setValue(Migration::C_GUID,$this->hash);
		if ($entity->add() === false){
			var_dump($entity->getErrors());
			throw new \LogicException('Add migration error');
		}
	}
	/**
	 * Down command
	 * @return boolean
	 */
	private function down()
	{
		$entity = Migration::getEntity();
		$this->found = true;
		$class = $this->package.'\\'.$this->service.'\\Migration';
		if(!class_exists($class)){
			throw new \LogicException("Class `{$class}` is not found");
		}
		$instance = new $class();
		foreach ($this->getLastFunctions() as $func){
			call_user_func_array([$instance,$func[Migration::C_FUNCTION]],[false]);
			if ($entity->delete($func[Migration::C_ID]) === false){
				throw new \LogicException('Delete migration error');
			}
		}
	}
	private function getLastFunctions()
	{
		$used_function = [];
		$migrations = Migration::filter()
			->sort([Migration::C_TIMESTAMP_X => 'desc'])
			->filter([
				'='.Migration::C_SERVICE => $this->service,
				'='.Migration::C_PACKAGE => $this->package,
			])
			->select(Migration::C_FUNCTION,Migration::C_GUID,Migration::C_ID)
			->asArray();
		$unique_id = null;
		foreach ($migrations as $migration){
			if ($unique_id === null){
				$unique_id = $migration[Migration::C_GUID];
			} else{
				if ($unique_id !== $migration[Migration::C_GUID]){
					break;
				}
			}
			$used_function[] = $migration;
		}
		return $used_function;
	}
	/**
	 * Get migrate function
	 * @return array
	 */
	private function getUsedFunction()
	{
		$used_function = [];
		$migrations = Migration::filter()
			->filter([
				'='.Migration::C_SERVICE => $this->service,
				'='.Migration::C_PACKAGE => $this->package,
			])
			->select(Migration::C_FUNCTION)
			->asArray();
		foreach ($migrations as $migration){
			$used_function[] = $migration[Migration::C_FUNCTION];
		}
		return $used_function;
	}
	/**
	 * Parse tree
	 * @param type $stack
	 */
	private function parseTree($stack,array $used_func = null)
	{
		if ($used_func === null){
			$used_func = $this->getUsedFunction();
		}
		foreach ($stack as $func => $hash_table){
			if (!in_array($func,$used_func)){
				$this->add($func);
			}
			if ($hash_table !== false){
				$this->parseTree($hash_table,$used_func);
			}
		}
	}
	/**
	 * Build tree
	 * @param string $hash_table
	 * @param string $root
	 * @return array
	 */
	private function buildTree($hash_table,$root)
	{
		$result = [];
		$aKeys = array_keys($hash_table,$root,true);
		if (empty($aKeys)){
			return false;
		}
		foreach ($aKeys as $func){
			unset($aKeys[$func]);
		}
		foreach ($aKeys as $func){
			$result[$func] = $this->buildTree($hash_table,$func);
		}
		return $result;
	}
}