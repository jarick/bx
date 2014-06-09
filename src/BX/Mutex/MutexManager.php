<?php namespace BX\Mutex;
use BX\Base\Dictionary;
use BX\Config\Config;
use BX\Mutex\Adaptor\IMutexAdaptor;
use BX\Mutex\Adaptor\SystemMutex;
use BX\Mutex\Entity\MutexEntity;

class MutexManager #implements IMutexManager
{
	use \BX\String\StringTrait;
	/**
	 * @var Dictionary
	 */
	private $dictionary;
	/**
	 * @var IMutexAdaptor
	 */
	private $mutex = null;
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->dictionary = new Dictionary('BX\Mutex\Entity\MutexEntity');
	}
	/**
	 * Return mutex
	 *
	 * @return IMutexAdaptor
	 */
	public function adaptor()
	{
		if ($this->mutex === null){
			if (!Config::exists('mutex','class')){
				$this->mutex = new SystemMutex();
			}else{
				$reg = Config::get('mutex','class');
				$this->mutex = new $reg();
			}
		}
		return $this->mutex;
	}
	/**
	 * Acquire key
	 *
	 * @param string $key
	 * @param integer $max_acquire
	 * @return boolean
	 */
	public function acquire($key,$max_acquire = 1)
	{
		if ($this->string()->length($key) === 0){
			throw new \InvalidArgumentException('Key is not set');
		}
		$key = $this->string()->toUpper($key);
		if ($this->dictionary->has($key)){
			$entity = $this->dictionary->get($key);
		}else{
			$entity = new MutexEntity();
			$entity->generate($key,$max_acquire);
			if (Config::exists('mutex','perm')){
				$entity->permission = Config::get('mutex','perm');
			}
			$this->dictionary->add($key,$entity);
		}
		return $this->adaptor()->acquire($entity);
	}
	/**
	 * Release key
	 *
	 * @param string $key
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function release($key)
	{
		$key = $this->string()->toUpper($key);
		if (!$this->dictionary->has($key)){
			return true;
		}
		$entity = $this->dictionary->get($key);
		$this->dictionary->delete($key);
		return $this->adaptor()->release($entity);
	}
	/**
	 * Unlock all mutex
	 *
	 * @return boolean
	 */
	public function releaseAll()
	{
		foreach($this->dictionary as $entity){
			$this->adaptor()->release($entity);
		}
		return true;
	}
}