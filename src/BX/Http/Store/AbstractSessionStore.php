<?php namespace BX\Http\Store;

abstract class AbstractSessionStore implements \ArrayAccess
{
	protected $bag = null;
	abstract protected function getSessionBag();
	abstract protected function saveSessionBag();
	abstract public function getId();
	/**
	 * Offset exists
	 *
	 * @param array\string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		$key = (array)$offset;
		$bag = $this->getSessionBag();
		for($i = count($key) - 1; $i >= 0; $i--){
			if (array_key_exists($key[$i],$bag)){
				$bag = $bag[$key[$i]];
			}else{
				return false;
			}
		}
		return true;
	}
	/**
	 * Offset get
	 *
	 * @param type $offset
	 * @return mised
	 */
	public function &offsetGet($offset)
	{
		$key = (array)$offset;
		$bag = $this->getSessionBag();
		foreach($key as $item){
			if (!isset($bag[$item])){
				$bag[$item] = null;
			}
			$bag = &$bag[$item];
		}
		return $bag;
	}
	/**
	 * Offset set
	 *
	 * @param array|string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset,$value)
	{
		$key = (array)$offset;
		$bag = $this->getSessionBag();
		$data = $value;
		for($i = count($key) - 1; $i > 0; $i--){
			$data = [$key[$i] => $data];
		}
		#if (isset($this->bag[$key[0]])){
		#	$data = array_merge_recursive($this->bag[$key[0]],$data);
		#}
		$this->bag[$key[0]] = $data;
	}
	public function offsetUnset($offset)
	{

	}
	public function save()
	{
		$this->saveSessionBag();
	}
}