<?php namespace BX\User\Store;
use BX\DbTest;
use BX\User\Entity\UserEntity;

class AuthStoreTest extends DbTest
{
	use \BX\String\StringTrait;
	public function test()
	{
		$array = new ArrayStore('121.0.0.1');
		$user = UserEntity::filter()->filter(['ID' => 1])->get();
		$array->save($user);
		$store = $array->current();
		$this->assertEquals(1,$store->id);
		$this->assertEquals('admin',$store->login);
		$this->assertEquals('no@email.com',$store->email);
	}
}