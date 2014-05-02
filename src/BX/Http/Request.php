<?php namespace BX\Http;
use BX\Base\Registry;
use BX\Http\CookieStore;
use BX\Http\IRequest;
use BX\Http\Store;

class Request implements IRequest
{
	/**
	 * @var Store|array
	 */
	private $get = null;
	/**
	 * @var Store|array
	 */
	private $post = null;
	/**
	 * @var Store|array
	 */
	private $files = null;
	/**
	 * @var Store|array
	 */
	private $server = null;
	/**
	 * @var Cookie|array
	 */
	private $cookie = null;
	/**
	 * Set query
	 * @param array|Store $get
	 */
	public function setQuery($get)
	{
		$this->get = $get;
	}
	/**
	 * Set post
	 * @param array|Store $post
	 */
	public function setPost($post)
	{
		$this->post = $post;
	}
	/**
	 * Set files
	 * @param array|Store $files
	 */
	public function setFiles($files)
	{
		$this->files = $files;
	}
	/**
	 * Set server array
	 * @param array|Store $server
	 */
	public function setServer($server)
	{
		$this->server = $server;
	}
	/**
	 * Get query dictionary
	 * @return Store
	 */
	public function query()
	{
		if ($this->get === null){
			$this->get = (array)filter_input_array(INPUT_GET);
		}
		if (!is_object($this->get)){
			$this->get = new Store($this->get);
		}
		return $this->get;
	}
	/**
	 * Get post dictionary
	 * @return Store
	 */
	public function post()
	{
		if ($this->post === null){
			$this->post = (array)filter_input_array(INPUT_POST);
		}
		if (!is_object($this->post)){
			$this->post = new Store($this->post);
		}
		return $this->post;
	}
	/**
	 * Get files dictionary
	 * @return Store
	 */
	public function files()
	{
		if ($this->files === null){
			$this->files = $_FILES;
		}
		if (is_array($this->files)){
			$this->files = new Store($this->files);
		}
		return $this->files;
	}
	/**
	 * Get server dictionary
	 * @return Store
	 */
	public function server()
	{
		if ($this->server === null){
			$this->server = (array)filter_input_array(INPUT_SERVER);
		}
		if (!is_object($this->server)){
			$this->server = new Store($this->server);
		}
		return $this->server;
	}
	/**
	 * Get server dictionary
	 * @return CookieStore
	 */
	public function cookie()
	{
		if ($this->cookie === null){
			$this->cookie = filter_input_array(INPUT_COOKIE);
		}
		if (!is_object($this->cookie)){
			$this->cookie = new CookieStore($this->cookie);
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
		return explode('?',$this->server()->get('REQUEST_URI'))[0];
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
	 * Constructor
	 * @throws RuntimeException
	 */
	public function __construct()
	{
		if (Registry::isDevMode()){
			$nead_keys = [
				'REQUEST_METHOD',
				'SCRIPT_NAME',
				'REQUEST_URI',
				'QUERY_STRING',
				'SERVER_NAME',
			];
			foreach($nead_keys as $key){
				if (!$this->server()->has($key)){
					throw new \RuntimeException("Server array doesn't have key `$key`");
				}
			}
		}
	}
}