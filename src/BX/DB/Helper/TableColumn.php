<?php namespace BX\DB\Helper;

class TableColumn
{
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var integer
	 */
	protected $length = false;
	/**
	 * @var array
	 */
	protected $keys = [];
	/**
	 * @var string
	 */
	protected $default = null;
	/**
	 * Constructor
	 * @param string $name
	 * @param string $type
	 * @param integer $length
	 * @param array $keys
	 */
	public function __construct($name,$type,$length = false,array $keys = [])
	{
		$this->name = $name;
		$this->type = $type;
		$this->length = $length;
		$this->keys = $keys;
	}
	/**
	 * Get string column
	 * @param string $name
	 * @param integer $length
	 * @return TableColumn
	 */
	public static function getString($name,$length)
	{
		return new static($name,'STRING',$length);
	}
	/**
	 * Get timestamp column
	 * @param string $name
	 * @return TableColumn
	 */
	public static function getTimestamp($name)
	{
		return new static($name,'TIMESTAMP');
	}
	/**
	 * Get integer column
	 * @param type $name
	 * @return TableColumn
	 */
	public static function getInteger($name)
	{
		return new static($name,'INTEGER');
	}
	/**
	 * Get boolean column
	 * @param type $name
	 * @return TableColumn
	 */
	public static function getBoolean($name)
	{
		return new static($name,'STRING',1);
	}
	/**
	 * Get numeric column
	 * @param string $name
	 * @return TableColumn
	 */
	public static function getNumeric($name)
	{
		return new static($name,'REAL');
	}
	/**
	 * Get text column
	 * @param string $name
	 * @return TableColumn
	 */
	public static function getText($name)
	{
		return new static($name,'TEXT');
	}
	/**
	 * Get integer column with primary key
	 * @param string $name
	 * @return \static
	 */
	public static function getPK($name = 'ID')
	{
		return new static($name,'INTEGER',false,['NN','PK','AI']);
	}
	/**
	 * Set auto increment
	 * @return \BX\DB\Helper\TableColumn
	 */
	public function setAutoIncrement()
	{
		$this->keys[] = 'AI';
		return $this;
	}
	/**
	 * Set primary key
	 * @return \BX\DB\Helper\TableColumn
	 */
	public function setPk()
	{
		$this->keys[] = 'PK';
		return $this;
	}
	/**
	 * Set unique key
	 * @return \BX\DB\Helper\TableColumn
	 */
	public function setUnique()
	{
		$this->keys[] = 'UQ';
		return $this;
	}
	/**
	 * Set not null
	 * @return \BX\DB\Helper\TableColumn
	 */
	public function setNotNull()
	{
		$this->keys[] = 'NN';
		return $this;
	}
	/**
	 * Set default value
	 * @param string $value
	 * @param bool $safe
	 * @return \BX\DB\Helper\TableColumn
	 */
	public function setDefault($value,$safe = true)
	{
		$this->default = [
			'safe'	 => $safe,
			'value'	 => $value,
		];
		return $this;
	}
	/**
	 * Convert to array
	 * @return array
	 */
	public function toArray()
	{
		$result = [$this->name,$this->type,$this->length,implode(',',$this->keys)];
		if (is_array($this->default)){
			if ($this->default['safe']){
				$result['def'] = $this->default['value'];
			}else{
				$result['~def'] = $this->default['value'];
			}
		}
		return $result;
	}
}