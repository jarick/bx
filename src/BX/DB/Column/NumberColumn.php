<?php namespace BX\DB\Column;

class NumberColumn extends BaseColumn
{
	/**
	 * @var boolean
	 */
	public $integer;
	/**
	 * Get filter filter rule name
	 *
	 * @return string
	 */
	public function getFilterRule()
	{
		return 'number';
	}
	/**
	 * Create column
	 * @param string $column
	 * @param bollean $integer
	 * @return self
	 */
	public static function create($column,$integer = true)
	{
		$return = parent::create($column);
		$return->integer = $integer;
		return $return;
	}
	/**
	 * Convert value to db
	 * @param int|float $value
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function convertToDB($value)
	{
		if ($value === null){
			return $value;
		}
		if (!is_numeric($value)){
			throw new \InvalidArgumentException('Bad format for numeric');
		}
		if ($this->integer){
			return intval($value);
		}else{
			return floatval($value);
		}
	}
	/**
	 * Convert value from db
	 * @param string $value
	 * @return int|float
	 * @throws \InvalidArgumentException
	 */
	public function convertFromDB($value)
	{
		if ($value === null){
			return $value;
		}
		if (!is_numeric($value)){
			throw new \InvalidArgumentException('Bad format for numeric');
		}
		if ($this->integer){
			return intval($value);
		}else{
			return floatval($value);
		}
	}
}