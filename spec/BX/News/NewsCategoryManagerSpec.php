<?php namespace spec\BX\News;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use BX\DB\Schema;

class NewsCategoryManagerSpec extends ObjectBehavior
{
	function let()
	{
		Schema::loadFromYamlFile();
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\News\NewsCategoryManager');
	}
	function it_add()
	{
		$save = [
			'ACTIVE'	 => 'Y',
			'NAME'		 => 'Category 2',
			'TEXT'		 => 'text',
			'PARENT_ID'	 => 1,
			'USER_ID'	 => 1,
		];
		$this->add($save)->shouldBeLike(2);
		$save['CODE'] = 'category-2';
		$save['SORT'] = 500;
		$this->finder()->filter(['ID' => 2])->get()->getData()->shouldDbResult($save);
	}
	function it_update()
	{
		$save = [
			'ACTIVE' => 'Y',
			'NAME'	 => 'Category 1',
		];
		$this->update(1,$save)->shouldBe(true);
		$this->finder()->filter(['ID' => 1])->get()->getData()->shouldDbResult($save);
	}
	function it_delete()
	{
		$this->delete(1)->shouldBe(true);
		$this->finder()->count()->shouldBe(0);
		if (\BX\News\NewsCategoryLink::finder()->count() > 0){
			throw new \RuntimeException('Test fall');
		}
	}
	function it_finder()
	{
		$this->finder()->shouldHaveType('BX\DB\Filter\SqlBuilder');
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