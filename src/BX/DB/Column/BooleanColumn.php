<?php namespace BX\DB\Column;

class BooleanColumn extends BaseColumn
{
	public $true;
	public $false;
	public $strict;
	public function getFilterRule()
	{
		return 'boolean';
	}
	public static function create($column,$true = 'Y',$false = 'N',$strict = false)
	{
		$return = parent::create($column);
		$return->true = $true;
		$return->false = $false;
		$return->strict = $strict;
		return $return;
	}
	public function convertToDB($key,$value,array $values)
	{
		if ($this->strict){
			$value = intval($value == $this->true);
		} else{
			$value = intval($value === $this->true);
		}
		return $value;
	}
	public function convertFromDB($key,$value,array $values)
	{
		if ($value === 1){
			return $this->true;
		} else{
			return $this->false;
		}
	}
}