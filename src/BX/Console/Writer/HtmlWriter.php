<?php namespace BX\Console\Writer;

class HtmlWriter implements IWriter
{
	/**
	 * Write safe message
	 *
	 * @param string $message
	 */
	public function html($message)
	{
		echo $message;
	}
	/**
	 * Write message
	 *
	 * @param string $message
	 */
	public function write($message)
	{
		echo nl2br($message);
	}
	/**
	 * Emulate read string
	 *
	 * @return string
	 */
	public function read()
	{
		echo 'y'.PHP_EOL;
		return 'y';
	}
	/**
	 * Write error message
	 *
	 * @param string $message
	 */
	public function error($message)
	{
		$this->write('Caught exception: ');
		$this->write($this->color($message,'red').PHP_EOL);
	}
	/**
	 * Write success message
	 *
	 * @param string $message
	 */
	public function success($message)
	{
		$this->write($this->color($message,'green').PHP_EOL);
	}
	/**
	 * Write color message
	 *
	 * @param string $message
	 * @param string $color
	 * @param string $background_color
	 * @return string
	 */
	public function color($message,$color,$background_color = false)
	{
		return "<span style='color:{$color};background-color:{$background_color}'>{$message}</span>";
	}
}