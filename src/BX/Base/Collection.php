<?php namespace BX\Base;

class Collection implements \Iterator
{
	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var array
	 */
	protected $array = [];
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
	public function add($value)
	{
		if ($this->validateType($value)){
			$this->array[] = $value;
		}
		return $this;
	}
	/**
	 * Has key
	 * @param mixed $value
	 * @return boolean
	 */
	public function has($value)
	{
		return in_array($value,$this->array);
	}
	/**
	 * Delete item by key
	 * @param mixed $item
	 */
	public function delete($item)
	{
		$key = array_search($item,$this->array);
		if ($key !== false){
			unset($this->array[$key]);
		}
	}
	/**
	 * Pop array
	 * @return mixed
	 */
	public function pop()
	{
		return array_pop($this->array);
	}
	/**
	 * Set data
	 * @param array $data
	 * @return \BX\Base\Dictionary
	 */
	public function setData(array $data)
	{
		foreach($data as $item){
			$this->add($item);
		}
		return $this;
	}
	/**
	 * Rewind
	 * @return bool
	 */
	public function rewind()
	{
		return reset($this->array);
	}
	/**
	 * Get current
	 * @return mixed
	 */
	public function current()
	{
		return current($this->array);
	}
	/**
	 * Get key
	 * @return string
	 */
	public function key()
	{
		return key($this->array);
	}
	/**
	 * Get next
	 * @return mixed
	 */
	public function next()
	{
		return next($this->array);
	}
	/**
	 * Valid
	 * @return boolean
	 */
	public function valid()
	{
		return key($this->array) !== null;
	}
	/**
	 * Get count
	 * @return integer
	 */
	public function count()
	{
		return count($this->array);
	}
}