<?php namespace BX\DB;
use BX\DB\IDatabaseResult;

class DBResultManager implements \Iterator, IDatabaseResult
{
	use \BX\String\StringTrait;
	/**
	 * @var array
	 */
	protected $result;
	/**
	 * @param array $result
	 */
	public function __construct($result)
	{
		$this->result = $result;
	}
	/**
	 * Get result
	 * @return array
	 */
	public function getData()
	{
		return $this->result;
	}
	/**
	 * Count selected rows
	 * @return integer
	 */
	public function count()
	{
		return count($this->result);
	}
	/**
	 * Fetch result
	 * @return array
	 */
	public function fetch()
	{
		return next($this->result);
	}
	/**
	 * Get next result
	 */
	public function getNext()
	{
		$this->next();
	}
	/**
	 * Rewind
	 */
	public function rewind()
	{
		rewind($this->result);
	}
	/**
	 * Get current row
	 * @return array
	 */
	public function current()
	{
		$result = current($this->result);
		foreach ($result as $key => $value){
			$result['~'.$key] = $value;
			$result[$key] = $this->string()->escape($value);
		}
		return $result;
	}
	/**
	 * Get key
	 * @return string
	 */
	public function key()
	{
		return key($this->result);
	}
	/**
	 * Next row
	 * @return array
	 */
	public function next()
	{
		$result = next($this->result);
		foreach ($result as $key => $value){
			$result['~'.$key] = $value;
			$result[$key] = $this->string()->escape($value);
		}
		return $result;
	}
	/**
	 * Is valud
	 * @return boolean
	 */
	public function valid()
	{
		return key($this->result) !== null;
	}
}