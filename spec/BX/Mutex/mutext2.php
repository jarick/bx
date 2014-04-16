<?php
$memcache_obj = memcache_connect("localhost",11211);
$name = 'test';
$key = abs(crc32($name));
$file = sys_get_temp_dir()."/{$key}.tmp";
if (!file_exists($file)){
	file_put_contents($file,'');
}
$key = ftok($file,'m');
$sem = sem_get($key,1,0666,1);
if ($sem === false){
	throw new RuntimeException('Error geting semaphore');
}
if (!sem_acquire($sem)){
	throw new RuntimeException('Error locking mutex');
}
$val1 = memcache_get($memcache_obj,'var_key');
memcache_set($memcache_obj,'var_key',$val1.'|a',false,3000);
usleep(50000);
$val = memcache_get($memcache_obj,'var_key');
memcache_set($memcache_obj,'var_key',$val.'|b',false,3000);
if (!sem_release($sem)){
	throw new RuntimeException('Error unlocking mutex');
}
unlink($file);
