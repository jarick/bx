<?php namespace BX\Error;
use Monolog;

class ErrorManager implements IErrorManager
{
	/**
	 * @var \Exception
	 */
	static $e = null;
	/**
	 * Reset error
	 *
	 * @return boolean
	 */
	public static function reset()
	{
		self::$e = null;
		return true;
	}
	/**
	 * Set catch exception
	 *
	 * @param \Exception $ex
	 */
	public static function set(\Exception $ex,$component = 'default')
	{
		self::$e = $ex;
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
		Monolog::get($component)->error($result);
		return true;
	}
	/**
	 * Return exception
	 *
	 * @return \Exception
	 */
	public static function get()
	{
		return self::$e;
	}
}