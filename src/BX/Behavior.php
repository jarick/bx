<?php
namespace BX;

class Behavior
{
	protected $owner;
	/**
	 * init
	 */
	public function init(){}
	/**
	 * Get owner
	 * @return Entity
	 */
	public function getOwner()
	{
		return $this->owner;
	}
	/**
	 * Attach owner
	 * @param Entity $owner
	 */
	public function attach($owner)
	{
		$this->owner = $owner;
	}
	/**
	 * Detach owner
	 */
	public function detach()
	{
		$this->owner = null;
	}
}