<?php namespace BX;
use InvalidArgumentException;

class Collection extends \SplObjectStorage
{
	private $type;
	/**
	 * Constructor
	 * @param type $type
	 */
	public function __construct($type)
	{
		$this->type = $type;
	}
	private function validateType($value)
	{
		if (!($value instanceof $this->type)){
			throw new InvalidArgumentException("{$value} is not type {$this->type}");
		}
		return true;
	}
	public function add($value)
	{
		if ($this->validateType($value)){
			$this->attach($value);
		}
	}
}