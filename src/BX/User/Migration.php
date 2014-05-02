<?php namespace BX\User;
use BX\DB\Helper\TableColumn;
use BX\User\Entity\AccessEntity;
use BX\User\Entity\UserEntity;
use BX\User\Entity\UserGroupEntity;
use BX\User\Entity\UserGroupMemberEntity;

class Migration
{
	use \BX\DB\DBTrait;
	const USER_TABLE = 'tbl_user';
	const AUTH_TABLE = 'tbl_auth';
	const REMEMBER_PASSWORD_TABLE = 'tbl_remember_password';
	const CONFIRM_REGISTRATION_TABLE = 'tbl_confirm_registration';
	const GROUP_TABLE = 'tbl_user_group';
	const GROUP_MEMBER_TABLE = 'tbl_user_group_member';
	/**
	 * @root
	 */
	public function upUserTable($up)
	{
		if ($up){
			$this->db()->createTable(self::USER_TABLE,[
				TableColumn::getPK(UserEntity::C_ID),
				TableColumn::getString(UserEntity::C_GUID,100),
				TableColumn::getString(UserEntity::C_LOGIN,100),
				TableColumn::getString(UserEntity::C_PASSWORD,100),
				TableColumn::getString(UserEntity::C_EMAIL,100),
				TableColumn::getString(UserEntity::C_CODE,50),
				TableColumn::getTimestamp(UserEntity::C_CREATE_DATE),
				TableColumn::getTimestamp(UserEntity::C_TIMESTAMP_X),
				TableColumn::getString(UserEntity::C_DISPLAY_NAME,100),
				TableColumn::getBoolean(UserEntity::C_REGISTERED),
				TableColumn::getBoolean(UserEntity::C_ACTIVE),
			]);
		}else{
			$this->db()->dropTable(self::USER_TABLE);
		}
	}
	/**
	 * @parent upUserTable
	 */
	public function upAuthTable($up)
	{
		if ($up){
			$this->db()->createTable(self::AUTH_TABLE,[
				TableColumn::getPK(AccessEntity::C_ID),
				TableColumn::getInteger(AccessEntity::C_USER_ID),
				TableColumn::getString(AccessEntity::C_GUID,100),
				TableColumn::getString(AccessEntity::C_TOKEN,100),
				TableColumn::getTimestamp(AccessEntity::C_TIMESTAMP_X),
			]);
		}else{
			$this->db()->dropTable(self::AUTH_TABLE);
		}
	}
	/**
	 * @parent upUserTable
	 */
	public function upRememberTable($up)
	{
		if ($up){
			$this->db()->createTable(self::REMEMBER_PASSWORD_TABLE,[
				TableColumn::getPK(AccessEntity::C_ID),
				TableColumn::getInteger(AccessEntity::C_USER_ID),
				TableColumn::getString(AccessEntity::C_GUID,100),
				TableColumn::getString(AccessEntity::C_TOKEN,100),
				TableColumn::getTimestamp(AccessEntity::C_TIMESTAMP_X),
			]);
		}else{
			$this->db()->dropTable(self::REMEMBER_PASSWORD_TABLE);
		}
	}
	/**
	 * @parent upUserTable
	 */
	public function upConfirmTable($up)
	{
		if ($up){
			$this->db()->createTable(self::CONFIRM_REGISTRATION_TABLE,[
				TableColumn::getPK(AccessEntity::C_ID),
				TableColumn::getInteger(AccessEntity::C_USER_ID),
				TableColumn::getString(AccessEntity::C_GUID,100),
				TableColumn::getString(AccessEntity::C_TOKEN,100),
				TableColumn::getTimestamp(AccessEntity::C_TIMESTAMP_X),
			]);
		}else{
			$this->db()->dropTable(self::CONFIRM_REGISTRATION_TABLE);
		}
	}
	/**
	 * @parent upUserTable
	 */
	public function upGroupTable($up)
	{
		if ($up){
			$this->db()->createTable(self::GROUP_TABLE,[
				TableColumn::getPK(UserGroupEntity::C_ID),
				TableColumn::getString(UserGroupEntity::C_GUID,100),
				TableColumn::getBoolean(UserGroupEntity::C_ACTIVE),
				TableColumn::getTimestamp(UserGroupEntity::C_TIMESTAMP_X),
				TableColumn::getString(UserGroupEntity::C_NAME,255),
				TableColumn::getText(UserGroupEntity::C_DESCRIPTION),
				TableColumn::getInteger(UserGroupEntity::C_SORT),
			]);
		}else{
			$this->db()->dropTable(self::GROUP_TABLE);
		}
	}
	/**
	 * @parent upGroupTable
	 */
	public function upMemberTable($up)
	{
		if ($up){
			$this->db()->createTable(self::GROUP_MEMBER_TABLE,[
				TableColumn::getPK(UserGroupMemberEntity::C_ID),
				TableColumn::getInteger(UserGroupMemberEntity::C_GROUP_ID),
				TableColumn::getInteger(UserGroupMemberEntity::C_USER_ID),
				TableColumn::getTimestamp(UserGroupMemberEntity::C_TIMESTAMP_X),
			]);
		}else{
			$this->db()->dropTable(self::GROUP_MEMBER_TABLE);
		}
	}
}