<?php
namespace BX\DB\Filter\Rule;
use BX\DB\Filter\SqlBuilder;
use BX\DB\Adaptor\IAdaptor;

class DateTime extends Base
{
	use \BX\Date\DateTrait;
		
	private function getNull($sColumn)
	{
		return 	'('.$sColumn." IS NULL OR ".$this->adaptor()->length($sColumn).'=0)';
	}
	
	private $sFormat;
	
	public static function short(SqlBuilder $oFilter,IAdaptor $oAdaptor)
	{
		$date = new self($oFilter,$oAdaptor);
		$date->setFormat('short');
		return $date;
	}

	public static function full(SqlBuilder $oFilter,IAdaptor $oAdaptor)
	{
		$date = new self($oFilter,$oAdaptor);
		$date->setFormat('full');
		return $date;
	}
	
	public function setFormat($sFormat = 'short')
	{
		$this->sFormat = $sFormat;
		return $this;
	}
	
	private function getSql($sField,$sValue,$sOperation)
	{
		$sColumn = $this->getColumn($sField);
		if(strlen($sValue)>0){
			if(!$this->date()->checkDateTime($sValue,$this->sFormat)){
				throw new \InvalidArgumentException("Filter `$sField` must be date input `$sValue`");
			}
			return $sColumn.' '.$sOperation.' '.$this->bindParam($sField,$this->date()->makeTimeStamp($sValue,$this->sFormat) + $this->date()->getOffset());
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