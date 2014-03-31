<?php namespace BX\DB\Helper;
use BX\Base;
use BX\DB\Column\BooleanColumn;
use BX\DB\Column\NumberColumn;
use BX\DB\Column\StringColumn;
use BX\DB\Column\TimestampColumn;

class ColumnHelper extends Base
{
	/**
	 * Get string column
	 * @param string $column
	 * @return StringColumn
	 */
	public function string($column)
	{
		return StringColumn::create($column);
	}
	/**
	 * Get boolean column
	 * @param string $column
	 * @return BooleanColumn
	 */
	public function bool($column,$true = 'Y',$false = 'N',$strict = false)
	{
		return BooleanColumn::create($column,$true,$false,$strict);
	}
	/**
	 * Get interger column
	 * @param string $column
	 * @return NumberColumn
	 */
	public function int($column)
	{
		return NumberColumn::create($column,true);
	}
	/**
	 * Get number column
	 * @param string $column
	 * @return NumberColumn
	 */
	public function float($column)
	{
		return NumberColumn::create($column,false);
	}
	/**
	 * Get date column
	 * @param string $column
	 * @return TimestampColumn
	 */
	public function date($column)
	{
		return TimestampColumn::create($column,'short');
	}
	/**
	 * Get datetime column
	 * @param string $column
	 * @return TimestampColumn
	 */
	public function datetime($column)
	{
		return TimestampColumn::create($column,'full');
	}
}