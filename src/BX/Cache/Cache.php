<?php namespace BX\Cache;
use BX\Error\Error;
use BX\Config\DICService;

/**
 * Кеш
 *
 * Класс для работы с кешированием
 *
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Cache
{
	/**
	 * @var string
	 */
	private static $manager = 'cache';
	/**
	 * Return error manager
	 *
	 * @return ICacheManger
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new CacheManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Включение кеширования
	 *
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function enable()
	{
		try{
			Error::reset();
			return self::getManager()->enable();
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Выключение кеширования
	 *
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function disable()
	{
		try{
			Error::reset();
			return self::getManager()->disable();
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Добавление к директории с кешем тегов кеширования
	 *
	 * @param string $ns Директория с кешем
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function setTags($ns)
	{
		try{
			$tags = func_get_args();
			unset($tags[0]);
			Error::reset();
			return self::getManager()->setTags($ns,$tags);
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Возвращает значение из кеша
	 *
	 * @param string $unique_id Уникальный индефикатор кеша
	 * @param string $ns Директория с кешем
	 * @return string|false <p>Возвращает строку со значением из кеша.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае возникновения ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 * <p>
	 * <b>NULL</b> Либо если значение в кеше не найдено
	 * </p>
	 */
	public static function get($unique_id,$ns = 'base')
	{
		try{
			Error::reset();
			return self::getManager()->get($unique_id,$ns);
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	public static function set($unique_id,$value,$ns = 'base',$ttl = 3600,$tags = [])
	{
		try{
			Error::reset();
			return self::getManager()->set($unique_id,$value,$ns,$ttl,$tags);
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	public static function remove($unique_id,$ns = 'base')
	{
		try{
			Error::reset();
			return self::getManager()->remove($unique_id,$ns);
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Уделение всего кеша из директории <strong>ns</strong>
	 *
	 * @param $ns Директория с кешем
	 * @return boolean
	 */
	public static function removeByNamespace($ns)
	{
		try{
			Error::reset();
			return self::getManager()->removeByNamespace($ns);
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Уделение всего кеша, помеченным введенными тегами
	 *
	 * @return boolean
	 */
	public static function clearByTags()
	{
		try{
			Error::reset();
			return self::getManager()->clearByTags(func_get_args());
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Сброс всего кеша
	 *
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function flush()
	{
		try{
			Error::reset();
			return self::getManager()->flush();
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
}