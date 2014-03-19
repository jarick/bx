<?php namespace BX\Console\Command;
use BX\Console\IWriter;
use BX\Base;

abstract class Console extends Base
{
	/**
	 * @var IWriter
	 */
	protected $writer = false;
	/**
	 * Set writer
	 * @param type $writer
	 */
	public function setWriter($writer)
	{
		$this->writer = $writer;
		return $this;
	}
	abstract public function run(array $args);
	abstract public function command();
}