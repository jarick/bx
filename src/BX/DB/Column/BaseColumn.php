<?php namespace BX\DB\Column;
use BX\DB\Column\IColumn;
use BX\Base;

abstract class BaseColumn extends Base implements IColumn
{
	private $column;

	abstract public function getFilterRule();

	public function __construct($column)
	{
		$this->column = $column;
	}

	public function getColumn()
	{
		return $this->column;
	}

	public static function create($column)
	{
		$object = new static($column);
		return $object;
	}

	public function convertFromDB($key,$value,array $values)
	{
		return $value;
	}

	public function convertToDB($key,$value,array $values)
	{
		return $value;
	}
}
