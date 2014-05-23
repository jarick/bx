<?php namespace BX\String;
use BX\Config\DICService;
use BX\Error\Error;

/**
 * Хелпер для работы со строками
 *
 * Для корректной работы функции класса не в utf-8 установке нужно указать кодировку сайта.
 * В конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Config\Config</b>).
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Str
{
	/**
	 * @var string
	 */
	private static $manager = 'string';
	/**
	 * Get manager
	 *
	 * @return StringManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = function(){
				return new StringManager();
			};
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Получение случайной строки
	 *
	 * Строка состоит из заглавных латинских букв и арабских цифр
	 * Для корректной работы функции не в utf-8 установке нужно указать кодировку сайта.
	 * В конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Config\Config</b>).
	 * @param integer $length Длина строки
	 * @return string
	 */
	public static function getRandString($length)
	{
		Error::reset();
		try{
			$return = self::getManager()->getRandString($length);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Преобразование строки в верхний регистр
	 * @link http://php.net/manual/en/function.mb-strtoupper.php
	 * Для корректной работы функции не в utf-8 установке нужно указать кодировку
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Config\Config</b>).
	 * @param string $string Строка
	 * @return string
	 */
	public static function toUpper($string)
	{
		return self::getManager()->toUpper($string);
	}
	/**
	 * Преобразование строки в нижний регистр
	 * @link http://php.net/manual/en/function.mb-strtolower.php
	 * Для корректной работы функции не в utf-8 установке нужно указать кодировку
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Config\Config</b>).
	 * @param string $string Строка
	 * @return string
	 */
	public static function toLower($string)
	{
		return self::getManager()->toLower($string);
	}
	/**
	 * Экранирование специаьных символов в HTML строке
	 * @link  http://php.net/manual/en/function.htmlspecialchars.php
	 * Для корректной работы функции не в utf-8 установке нужно указать кодировку
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Config\Config</b>).
	 * @param string $string Строка
	 * @param integer $flags
	 * @return string
	 */
	public static function escape($string,$flags = ENT_COMPAT)
	{
		return self::getManager()->escape($string,$flags);
	}
	/**
	 * Получение длины строки
	 * @link http://php.net/manual/en/function.mb-strlen.php
	 * Для корректной работы функции не в utf-8 установке нужно указать кодировку
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Config\Config</b>).
	 * @param string $string Строка
	 * @return string
	 */
	public static function length($string)
	{
		return self::getManager()->length($string);
	}
	/**
	 * Перевод в верхний регистр заглавной буквы
	 * @link http://php.net/manual/en/function.mb-convert-case.php
	 * Для корректной работы функции не в utf-8 установке нужно указать кодировку
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Config\Config</b>).
	 * @param string $string Строка
	 * @return string
	 */
	public static function ucwords($string)
	{
		return self::getManager()->ucwords($string);
	}
	/**
	 * Подсчитывает, сколько раз подстрока <b>needle</b> встречается в строке <b>haystack</b>.
	 * @link http://www.php.net/manual/ru/function.mb-substr-count.php
	 * @param string $haystack
	 * @param string $needle
	 */
	public static function countSubstr($haystack,$needle)
	{
		return self::getManager()->countSubstr($haystack,$needle);
	}
	/**
	 * Поиск позиции первого вхождения одной строки в другую
	 * @link http://www.php.net/manual/ru/function.mb-strpos.php
	 * @param string $haystack Строка в которой производится поиск.
	 * @param string $needle Строка, поиск которой производится в строке haystack.
	 * @param integer $offset Смещение начала поиска. Если не задан, используется 0.
	 */
	public static function strpos($haystack,$needle,$offset = 0)
	{
		return self::getManager()->strpos($haystack,$needle,$offset);
	}
	/**
	 * Возвращает часть строки
	 * @link http://www.php.net/manual/ru/function.mb-substr.php
	 * @param string $str Исходная строка для получения подстроки.
	 * @param integer $start Позиция символа <b>str</b>, с которой выделяется подстрока.
	 * @param integer $length Максимальное количество символов возвращаемой подстроки.
	 */
	public static function substr($str,$start,$length = null)
	{
		return self::getManager()->substr($str,$start,$length);
	}
	/**
	 * Возвращает начинается ли строка <b>haystack</b> строкой <b>needle</b>
	 *
	 * @param string $haystack Базовая строка.
	 * @param string $needle Сравниваемая строка.
	 */
	public static function startsWith($haystack,$needle)
	{
		return self::getManager()->startsWith($haystack,$needle);
	}
	/**
	 * Возвращает заканчивается ли строка <b>haystack</b> строкой <b>needle</b>
	 *
	 * @param string $haystack Базовая строка.
	 * @param string $needle Сравниваемая строка.
	 */
	public static function endsWith($haystack,$needle)
	{
		return self::getManager()->endsWith($haystack,$needle);
	}
}