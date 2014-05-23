<?php namespace BX\Event;
use BX\Config\DICService;

/**
 * События
 *
 * Класс для работы со событиями
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Event
{
	/**
	 * @var string
	 */
	private static $manager = 'event';
	/**
	 * Get manager
	 *
	 * @return IEventManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new EventManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Вызвать обработчик события
	 *
	 * @param string $name Название события
	 * @param array $params Параметры
	 * @param boolean $halt Прерывать ли цикл вызовов обработчиков,
	 * если какой-то из них вернул не пустое значение. По-умолчанию равен <b>FALSE</b>
	 * @return false|null|array<p>
	 * Возвращает массив значений, которые вернули обработчики.<br>
	 * </p><p>
	 * Либо <b>NULL</b>, если ни один обработчик не был найден.<br>
	 * </p><p>
	 * * Либо <b>FALSE</b>, если произошла ошибка. Cаму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function fire($name,array $params = [],$halt = false)
	{
		Error::reset();
		try{
			$return = self::getManager()->fire($name,$params,$halt);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Зарегистрировать обработчик события
	 *
	 * @param string $name  Название события
	 * @param mixed $func Обработчик.
	 * @param type $sort Индекс сортировки
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function on($name,$func,$sort = 500)
	{
		Error::reset();
		try{
			self::getManager()->on($name,$func,$sort);
			$return = true;
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
}