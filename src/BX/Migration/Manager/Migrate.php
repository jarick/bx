<?php
namespace BX\Migration\Manager;
use BX\Manager;
use BX\Migration\Entity\Migration;

class Migrate extends Manager
{
	private $sPackage;
	
	public function setPackage($sPackage)
	{
		$this->sPackage = ucwords($sPackage);
	}
	
	private $sService;
	
	public function setService($sService)
	{
		$this->sService = ucwords($sService);
	}
	
	private $sMd5;
	
	public function init()
	{
		$this->sMd5 = uniqid('migrate'.$this->sPackage.$this->sService);
	}
	
	private function getTreeUp()
	{
		$oClass = new \ReflectionClass($this->sPackage.'\\'.$this->sService.'\\Migration');
		$aFunction = $oClass->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($aFunction as $iIndex => $oFunction){
			if(substr($oFunction->name, 0,2) !== 'up'){
				unset($aFunction[$iIndex]);
			}
		}
		$aHashTable = [];
		$aStack = [];
		foreach ($aFunction as $iIndex => $oFunction){
			$sDocBlock = $oFunction->getDocComment();
			if (strpos($sDocBlock,'@root')){
				$aStack[$oFunction->name] = false;
			}elseif (preg_match("/@parent\s+(\S+)/", $sDocBlock,$aMatch)){
				$aHashTable[$oFunction->name] = $aMatch[1];
			}
		}
		if(empty($aStack)){
			throw new \LogicException("Not found root function");
		}
		foreach($aStack as $sFunction => &$aNode){
			$aNode = $this->buildTree($aHashTable,$sFunction);
		}
		return $aStack;
	}
	
	protected $bFound = false;
	
	public function isFound()
	{
		return $this->bFound;
	}
	
	public function up()
	{
		$this->parseTree($this->getTreeUp());
		return true;
	}
	
	public function down()
	{
		
	}
	
	public function redo()
	{
		
	}
	
	private function delete($sFunction)
	{
		
	}
	
	private function add($sFunction)
	{
		$this->bFound = true;
		$sClass = $this->sPackage.'\\'.$this->sService.'\\Migration';
		$oInstance = new $sClass();
		call_user_func_array([$oInstance,$sFunction],[true]);
		$oEntity = Migration::getEntity();
		$oEntity->setValue(Migration::C_PACKAGE,$this->sPackage);
		$oEntity->setValue(Migration::C_SERVICE,$this->sService);
		$oEntity->setValue(Migration::C_FUNCTION,$sFunction);
		$oEntity->setValue(Migration::C_GUID,$this->sMd5);
		$oEntity->add();
	}
	
	private function getUsedFunction()
	{
		return [
			'upVersion',
		];
	}
	
	private function parseTree($aStack)
	{
		foreach ($aStack as $sFunction => $aHashTable){
			if(!in_array($sFunction,$this->getUsedFunction())){
				$this->add($sFunction);	
			}
			if($aHashTable !== false){
				$this->parseTree($aHashTable);
			}
		}
	}
	
	private function buildTree($aHashTable,$sRoot)
	{
		$aResult = [];
		$aKeys = array_keys($aHashTable, $sRoot ,true);
		if(empty($aKeys)){
			return false;
		}
		foreach ($aKeys as $sFunction){
			unset($aKeys[$sFunction]);
		}
		foreach ($aKeys as $sFunction){
			$aResult[$sFunction] = $this->buildTree($aHashTable, $sFunction);
		}
		return $aResult;
	}	
}