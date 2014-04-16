<?php namespace BX\Mutex\Adaptor;
use BX\Mutex\Entity\MutexEntity;

class SystemMutex implements \BX\Mutex\Adaptor\IMutexAdaptor
{
	const FILE = 'file';
	const SEM = 'sem';
	/**
	 * Acquire
	 * @param \BX\Mutex\Entity\MutexEntity $mutex
	 * @return boolean
	 */
	public function acquire(MutexEntity $mutex)
	{
		if (strtoupper(substr(PHP_OS,0,3)) === 'WIN'){
			return true;
		}
		$file = sys_get_temp_dir()."/{$mutex->key}.tmp";
		if (!file_exists($file)){
			file_put_contents($file,'');
		}
		$key = ftok($file,'m');
		$sem = sem_get($key,$mutex->max_acquire,$mutex->permission);
		$mutex->setMeta([self::FILE => $file,self::SEM => $sem]);
		return sem_acquire($sem);
	}
	/**
	 * Release
	 * @param \BX\Mutex\Entity\MutexEntity $mutex
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public function release(MutexEntity $mutex)
	{
		$meta = $mutex->getMeta();
		if ($meta === null){
			throw new \InvalidArgumentException('Mutex is not acquire');
		}
		if (strtoupper(substr(PHP_OS,0,3)) === 'WIN'){
			return true;
		}
		$return = sem_release($meta[self::SEM]);
		unlink($meta[self::FILE]);
		return $return;
	}
}