<?php namespace BX\DB\Column;

interface IColumn
{
	public function convertToDB($key,$value,array $values);
	public function convertFromDB($key,$value,array $values);
	public function getColumn();
	public function getFilterRule();
}
