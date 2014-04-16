<?php namespace BX\Http;

class CookieStore extends Store
{
	/**
	 * Set cookie
	 * @param string $name
	 * @param string $value
	 * @param integer $expire
	 */
	public function set($name,$value,$expire)
	{
		setcookie($name,$value,$expire,"/");
		return $this;
	}
	/**
	 * Offset cookie
	 * @param string $offset
	 * @param string $value
	 */
	public function offsetSet($offset,$value)
	{
		$this->set($offset,$value);
	}
	/**
	 * Unset cookie
	 * @param string $offset
	 * @throws \InvalidArgumentException
	 */
	public function offsetUnset($offset)
	{
		$this->set($offset,null,-1);
	}
}