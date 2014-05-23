<?php namespace BX\Cache;

interface ICacheManager
{
	public function enable();
	public function disable();
	public function setTags($ns,$tags);
	public function get($unique_id,$ns = 'base');
	public function set($unique_id,$value,$ns = 'base',$ttl = 3600,$tags = []);
	public function remove($unique_id,$ns = 'base');
	public function removeByNamespace($ns);
	public function clearByTags($tags);
	public function flush();
}