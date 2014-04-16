<?php namespace BX\DB;

class DBResult implements \Iterator, IDbResult
{
	use \BX\String\StringTrait;
	/**
	 * @var array
	 */
	protected $return = [];
	/**
	 * Construct
	 * @param array $data
	 */
	public function __construct(array $data = [])
	{
		$this->return = $data;
		reset($this->return);
	}
	/**
	 * Set Result
	 * @param array $return
	 * @return \BX\DB\Manager\DBResult
	 */
	public function setResult(array $return)
	{
		$this->return = $return;
		return $this;
	}
	/**
	 * Get data array
	 * @return array
	 */
	public function getData()
	{
		return $this->return;
	}
	/**
	 * Get count
	 * @return integer
	 */
	public function count()
	{
		return count($this->return);
	}
	/**
	 * Get array
	 * @return array
	 */
	public function fetch()
	{
		$return = current($this->return);
		if (is_array($return)){
			next($this->return);
		}
		return $return;
	}
	/**
	 * Get secure array
	 * @return array
	 */
	public function getNext()
	{
		return $this->next();
	}
	/**
	 * Rewind
	 * @return array
	 */
	public function rewind()
	{
		return reset($this->return);
	}
	/**
	 * Get current
	 * @return array
	 */
	public function current()
	{
		$return = current($this->return);
		if (is_array($return)){
			foreach($return as $key => $value){
				$return['~'.$key] = $value;
				$return[$key] = $this->string()->escape($value);
			}
			next($this->return);
		}
		return $return;
	}
	/**
	 * Get key
	 * @return string
	 */
	public function key()
	{
		return key($this->return);
	}
	/**
	 * Next row
	 * @return array
	 */
	public function next()
	{
		$return = current($this->return);
		foreach($return as $key => $value){
			$return['~'.$key] = $value;
			$return[$key] = $this->string()->escape($value);
		}
		return $return;
	}
	/**
	 * Valid
	 * @return boolean
	 */
	public function valid()
	{
		return key($this->return) !== null;
	}
}