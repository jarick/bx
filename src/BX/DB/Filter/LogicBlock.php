<?php
namespace BX\DB\Filter;
use BX\Object;
use BX\Registry;
use BX\DB\Filter\Rule\String;
use BX\DB\Filter\Rule\Number;
use BX\DB\Filter\Rule\DateTime;
use BX\DB\Filter\Rule\Boolean;

class LogicBlock extends Object
{
	private $oSqlBuilder;
	private $aFilterRule;
	
	public function __construct(SqlBuilder $oSqlBuilder,array $aFilterRule)
	{
		foreach($aFilterRule as $sFieldArray => $sRule){
			unset($aFilterRule[$sFieldArray]);
			foreach(explode(',', $sFieldArray) as $sField){
				$aFilterRule[$sField] = $sRule;
			}
		}
		if(empty($aFilterRule)){
			throw new \InvalidArgumentException("Filter rules is not set");
		}
		$this->oSqlBuilder = $oSqlBuilder;
		$this->aFilterRule = $aFilterRule;
	}
	
	private function getSpecialCharsArray()
	{
		return ['!','=','>','<','%'];
	}
	
	/**
	 * @return IParam
	 **/
	private function getType($sKey)
	{
		while(in_array(substr($sKey,0,1),$this->getSpecialCharsArray())){
			$sKey = substr($sKey,1);
		}
		if(array_key_exists($sKey, $this->aFilterRule)){
			$sType = $this->aFilterRule[$sKey];
			switch($sType){
				case 'string': return new String($this->oSqlBuilder,$this->oSqlBuilder->adaptor());
				case 'number': return new Number($this->oSqlBuilder,$this->oSqlBuilder->adaptor());
				case 'date': return DateTime::short($this->oSqlBuilder,$this->oSqlBuilder->adaptor());
				case 'datetime': return DateTime::full($this->oSqlBuilder,$this->oSqlBuilder->adaptor());
				case 'boolean': return new Boolean($this->oSqlBuilder,$this->oSqlBuilder->adaptor());
				default:
					if(Registry::exists('filter_rule',$sType)){
						if(is_string($sClass = Registry::get('filter_rule',$sType))){
							if(class_exists($sClass)){
								return new $sClass($this->oSqlBuilder,$this->oSqlBuilder->adaptor());
							} else{
								throw new \InvalidArgumentException("Filter rule class `$sClass` is not exists");
							}
						} else{
							throw new \InvalidArgumentException("Filter rule .$sType must be string");
						}
					} else{
						throw new \InvalidArgumentException("Filter rule `$sType` is not exists");
					}
			}
		} else{
			throw new \InvalidArgumentException("Unknow field `$sKey`");
		}
	}
	
	public function toSql($aFilter)
	{
		$aReturn = [];
		$sLogic = 'AND';
		foreach ($aFilter as $sKey => $mValue){
			if(is_numeric($sKey)){
				$oBlock = new LogicBlock($this->oSqlBuilder,$this->aFilterRule);
				$aReturn[] = '('.$oBlock->toSql((array)$mValue).')';
			} elseif($sKey === 'LOGIC'){
				if(in_array($mValue, ['OR','AND'])){
					$sLogic = $mValue;
				} else{
					throw new \InvalidArgumentException("Logic must be AND or OR set `$mValue`");
				}
			} else{
				$aReturn[] = $this->getType($sKey)->addCondition($sKey, $mValue);
			}
		}
		return implode(' '.$sLogic.' ', $aReturn); 
	}
}