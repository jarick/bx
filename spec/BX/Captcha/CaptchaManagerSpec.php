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
		$this->beConstructedWith('qwerty');
		$this->translator()->addArrayResource(['captcha.entity.error_check' => 'asdasd']);
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Captcha\CaptchaManager');
	}
	function it_check_true()
	{
		$this->check('qwerty','qwerty')->shouldBe(true);
		if ('qwerty' === $this->getWrappedObject()->getEntity()->code){
			throw new \RuntimeException('Is not clear');
		}
	}
	function it_check_false()
	{
		$this->check('qwerty','qwertu')->shouldBe(false);
		$this->getEntity()->getErrors()->get('CODE')->shouldBe(['asdasd']);
		if ('qwerty' === $this->getWrappedObject()->getEntity()->code){
			throw new \RuntimeException('Is not clear');
		}
	}
	function it_clear()
	{
		$this->clear(10)->shouldBe(true);
		$count = $this->db()->query('SELECT * FROM tbl_captcha')->count();
		if ($count > 0){
			throw new \RuntimeException('Is not clear');
		}
	}
	function it_reload()
	{
		$this->reload();
		if ('qwerty' === $this->getWrappedObject()->getEntity()->code){
			throw new \RuntimeException('Is not clear');
		}
	}
}