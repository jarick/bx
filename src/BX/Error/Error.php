<?php namespace BX\Error;
use BX\Config\DICService;

/**
 * Контейнер для ошибок
 *
 * Хранит в себе текущию возникшию ошибку
 * <code>
 * 	Error::reset();
 * 	try{
 * 		$return = self::getManager()->inc($entity,$entity_id);
 * 	}catch (Exception $ex){
 * 		Error::set($ex);
 * 		$return = false;
 * 	}
 * 	return $return;
 * </code>
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Error
{
	/**
	 * Return error manager
	 *
	 * @return BX\Error\ErrorManager
	 */
	private static function getManager()
	{
		$name = 'error';
		if (DICService::get($name) === null){
			$manager = function(){
				return new ErrorManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
	/**
	 * Сохраняет и логирует определенную пользователем ошибоку.
	 *
	 * @param Exception $entity <p>
	 * Класс сущности к которой привязан счетчик.
	 * </p>
	 * @param Exception $ex <p>
	 * Ошибка
	 * </p>
	 * @param string $component <p>
	 * Name of the logging channel
	 * </p>
	 */
	public static function set(\Exception $ex,$component = 'default')
	{
		return self::getManager()->set($ex,$component);
	}
	/**
	 * Обнуляет текущию ошибку
	 *
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае возникновения ошибки.
	 * </p>
	 */
	public static function reset()
	{
		return self::getManager()->reset();
	}
	/**
	 * Возвращает текущию ошибку
	 *
	 * @return Exception
	 */
	public static function get()
	{
		return self::getManager()->get();
	}
}