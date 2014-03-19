<?php
namespace BX\DB\Filter\Rule;

class Boolean extends Base
{
	private function getNull($sColumn)
	{
		return 	'('.$sColumn." IS NULL OR ".$this->adaptor()->length($sColumn).'=0)';
	}
	
	public function addCondition($sField, $sValue)
	{
		$bNot = false;
		if(substr($sField, 0, 1) === '!'){
			$sField = substr($sField, 1);
			$bNot = true;
		}
		if(in_array($sValue, [0,false,'N'],true) || strlen($sValue) === 0){
			$sSql = $this->getColumn($sField).' = 0';
		} elseif(in_array($sValue, [1,true,'Y'])){
			$sSql = $this->getColumn($sField).' = 1';
		} else{
			throw new \InvalidArgumentException("Condition must be 0|1|false|true|Y|N set `$sValue`");
		}
		if($bNot){
			$sSql = "NOT($sSql)";
		}
		return $sSql;
	}
}