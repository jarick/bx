<?php
namespace BX\Migration;
use BX\DB\ActiveRecord;
use BX\DB\Helper\TableColumn;
use BX\Migration\Entity\Migration as MigrationEntity;

/*
BX\Migration\Manager\Migrate::getManager(false,[
    'package' => 'BX',
    'service'=>'migration',
])->up();
*/

class Migration
{
	const MIGRATE_TABLE = 'tbl_migrate';
	/**
	 * @root
	 */
	public function up($bUp)
	{
		if($bUp){
			$entity = MigrationEntity::getEntity();
			$entity->db()->createTable(self::MIGRATE_TABLE, [
				TableColumn::getPK('ID')->toArray(),
				TableColumn::getString(MigrationEntity::C_PACKAGE,100)->setNotNull()->toArray(),
				TableColumn::getString(MigrationEntity::C_SERVICE, 100)->setNotNull()->toArray(),
				TableColumn::getString(MigrationEntity::C_FUNCTION, 100)->setNotNull()->toArray(),
				TableColumn::getString(MigrationEntity::C_GUID, 30)->setNotNull()->toArray(),
				TableColumn::getTimestamp(MigrationEntity::C_TIMESTAMP_X)->setNotNull()->toArray(),
			]);
		}else{
			ActiveRecord::getDB()->dropTable(self::MIGRATE_TABLE);
		}
	}
}