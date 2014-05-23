<?php namespace BX\Counter;
use BX\Config\DICService;
use BX\Error\Error;

/**
 * Счетчик
 *
 * Класс для работы со счетчиком событий.
 *
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Counter
{
	/**
	 * @var string
	 */
	private static $manager = 'counter';
	/**
	 * Get counter manager
	 *
	 * @return CounterManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new CounterManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Увеличивает счетчик на один.
	 *
	 * @param string $entity <p>
	 * Класс сущности к которой привязан счетчик.
	 * </p>
	 * @param string $entity_id <p>
	 * Уникальный индификатор сущности. Пара значений <b>$entity</b> - <b>$entity_id</b>
	 * должна быть уникальна для счетчика.
	 * </p>
	 * @return false|integer <p>Возвращает счетчик(<i>число</i>) увеличенный на один.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки саму ошибку можно получить с помощью функции <b>Error::get</b>
	 * </p>
	 */
	public static function inc($entity,$entity_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->inc($entity,$entity_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Возвращает текущий счетчик.
	 *
	 * @param string $entity <p>
	 * Класс сущности к которой привязан счетчик.
	 * </p>
	 * @param string $entity_id <p>
	 * Уникальный индификатор сущности. Пара значений <b>$entity</b> - <b>$entity_id</b>
	 * должна быть уникальна для счетчика.
	 * </p>
	 * @return false|integer <p>Возвращает счетчик(<i>число</i>).
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки саму ошибку можно получить с помощью функции <b>Error::get</b>
	 * </p>
	 */
	public static function get($entity,$entity_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->get($entity,$entity_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Обнуляет текущий счетчик.
	 *
	 * @param string $entity <p>
	 * Класс сущности к которой привязан счетчик.
	 * </p>
	 * @param string $entity_id <p>
	 * Уникальный индификатор сущности. Пара значений <b>$entity</b> - <b>$entity_id</b>
	 * должна быть уникальна для счетчика.
	 * </p>
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clear($entity,$entity_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->clear($entity,$entity_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаляет старые счетчики
	 *
	 * @param integer $day <p>
	 * Через сколько дней считать счетчик устаревшим.<br>
	 * Если значение не задано, оно берется из текущий конфигурации по ключу <i>['counter','day']</i>
	 * (см. функцию <b>BX\Config\Config</b>).
	 * Если же такого ключа нет, то берется значение в 30 дней.
	 * </p>
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Cаму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clearOld($day = null)
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