<?php namespace BX\Mutex;

interface IMutexManager
{
	public function acquire($key,$max_acquire = 1);
	public function release($key);
	public function releaseAll();
}