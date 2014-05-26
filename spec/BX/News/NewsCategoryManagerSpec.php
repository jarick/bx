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
			'TEST'		 => 'text',
			'PARENT_ID'	 => 1,
		];
		$this->add($save)->shouldBeLike(2);
		$save['CODE'] = 'news-2';
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
		$this->finder()->all()->shouldBe(false);
	}
	function it_finder()
	{
		$this->finder()->shouldHaveType('BX\DB\Filter\SqlBuilder');
	}
}