<?php
namespace BX\Migration\Manager;
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
		$this->package = $this->string()->ucwords($package);
		return $this;
	}
	/**
	 * Is found
	 * @return boolean
	 */
	public function isFound()
	{
		return $this->bFound;
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
			if(substr($function->name, 0,2) !== 'up'){
				unset($func_array[$index]);
			}
		}
		$hash_table = [];
		$stack = [];
		foreach ($func_array as $function){
			$doc_block = $function->getDocComment();
			if (strpos($doc_block,'@root')){
				$stack[$function->name] = false;
			}elseif (preg_match("/@parent\s+(\S+)/", $doc_block,$match = false)){
				$hash_table[$function->name] = $match[1];
			}
		}
		if(empty($stack)){
			throw new \LogicException("Not found root function");
		}
		foreach($stack as $func_name => &$node){
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
	 * Down command
	 * @return boolean
	 */	
	public function down()
	{
		
	}
	/**
	 * Redo command
	 * @return boolean
	 */
	public function redo()
	{
		
	}
	/**
	 * Add row in db table
	 * @param string $func
	 */
	private function add($func)
	{
		$this->bFound = true;
		$sClass = $this->package.'\\'.$this->service.'\\Migration';
		$oInstance = new $sClass();
		call_user_func_array([$oInstance,$func],[true]);
		/*$oEntity = Migration::getEntity();
		$oEntity->setValue(Migration::C_PACKAGE,$this->package);
		$oEntity->setValue(Migration::C_SERVICE,$this->service);
		$oEntity->setValue(Migration::C_FUNCTION,$func);
		$oEntity->setValue(Migration::C_GUID,$this->hash);
		$oEntity->add();*/
	}
	/**
	 * Delete row form db table
	 * @param string $func
	 */
	private function delete($func)
	{
		
	}
	/**
	 * Get migrate function 
	 * @return array
	 */
	private function getUsedFunction()
	{
		return [
			'upVersion',
		];
	}
	/**
	 * Parse tree
	 * @param type $stack
	 */
	private function parseTree($stack)
	{
		foreach ($stack as $func => $hash_table){
			if(!in_array($func,$this->getUsedFunction())){
				$this->add($func);	
			}
			if($hash_table !== false){
				$this->parseTree($hash_table);
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
		$aKeys = array_keys($hash_table, $root ,true);
		if(empty($aKeys)){
			return false;
		}
		foreach ($aKeys as $func){
			unset($aKeys[$func]);
		}
		foreach ($aKeys as $func){
			$result[$func] = $this->buildTree($hash_table, $func);
		}
		return $result;
	}	
}