<?php
namespace BX\DB\Filter\Rule;

class Number extends Base
{
	private function getNull($sColumn)
	{
		return 	'('.$sColumn." IS NULL OR ".$this->adaptor()->length($sColumn).'=0)';
	}
	private function getSql($sField,$sValue,$sOperation)
	{
		$sColumn = $this->getColumn($sField);
		if(strlen($sValue)>0){
			if(!is_numeric($sValue)){
				throw new \InvalidArgumentException("Filter `$sField` must be numeric input `$sValue`");
			}
			return $sColumn.' '.$sOperation.' '.$this->bindParam($sField,$sValue);
		} else{
			return $this->getNull($sColumn);
		}
	}
	
	public function addCondition($sField, $sValue)
	{
		$bNot = false;
		if(substr($sField, 0, 1) === '!'){
			$sField = substr($sField, 1);
			$bNot = true;
		}
		if(substr($sField, 0, 2) === '>='){
			$sSql = $this->getSql(substr($sField,2), $sValue, '>=');
		} elseif(substr($sField, 0, 2) === '<='){
			$sSql = $this->getSql(substr($sField,2), $sValue, '<=');
		} elseif(substr($sField, 0, 1) === '>'){
			$sSql = $this->getSql(substr($sField,1), $sValue, '>');
		} elseif(substr($sField, 0, 1) === '<'){
			$sSql = $this->getSql(substr($sField,1), $sValue, '<');
		} else{
			$sSql = $this->getSql($sField, $sValue, '=');
		}
		if($bNot){
			$sSql = "NOT($sSql)";
		}
		return $sSql;
	}
}