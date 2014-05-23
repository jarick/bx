<?php namespace BX\Captcha;
use BX\Config\DICService;

/**
 * Капча
 *
 * Класс для работы с капчей
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Captcha
{
	/**
	 * @var string
	 */
	private static $manager = 'captcha';
	/**
	 * Get manager
	 *
	 * @return CaptchaManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new CaptchaManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Получение уникального индификатора для капчи
	 *
	 * @return false|string <p>Возвращает строку с инфификатором в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function getGuid()
	{
		Error::reset();
		try{
			$return = self::getManager()->getGuid();
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Получение кода капчи по его индификатору
	 * @param string $guid Индификатор капчи, для его получения нужно вызвать функцию
	 * <b>Captcha::getGuid</b>
	 * @return false|null|string <p>Возвращает строку с кодом в случае успеха.
	 * </p>
	 * <p>
	 * Если капча не найдена возвращает <b>NULL</b>
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function getCode($guid)
	{
		Error::reset();
		try{
			$return = self::getManager()->getCode($guid);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Проверяет наличие капчи по переданному индификатору и коду капчи.
	 *
	 * @param string $guid Индификатор капчи, для его получения нужно вызвать функцию
	 * <b>Captcha::getGuid</b>
	 * @param string $code Код капчи, выводится на на картинку капчи
	 * @return boolean <p>Возвращает результат проверки капчи.
	 * </p>
	 * <p>
	 * Возвращает <b>NULL</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function check($guid,$code)
	{
		Error::reset();
		try{
			$return = self::getManager()->check($guid,$code);
		}catch (Exception $ex){
			Error::set($ex);
			$return = null;
		}
		return $return;
	}
	/**
	 * Перезагружает код капчи
	 *
	 * @param string $guid Индификатор капчи, для его получения нужно вызвать функцию
	 * <b>Captcha::getGuid</b>
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function reload($guid)
	{
		Error::reset();
		try{
			$return = self::getManager()->reload($guid);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаляет капчу с переданым символьным кодом
	 *
	 * @param string $guid Индификатор капчи, для его получения нужно вызвать функцию
	 * <b>Captcha::getGuid</b>
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 */
	public static function clear($guid)
	{
		Error::reset();
		try{
			$return = self::getManager()->clear($guid);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаляет старые капчи
	 *
	 * @param integer $day Через сколько дней считать капчу устаревшей
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
}