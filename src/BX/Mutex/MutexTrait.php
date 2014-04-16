<?php namespace BX\Mutex;

trait MutexTrait
{
	/**
	 * Get mutex manager
	 * @return IMutexManager
	 */
	public function mutex()
	{
		if (\BX\Base\DI::get('mutex') === null){
			\BX\Base\DI::set('mutex',new MutexManager());
		}
		return \BX\Base\DI::get('mutex');
	}
}