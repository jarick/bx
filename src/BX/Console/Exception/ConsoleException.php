<?php namespace BX\Console\Exception;

class ConsoleException extends \Exception
{
	use \BX\Logger\LoggerTrait,
	 \BX\Event\EventTrait;
	/**
	 * @var \BX\Console\IWriter;
	 */
	private $writer;
	/**
	 * Constructor
	 * @param string $message
	 * @param \BX\Console\IWriter $writer
	 */
	public function __construct($message,$writer,$code = 0,$previous = null)
	{
		parent::__construct($message,$code,$previous);
		$this->writer = $writer;
	}
	/**
	 * Set file and line
	 * @param string $file
	 * @param string $line
	 */
	public function setDebugInfo($file,$line)
	{
		$this->file = $file;
		$this->line = $line;
	}
	/**
	 * Write exception
	 */
	public function render()
	{
		$this->log()->error($this->message.' FILE: '.$this->file.' LINE: '.$this->line);
		$this->fire('BeforeConsoleException');
		$this->writer->error($this->message.' FILE: '.$this->file.' LINE: '.$this->line);
		$this->fire('AfterConsoleException');
	}
}