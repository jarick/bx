<?php namespace BX\Console\Writer;

class CliWriter implements IWriter
{
	/**
	 * Get background color array
	 * @return array
	 */
	protected function getBackgroundColorArray()
	{
		return [
			'black'		 => '40',
			'red'		 => '41',
			'green'		 => '42',
			'yellow'	 => '43',
			'blue'		 => '44',
			'magenta'	 => '45',
			'cyan'		 => '46',
			'light_gray' => '47',
		];
	}
	/**
	 * Get color array
	 * @return array
	 */
	protected function getColorArray()
	{
		return [
			'black'			 => '0;30',
			'dark_gray'		 => '1;30',
			'blue'			 => '0;34',
			'light_blue'	 => '1;34',
			'green'			 => '0;32',
			'light_green'	 => '1;32',
			'cyan'			 => '0;36',
			'light_cyan'	 => '1;36',
			'red'			 => '0;31',
			'light_red'		 => '1;31',
			'purple'		 => '0;35',
			'light_purple'	 => '1;35',
			'brown'			 => '0;33',
			'yellow'		 => '1;33',
			'light_gray'	 => '0;37',
			'white'			 => '1;37',
		];
	}
	/**
	 * Write message
	 * @param string $message
	 */
	public function write($message)
	{
		fwrite(STDOUT,$message);
	}
	/**
	 * Emulate read string
	 * @return string
	 */
	public function read()
	{
		return trim(fgets(STDIN));
	}
	/**
	 * Write error message
	 * @param string $message
	 */
	public function error($message)
	{
		$this->write('Caught exception: ');
		$this->write($this->color($message,'red').PHP_EOL);
	}
	/**
	 * Write success message
	 * @param string $message
	 */
	public function success($message)
	{
		$this->write($this->color($message,'green').PHP_EOL);
	}
	/**
	 * Write color message
	 * @param string $message
	 * @param string $color
	 * @param string $bgcolor
	 * @return string
	 */
	public function color($message,$color,$bgcolor = false)
	{
		$string = '';
		$color_array = $this->getColorArray();
		if (isset($color_array[$color])){
			$string .= "\033[".$color_array[$color]."m";
		}
		$bgcolor_array = $this->getBackgroundColorArray();
		if ($bgcolor !== false && isset($bgcolor_array[$bgcolor])){
			$string .= "\033[".$bgcolor_array[$bgcolor]."m";
		}
		return $string.$message."\033[0m";
	}
}