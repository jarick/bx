<?php namespace BX\DB\Column;

class BooleanColumn extends BaseColumn
{
	/**
	 * @var mixed
	 */
	public $true;
	/**
	 * @var mixed
	 */
	public $false;
	/**
	 * @var boolean
	 */
	public $strict;
	/**
	 * Get filter rule name
	 * @return string
	 */
	public function getFilterRule()
	{
		return 'boolean';
	}
	/**
	 * Create column
	 * @param string $column
	 * @param mixed $true
	 * @param mixed $false
	 * @param boolean $strict
	 * @return BooleanColumn
	 */
	public static function create($column,$true = 'Y',$false = 'N',$strict = false)
	{
		$return = parent::create($column);
		$return->true = $true;
		$return->false = $false;
		$return->strict = $strict;
		return $return;
	}
	/**
	 * Convert value to db
	 * @param mixed $value
	 * @return string
	 */
	public function convertToDB($value)
	{
		if ($this->strict){
			if ($value != $this->true && $value != $this->false){
				throw new \InvalidArgumentException("Bad format, must be `{$this->true}` or `{$this->false}`");
			}
			$value = intval($value == $this->true);
		} else{
			if ($value !== $this->true && $value !== $this->false){
				throw new \InvalidArgumentException("Bad format, must be `{$this->true}` or `{$this->false}`");
			}
			$value = intval($value === $this->true);
		}
		return $value;
	}
	/**
	 * Convert value from db
	 * @param string $value
	 * @return mixed
	 */
	public function convertFromDB($value)
	{
		if ($value != 0 && $value != 1){
			throw new \InvalidArgumentException("Bad format, must be `0` or `1`");
		}
		if ($value == 1){
			return $this->true;
		} else{
			return $this->false;
		}
	}
}