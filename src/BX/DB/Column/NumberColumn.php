<?php namespace BX\DB\Column;

class NumberColumn extends BaseColumn
{
	private $integer;
	
	public function getFilterRule()
	{
		return 'numeric';
	}
	
	public static function create($column,$integer = true)
	{
		$this->integer = $integer;
		parent::create($column);
	}

	public function convertToDB($key,$value,array $values)
	{
		if($value > 0){
			if($this->integer){
				return intval($value);
			} else{
				return floatval($value);
			}
		}
	}
}