<?php namespace BX\User;
use BX\Config\DICService;
use BX\User\AuthManager;
use BX\Error\Error;

/**
 * Авторизация пользователя
 *
 * Класс для работы с авторизацией
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Auth
{
	/**
	 * @var string
	 */
	private static $manager = 'auth';
	/**
	 * Get manager
	 *
	 * @return AuthManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new AuthManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Проверяет есть ли авторизация у пользователя
	 *
	 * Сначала функция пытается найти авторизацию в сессии.
	 * Если в сессии ничего нет, то авторизация ищется авторизация по переданным параметрам в БД.
	 * @param string $guid Если параметр не задан, то значение параметра будет взято из cookie
	 * @param string $token Если параметр не задан, то значение параметра будет взято из cookie
	 * @return boolean|null <p>Возвращает <b>TRUE</b> в случае если авторизация найдена.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае если авторизация найдена.
	 * </p><p>
	 * Возвращает <b>NULL</b> в случае возникновения ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function login($guid = null,$token = null)
	{
		Error::reset();
		try{
			$return = self::getManager()->login($guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Возвращает авторизоционный токен
	 *
	 * Сначала функция пытается найти авторизацию в сессии.
	 * Если в сессии ничего нет, то авторизация ищется авторизация по переданным параметрам в БД.
	 * @param string $guid Если параметр не задан, то значение параметра будет взято из cookie
	 * @return string|null <p>Возвращает строку в случае, если авторизация найдена.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае возникновения ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function getToken($guid = null)
	{
		Error::reset();
		try{
			$return = self::getManager()->getToken($guid);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Добавление авторизации
	 *
	 * @param integer $user_id ID пользователя.
	 * @param string $user_guid GUID пользователя.
	 * @param string $token Если параметр не задан, то значение параметра будет взято из cookie
	 * @param boolean $http Сохранять ли авторизационный ключ в куках пользователя.
	 * По-умолчанию ключ сохраняется.
	 * @return string|null <p>Возвращает строку с индификатором авторизации
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае возникновения ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function add($user_id,$user_guid,$token = null,$http = true)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($user_id,$user_guid,$token,$http);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление авторизации.
	 *
	 * @param string $guid Если параметр не задан, то значение параметра будет взято из cookie
	 * @param string $token Если параметр не задан, то значение параметра будет взято из cookie
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function logout($guid = null,$token = null)
	{
		Error::reset();
		try{
			$return = self::getManager()->logout($guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаляет старые авторизации
	 *
	 * @param integer $day Через сколько дней считать авторизацию устаревшей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clearOld($day = 30)
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
	/**
	 * Возвращает ID текущего пользователя
	 *
	 * @param string $guid Если параметр не задан, то значение параметра будет взято из cookie
	 * @param string $token Если параметр не задан, то значение параметра будет взято из cookie
	 * @return integer <p>Возвращает число в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function getId($guid = null,$token = null)
	{
		Error::reset();
		try{
			$session = self::getManager()->getSession($guid,$token);
			if ($session === null){
				return false;
			}else{
				return $session['ID'];
			}
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Возвращает GUID текущего пользователя
	 *
	 * @param string $guid Если параметр не задан, то значение параметра будет взято из cookie
	 * @param string $token Если параметр не задан, то значение параметра будет взято из cookie
	 * @return string <p>Возвращает строку в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function getGuid($guid = null,$token = null)
	{
		Error::reset();
		try{
			$session = self::getManager()->getSession($guid,$token);
			if ($session === null){
				return false;
			}else{
				return $session['GUID'];
			}
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
}