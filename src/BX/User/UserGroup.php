<?php namespace BX\User;
use BX\User\UserGroupManager;
use BX\Config\DICService;
use BX\Error\Error;

/**
 * Класс для работы с группами пользователей
 *
 * Доступные поля:
 * <ol>
 * <li>ID <b>INTEGER</b> ID группы, автоматически генирируется БД</li>
 * <li>GUID <b>STRING</b> GUID группы, генирируется автоматически</li>
 * <li>ACTIVE <b>STRING</b> Активность группы, по-умолчанию группа активна</li>
 * <li>TIMESTAMP_X <b>STRING</b> Дата и время последнего изменения, генирируется автоматически</li>
 * <li>NAME <b>STRING</b> Название группы, обязательное поле</li>
 * <li>DESCRIPTION <b>STRING</b> Описание группы</li>
 * <li>SORT <b>INTEGER</b> Индекс сортировки</li>
 * </ol>
 */
class UserGroup
{
	/**
	 * @var string
	 */
	private static $manager = 'user_group';
	/**
	 * Get manager
	 *
	 * @return UserGroupManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new UserGroupManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Добавление группы пользователей
	 *
	 * @param array $group Массив значение полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function add(array $group)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($group);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Изменение группы пользователей
	 *
	 * @param integer $id ID группы
	 * @param array $group Массив значение полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function update($id,array $group)
	{
		Error::reset();
		try{
			$return = self::getManager()->update($id,$group);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление группы пользователей
	 *
	 * @param integer $id ID группы
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function delete($id)
	{
		Error::reset();
		try{
			$return = self::getManager()->delete($id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Фильтр по группам пользователей
	 *
	 * @return SqlBuilder
	 */
	public static function finder()
	{
		return $this->finder();
	}
}