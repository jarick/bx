<?php namespace spec\BX\News;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NewsCategoryLinkManagerSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\News\NewsCategoryLinkManager');
	}
	function it_add()
	{
		$save = [
			'NEWS_ID'		 => 1,
			'CATEGORY_ID'	 => 'Category 2',
		];
		$this->add($save)->shouldBeLike(2);
		$this->finder()->filter(['ID' => 2])->get()->shouldDbResult($save);
	}
	function it_delete()
	{
		$this->delete(1)->shouldBe(true);
		$this->finder()->count()->shouldBe(0);
	}
	function getMatchers()
	{
		return [
			'dbResult' => function($object,$array){
			foreach($array as $key => $value){
				if ($object->$key != $value){
					return false;
				}
			}
			return true;
		}
		];
	}
}