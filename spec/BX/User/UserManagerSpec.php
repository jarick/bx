<?php namespace spec\BX\User;
use BX\DB\Schema;
use PhpSpec\ObjectBehavior;

class UserManagerSpec extends ObjectBehavior
{
	use \BX\String\StringTrait,
	 \BX\DB\DBTrait;
	function let()
	{
		Schema::loadFromYamlFile();
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\User\UserManager');
	}
	function it_add()
	{
		$save = [
			'LOGIN'			 => 'user2',
			'DISPLAY_NAME'	 => 'user2',
			'EMAIL'			 => 'no2@email.com',
			'REGISTERED'	 => 'Y',
			'ACTIVE'		 => 'Y',
			'PASSWORD'		 => 'qwerty',
		];
		$this->add($save)->shouldBeLike(2);
		unset($save['PASSWORD']);
		$save['CODE'] = 'user2';
		$this->finder()->filter(['=LOGIN' => 'user2'])->get()->getData()->shouldDbResult($save);
	}
	function it_update()
	{
		$save = [
			'LOGIN'			 => 'user2',
			'DISPLAY_NAME'	 => 'user2',
			'EMAIL'			 => 'no2@email.com',
			'REGISTERED'	 => 'Y',
			'ACTIVE'		 => 'Y',
			'PASSWORD'		 => 'qwerty',
		];
		$this->update(1,$save)->shouldBe(true);
		unset($save['PASSWORD']);
		$this->finder()->filter(['ID' => 1])->get()->getData()->shouldDbResult($save);
	}
	function it_filter()
	{
		$this->finder()->shouldHaveType('\BX\DB\Filter\SqlBuilder');
	}
	function it_delete()
	{
		$this->delete(1)->shouldBe(true);
		$sql = 'SELECT * FROM tbl_user';
		$count = $this->db()->query($sql)->count();
		if ($count > 0){
			throw new \RuntimeException('Test fall');
		}
	}
	function getMatchers()
	{
		return [
			'dbResult' => function($object,$array){
			foreach($array as $key => $value){
				if ($object[$key] != $value){
					return false;
				}
			}
			return true;
		}
		];
	}
}