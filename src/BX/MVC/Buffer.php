<?php namespace BX\MVC;

class Buffer
{
	/**
	 * @var integer
	 */
	protected $stack = 0;
	/**
	 * Get stack
	 * @return integer
	 */
	public function getStack()
	{
		return $this->stack;
	}
	/**
	 * Start buffer
	 * @return integer level node
	 */
	public function start()
	{
		ob_start();
		ob_implicit_flush(false);
		return $this->stack++;
	}
	/**
	 * Flush buffer
	 * @return boolean success
	 */
	public function flush()
	{
		for(; $this->stack > 0; $this->stack--){
			ob_end_clean();
		}
		return true;
	}
	/**
	 * End buffer
	 * @return string
	 */
	public function end()
	{
		if ($this->stack > 0){
			$result = ob_get_contents();
			ob_end_clean();
			$this->stack--;
			return $result;
		}else{
			return '';
		}
	}
	/**
	 * Abort buffer
	 * @return string
	 */
	public function abort()
	{
		$result = '';
		for(; $this->stack > 0; $this->stack--){
			$result = ob_get_contents().$result;
			ob_end_clean();
		}
		return $result;
	}
}