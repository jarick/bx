<?php namespace BX\DB\Helper;

class RelationHelper
{
	/**
	 * Get inner join sql
	 * @param string $table
	 * @param string $on
	 */
	public function inner($table,$on)
	{
		return "INNER JOIN {$table} ON {$on}";
	}
	/**
	 * Get left outer join sql
	 * @param string $table
	 * @param string $on
	 */
	public function left($table,$on)
	{
		return "LEFT OUTER JOIN {$table} ON {$on}";
	}
	/**
	 * Get right outer join sql
	 * @param string $table
	 * @param string $on
	 */
	public function right($table,$on)
	{
		return "RIGHT OUTER JOIN {$table} ON {$on}";
	}
	/**
	 * Get full outer join sql
	 * @param string $table
	 * @param string $on
	 */
	public function full($table,$on)
	{
		return "FULL OUTER JOIN {$table} ON {$on}";
	}
	/**
	 * Get cross join sql
	 * @param string $table
	 * @param string $on
	 */
	public function cross($table,$on)
	{
		return "CROSS JOIN {$table} ON {$on}";
	}
}