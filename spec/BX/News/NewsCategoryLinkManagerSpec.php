<?php namespace spec\BX\News;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NewsCategoryLinkManagerSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\News\NewsCategoryLinkManager');
	}
	function it_delete()
	{
		$this->delete(1,1)->shouldBe(true);
		$this->finder()->count()->shouldBe(0);
	}
	function it_add()
	{
		$this->add(1,1)->shouldBeLike(2);
		$save = [
			'NEWS_ID'		 => 1,
			'CATEGORY_ID'	 => 1,
		];
		$this->finder()->filter(['ID' => 2])->get()->shouldDbResult($save);
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