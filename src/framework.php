<?php
use BX\Base\DI;
use BX\Captcha\CaptchaManager;
use BX\Counter\CounterManager;
use BX\Date\DateTimeManager;
use BX\Event\EventManager;
use BX\FileSystem\FileSystemManager;
use BX\String\StringManager;

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
	 * @var Exception
	 */
	private static $ex = null;
	/**
	 * Сохраняет и логирует определенную пользователем ошибоку.
	 *
	 * @param Exception $entity <p>
	 * Класс сущности к которой привязан счетчик.
	 * </p>
	 * @param string $entity_id <p>
	 * Ошибка
	 * </p>
	 * @param string $component <p>
	 * Name of the logging channel
	 * </p>
	 */
	public static function set(Exception $ex,$component = 'default')
	{
		self::$ex = $ex;
		if (DI::get('logger') === null){
			DI::set('logger',new LoggerManager());
		}
		$trace = $ex->getTrace();
		$result = 'Exception: "';
		$result .= $ex->getMessage();
		$result .= '" @ ';
		if ($trace[0]['class'] !== ''){
			$result .= $trace[0]['class'];
			$result .= '->';
		}
		$result .= $trace[0]['function'];
		$result .= '();'.PHP_EOL;
		DI::get('logger')->get($component)->error($result);
	}
	/**
	 * Reset exception
	 */
	public static function reset()
	{
		self::$ex = null;
	}
	/**
	 * Return exception
	 * @return Exception
	 */
	public static function get()
	{
		return self::$ex;
	}
}

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
	 * Get manager
	 *
	 * @return CounterManager
	 */
	private static function getManager()
	{
		if (DI::get(self::$manager) === null){
			DI::set(self::$manager,new CounterManager());
		}
		return DI::get(self::$manager);
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
	 * (см. функцию <b>BX\Base\Registry</b>).
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

/**
 * Хелпер для работы со строками
 *
 * Для корректной работы функции класса не в utf-8 установке нужно указать кодировку сайта.
 * В конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Base\Registry</b>).
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
		if (DI::get(self::$manager) === null){
			DI::set(self::$manager,new StringManager());
		}
		return DI::get(self::$manager);
	}
	/**
	 * Получение случайной строки
	 *
	 * Строка состоит из заглавных латинских букв и арабских цифр
	 * Для корректной работы функции не в utf-8 установке нужно указать кодировку сайта.
	 * В конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Base\Registry</b>).
	 * @param integer $length Длина строки
	 * @return string
	 */
	public static function getRandString($length)
	{
		return self::getManager()->getRandString($length);
	}
	/**
	 * Преобразование строки в верхний регистр
	 * @link http://php.net/manual/en/function.mb-strtoupper.php
	 * Для корректной работы функции не в utf-8 установке нужно указать кодировку
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Base\Registry</b>).
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
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Base\Registry</b>).
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
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Base\Registry</b>).
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
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Base\Registry</b>).
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
	 * в конфигурации сайта ключ <i>['charset'] </i>(см. функцию <b>BX\Base\Registry</b>).
	 * @param string $string Строка
	 * @return string
	 */
	public static function ucwords($string)
	{
		return self::getManager()->ucwords($string);
	}
	/*
	  string Str::countSubstr($haystack,$needle)
	  int Str::strpos($haystack,$needle,$offset = 0)
	  string Str::substr($str,$start,$length = null)
	  string Str::startsWith($haystack,$needle)
	  string Str::endsWith($haystack,$needle)
	 *
	 */
}

/**
 * Файловая система
 *
 * Хелпер для работы с файловой системой
 *
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class FileSystem
{
	/**
	 * @var string
	 */
	private static $manager = 'filesystem';
	/**
	 * Get manager
	 *
	 * @return FileSystemManager
	 */
	private static function getManager()
	{
		if (DI::get(self::$manager) === null){
			DI::set(self::$manager,new FileSystemManager());
		}
		return DI::get(self::$manager);
	}
	/**
	 * Рекурсивное удаление папки
	 * @param string $path путь к папке
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки, саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function removePathDir($path)
	{
		Error::reset();
		try{
			$return = self::getManager()->removePathDir($path);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Рекурсивное создание папок
	 *
	 * Проверяется наличие папок по задоному пути и если на пути они не найдены, то функция их создает
	 * @param string $path путь к папке
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки, саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function checkPathDir($path)
	{
		Error::reset();
		try{
			$return = self::getManager()->checkPathDir($path);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
}

/**
 * Дата\время
 *
 * Хелпер для работы с датами и временем
 * <p>Текущая временная зона берется из текущий конфигурации по ключу <i>['counter','day']</i>
 * (см. функцию <b>BX\Base\Registry</b>).<br />Если такого ключа, то берется значение функции date_default_timezone_get</p>
 * @link http://php.net/manual/en/function.date-default-timezone-get.php
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Date
{
	/**
	 * @var string
	 */
	private static $manager = 'datetime';
	/**
	 * Get manager
	 *
	 * @return DateTimeManager
	 */
	private static function getManager()
	{
		if (DI::get(self::$manager) === null){
			DI::set(self::$manager,new DateTimeManager());
		}
		return DI::get(self::$manager);
	}
	/**
	 * Включение работы с времеными зонами
	 *
	 * @return boolean  Возвращает <b>TRUE</b> в случае успеха.
	 */
	public static function activeTimeZone()
	{
		return self::getManager()->activeTimeZone();
	}
	/**
	 * Включение работы с времеными зонами
	 *
	 * @return boolean  Возвращает <b>TRUE</b> в случае успеха.
	 */
	public static function disableTimeZone()
	{
		return self::getManager()->disableTimeZone();
	}
	/**
	 * Проверка что переданная строка является датой.
	 *
	 * @param string $datetime Cтрока с датой и временем
	 * @param string $format <p>
	 * Формат даты и времени.<br>
	 * Если значение не задано, оно берется из заданной конфигурации <i>['date','full']</i>
	 * (см. функцию <b>BX\Base\Registry</b>) по ключу.
	 * Если же такого ключа нет, то берется значение <i>'d.m.Y H:i:s'</i>.
	 * </p>
	 * @return boolean
	 */
	public static function checkDateTime($datetime,$format = 'full')
	{
		return self::getManager()->checkDateTime($datetime,$format);
	}
	/**
	 * Конвертация строки с датой и временем в <b>timestamp</b>
	 *
	 * При конвертации учитывается часовой пояс
	 * @param string $datetime Cтрока с датой и временем
	 * @param string $format <p>
	 * Формат даты и времени.<br>
	 * Если значение не задано, оно берется из заданной конфигурации <i>['date','full']</i>
	 * (см. функцию <b>BX\Base\Registry</b>) по ключу.
	 * Если же такого ключа нет, то берется значение <i>'d.m.Y H:i:s'</i>.
	 * </p>
	 * @return integer
	 */
	public static function makeTimeStamp($datetime,$format = 'full')
	{
		return self::getManager()->makeTimeStamp($datetime,$format);
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
	 * (см. функцию <b>BX\Base\Registry</b>) по ключу.
	 * Если же такого ключа нет, то берется значение <i>'d.m.Y H:i:s'</i>.
	 * </p>
	 * @return string
	 */
	public static function convertTimeStamp($timestamp = false,$format = 'full')
	{
		return self::getManager()->convertTimeStamp($timestamp,$format);
	}
	/**
	 * Смещение часового пояска
	 * @link http://php.net/manual/en/function.date.php
	 * @return integer Возвращает результат <b>date('Z')</b>
	 */
	public static function getOffset()
	{
		return self::getManager()->getOffset();
	}
	/**
	 * Возвращает текущее время в формате gmt
	 *
	 * @return integer
	 */
	public static function getUtc()
	{
		return self::getManager()->getUtc();
	}
}

/**
 * События
 *
 * Хелпер для работы со событиями
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
	 * @return EventManager
	 */
	private static function getManager()
	{
		if (DI::get(self::$manager) === null){
			DI::set(self::$manager,new EventManager());
		}
		return DI::get(self::$manager);
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
		if (DI::get(self::$manager) === null){
			DI::set(self::$manager,new CaptchaManager());
		}
		return DI::get(self::$manager);
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