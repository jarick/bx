<?php namespace spec\BX\Captcha;
use BX\DB\Schema;
use PhpSpec\ObjectBehavior;

class CaptchaManagerSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait;
	function let()
	{
		Schema::loadFromYamlFile();
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Captcha\CaptchaManager');
	}
	function it_get()
	{
		$this->check('qwerty','qwerty')->shouldBe(true);
		$this->reload('qwerty');
		$this->check('qwerty','qwerty')->shouldBe(false);
		$sql = 'SELECT * FROM tbl_captcha';
		$captcha = $this->db()->query($sql)->fetch();
		if ($captcha['CODE'] === 'qwerty'){
			throw new \RuntimeException('Reload fall');
		}
		$this->getGuid();
		$count = $this->db()->query($sql)->count();
		if ($count !== 2){
			throw new \RuntimeException('Create fall');
		}
	}
	function it_clear()
	{
		$this->clear('qwerty')->shouldBe(true);
		$sql = 'SELECT * FROM tbl_captcha';
		$count = $this->db()->query($sql)->count();
		if ($count !== 0){
			throw new \RuntimeException('Clear fall');
		}
	}
	function it_clear_old()
	{
		$this->clearOld(10)->shouldBe(true);
		$sql = 'SELECT * FROM tbl_captcha';
		$count = $this->db()->query($sql)->count();
		if ($count > 0){
			throw new \RuntimeException('Is not clear');
		}
	}
}