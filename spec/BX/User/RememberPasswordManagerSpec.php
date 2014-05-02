<?php namespace spec\BX\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RememberPasswordManagerSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait;
	function let()
	{
		\BX\DB\Schema::loadFromYamlFile();
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\User\RememberPasswordManager');
	}
	function it_get_token()
	{
		$this->getToken(1,'qwerty','qwerty')->shouldBe('qwerty');
	}
	function it_check()
	{
		$this->check('qwerty','qwerty')->shouldBeLike(1);
	}
	function it_clear()
	{
		$this->clear(1)->shouldBe(true);
		$sql = 'SELECT * FROM tbl_remember_password';
		if ($this->db()->query($sql)->count() > 0){
			throw new \RuntimeException('Test fall.');
		}
	}
	function it_clear_old()
	{
		$this->clearOld(10)->shouldBe(true);
		$sql = 'SELECT * FROM tbl_remember_password';
		if ($this->db()->query($sql)->count() > 0){
			throw new \RuntimeException('Test fall.');
		}
	}
}