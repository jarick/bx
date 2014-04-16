<?php namespace BX\Base;

class Dictionary implements \Iterator
{
	use \BX\String\StringTrait;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var array
	 */
	private $array = [];
	/**
	 * Constructor
	 * @param type $type
	 */
	public function __construct($type)
	{
		$this->position = 0;
		$this->type = $type;
	}
	/**
	 * Validate type
	 * @param mixed $value
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	private function validateType($value)
	{
		if (!($value instanceof $this->type)){
			throw new \InvalidArgumentException("Value is not type {$this->type}");
		}
		return true;
	}
	/**
	 * Add item
	 * @param string $key
	 * @param mixed $value
	 * @return \BX\Base\Dictionary
	 */
	public function add($key,$value)
	{
		$key = $this->string()->toUpper($key);
		if ($this->validateType($value)){
			$this->array[$key] = $value;
		}
		return $this;
	}
	/**
	 * Has key
	 * @param string $key
	 * @return boolean
	 */
	public function has($key)
	{
		return array_key_exists($key,$this->array);
	}
	/**
	 * Get value by key
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return $this->array[$key];
	}
	/**
	 * Delete item by key
	 * @param string $key
	 */
	public function delete($key)
	{
		unset($this->array[$key]);
	}
	/**
	 * Set data
	 * @param array $data
	 * @return \BX\Base\Dictionary
	 */
	public function setData(array $data)
	{
		foreach($data as $key => $item){
			$this->add($key,$item);
		}
		return $this;
	}
	/**
	 * Rewind
	 * @return bool
	 */
	function rewind()
	{
		return reset($this->array);
	}
	/**
	 * Get current
	 * @return mixed
	 */
	function current()
	{
		return current($this->array);
	}
	/**
	 * Get key
	 * @return string
	 */
	function key()
	{
		return key($this->array);
	}
	/**
	 * Get next
	 * @return mixed
	 */
	function next()
	{
		return next($this->array);
	}
	/**
	 * Valid
	 * @return boolean
	 */
	function valid()
	{
		return key($this->array) !== null;
	}
}