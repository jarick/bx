<?php namespace BX\Http\Manager;
use BX\Http\Cookie;
use BX\Http\Dictionary;
use BX\Http\IRequest;
use BX\Manager;
use LogicException;

class Request extends Manager implements IRequest
{
	/**
	 * @var Dictionary|array
	 */
	private $get = null;
	/**
	 * @var Dictionary|array
	 */
	private $post = null;
	/**
	 * @var Dictionary|array
	 */
	private $files = null;
	/**
	 * @var Dictionary|array
	 */
	private $server = null;
	/**
	 * @var Cookie|array
	 */
	private $cookie = null;
	/**
	 * Set query
	 * @param array|Dictionary $get
	 */
	public function setQuery($get)
	{
		$this->get = $get;
	}
	/**
	 * Set post
	 * @param array|Dictionary $post
	 */
	public function setPost($post)
	{
		$this->post = $post;
	}
	/**
	 * Set files
	 * @param array|Dictionary $files
	 */
	public function setFiles($files)
	{
		$this->files = $files;
	}
	/**
	 * Set server array
	 * @param array|Dictionary $server
	 */
	public function setServer($server)
	{
		$this->server = $server;
	}
	/**
	 * Get query dictionary
	 * @return Dictionary
	 */
	public function query()
	{
		if ($this->get === null){
			$this->get = INPUT_GET;
		}
		if (!is_object($this->get)){
			$this->get = new Dictionary($this->get);
		}
		return $this->get;
	}
	/**
	 * Get post dictionary
	 * @return Dictionary
	 */
	public function post()
	{
		if ($this->post === null){
			$this->post = INPUT_POST;
		}
		if (!is_object($this->post)){
			$this->post = new Dictionary($this->post);
		}
		return $this->post;
	}
	/**
	 * Get files dictionary
	 * @return Dictionary
	 */
	public function files()
	{
		if ($this->files === null){
			$this->files = $_FILES;
		}
		if (is_array($this->files)){
			$this->files = new Dictionary($this->files);
		}
		return $this->files;
	}
	/**
	 * Get server dictionary
	 * @return Dictionary
	 */
	public function server()
	{
		if ($this->server === null){
			$this->server = INPUT_SERVER;
		}
		if (!is_object($this->server)){
			$this->server = new Dictionary($this->server);
		}
		return $this->server;
	}
	/**
	 * Get server dictionary
	 * @return Dictionary
	 */
	public function cookie()
	{
		if ($this->cookie === null){
			$this->cookie = INPUT_COOKIE;
		}
		if (!is_object($this->cookie)){
			$this->cookie = new Cookie($this->cookie);
		}
		return $this->cookie;
	}
	/**
	 * Get request method
	 * @return string
	 */
	public function getRequestMethod()
	{
		return $this->server()->get('REQUEST_METHOD');
	}
	/**
	 * Get host
	 * @return string
	 */
	public function getHost()
	{
		return $this->server()->get('SERVER_NAME');
	}
	/**
	 * Get path info
	 * @return string
	 */
	public function getPathInfo()
	{
		return $this->server()->get('PATH_INFO');
	}
	/**
	 * Get path info with index page
	 * @return string
	 */
	public function getPathInfoWithInex()
	{
		$path = $this->getPathInfo();
		$path = '/'.ltrim($path,'/');
		if (substr($path,-1) === '/'){
			$path .= 'index';
		}
		return $path;
	}
	/**
	 * Get query string
	 * @return string
	 */
	public function getQueryString()
	{
		return $this->server()->get('QUERY_STRING');
	}
	/**
	 * Get script
	 * @return string
	 */
	public function getScript()
	{
		return $this->server()->get('SCRIPT_NAME');
	}
	/**
	 * init
	 * @throws LogicException
	 */
	public function init()
	{
		if ($this->isDevMode()){
			$nead_keys = [
				'REQUEST_METHOD',
				'SCRIPT_NAME',
				'PATH_INFO',
				'QUERY_STRING',
				'SERVER_NAME',
			];
			foreach($nead_keys as $key){
				if (!$this->server()->has($key)){
					throw new LogicException("Server array doesn't have key `$key`");
				}
			}
		}
	}
}