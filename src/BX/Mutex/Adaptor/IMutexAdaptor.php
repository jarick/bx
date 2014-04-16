<?php namespace BX\Mutex\Adaptor;

interface IMutexAdaptor
{
	public function acquire(\BX\Mutex\Entity\MutexEntity $mutex);
	public function release(\BX\Mutex\Entity\MutexEntity $mutex);
}