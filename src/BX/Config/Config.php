<?php namespace BX\Config;
use BX\Error\Error;

/**
 * Конфигурация
 *
 * Класс для работы с конфигурацией сайта
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Config
{
	/**
	 * @var string
	 */
	private static $manager = 'config';
	/**
	 * Return error manager
	 *
	 * @return \BX\Config\IConfigManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new ConfigManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Инициализацтя конфигурации
	 *
	 * @param string $format Возможнные параметры:
	 * <ul>
	 * 	<li><i>yaml_file</i> в параметре <b>format</b> нужно передать путь к yml файлу,
	 * с данными</li>
	 *  <li><i>yaml</i> в параметре <b>format</b> нужно передать строку с данными в формате yml</li>
	 *  <li><i>array</i> в параметре <b>format</b> нужно передать массив с данными</li>
	 *  <li><i>array_file</i> в параметре <b>format</b> нужно передать путь к php файлу,
	 * который возращает данные</li>
	 * </ul>
	 * @param mixed $store Путь к файлу с конфигурации, либо массив с данными
	 * (формат зависит от значения <b>format</b>)
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Cаму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function init($format,$store)
	{
		try{
			Error::reset();
			return self::getManager()->init($format,$store);
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Поиск ключа
	 *
	 * @return mixed
	 */
	public static function exists()
	{
		try{
			Error::reset();
			return self::getManager()->exists(func_get_args());
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Получение конфигурации
	 *
	 * @return mixed
	 */
	public static function get()
	{
		try{
			Error::reset();
			return self::getManager()->get(func_get_args());
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Получить всю конфигурацию
	 *
	 * @return array
	 */
	public static function all()
	{
		try{
			Error::reset();
			return self::getManager()->all();
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
}