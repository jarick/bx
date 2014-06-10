<?php namespace BX\Http\Store;

class NativeSessionStore extends AbstractSessionStore
{
	/**
	 * Return session bag
	 *
	 * @return array
	 */
	protected function getSessionBag()
	{
		if ($this->bag === null){
			session_start();
			$this->bag = $_SESSION;
		}
		return $this->bag;
	}
	/**
	 * Save session bag
	 *
	 */
	protected function saveSessionBag()
	{
		$_SESSION = $this->bag;
	}
	/**
	 * Return session id
	 *
	 * @return string
	 */
	public function getId()
	{
		return session_id();
	}
}