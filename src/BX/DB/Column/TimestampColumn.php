<?php namespace BX\DB\Column;

class TimestampColumn extends BaseColumn
{
	use \BX\Date\DateTrait;
	private $format;
	public function getFilterRule()
	{
		return ($this->format === 'short') ? 'date' : 'datetime';
	}
	public function setFormat($format)
	{
		$this->format = $format;
	}
	public static function create($column,$format = 'full')
	{
		$return = parent::create($column);
		$return->setFormat($format);
		return $return;
	}
	public function convertToDB($key,$value,array $values)
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
	public function convertFromDB($key,$value,array $values)
	{
		if ($value > 0){
			$value = intval($value) - $this->date()->getOffset();
			return $this->date()->convertTimeStamp($value,$this->format);
		} else{
			throw new \InvalidArgumentException('Bad format for timestamp');
		}
	}
}