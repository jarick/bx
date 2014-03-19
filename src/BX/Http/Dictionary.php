<?php namespace BX\Http;
use BX\Object;

class Dictionary extends Object implements \IteratorAggregate, \ArrayAccess, \Countable
{
	private $data;
	public function __construct(array $data)
	{
		$this->data = $data;
	}
	public function get($key)
	{
		if ($this->has($key)){
			return $this->data[$key];
		} else{
			return null;
		}
	}
	public function has($key)
	{
		return array_key_exists($key,$this->data);
	}
	public function all()
	{
		return $this->data;
	}
	public function count()
	{
		return count($this->data);
	}
	public function getIterator()
	{
		return new ArrayIterator($this->data);
	}
	public function offsetExists($offset)
	{
		return $this->has($offset);
	}
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}
	public function offsetSet($offset,$value)
	{
		return $this->set($offset,$value);
	}
	public function offsetUnset($offset)
	{
		$this->delete($offset);
	}
}