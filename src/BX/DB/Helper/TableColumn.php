<?php namespace BX\DB\Helper;
use BX\Base;

class TableColumn extends Base
{
	protected $name;
	protected $type;
	protected $length = false;
	protected $keys = [];
	protected $default = null;
	public function __construct($name,$type,$length = false,$keys = [])
	{
		$this->name = $name;
		$this->type = $type;
		$this->length = $length;
		$this->keys = $keys;
	}
	public static function getString($name,$length)
	{
		return new static($name,'STRING',$length);
	}
	public static function getTimestamp($name)
	{
		return new static($name,'TIMESTAMP');
	}
	public static function getInteger($name)
	{
		return new static($name,'INTEGER');
	}
	public static function getBoolean($name)
	{
		return new static($name,'STRING',1);
	}
	public static function getText($name)
	{
		return new static($name,'TEXT');
	}
	public static function getPK($name)
	{
		return new static($name,'INTEGER',false,['NN','PK','AI']);
	}
	public function setAutoIncrement()
	{
		$this->keys[] = 'AI';
		return $this;
	}
	public function setPk()
	{
		$this->keys[] = 'PK';
		return $this;
	}
	public function setUnique()
	{
		$this->keys[] = 'UQ';
		return $this;
	}
	public function setNotNull()
	{
		$this->keys[] = 'NN';
		return $this;
	}
	public function setDefault($value,$safe = true)
	{
		$this->default = [
			'safe'	 => $safe,
			'value'	 => $value,
		];
		return $this;
	}
	public function toArray()
	{
		$result = [$this->name,$this->type,$this->length,implode(',',$this->keys)];
		if (is_array($this->default)){
			if ($this->default['safe']){
				$result['def'] = $this->default['value'];
			} else{
				$result['~def'] = $this->default['value'];
			}
		}
		return $result;
	}
}