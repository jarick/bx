<?php namespace BX\User;
use BX\DB\Helper\TableColumn;
use BX\User\Entity\UserEntity;
use BX\User\Entity\AuthEntity;

class Migration
{
	use \BX\DB\DBTrait;
	const USER_TABLE = 'tbl_user';
	const AUTH_TABLE = 'tbl_auth';
	/**
	 * @root
	 */
	public function upUserTable($up)
	{
		if ($up){
			$this->db()->createTable(self::USER_TABLE,[
				TableColumn::getPK(UserEntity::C_ID),
				TableColumn::getString(UserEntity::C_LOGIN,50),
				TableColumn::getString(UserEntity::C_PASSWORD,255),
				TableColumn::getString(UserEntity::C_EMAIL,50),
				TableColumn::getString(UserEntity::C_CODE,50),
				TableColumn::getTimestamp(UserEntity::C_CREATE_DATE),
				TableColumn::getTimestamp(UserEntity::C_TIMESTAMP_X),
				TableColumn::getString(UserEntity::C_URL,255),
				TableColumn::getBoolean(UserEntity::C_REGISTERED),
				TableColumn::getString(UserEntity::C_ACTIVATION_KEY,255),
				TableColumn::getBoolean(UserEntity::C_ACTIVE),
				TableColumn::getString(UserEntity::C_DISPLAY_NAME,100),
			]);
		} else{
			$this->db()->dropTable(self::USER_TABLE);
		}
	}
	/**
	 * @parent upUserTable
	 * @param boolean $up
	 */
	public function upAuthTable($up)
	{
		if ($up){
			$this->db()->createTable(self::AUTH_TABLE,[
				TableColumn::getPK(AuthEntity::C_ID),
				TableColumn::getString(AuthEntity::C_UNIQUE_ID,100),
				TableColumn::getString(AuthEntity::C_ACCESS_TOKEN,100),
				TableColumn::getInteger(AuthEntity::C_USER_ID),
				TableColumn::getTimestamp(AuthEntity::C_CREATE_DATE),
				TableColumn::getTimestamp(AuthEntity::C_TIMESTAMP_X),
				TableColumn::getInteger(AuthEntity::C_EXPIRE),
			]);
		} else{
			$this->db()->dropTable(self::AUTH_TABLE);
		}
	}
}