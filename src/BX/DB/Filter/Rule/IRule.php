<?php
namespace BX\DB\Filter\Rule;
use BX\DB\Filter\SqlBuilder;
use BX\DB\Adaptor\IAdaptor;

interface IRule
{
	public function __construct(SqlBuilder $oFilter,IAdaptor $oAdaptor);
	
	public function addCondition($sField, $sValue);
}