<?php namespace BX\Date;
use BX\Error\Error;
use BX\Config\DICService;

/**
 * Дата\время
 *
 * Хелпер для работы с датами и временем
 * <p>Текущая временная зона берется из текущий конфигурации по ключу <i>['counter','day']</i>
 * (см. функцию <b>BX\Config\Config</b>).<br />Если такого ключа, то берется значение функции date_default_timezone_get</p>
 * @link http://php.net/manual/en/function.date-default-timezone-get.php
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Date
{
	/**
	 * Get manager
	 *
	 * @return DateTimeManager
	 */
	private static function getManager()
	{
		$name = 'date';
		if (DICService::get($name) === null){
			$manager = function(){
				return new DateTimeManager();
			};
			DICService::set($name,$manager);
		}
		return DICService::get($name);
	}
	/**
	 * Включение работы с времеными зонами
	 *
	 * @return boolean  Возвращает <b>TRUE</b> в случае успеха.
	 */
	public static function activeTimeZone()
	{
		try{
			Error::reset();
			return self::getManager()->activeTimeZone();
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Включение работы с времеными зонами
	 *
	 * @return boolean  Возвращает <b>TRUE</b> в случае успеха.
	 */
	public static function disableTimeZone()
	{
		try{
			Error::reset();
			return self::getManager()->disableTimeZone();
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Проверка что переданная строка является датой.
	 *
	 * @param string $datetime Cтрока с датой и временем
	 * @param string $format <p>
	 * Формат даты и времени.<br>
	 * Если значение не задано, оно берется из заданной конфигурации <i>['date','full']</i>
	 * (см. функцию <b>BX\Config\Config</b>) по ключу.
	 * Если же такого ключа нет, то берется значение <i>'d.m.Y H:i:s'</i>.
	 * </p>
	 * @return boolean
	 */
	public static function checkDateTime($datetime,$format = 'full')
	{
		try{
			Error::reset();
			return self::getManager()->checkDateTime($datetime,$format);
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Конвертация строки с датой и временем в <b>timestamp</b>
	 *
	 * При конвертации учитывается часовой пояс
	 * @param string $datetime Cтрока с датой и временем
	 * @param string $format <p>
	 * Формат даты и времени.<br>
	 * Если значение не задано, оно берется из заданной конфигурации <i>['date','full']</i>
	 * (см. функцию <b>BX\Config\Config</b>) по ключу.
	 * Если же такого ключа нет, то берется значение <i>'d.m.Y H:i:s'</i>.
	 * </p>
	 * @return integer
	 */
	public static function makeTimeStamp($datetime,$format = 'full')
	{
		try{
			Error::reset();
			return self::getManager()->makeTimeStamp($datetime,$format);
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Конвертация <b>timestamp</b> в строку с датой и веременем
	 *
	 * При конвертации учитывается часовой пояс
	 * @param integer $timestamp <p>
	 * <b>timestamp</b><br/>
	 * Если ничего не передано, то берется значение функции <b>time()</b>
	 * @link http://php.net/manual/en/function.time.php
	 * </p>
	 * @param string $format <p>
	 * Формат даты и времени.<br>
	 * Если значение не задано, оно берется из заданной конфигурации <i>['date','full']</i>
	 * (см. функцию <b>BX\Config\Config</b>) по ключу.
	 * Если же такого ключа нет, то берется значение <i>'d.m.Y H:i:s'</i>.
	 * </p>
	 * @return string
	 */
	public static function convertTimeStamp($timestamp = false,$format = 'full')
	{
		try{
			Error::reset();
			return self::getManager()->convertTimeStamp($timestamp,$format);
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Смещение часового пояска
	 * @link http://php.net/manual/en/function.date.php
	 * @return integer Возвращает результат <b>date('Z')</b>
	 */
	public static function getOffset()
	{
		try{
			Error::reset();
			return self::getManager()->getOffset();
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
	/**
	 * Возвращает текущее время в формате gmt
	 *
	 * @return integer
	 */
	public static function getUtc()
	{
		try{
			Error::reset();
			return self::getManager()->getUtc();
		}catch (Exception $ex){
			Error::set($ex);
			return false;
		}
	}
}