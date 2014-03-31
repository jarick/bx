<?php namespace BX\User\Store;

class ArrayAuthStore extends AuthStore
{
	private $data = [];
	/**
	 * Get data from session
	 * @return array
	 */
	protected function get()
	{
		if (!empty($this->data)){
			return $this->data;
		} else{
			return null;
		}
	}
	/**
	 * Set data session
	 * @param array $sess
	 * @return \BX\User\Store\SessionAuthStore
	 */
	protected function set($sess)
	{
		$this->data = $sess;
		return $this;
	}
	/**
	 * Get unique is from session
	 * @return string
	 */
	protected function getUniqueId()
	{
		return sha1(mt_rand(1,1000));
	}
}