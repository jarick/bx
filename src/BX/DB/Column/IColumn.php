<?php namespace BX\DB\Column;

interface IColumn
{
	public function convertToDB($value);
	public function convertFromDB($value);
	public function getColumn();
	public function getFilterRule();
}