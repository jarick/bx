<?php
use BX\Base\DI;
use BX\Captcha\CaptchaManager;
use BX\Counter\CounterManager;
use BX\Date\DateTimeManager;
use BX\DB\Filter\SqlBuilder;
use BX\Event\EventManager;
use BX\FileSystem\FileSystemManager;
use BX\Logger\LoggerManager;
use BX\String\StringManager;
use BX\User\AuthManager;
use BX\User\RememberPasswordManager;
use BX\User\UserGroupManager;
use BX\User\UserGroupMemberManager;
use BX\User\UserManager;
use BX\Config\DICService;
use BX\Config\ConfigManager;

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
	 * @var string
	 */
	private static $manager = 'error';
	/**
	 * Return error manager
	 *
	 * @return BX\Error\ErrorManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = DICService::getContainer()->share(function(){
				return new BX\Error\ErrorManager();
			});
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
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
	public static function set(Exception $ex,$component = 'default')
	{
		return self::getManager()->set($ex,$component);
	}
	/**
	 * Reset exception
	 */
	public static function reset()
	{
		return self::getManager()->reset();
	}
	/**
	 * Return exception
	 * @return Exception
	 */
	public static function get()
	{
		return self::getManager()->get();
	}
}

/**
 * Класс для логирования ошибок
 * см. @link https://github.com/Seldaek/monolog
 *
 * Пример кода:
 * <code>
 * $user = User::finder()->filter(['ID' => 1])->get();
 * if($user === false){
 * 	Logger::get('user')->err('User not found');
 * }
 * </code>
 * @author jarick <zolotarev.jar@gmail.com>
 * @version 0.1
 */
class Monolog
{
	/**
	 * @var string
	 */
	private static $manager = 'logger';
	/**
	 * Return error manager
	 *
	 * @return BX\Logger\ILoggerManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = DICService::getContainer()->factory(function(){
				return new LoggerManager();
			});
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Возвращает логер, который наследуется от интерфейса стандарта psr-3
	 * @link https://github.com/Seldaek/monolog
	 *
	 * @param string $component Имя текущего компонента
	 * @return \Monolog\Logger
	 */
	public static function get($component = 'default')
	{
		return self::getManager()->get($component);
	}
}

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
			$manager = DICService::getContainer()->factory(function(){
				return new ConfigManager();
			});
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Инициализацтя конфигурации
	 *
	 * @param mixed $store
	 * @param string $format
	 * @return boolean
	 */
	public static function init($store,$format)
	{
		return self::getManager()->init($store,$format);
	}
	/**
	 * Поиск ключа
	 *
	 * @return boolean
	 */
	public static function exists()
	{
		return self::getManager()->exists(func_get_args());
	}
	public static function get()
	{
		return self::getManager()->get(func_get_args());
	}
	public static function all()
	{
		return self::getManager()->all();
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
		if (DICService::get(self::$manager) === null){
			$manager = DICService::getContainer()->factory(function(){
				return new CounterManager();
			});
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
		if (DICService::get(self::$manager) === null){
			$manager = DICService::getContainer()->factory(function(){
				return new StringManager();
			});
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
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
		if (DICService::get(self::$manager) === null){
			$manager = DICService::getContainer()->factory(function(){
				return new FileSystemManager();
			});
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
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
		if (DICService::get(self::$manager) === null){
			$manager = DICService::getContainer()->factory(function(){
				return new DateTimeManager();
			});
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
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
	 * @return IEventManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = DICService::getContainer()->factory(function(){
				return new EventManager();
			});
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
			$manager = DICService::getContainer()->factory(function(){
				return new CaptchaManager();
			});
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
			$manager = DICService::getContainer()->factory(function(){
				return new AuthManager();
			});
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

/**
 * Восстановление пароля
 *
 * Получение токена:
 * <code>
 *  $token = RememberPassword::getToken($user->id,$user->guid);
 * </code>
 * Пользователю для подтверждения смены пароля передается его GUID и сгенировааный токен.
 * Проверка токена:
 * <code>
 * 	return RememberPassword::check($guid,$token);
 * </code>
 */
class RememberPassword
{
	/**
	 * @var string
	 */
	private static $manager = 'remember';
	/**
	 * Get manager
	 *
	 * @return RememberPasswordManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = DICService::getContainer()->factory(function(){
				return new RememberPasswordManager();
			});
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Получение токена
	 *
	 * @param integer $user_id ID пользователя
	 * @param string $user_guid GUID пользователя
	 * @param string $token Токен, по-умолчанию токен можно не передавать,
	 *  тогда функция сама сгенирирует случайный токен.
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function getToken($user_id,$user_guid,$token = null)
	{
		Error::reset();
		try{
			$return = self::getManager()->getToken($user_id,$user_guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Проверка токена
	 *
	 * @param string $guid GUID пользователя
	 * @param string $token Токен
	 * @return null|false|integer <p>Возвращает ID пользователя в случае если токен был найден.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае если токен не был найден
	 * </p>
	 * <p>
	 * Возвращает <b>NULL</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function check($guid,$token)
	{
		Error::reset();
		try{
			$return = self::getManager()->check($guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = null;
		}
		return $return;
	}
	/**
	 * Удаление запроса смены пароля для пользователя
	 *
	 * @param integer $user_id ID пользователя
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clear($user_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->clear($user_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление старых запросов смены пароля
	 *
	 * @param integer $day Через сколько дней считать запрос просроченным
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clearOld($day = 3)
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
 * Подтверждение регистрации
 *
 * Получение токена:
 * <code>
 *  $token = ConfirmRegistration::getToken($user->id,$user->guid);
 * </code>
 * Пользователю для подтверждения регистрации передается его GUID и сгенировааный токен.
 * Проверка токена:
 * <code>
 * 	return ConfirmRegistration::check($guid,$token);
 * </code>
 */
class ConfirmRegistration
{
	/**
	 * @var string
	 */
	private static $manager = 'confirm';
	/**
	 * Get manager
	 *
	 * @return RememberPasswordManager
	 */
	private static function getManager()
	{
		if (DICService::get(self::$manager) === null){
			$manager = DICService::getContainer()->factory(function(){
				return new RememberPasswordManager();
			});
			DICService::set(self::$manager,$manager);
		}
		return DICService::get(self::$manager);
	}
	/**
	 * Получение токена
	 *
	 * @param integer $user_id ID пользователя
	 * @param string $user_guid GUID пользователя
	 * @param string $token Токен, по-умолчанию токен можно не передавать,
	 *  тогда функция сама сгенирирует случайный токен.
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function getToken($user_id,$user_guid,$token = null)
	{
		Error::reset();
		try{
			$return = self::getManager()->getToken($user_id,$user_guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Проверка токена
	 *
	 * @param string $guid GUID пользователя
	 * @param string $token Токен
	 * @return null|false|integer <p>Возвращает ID пользователя в случае если токен был найден.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае если токен не был найден
	 * </p>
	 * <p>
	 * Возвращает <b>NULL</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function check($guid,$token)
	{
		Error::reset();
		try{
			$return = self::getManager()->check($guid,$token);
		}catch (Exception $ex){
			Error::set($ex);
			$return = null;
		}
		return $return;
	}
	/**
	 * Удаление запроса подтверждения регистрации для пользователя
	 *
	 * @param integer $user_id ID пользователя
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clear($user_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->clear($user_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление старых запросов подтверждения регистрации
	 *
	 * @param integer $day Через сколько дней считать запрос просроченным
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function clearOld($day = 3)
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
 * Пользователи
 *
 * Класс для работы с пользователями. Поля полльзователя:
 * <ol>
 * <li>ID <b>INTEGER</b> ID пользователя, автоматически назначается БД</li>
 * <li>GUID <b>STRING</b> GUID пользователя,генирируется автоматически</li>
 * <li>LOGIN <b>STRING</b> логин, обязательное поле должен быть уникальным</li>
 * <li>PASSWORD <b>STRING</b> генирируется автоматически, хранит хеш пароля</li>
 * <li>EMAIL <b>STRING</b> e-mail, обязательное поле должен быть уникальным</li>
 * <li>CREATE_DATE <b>STRING</b> дата создания, генирируется автоматически</li>
 * <li>TIMESTAMP_X <b>STRING</b> дата изменения, генирируется автоматически</li>
 * <li>REGISTERED <b>BOOLEAN</b> зарегистрирован ли пользователь</li>
 * <li>ACTIVE <b>BOOLEAN</b> активность пользователя</li>
 * <li>CODE <b>STRING</b> символьный код полльзователя, генерируется автоматически путем транслитерации логина</li>
 * <li>DISPLAY_NAME <b>STRING</b> Имя пользователя на сайте, не обязательное</li>
 * </ol>
 */
class User
{
	/**
	 * @var string
	 */
	private static $manager = 'user';
	/**
	 * Get manager
	 *
	 * @return UserManager
	 */
	private static function getManager()
	{
		if (DI::get(self::$manager) === null){
			DI::set(self::$manager,new UserManager());
		}
		return DI::get(self::$manager);
	}
	/**
	 * Добавление пользователя.
	 *
	 * @param array $user Массиы значения полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function add(array $user)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($user);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Изменение пользователя.
	 *
	 * @param integer $id ID пользователя
	 * @param array $user Массиы значения полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function update($id,array $user)
	{
		Error::reset();
		try{
			$return = self::getManager()->update($id,$user);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление пользователя.
	 *
	 * @param integer $id ID пользователя
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function delete($id)
	{
		Error::reset();
		try{
			$return = self::getManager()->delete($id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Фильтр по пользователям
	 *
	 * @return SqlBuilder
	 */
	public static function finder()
	{
		return self::getManager()->getFinder();
	}
}

/**
 * Класс для работы с группами пользователей
 *
 * Доступные поля:
 * <ol>
 * <li>ID <b>INTEGER</b> ID группы, автоматически генирируется БД</li>
 * <li>GUID <b>STRING</b> GUID группы, генирируется автоматически</li>
 * <li>ACTIVE <b>STRING</b> Активность группы, по-умолчанию группа активна</li>
 * <li>TIMESTAMP_X <b>STRING</b> Дата и время последнего изменения, генирируется автоматически</li>
 * <li>NAME <b>STRING</b> Название группы, обязательное поле</li>
 * <li>DESCRIPTION <b>STRING</b> Описание группы</li>
 * <li>SORT <b>INTEGER</b> Индекс сортировки</li>
 * </ol>
 */
class UserGroup
{
	/**
	 * @var string
	 */
	private static $manager = 'user_group';
	/**
	 * Get manager
	 *
	 * @return UserGroupManager
	 */
	private static function getManager()
	{
		if (DI::get(self::$manager) === null){
			DI::set(self::$manager,new UserGroupManager());
		}
		return DI::get(self::$manager);
	}
	/**
	 * Добавление группы пользователей
	 *
	 * @param array $group Массив значение полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function add(array $group)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($group);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Изменение группы пользователей
	 *
	 * @param integer $id ID группы
	 * @param array $group Массив значение полей
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function update($id,array $group)
	{
		Error::reset();
		try{
			$return = self::getManager()->update($id,$group);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление группы пользователей
	 *
	 * @param integer $id ID группы
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function delete($id)
	{
		Error::reset();
		try{
			$return = self::getManager()->delete($id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Фильтр по группам пользователей
	 *
	 * @return SqlBuilder
	 */
	public static function finder()
	{
		return $this->finder();
	}
}

/**
 * Класс для работы с привязками пользоватей к группам
 *
 * Доступные поля:
 * <ol>
 * <li>ID <b>INTEGER</b> ID привязки, автоматически генирируется БД</li>
 * <li>USER_ID <b>INTEGER</b> ID пользователя,обязательное поле</li>
 * <li>GROUP_ID <b>INTEGER</b> ID группы пользователей</li>
 * <li>TIMESTAMP_X <b>STRING</b> Дата и время последнего изменения, генирируется автоматически</li>
 * </ol>
 */
class UserGroupMember
{
	/**
	 * @var string
	 */
	private static $manager = 'user_group_member';
	/**
	 * Get manager
	 *
	 * @return UserGroupMemberManager
	 */
	private static function getManager()
	{
		if (DI::get(self::$manager) === null){
			DI::set(self::$manager,new UserGroupMemberManager());
		}
		return DI::get(self::$manager);
	}
	/**
	 * Добавление пользователя в группу
	 *
	 * @param integer $user_id ID пользователя
	 * @param integer $group_id ID группы
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function add($user_id,$group_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->add($user_id,$group_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Удаление пользователя из группы
	 *
	 * @param integer $user_id ID пользователя
	 * @param integer $group_id ID группы
	 * @return boolean <p>Возвращает <b>TRUE</b> в случае успеха.
	 * </p>
	 * <p>
	 * Возвращает <b>FALSE</b> в случае ошибки. Саму ошибку можно получить с помощью
	 * функции <b>Error::get</b>
	 * </p>
	 */
	public static function delete($user_id,$group_id)
	{
		Error::reset();
		try{
			$return = self::getManager()->delete($user_id,$group_id);
		}catch (Exception $ex){
			Error::set($ex);
			$return = false;
		}
		return $return;
	}
	/**
	 * Фильтр по привязки пользователей к группе
	 *
	 * @return SqlBuilder
	 */
	public static function finder()
	{
		return self::getManager()->finder();
	}
}

class News
{
	public static function add(array $news)
	{

	}
	public static function update($id,array $news)
	{

	}
	public static function delete($id)
	{

	}
	public static function finder()
	{

	}
}

class NewsCategory
{
	public static function add(array $news)
	{

	}
	public static function update($id,array $news)
	{

	}
	public static function delete($id)
	{

	}
	public static function finder()
	{

	}
}

class NewsCategoryLink
{
	public static function add($news_id,$category_id)
	{

	}
	public static function delete($news_id,$category_id)
	{

	}
}

class MailType
{
	public static function add(array $news)
	{

	}
	public static function update($id,array $news)
	{

	}
	public static function delete($id)
	{

	}
	public static function finder()
	{

	}
}

class MailMessage
{
	public static function add(array $news)
	{

	}
	public static function update($id,array $news)
	{

	}
	public static function delete($id)
	{

	}
	public static function finder()
	{

	}
}

class Mail
{
	public static function send($type,$params,$immediate = true)
	{

	}
	public static function checkEvents()
	{

	}
}