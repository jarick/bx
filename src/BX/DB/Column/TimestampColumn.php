<?php namespace BX\DB\Column;

class TimestampColumn extends BaseColumn
{
	use \BX\Date\DateTrait;
	/**
	 * @var string
	 */
	private $format;
	/**
	 * Get filter rule name
	 * @return string
	 */
	public function getFilterRule()
	{
		return ($this->format === 'short') ? 'date' : 'datetime';
	}
	/**
	 * Set format
	 * @param string $format
	 * @return self
	 */
	public function setFormat($format)
	{
		$this->format = $format;
		return $this;
	}
	/**
	 * Create column
	 * @param string $column
	 * @param string $format
	 * @return self
	 */
	public static function create($column,$format = 'full')
	{
		$return = parent::create($column);
		$return->setFormat($format);
		return $return;
	}
	/**
	 * Convert value to db
	 * @param string $value
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function convertToDB($value)
	{
		if (!$this->date()->checkDateTime($value,$this->format)){
			throw new \InvalidArgumentException('Bad format for timestamp');
		}
		$timestamp = $this->date()->makeTimeStamp($value,$this->format);
		if ($timestamp > 0){
			return $timestamp + $this->date()->getOffset();
		} else{
			throw new \InvalidArgumentException('Bad format for timestamp');
		}
	}
	/**
	 * Convert value from db
	 * @param string $value
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function convertFromDB($value)
	{
		if ($value > 0){
			$value = intval($value) - $this->date()->getOffset();
			return $this->date()->convertTimeStamp($value,$this->format);
		} else{
			throw new \InvalidArgumentException('Bad format for timestamp');
		}
	}
}