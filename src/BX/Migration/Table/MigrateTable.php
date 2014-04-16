<?php namespace BX\Migration\Store;
use BX\DB\ITable;

class MigrateTable implements ITable
{
	use \BX\DB\TableTrait;
	/**
	 * Get settings
	 * @return array
	 */
	public function settings()
	{
		return [
			self::DB_TABLE => 'tbl_migrate',
		];
	}
	/**
	 * Get columns
	 * @return array
	 */
	protected function columns()
	{
		return [
			self::C_ID			 => $this->column()->int('T.ID'),
			self::C_PACKAGE		 => $this->column()->string('T.PACKAGE'),
			self::C_SERVICE		 => $this->column()->string('T.SERVICE'),
			self::C_FUNCTION	 => $this->column()->string('T.FUNCTION'),
			self::C_GUID		 => $this->column()->string('T.GUID'),
			self::C_TIMESTAMP_X	 => $this->column()->datetime('T.TIMESTAMP_X'),
		];
	}
}