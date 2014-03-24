<?php namespace BX\MVC\Manager;
use BX\Manager;
use BX\MVC\Buffer;
use BX\MVC\Exception\Abort;
use BX\MVC\Exception\Exception;
use BX\MVC\Widget;

class View extends Manager implements \ArrayAccess
{
	use \BX\Http\HttpTrait,
	 \BX\Engine\EngineTrait;
	private static $buffer;
	public $meta = [];
	public function init()
	{
		if (!isset(self::$buffer)){
			self::$buffer = new Buffer();
		}
	}
	public function buffer()
	{
		return self::$buffer;
	}
	public function loadMeta()
	{
		if (call_user_func_array(array('BX\Registry','exists'),func_get_args())){
			$this->meta = call_user_func_array(array('BX\Registry','get'),func_get_args());
		}
		return $this;
	}
	public function render($path,$params = [])
	{
		$this->fire('beforeRender',[$path,&$params]);
		$this->buffer()->start();
		$found = $this->engine()->render($this,$path,$params);
		$return = $this->buffer()->end();
		if (!$found){
			throw new Exception("Template $path not found",404);
		}
		$this->fire('afterRender',[$path,&$return]);
		return $return;
	}
	public function abort()
	{
		$this->send($this->buffer()->abort());
		throw new Abort();
	}
	public function redirect($url,$status = 302)
	{
		$this->buffer()->flush();
		$this->response()->redirect($url,$status);
		throw new Abort();
	}
	public function send($content,$status = false,array $headers = [])
	{
		$response = $this->response();
		if ($status > 0){
			$response->setStatus($status);
		}
		if (!isset($headers['Cache-Control'])){
			$response['Cache-Control'] = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
		}
		if (!isset($headers['Pragma'])){
			$response['Pragma'] = 'no-cache';
		}
		$response->send($content);
	}
	public function widget($widget,$params = [])
	{
		return Widget::widget($this,$params,$widget);
	}
	public function offsetExists($offset)
	{
		return isset($this->meta[$offset]);
	}
	public function offsetGet($offset)
	{
		return isset($this->meta[$offset]) ? $this->meta[$offset] : null;
	}
	public function offsetSet($offset,$value)
	{
		$this->meta[$offset] = $value;
	}
	public function offsetUnset($offset)
	{
		unset($this->meta[$offset]);
	}
}