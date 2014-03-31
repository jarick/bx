<?php namespace BX\User\Store;

class AuthStoreTest extends \BX\Test
{
	use \BX\String\StringTrait;
	public function test()
	{
		$store = new ArrayAuthStore();
		$id = 1;
		$login = 'admin';
		$email = 'admin@email.no';
		$store->save($id,$login,$email);
		$this->assertTrue($store->exits());
		$this->assertEquals($id,$store->id);
		$this->assertEquals($login,$store->login);
		$this->assertEquals($email,$store->email);
		$this->assertTrue($this->string()->length($store->unique_id) > 0);
	}
}