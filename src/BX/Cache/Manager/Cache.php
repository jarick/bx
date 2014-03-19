<?php namespace BX\Cache\Manager;
use BX\Manager;
use BX\Registry;
use Illuminate\Cache\ApcStore;
use Illuminate\Cache\ApcWrapper;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\MemcachedStore;
use Illuminate\Cache\StoreInterface;
use Illuminate\Cache\WinCacheStore;
use Illuminate\Cache\XCacheStore;

class Cache extends Manager
{
	/**
	 * @var boolean
	 */
	private static $enable = true;
	/**
	 * @var StoreInterface
	 */
	private static $store = null;
	/**
	 * get cache adaptor
	 * @return StoreInterface
	 * @throws InvalidArgumentException
	 */
	public function adaptor()
	{
		if (self::$store === null){
			if (!Registry::exists('cache')){
				self::$store = new ArrayStore();
			} else{
				$cache = Registry::get('cache','type');
				switch ($cache){
					case 'wincache':
						self::$store = new WinCacheStore();
						break;
					case 'xcache':
						self::$store = new XCacheStore();
						break;
					case 'array':
						self::$store = new ArrayStore();
						break;
					case 'apc':
						self::$store = new ApcStore(new ApcWrapper());
						break;
					case 'memcache':
						$oMemcache = new Memcached();
						$oMemcache->addServer(Registry::get('cache','host'),Registry::get('cache','port'));
						self::$store = new MemcachedStore($oMemcache);
						break;
					default:
						if (is_string($cache)){
							if (class_exists($cache)){
								self::$store = new $cache();
							} else{
								throw new \InvalidArgumentException("Cache adaptor `$cache` is not exists");
							}
						} elseif (self::$store instanceof StoreInterface){
							self::$store = $cache;
						} else{
							throw new \InvalidArgumentException("Class `".get_class($cache)."` is not interface of StoreInterface");
						}
						break;
				}
			}
		}
		return self::$store;
	}
	/**
	 * Enable timezone
	 */
	public function enable()
	{
		self::$enable = true;
	}
	/**
	 * Disable timezone
	 */
	public function disable()
	{
		self::$enable = false;
	}
	/**
	 * Set cache tag
	 * @param string $ns
	 * @param array $tags
	 * @return array
	 */
	public function setTags($ns,array $tags)
	{
		if (empty($tags)){
			throw new InvalidArgumentException('Set empty tags');
		}
		if (self::$enable){
			$tags = array_merge($tags,(array) $this->adaptor()->get($ns));
			$this->adaptor()->forever($ns,$tags);
		}
		return $tags;
	}
	/**
	 * Get cache
	 * @param string $unique_id
	 * @param string $ns
	 * @return null|string
	 */
	public function get($unique_id,$ns = 'base')
	{
		if (self::$enable){
			$tags = $this->adaptor()->get($ns);
			if ($tags === null || empty($tags)){
				return null;
			} else{
				return $this->adaptor()->tags($tags)->get($unique_id);
			}
		}
		return null;
	}
	/**
	 * Set cache
	 * @param string $unique_id
	 * @param mixed $value
	 * @param string $ns
	 * @param integer $ttl
	 * @param array|string $tags
	 */
	public function set($unique_id,$value,$ns = 'base',$ttl = 3600,$tags = [])
	{
		if (self::$enable){
			if (is_string($tags)){
				$tags = (array) $tags;
			} elseif (empty($tags)){
				$tags = ['default'];
			}
			$tags = $this->setTags($ns,$tags);
			$this->adaptor()->tags($tags)->put($unique_id,$value,$ttl);
		}
	}
	/**
	 * Remove cache
	 * @param string $unique_id
	 * @param string $ns
	 * @return mixed
	 */
	public function remove($unique_id,$ns = 'base')
	{
		if (self::$enable){
			$tags = $this->adaptor()->get($ns);
			if ($tags !== null){
				$this->adaptor()->tags($tags)->forget($unique_id);
			}
		}
	}
	/**
	 * Remove cache by namespace
	 * @param string $ns
	 */
	public function removeByNamespace($ns)
	{
		$this->adaptor()->forget($ns);
	}
	/**
	 * Clear cache by tags
	 * @param array|string $aTags
	 */
	public function clearByTags()
	{
		$tags = func_get_args();
		if (isset($tags[0]) && is_array($tags[0])){
			$tags = $tags[0];
		}
		if (!empty($tags)){
			$this->adaptor()->tags($tags)->flush();
		} else{
			throw new \InvalidArgumentException('Tags is not empty');
		}
	}
	/**
	 * Flush cache
	 */
	public function flush()
	{
		if ($this->fire('cache.manager.cache.flush') !== false){
			$this->adaptor()->flush();
		}
	}
}