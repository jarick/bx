<?php namespace BX\DB\Column;

class BooleanColumn extends BaseColumn
{
	private $true;
	private $false;
	private $strict;

	public function getFilterRule()
	{
		return 'boolean';
	}

	public static function create($column,$true = 'Y',$false = 'N',$strict = false)
	{
		$this->true = $true;
		$this->false = $false;
		$this->strict = $strict;
		parent::create($column);
	}

	public static function convertToDB($key,$value,array $values)
	{
		if($this->bStrict){
			$value = intval($value == $this->true);
		} else{
			$value = intval($value === $this->true);
		}
		return $value;
	}

	public function convertFromDB($key,$value,array $values)
	{
		if($value === 1){
			return $this->true;
		} else{
			return $this->false;
		}
	}
}