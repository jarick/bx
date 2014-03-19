<?php
namespace BX\DB\Filter\Rule;
use BX\Object;
use BX\DB\Filter\SqlBuilder;
use BX\DB\Adaptor\IAdaptor;

abstract class Base extends Object implements IRule
{
	use \BX\String\StringTrait;
 	private $oAdaptor;
 	private $oFilter;
 	
 	public function __construct(SqlBuilder $oFilter,IAdaptor $oAdaptor)
 	{
 		$this->oFilter = $oFilter;
 		$this->oAdaptor = $oAdaptor;
 	}
 	
 	protected function getColumn($sKey)
 	{
 		return $this->builder()->getColumn($sKey);
 	}
 	
 	protected function bindParam($sKey,$sValue)
 	{
 		return $this->builder()->bindParam($sKey,$sValue);
 	}
 	
 	protected function builder()
 	{
 		return $this->oFilter;
 	}
 	
 	protected function adaptor()
 	{
 		return $this->oAdaptor;
 	}
 	 	
 	protected function esc($sString)
 	{
		$sQuote = $this->adaptor()->getQuoteChar();
		return $sQuote.$this->string()->toUpper($sString).$sQuote;
 	}
	
	abstract public function addCondition($sField,$sValue);
}