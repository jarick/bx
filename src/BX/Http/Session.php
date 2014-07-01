<?php namespace BX\Http;
use BX\Http\Store\NativeSessionStore;

class Session implements \ArrayAccess
{
	use \BX\Config\ConfigTrait;
	const VALUE = 'value';
	const IS_MULTY = 'is_multy';
	const FLASH_KEY = 'BX.Http.Flash';
	/**
	 * @var IRequest
	 */
	private $request;
	/**
	 * @var array
	 */
	protected static $flash = [];
	/**
	 * @var array
	 */
	protected static $save_flash = [];
	/**
	 * @var boolean
	 */
	protected static $start = false;
	/**
	 * @var AbstractSessionStore
	 */
	protected $store = null;
	/**
	 * Constructor
	 *
	 * @param \BX\Http\IRequest $request
	 */
	public function __construct(IRequest $request)
	{
		$this->request = $request;
	}
	/**
	 * Return flash store
	 *
	 * @return Store\AbstractSessionStore
	 */
	public function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('session','store')){
				$store = $this->config()->get('session','store');
				switch ($store){
					case 'native': $this->store = new NativeSessionStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new NativeSessionStore();
			}
		}
		return $this->store;
	}
	/**
	 * Return flash message
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param boolean $multy_hits
	 * @return null
	 */
	public function getFlash($key)
	{
		if (array_key_exists($key,self::$flash)){
			return self::$flash[$key];
		}
		return null;
	}
	/**
	 * Set flash message
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param boolean $multy_hits
	 * @return Session
	 */
	public function setFlash($key,$value,$multy_hits = false)
	{
		self::$flash[$key] = $value;
		self::$save_flash[$key] = array(
			self::VALUE		 => $value,
			self::IS_MULTY	 => $multy_hits
		);
		$store = $this->store();
		$store[self::FLASH_KEY] = self::$save_flash;
		return $this;
	}
	/**
	 * Get session token
	 *
	 * @return string
	 */
	public function getId()
	{
		return crc32($this->store()->getId());
	}
	/**
	 * Flush session
	 *
	 */
	public function save()
	{
		$this->store()->save();
	}
	/**
	 * Load session
	 */
	public function load()
	{
		if (!self::$start){
			$store = $this->store();
			$save = $store[self::FLASH_KEY];
			if (!empty($save)){
				foreach($save as $key => $flash){
					self::$flash[$key] = $flash[self::VALUE];
					if ($flash[self::IS_MULTY] === true){
						self::$save_flash[$key] = $flash;
					}
				}
			}
			$store[self::FLASH_KEY] = self::$save_flash;
			self::$start = true;
		}
	}
	/**
	 * Set value in session
	 *
	 * @param string $key
	 * @param string $value
	 * @return Session
	 */
	public function set($key,$value)
	{
		$this->store()->offsetSet($key,$value);
		return $this;
	}
	/**
	 * Return value by key
	 *
	 * @param string $key
	 * @return Session
	 */
	public function get($key)
	{
		return $this->store()->offsetGet($key);
	}
	/**
	 * Return is has key in store
	 *
	 * @param string $key
	 */
	public function has($key)
	{
		return $this->store()->offsetExists($key);
	}
	/**
	 * Remove store by key
	 *
	 * @param string $key
	 * @return Session
	 */
	public function remove($key)
	{
		$this->store()->offsetUnset($key);
		return $this;
	}
	/**
	 * Exists key in store
	 *
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return $this->store()->offsetExists($offset);
	}
	/**
	 * Return value by key from store
	 *
	 * @param string $offset
	 * @return string
	 */
	public function &offsetGet($offset)
	{
		return $this->store()->offsetGet($offset);
	}
	/**
	 * Set value in store
	 *
	 * @param string $offset
	 * @param string $value
	 */
	public function offsetSet($offset,$value)
	{
		$this->store()->offsetSet($offset,$value);
	}
	/**
	 * Unset value in store by key
	 *
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		$this->store()->offsetUnset($offset);
	}
}