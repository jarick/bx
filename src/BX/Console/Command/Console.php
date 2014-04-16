<?php namespace BX\Console\Command;
use BX\Console\Writer\IWriter;

abstract class Console
{
	/**
	 * @var IWriter
	 */
	protected $writer = false;
	/**
	 * Set writer
	 * @param IWriter2 $writer
	 */
	public function setWriter(IWriter2 $writer)
	{
		$this->writer = $writer;
		return $this;
	}
	abstract public function run(array $args);
	abstract public function command();
}