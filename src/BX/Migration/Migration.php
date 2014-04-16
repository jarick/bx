<?php namespace BX\Migration;
use BX\DB\Helper\TableColumn;
use BX\Migration\Entity\MigrationEntity;

class Migration
{
	use \BX\DB\DBTrait;
	const MIGRATE_TABLE = 'tbl_migrate';
	/**
	 * @root
	 */
	public function up($up)
	{
		if ($up){
			$this->db()->createTable(self::MIGRATE_TABLE,[
				TableColumn::getPK('ID'),
				TableColumn::getString(MigrationEntity::C_PACKAGE,100)->setNotNull(),
				TableColumn::getString(MigrationEntity::C_SERVICE,100)->setNotNull(),
				TableColumn::getString(MigrationEntity::C_FUNCTION,100)->setNotNull(),
				TableColumn::getString(MigrationEntity::C_GUID,30)->setNotNull(),
				TableColumn::getTimestamp(MigrationEntity::C_TIMESTAMP_X)->setNotNull(),
			]);
		}else{
			$this->db()->dropTable(self::MIGRATE_TABLE);
		}
	}
}