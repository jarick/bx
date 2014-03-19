<?php namespace BX\Http\Manager;
use BX\Manager;
use BX\Http\IRequest;
use BX\Http\Dictionary;

class Request extends Manager implements IRequest
{
	private $get = false;
	private $post = false;
	private $files = false;
	private $server = false;
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
	 * Get query
	 * @return Dictionary
	 */
	public function query()
	{
		if ($this->get === false){
			$this->get = $_GET;
		}
		if (is_array($this->get)){
			$this->get = new Dictionary($this->get,true);
		}
		return $this->get;
	}
	/**
	 * Get post
	 * @return Dictionary
	 */
	public function post()
	{
		if ($this->post === false){
			$this->post = $_POST;
		}
		if (is_array($this->post)){
			$this->post = new Dictionary($this->post,true);
		}
		return $this->post;
	}
	/**
	 * Get files
	 * @return Dictionary
	 */
	public function files()
	{
		if ($this->files === false){
			$this->files = $_FILES;
		}
		if (is_array($this->files)){
			$this->files = new Dictionary($this->files,true);
		}
		return $this->files;
	}
	/**
	 * Get server
	 * @return Dictionary
	 */
	public function server()
	{
		if ($this->server === false){
			$this->server = $_SERVER;
		}
		if (is_array($this->server)){
			$this->server = new Dictionary($this->server,true);
		}
		return $this->server;
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
	 * @throws \LogicException
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
			foreach ($nead_keys as $key){
				if (!$this->server()->has($key)){
					throw new \LogicException("Server array doesn't have key `$key`");
				}
			}
		}
	}
}