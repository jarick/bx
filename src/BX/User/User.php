<?php namespace BX\User;
use BX\Config\DICService;
use BX\User\UserManager;
use BX\Error\Error;

/**
 * Пользователи
 *
 * Класс для работы с пользователями. Поля полльзователя:
 * <ol>
 * <li>ID <b>INTEGER</b> ID пользователя, автоматически назначается БД</li>
 * <li>GUID <b>STRING</b> GUID пользователя,генирируется автоматически</li>
 * <li>LOGIN <b>STRING</b> логин, обязательное поле должен быть уникальным</li>
 * <li>PASSWORD <b>STRING</b> генирируется автоматически, хранит хеш пароля</li>
 * <li>EMAIL <b>STRING</b> e-mail, обязательное поле должен быть уникальным</li>
 * <li>CREATE_DATE <b>STRING</b> дата создания, генирируется автоматически</li>
 * <li>TIMESTAMP_X <b>STRING</b> дата изменения, генирируется автоматически</li>
 * <li>REGISTERED <b>BOOLEAN</b> зарегистрирован ли пользователь</li>
 * <li>ACTIVE <b>BOOLEAN</b> активность пользователя</li>
 * <li>CODE <b>STRING</b> символьный код полльзователя, генерируется автоматически путем транслитерации логина</li>
 * <li>DISPLAY_NAME <b>STRING</b> Имя пользователя на сайте, не обязательное</li>
 * </ol>
 */
class User
{
	/**
	 * @var string
	 */
	private static $manager = 'user';
	/**
	 * Get manager
	 *
	 * @return UserManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new UserManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Добавление пользователя.
	 *
	 * @param array $user Массиы значения полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function add(array $user)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($user);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Изменение пользователя.
	 *
	 * @param integer $id ID пользователя
	 * @param array $user Массиы значения полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function update($id,array $user)
	{
		Error::reset();
		try{
			$return = self::getManager()->update($id,$user);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление пользователя.
	 *
	 * @param integer $id ID пользователя
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
	 * Фильтр по пользователям
	 *
	 * @return SqlBuilder
	 */
	public static function finder()
	{
		return self::getManager()->getFinder();
	}
}