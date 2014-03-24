<?php namespace BX\DB\Column;

class NumberColumn extends BaseColumn
{
	public $integer;
	public function getFilterRule()
	{
		return 'numeric';
	}
	public static function create($column,$integer = true)
	{
		$return = parent::create($column);
		$return->integer = $integer;
		return $return;
	}
	public function convertToDB($key,$value,array $values)
	{
		if ($value > 0){
			if ($this->integer){
				return intval($value);
			} else{
				return floatval($value);
			}
		}
	}
}