<?php namespace spec\BX\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserGroupMemberManagerSpec extends ObjectBehavior
{
	function let()
	{
		\BX\DB\Schema::loadFromYamlFile();
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\User\UserGroupMemberManager');
	}
	function it_test()
	{
		$this->delete(1,1)->shouldBe(true);
		$this->finder()->count()->shouldBeLike(0);
		$this->add(1,1)->shouldBe(1);
		$should = [
			'USER_ID'	 => 1,
			'GROUP_ID'	 => 1,
		];
		$this->finder()->get()->getData()->shouldDbResult($should);
	}
	function getMatchers()
	{
		return [
			'dbResult' => function($object,$array){
			foreach($array as $key => $value){
				if (isset($object[$key])){
					$new_value = $object[$key];
				}else{
					$new_value = null;
				}
				if ($new_value != $value){
					return false;
				}
			}
			return true;
		}
		];
	}
}