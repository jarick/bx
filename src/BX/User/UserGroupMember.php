<?php namespace BX\User;
use BX\Config\DICService;
use BX\Error\Error;
use BX\User\UserGroupMemberManager;

/**
 * Класс для работы с привязками пользоватей к группам
 *
 * Доступные поля:
 * <ol>
 * <li>ID <b>INTEGER</b> ID привязки, автоматически генирируется БД</li>
 * <li>USER_ID <b>INTEGER</b> ID пользователя,обязательное поле</li>
 * <li>GROUP_ID <b>INTEGER</b> ID группы пользователей</li>
 * <li>TIMESTAMP_X <b>STRING</b> Дата и время последнего изменения, генирируется автоматически</li>
 * </ol>
 */
class UserGroupMember
{
	/**
	 * @var string
	 */
	private static $manager = 'user_group_member';
	/**
	 * Get manager
	 *
	 * @return UserGroupMemberManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new UserGroupMemberManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Добавление пользователя в группу
	 *
	 * @param integer $user_id ID пользователя
	 * @param integer $group_id ID группы
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function add($user_id,$group_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($user_id,$group_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление пользователя из группы
	 *
	 * @param integer $user_id ID пользователя
	 * @param integer $group_id ID группы
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function delete($user_id,$group_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->delete($user_id,$group_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление всех привязок пользователя к группам
	 *
	 * @param integer $user_id ID пользователя
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function deleteAllByUserId($user_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->deleteAllByUserId($user_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление всех привязок пользователей к группе
	 *
	 * @param integer $group_id ID группы
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function deleteAllByGroupId($group_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->deleteAllByGroupId($group_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Фильтр по привязки пользователей к группе
	 *
	 * @return SqlBuilder
	 */
	public static function finder()
	{
		return self::getManager()->finder();
	}
}