<?php namespace BX\DB\Filter\Rule;
use BX\DB\Filter\SqlBuilder;

interface IRule
{
	public function __construct(SqlBuilder $filter);
	public function addCondition($field,$value);
}