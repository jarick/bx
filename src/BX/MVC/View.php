<?php namespace BX\MVC;
use BX\Config\DICService;
use BX\MVC\Buffer;
use BX\MVC\Exception\Abort;
use BX\MVC\Exception\Exception;
use BX\MVC\Exception\PageNotFound;

class View implements IView, \ArrayAccess
{
	use \BX\Http\HttpTrait,
	 \BX\Engine\EngineTrait,
	 \BX\Event\EventTrait;
	/**
	 * @var array
	 */
	public $meta = [];
	/**
	 * Return buffer
	 *
	 * @return Buffer
	 */
	public function buffer()
	{
		if (DICService::get('buffer') === null){
			$manager = function(){
				return new Buffer();
			};
			DICService::set('buffer',$manager);
		}
		return DICService::get('buffer');
	}
	/**
	 * Load meta data from registry
	 * @return \BX\MVC\View
	 */
	public function loadMeta()
	{
		if (call_user_func_array(array('BX\Config\Config','exists'),func_get_args())){
			$this->meta = call_user_func_array(array('BX\Config\Config','get'),func_get_args());
		}
		return $this;
	}
	/**
	 * Is exists page
	 *
	 * @param string $path
	 * @return boolean
	 */
	public function exists($path)
	{
		return $this->engine()->exists($path);
	}
	/**
	 * Render page
	 *
	 * @param string $path
	 * @param array $params
	 * @return string
	 * @throws Exception
	 */
	public function render($path,array $params = [])
	{
		$this->fire('beforeRender',[$path,&$params]);
		$this->buffer()->start();
		$found = $this->engine()->render($this,$path,$params);
		$return = $this->buffer()->end();
		if (!$found){
			throw new PageNotFound();
		}
		$this->fire('afterRender',[$path,&$return]);
		return $return;
	}
	/**
	 * Abort page not found
	 *
	 * @throws PageNotFound
	 */
	public function throwPageNotFound()
	{
		throw new PageNotFound();
	}
	/**
	 * Abort request
	 *
	 * @throws Abort
	 */
	public function abort()
	{
		$this->send($this->buffer()->abort());
		throw new Abort();
	}
	/**
	 * Redirect
	 *
	 * @param string $url
	 * @param integer $status
	 * @throws Abort
	 */
	public function redirect($url,$status = 302)
	{
		$this->buffer()->flush();
		$this->response()->redirect($url,$status);
		throw new Abort();
	}
	/**
	 * Send request
	 *
	 * @param string $content
	 * @param integer $status
	 * @param array $headers
	 */
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
	/**
	 * Is set meta value
	 *
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->meta[$offset]);
	}
	/**
	 * Return meta value by key
	 *
	 * @param string $offset
	 * @return string|null
	 */
	public function offsetGet($offset)
	{
		return isset($this->meta[$offset]) ? $this->meta[$offset] : null;
	}
	/**
	 * Set meta
	 *
	 * @param string $offset
	 * @param string $value
	 */
	public function offsetSet($offset,$value)
	{
		$this->meta[$offset] = $value;
	}
	/**
	 * Un set meta
	 *
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->meta[$offset]);
	}
}