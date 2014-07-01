<?php namespace spec\BX\User;
use BX\DB\Schema;
use BX\User\AuthManager;
use BX\User\Entity\AccessEntity;
use PhpSpec\ObjectBehavior;

class AuthManagerSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait,
	 \BX\Http\HttpTrait;
	function let()
	{
		Schema::loadFromYamlFile();
		$session = [
			'ID'		 => 1,
			'USER_ID'	 => 1,
			'GUID'		 => 'qwerty',
		];
		$this->session()->set(AuthManager::KEY,$session);
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\User\AuthManager');
	}
	function it_login()
	{
		$this->login()->shouldBe(true);
		$this->session()->remove(AuthManager::KEY);
		$this->login('qwerty','qwerty')->shouldBe(true);
		$this->get()->shouldNotBe(null);
	}
	function it_logout()
	{
		$this->logout()->shouldBe(true);
		$sql = 'SELECT * FROM tbl_auth';
		$count = $this->db()->query($sql)->count();
		if ($count !== 0){
			throw new \RuntimeException('Fall test');
		}
		$this->get()->shouldBe(null);
	}
	function it_add()
	{
		$this->logout()->shouldBe(true);
		$this->add(1,'qwerty')->shouldBe(true);
		$sql = 'SELECT * FROM tbl_auth';
		$count = $this->db()->query($sql)->count();
		if ($count !== 1){
			throw new \RuntimeException('Fall test');
		}
	}
	function it_clear_old()
	{
		$this->clearOld(10)->shouldBe(true);
		$sql = 'SELECT * FROM tbl_auth';
		$count = $this->db()->query($sql)->count();
		if ($count !== 0){
			throw new \RuntimeException('Fall test');
		}
	}
	function it_current()
	{
		$this->get()->shouldHaveType(AccessEntity::getClass());
	}
}