<?php namespace BX\Console;
use BX\Console\Exception\ConsoleException;
use BX\Base\Collection;
use \BX\Console\Writer\CliWriter;
use \BX\Console\Writer\HtmlWriter;
use \BX\Console\Writer\IWriter;
use \BX\Console\IConsoleController;

class ConsoleController implements IConsoleController
{
	use \BX\Config\ConfigTrait;
	/**
	 * @var Collection
	 */
	public $command;
	/**
	 * @var IWriter
	 */
	protected $writer = false;
	/**
	 * Set writer
	 *
	 * @param IWriter $writer
	 */
	public function setWriter($writer)
	{
		$this->writer = $writer;
		return $this;
	}
	/**
	 * Get writer
	 *
	 * @return IWriter
	 */
	public function getWriter()
	{
		return $this->writer;
	}
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->command = new Collection('BX\Console\Command\Console');
		if ($this->writer === false){
			if ($this->config()->exists('console','writer')){
				$class = $this->config()->get('console','writer');
				$this->setWriter(new $class());
			}else{
				if (php_sapi_name() === 'cli'){
					$this->setWriter(new CliWriter());
				}else{
					$this->setWriter(new HtmlWriter());
				}
			}
		}
	}
	/**
	 * Prepare argv argument
	 *
	 * @param string $io
	 */
	private function getArgvArray($io = false)
	{
		if ($io !== false){
			$argv_array = preg_split("/\s+/",'0 '.$io);
		}else{
			global $argv;
			$argv_array = $argv;
		}
		return $argv_array;
	}
	/**
	 * Render string
	 *
	 * @global array $argv
	 * @param string $io
	 * @throws \InvalidArgumentException
	 */
	public function exec($io = false)
	{
		$argv_array = $this->getArgvArray($io);
		try{
			if (!isset($argv_array[1])){
				throw new \InvalidArgumentException('Command is not set');
			}
			$name = $argv_array[1];
			$has_command = false;
			foreach($this->command as $command){
				if ($command->command() === $name){
					$command->setWriter($this->writer)->run(array_slice($argv_array,2));
					$has_command = true;
				}
			}
			if (!$has_command){
				throw new \InvalidArgumentException("Unknow command `$name`");
			}
		}catch (ConsoleException $e){
			$e->render();
		}catch (\Exception $e){
			$exception = new ConsoleException($e->getMessage(),$this->writer);
			$exception->setDebugInfo($e->getFile(),$e->getLine());
			$exception->render();
		}
	}
}