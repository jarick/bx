<?php namespace BX\Mutex;
use BX\Base\Dictionary;
use BX\Base\Registry;
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
	 * Get mutex
	 * @return IMutexAdaptor
	 */
	public function adaptor()
	{
		if ($this->mutex === null){
			if (!Registry::exists('mutex','class')){
				$this->mutex = new SystemMutex();
			}else{
				$reg = Registry::get('mutex','class');
				$this->mutex = new $reg();
			}
		}
		return $this->mutex;
	}
	/**
	 * Acquire key
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
			if (Registry::exists('mutex','perm')){
				$entity->permission = Registry::get('mutex','perm');
			}
			$this->dictionary->add($key,$entity);
		}
		return $this->adaptor()->acquire($entity);
	}
	/**
	 * Release key
	 * @param string $key
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function release($key)
	{
		$key = $this->string()->toUpper($key);
		if (!$this->dictionary->has($key)){
			throw new \InvalidArgumentException('Mutex is not acquire');
		}
		$entity = $this->dictionary->get($key);
		$this->dictionary->delete($key);
		return $this->adaptor()->release($entity);
	}
	/**
	 * Unlock all mutex
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