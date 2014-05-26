<?php namespace BX\Cache;
use Illuminate\Cache\ApcStore;
use Illuminate\Cache\ApcWrapper;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\MemcachedStore;
use Illuminate\Cache\StoreInterface;
use Illuminate\Cache\WinCacheStore;
use Illuminate\Cache\XCacheStore;

class CacheManager implements ICacheManager
{
	use \BX\Event\EventTrait,
	 \BX\Config\ConfigTrait;
	/**
	 * @var boolean
	 */
	private $enable = true;
	/**
	 * @var StoreInterface
	 */
	private $store = null;
	/**
	 * Set store
	 *
	 * @param \Illuminate\Cache\StoreInterface $store
	 * @return \BX\Cache\CacheManager
	 */
	public function setStore(StoreInterface $store)
	{
		$this->store = $store;
		return $this;
	}
	/**
	 * get cache adaptor
	 * @return StoreInterface
	 * @throws InvalidArgumentException
	 */
	public function adaptor()
	{
		if ($this->store === null){
			if (!$this->config()->exists('cache')){
				$this->store = new ArrayStore();
			}else{
				$cache = $this->config()->get('cache','type');
				switch ($cache){
					case 'wincache':
						$this->store = new WinCacheStore();
						break;
					case 'xcache':
						$this->store = new XCacheStore();
						break;
					case 'array':
						$this->store = new ArrayStore();
						break;
					case 'apc':
						$this->store = new ApcStore(new ApcWrapper());
						break;
					case 'memcache':
						$memcache = new Memcached();
						$host = $this->config()->get('cache','host');
						$port = $this->config()->get('cache','port');
						$memcache->addServer($host,$port);
						$this->store = new MemcachedStore($memcache);
						break;
					default:
						$this->store = new $cache();
						break;
				}
			}
		}
		return $this->store;
	}
	/**
	 * Enable timezone
	 * @return boolean
	 */
	public function enable()
	{
		$this->enable = true;
		return true;
	}
	/**
	 * Disable timezone
	 * @return boolean
	 */
	public function disable()
	{
		$this->enable = false;
		return true;
	}
	/**
	 * Set cache tag
	 *
	 * @param string $ns
	 * @param array|string $tags
	 * @return boolean
	 */
	public function setTags($ns,&$tags)
	{
		$tags = (array)$tags;
		if (empty($tags)){
			throw new InvalidArgumentException('Set empty tags');
		}
		if ($this->enable){
			$tags = array_merge($tags,(array)$this->adaptor()->get($ns));
			$this->adaptor()->forever($ns,$tags);
		}
		return true;
	}
	/**
	 * Get cache
	 *
	 * @param string $unique_id
	 * @param string $ns
	 * @return null|string
	 */
	public function get($unique_id,$ns = 'base')
	{
		if ($this->enable){
			$tags = $this->adaptor()->get($ns);
			if ($tags === null || empty($tags)){
				return null;
			}else{
				return $this->adaptor()->tags($tags)->get($unique_id);
			}
		}
		return null;
	}
	/**
	 * Set cache
	 *
	 * @param string $unique_id
	 * @param mixed $value
	 * @param string $ns
	 * @param integer $ttl
	 * @param array|string $tags
	 */
	public function set($unique_id,$value,$ns = 'base',$ttl = 3600,$tags = [])
	{
		if ($this->enable){
			if (is_string($tags)){
				$tags = (array)$tags;
			}elseif (empty($tags)){
				$tags = ['default'];
			}
			$this->setTags($ns,$tags);
			$this->adaptor()->tags($tags)->put($unique_id,$value,$ttl);
		}
		return true;
	}
	/**
	 * Remove cache
	 *
	 * @param string $unique_id
	 * @param string $ns
	 * @return mixed
	 */
	public function remove($unique_id,$ns = 'base')
	{
		if ($this->enable){
			$tags = $this->adaptor()->get($ns);
			if ($tags !== null){
				$this->adaptor()->tags($tags)->forget($unique_id);
			}
		}
		return true;
	}
	/**
	 * Remove cache by namespace
	 *
	 * @param string $ns
	 * @return boolean
	 */
	public function removeByNamespace($ns)
	{
		if ($this->enable){
			$this->adaptor()->forget($ns);
		}
		return true;
	}
	/**
	 * Clear cache by tags
	 *
	 * @param boolean
	 */
	public function clearByTags($tags)
	{
		if ($this->enable){
			$tags = (array)$tags;
			if (isset($tags[0]) && is_array($tags[0])){
				$tags = $tags[0];
			}
			if (!empty($tags)){
				$this->adaptor()->tags($tags)->flush();
			}else{
				throw new \InvalidArgumentException('Tags is not empty');
			}
		}
		return true;
	}
	/**
	 * Flush cache
	 *
	 * @return boolean
	 */
	public function flush()
	{
		if ($this->enable){
			if ($this->fire('cache.manager.cache.flush') !== false){
				$this->adaptor()->flush();
			}
		}
		return true;
	}
}