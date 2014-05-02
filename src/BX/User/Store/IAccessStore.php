<?php namespace BX\User\Store;

interface IAccessStore
{
	public function add(\BX\User\Entity\AccessEntity $entity);
	public function get($guid,$token);
	public function clear($user_id);
	public function clearOld($day);
}