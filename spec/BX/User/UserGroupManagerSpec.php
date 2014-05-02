<?php namespace spec\BX\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserGroupManagerSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\User\UserGroupManager');
	}
	function let()
	{
		\BX\DB\Schema::loadFromYamlFile();
	}
	function it_add()
	{
		$save = [
			'ACTIVE'		 => 'Y',
			'NAME'			 => 'GROUP 2',
			'DESCRIPTION'	 => '',
			'SORT'			 => 500,
		];
		$this->add($save)->shouldBe(true);
		$save['ACTIVE'] = '1';
		$this->finder()->filter(['=NAME' => 'GROUP 2'])->get()->getData()->shouldDbResult($save);
	}
	function it_update()
	{
		$save = [
			'ACTIVE'		 => 'Y',
			'NAME'			 => 'GROUP 2',
			'DESCRIPTION'	 => '',
			'SORT'			 => 500,
		];
		$this->update(1,$save)->shouldBe(true);
		$save['ACTIVE'] = '1';
		$this->finder()->filter(['=NAME' => 'GROUP 2'])->get()->getData()->shouldDbResult($save);
	}
	function it_delete()
	{
		$this->delete(1)->shouldBe(true);
		$this->finder()->count()->shouldBeLike(0);
	}
	function it_finder()
	{
		$this->finder()->shouldHaveType('\BX\DB\Filter\SqlBuilder');
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