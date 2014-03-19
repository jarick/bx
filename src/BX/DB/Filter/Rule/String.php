<?php
namespace BX\DB\Filter\Rule;

class String extends Base
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
		if(substr($sField, 0, 1) === '%'){
			$sField = substr($sField, 1);
			$sColumn = $this->getColumn($sField);
			if(strlen($sValue)>0){
				$sSql = $this->adaptor()->upper($sColumn).' LIKE '.$this->adaptor()->upper($this->bindParam($sField,'%'.$sValue.'%'));
			} else{
				$sSql = $this->getNull($sColumn);
			}
		} elseif(substr($sField, 0, 1) === '='){
			$sField = substr($sField, 1);
			$sColumn = $this->getColumn($sField);
			if(strlen($sValue)>0){
				$sSql = $sColumn.' = '.$this->bindParam($sField,$sValue);
			} else{
				$sSql = $this->getNull($sColumn);
			}
		} elseif(strlen($sValue)<=0){
			$sColumn = $this->getColumn($sField);
			$sSql = $this->getNull($sColumn);
		} else{
			$sColumn = $this->getColumn($sField);
			$sSql = $sColumn.' LIKE '.$this->bindParam($sField,$sValue);
		}
		if($bNot){
			$sSql = "NOT($sSql)";
		}
		return $sSql;
	}
}