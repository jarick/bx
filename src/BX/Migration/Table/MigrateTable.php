<?php namespace BX\Migration\Table;
use \BX\DB\ITable,
	\BX\Migration\Entity\MigrationEntity;

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
			'db_table' => 'tbl_migrate',
		];
	}
	/**
	 * Get columns
	 * @return array
	 */
	protected function columns()
	{
		return [
			MigrationEntity::C_ID			 => $this->column()->int('T.ID'),
			MigrationEntity::C_PACKAGE		 => $this->column()->string('T.PACKAGE'),
			MigrationEntity::C_SERVICE		 => $this->column()->string('T.SERVICE'),
			MigrationEntity::C_FUNCTION		 => $this->column()->string('T.FUNCTION'),
			MigrationEntity::C_GUID			 => $this->column()->string('T.GUID'),
			MigrationEntity::C_TIMESTAMP_X	 => $this->column()->datetime('T.TIMESTAMP_X'),
		];
	}
}