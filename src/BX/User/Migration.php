<?php namespace BX\User;
use BX\DB\Helper\TableColumn;
use BX\User\Entity\UserEntity;

class Migration
{
	use \BX\DB\DBTrait;
	const TABLE = 'tbl_user';
	/**
	 * @root
	 */
	public function upCreateTable($up)
	{
		if ($up){
			$this->db()->createTable(self::TABLE,[
				TableColumn::getPK(UserEntity::C_ID)->toArray(),
				TableColumn::getString(UserEntity::C_LOGIN,50)->toArray(),
				TableColumn::getString(UserEntity::C_PASSWORD,255)->toArray(),
				TableColumn::getString(UserEntity::C_EMAIL,50)->toArray(),
				TableColumn::getString(UserEntity::C_CODE,50)->toArray(),
				TableColumn::getTimestamp(UserEntity::C_CREATE_DATE)->toArray(),
				TableColumn::getTimestamp(UserEntity::C_TIMESTAMP_X)->toArray(),
				TableColumn::getString(UserEntity::C_URL,255)->toArray(),
				TableColumn::getBoolean(UserEntity::C_REGISTERED)->toArray(),
				TableColumn::getString(UserEntity::C_ACTIVATION_KEY,255)->toArray(),
				TableColumn::getBoolean(UserEntity::C_ACTIVE)->toArray(),
				TableColumn::getString(UserEntity::C_DISPLAY_NAME,100)->toArray(),
			]);
		} else{
			$this->db()->dropTable(self::TABLE);
		}
	}
}