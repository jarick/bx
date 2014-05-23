<?php namespace BX\User;
use BX\Config\DICService;
use BX\User\RememberPasswordManager;
use BX\Error\Error;

/**
 * Восстановление пароля
 *
 * Получение токена:
 * <code>
 *  $token = RememberPassword::getToken($user->id,$user->guid);
 * </code>
 * Пользователю для подтверждения смены пароля передается его GUID и сгенировааный токен.
 * Проверка токена:
 * <code>
 * 	return RememberPassword::check($guid,$token);
 * </code>
 */
class RememberPassword
{
	/**
	 * @var string
	 */
	private static $manager = 'remember';
	/**
	 * Get manager
	 *
	 * @return RememberPasswordManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new RememberPasswordManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Получение токена
	 *
	 * @param integer $user_id ID пользователя
	 * @param string $user_guid GUID пользователя
	 * @param string $token Токен, по-умолчанию токен можно не передавать,
	 *  тогда функция сама сгенирирует случайный токен.
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function getToken($user_id,$user_guid,$token = null)
	{
		Error::reset();
		try{
			$return = self::getManager()->getToken($user_id,$user_guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Проверка токена
	 *
	 * @param string $guid GUID пользователя
	 * @param string $token Токен
	 * @return null|false|integer <p>Возвращает ID пользователя в случае если токен был найден.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае если токен не был найден
	 * </p>
	 * <p>
	 * Возвращает <b>NULL</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function check($guid,$token)
	{
		Error::reset();
		try{
			$return = self::getManager()->check($guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = null;
		}
		return $return;
	}
	/**
	 * Удаление запроса смены пароля для пользователя
	 *
	 * @param integer $user_id ID пользователя
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clear($user_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->clear($user_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление старых запросов смены пароля
	 *
	 * @param integer $day Через сколько дней считать запрос просроченным
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clearOld($day = 3)
	{
		Error::reset();
		try{
			$return = self::getManager()->clearOld($day);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
}

/**
 * Подтверждение регистрации
 *
 * Получение токена:
 * <code>
 *  $token = ConfirmRegistration::getToken($user->id,$user->guid);
 * </code>
 * Пользователю для подтверждения регистрации передается его GUID и сгенировааный токен.
 * Проверка токена:
 * <code>
 * 	return ConfirmRegistration::check($guid,$token);
 * </code>
 */
class ConfirmRegistration
{
	/**
	 * @var string
	 */
	private static $manager = 'confirm';
	/**
	 * Get manager
	 *
	 * @return RememberPasswordManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new RememberPasswordManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Получение токена
	 *
	 * @param integer $user_id ID пользователя
	 * @param string $user_guid GUID пользователя
	 * @param string $token Токен, по-умолчанию токен можно не передавать,
	 *  тогда функция сама сгенирирует случайный токен.
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function getToken($user_id,$user_guid,$token = null)
	{
		Error::reset();
		try{
			$return = self::getManager()->getToken($user_id,$user_guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Проверка токена
	 *
	 * @param string $guid GUID пользователя
	 * @param string $token Токен
	 * @return null|false|integer <p>Возвращает ID пользователя в случае если токен был найден.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае если токен не был найден
	 * </p>
	 * <p>
	 * Возвращает <b>NULL</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function check($guid,$token)
	{
		Error::reset();
		try{
			$return = self::getManager()->check($guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = null;
		}
		return $return;
	}
	/**
	 * Удаление запроса подтверждения регистрации для пользователя
	 *
	 * @param integer $user_id ID пользователя
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clear($user_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->clear($user_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление старых запросов подтверждения регистрации
	 *
	 * @param integer $day Через сколько дней считать запрос просроченным
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clearOld($day = 3)
	{
		Error::reset();
		try{
			$return = self::getManager()->clearOld($day);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
}