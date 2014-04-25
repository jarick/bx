<?php namespace spec\BX\Captcha;
use BX\DB\Schema;
use PhpSpec\ObjectBehavior;

class CaptchaManagerSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait,
	 \BX\Translate\TranslateTrait;
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
		$this->get('qwerty','qwerty')->shouldHaveType(\BX\Captcha\Entity\CaptchaEntity::getClass());
		$this->reload(1)->shouldHaveType(\BX\Captcha\Entity\CaptchaEntity::getClass());
		$sql = 'SELECT * FROM tbl_captcha';
		$captcha = $this->db()->query($sql)->fetch();
		if ($captcha['CODE'] === 'qwerty'){
			throw new \RuntimeException('Reload fall');
		}
		$this->create()->shouldHaveType(\BX\Captcha\Entity\CaptchaEntity::getClass());
		$count = $this->db()->query($sql)->count();
		if ($count !== 2){
			throw new \RuntimeException('Create fall');
		}
	}
	function it_clear()
	{
		$this->clear(1)->shouldBe(true);
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