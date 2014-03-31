<?php namespace BX\User\Store;

class SessionAuthStore extends AuthStore
{
	use \BX\Http\HttpTrait;
	/**
	 * Get data from session
	 * @return array
	 */
	protected function get()
	{
		return $this->session()->get(self::STORE_KEY);
	}
	/**
	 * Set data session
	 * @param array $sess
	 * @return \BX\User\Store\SessionAuthStore
	 */
	protected function set($sess)
	{
		$this->session()->set(self::STORE_KEY,$sess);
		return $this;
	}
	/**
	 * Get unique is from session
	 * @return string
	 */
	protected function getUniqueId()
	{
		return sha1($this->session()->getSessionId());
	}
}