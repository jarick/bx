<?php namespace BX\Migration;
use BX\DB\UnitOfWork\Repository;
use BX\Migration\Entity\MigrationEntity;
use BX\Migration\Migration;
use BX\Migration\Table\MigrateTable;
use \BX\Base\Registry;

class MigrateManager
{
	use \BX\String\StringTrait;
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
	 * @var MigrateTable
	 */
	protected $table = null;
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
	 * Constructor
	 */
	public function __construct($package,$service)
	{
		$this->package = $package;
		$this->service = $service;
		$this->hash = uniqid('migrate'.$this->package.$this->service);
		if ($this->table === null){
			if (!Registry::exists('migrate','class')){
				$this->table = new MigrateTable();
			}else{
				$reg = Registry::get('migrate','class');
				$this->table = new $reg();
			}
		}
	}
	/**
	 * Get up function
	 * @return array
	 * @throws \LogicException
	 */
	private function getTreeUp()
	{
		$class_name = $this->package.'\\'.$this->service.'\\Migration';
		if (!class_exists($class_name)){
			throw new \RuntimeException("Class not found `$class_name`");
		}
		$class = new \ReflectionClass($class_name);
		$func_array = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach($func_array as $index => $function){
			if (substr($function->name,0,2) !== 'up'){
				unset($func_array[$index]);
			}
		}
		$hash_table = [];
		$stack = [];
		foreach($func_array as $function){
			$doc_block = $function->getDocComment();
			$match = [];
			if (strpos($doc_block,'@root')){
				$stack[$function->name] = false;
			}elseif (preg_match("/@parent\s+(\S+)/",$doc_block,$match)){
				$hash_table[$function->name] = $match[1];
			}
		}
		if (empty($stack)){
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
	 * Redo command
	 * @return boolean
	 */
	public function redo()
	{
		$this->found = true;
		$class = $this->package.'\\'.$this->service.'\\Migration';
		if (!class_exists($class)){
			throw new \LogicException("Class `{$class}` is not found");
		}
		$instance = new $class();
		foreach($this->getLastFunctions() as $func){
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
		if (!class_exists($class)){
			throw new \LogicException("Class `{$class}` is not found");
		}
		$instance = new $class();
		call_user_func_array([$instance,$func],[true]);
		$entity = new MigrationEntity();
		$entity->package = $this->package;
		$entity->service = $this->service;
		$entity->function = $func;
		$entity->guid = $this->hash;
		$trans = new Repository();
		$trans->add($this->table,$entity);
		if (!$trans->commit(false)){
			throw new \RuntimeException('Add migration error');
		}
	}
	/**
	 * Down command
	 * @return boolean
	 */
	public function down()
	{
		$this->found = true;
		$class = $this->package.'\\'.$this->service.'\\Migration';
		if (!class_exists($class)){
			throw new \LogicException("Class `{$class}` is not found");
		}
		$instanse = new $class();
		$trans = new Repository();
		foreach($this->getLastFunctions() as $func){
			call_user_func_array([$instanse,$func->function],[false]);
			$trans->delete($this->table,$func);
		}
		if (!$trans->commit(false)){
			throw new \RuntimeException('Delete migration error');
		}
	}
	/**
	 * Get last function by guid
	 * @return MigrationEntity[]
	 */
	private function getLastFunctions()
	{
		$migrations = MigrateTable::finder(MigrationEntity::getClass())
			->sort([Migration::C_TIMESTAMP_X => 'DESC'])
			->filter([
				'='.Migration::C_SERVICE => $this->service,
				'='.Migration::C_PACKAGE => $this->package,
			])
			->all();
		$unique_id = null;
		$used_function = [];
		foreach($migrations as $migration){
			if ($unique_id === null){
				$unique_id = $migration->guid;
			}else{
				if ($unique_id !== $migration->guid){
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
		$functions = MigrateTable::finder(MigrationEntity::getClass())
			->filter([
				'='.MigrationEntity::C_SERVICE	 => $this->service,
				'='.MigrationEntity::C_PACKAGE	 => $this->package,
			])
			->select([MigrationEntity::C_FUNCTION])
			->all();
		$return = [];
		foreach($functions as $function){
			$return[] = $function->function;
		}
		return $return;
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
		foreach($stack as $func => $hash_table){
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
		foreach($aKeys as $func){
			unset($aKeys[$func]);
		}
		foreach($aKeys as $func){
			$result[$func] = $this->buildTree($hash_table,$func);
		}
		return $result;
	}
}