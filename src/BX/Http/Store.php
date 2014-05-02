<?php namespace BX\Http;

class Store implements \IteratorAggregate, \ArrayAccess, \Countable
{
	/**
	 * @var array
	 */
	private $data = [];
	/**
	 * Constructor
	 * @param array|integer $data
	 */
	public function __construct($data = [])
	{
		$this->data = $data;
	}
	/**
	 * Get value
	 * @param string|array $key
	 * @return null|string
	 */
	public function get($key)
	{
		if ($this->has($key)){
			return $this->data[$key];
		}else{
			return null;
		}
	}
	/**
	 * has key
	 * @param string|array $key
	 * @return boolean
	 */
	public function has($key)
	{
		return array_key_exists($key,$this->data);
	}
	/**
	 * Get all values
	 * @return array
	 */
	public function all()
	{
		return $this->data;
	}
	/**
	 * Get count
	 * @return integer
	 */
	public function count()
	{
		return count($this->data);
	}
	/**
	 * Get iterator
	 * @return \BX\Http\ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->data);
	}
	/**
	 * Offset exists
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return $this->has($offset);
	}
	/**
	 * Offset get
	 * @param string $offset
	 * @return string|array
	 */
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}
	/**
	 * Read only
	 * @param string $offset
	 * @param string $value
	 * @throws \InvalidArgumentException
	 */
	public function offsetSet($offset,$value)
	{
		throw new \InvalidArgumentException('Is read only');
	}
	/**
	 * Read only
	 * @param string $offset
	 * @throws \InvalidArgumentException
	 */
	public function offsetUnset($offset)
	{
		throw new \InvalidArgumentException('Is read only');
	}
}