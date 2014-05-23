<?php namespace BX\Date;
use Carbon\Carbon;

class DateTimeManager
{
	use \BX\Config\ConfigTrait;
	/**
	 * @var boolean
	 */
	private static $disableTimeZone = false;
	/**
	 * Active timezone
	 * @return \BX\Date\Manager\DateTimeManager
	 */
	public function activeTimeZone()
	{
		self::$disableTimeZone = false;
		return true;
	}
	/**
	 * Disable timezone
	 * @return \BX\Date\Manager\DateTimeManager
	 */
	public function disableTimeZone()
	{
		self::$disableTimeZone = true;
		return true;
	}
	/**
	 * Get timezone
	 * @return string
	 */
	private function getTimeZone()
	{
		if ($this->config()->exists('date','timezone')){
			return $this->config()->get('date','timezone');
		}else{
			return date_default_timezone_get();
		}
	}
	/**
	 * Get format
	 * @param string $type
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	private function getFormat($type)
	{
		if ($this->config()->exists('date',$type)){
			return $this->config()->get('date',$type);
		}elseif ($type === 'full'){
			return 'd.m.Y H:i:s';
		}elseif ($type === 'short'){
			return 'd.m.Y';
		}else{
			throw new \InvalidArgumentException('Date type not found');
		}
	}
	/**
	 * Check date time
	 * @param string $datetime
	 * @param string $format
	 * @return boolean
	 */
	public function checkDateTime($datetime,$format = 'full')
	{
		if (in_array($format,['short','full'])){
			$format = $this->getFormat($format);
		}
		return \DateTime::createFromFormat($format,$datetime) !== false;
	}
	/**
	 * Make timestamp
	 * @param string $datetime
	 * @param string $format
	 * @return integer
	 */
	public function makeTimeStamp($datetime,$format = 'full')
	{
		if (in_array($format,['short','full'])){
			$format = $this->getFormat($format);
		}
		return Carbon::createFromFormat($format,$datetime,$this->getTimeZone())->getTimestamp();
	}
	/**
	 * Convert timestamp
	 * @param strng $timestamp
	 * @param string $format
	 * @return string
	 */
	public function convertTimeStamp($timestamp = false,$format = 'full')
	{
		if (in_array($format,['short','full'])){
			$format = $this->getFormat($format);
		}
		if ($timestamp === false){
			$timestamp = time();
		}
		return Carbon::createFromTimestamp($timestamp,$this->getTimeZone())->format($format);
	}
	/**
	 * Get offset timestamp
	 * @return integer
	 */
	public function getOffset()
	{
		if ($this->config()->exists('date','disable_timezone') && self::$disableTimeZone){
			return 0;
		}else{
			return date('Z');
		}
	}
	/**
	 * Get GTM
	 * @return integr
	 */
	public function getUtc()
	{
		return time() + $this->getOffset();
	}
}