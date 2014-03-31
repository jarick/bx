<?php namespace BX\DB\Column;
use BX\DB\Column\IColumn;
use BX\Base;

abstract class BaseColumn extends Base implements IColumn
{
	/**
	 * @var string
	 */
	private $column;
	/**
	 * Get filter rule name
	 * @return string
	 */
	abstract public function getFilterRule();
	/**
	 * Constructor
	 * @param string $column
	 */
	public function __construct($column)
	{
		$this->column = $column;
	}
	/**
	 * Get column name
	 * @return string
	 */
	public function getColumn()
	{
		return $this->column;
	}
	/**
	 * Create column
	 * @param string $column
	 * @return \static
	 */
	public static function create($column)
	{
		$object = new static($column);
		return $object;
	}
	/**
	 * Convert value from db
	 * @param string $value
	 * @return mixed
	 */
	public function convertFromDB($value)
	{
		return $value;
	}
	/**
	 * Convert value to db
	 * @param mixed $value
	 * @return string
	 */
	public function convertToDB($value)
	{
		return $value;
	}
}