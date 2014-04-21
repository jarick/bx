<?php namespace spec\BX\Captcha;
use BX\DB\Schema;
use PhpSpec\ObjectBehavior;
use Whoops\Exception\ErrorException;
use BX\Base\Registry;

class CaptchaManagerSpec extends ObjectBehavior
{
	use \BX\DB\DBTrait,
	 \BX\Translate\TranslateTrait;
	private $reg;
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
	function it_check()
	{
		$this->check('qwerty','qwerty')->shouldBe(true);
		$this->check('qwerty','qwertu')->shouldBe(false);
		$this->getEntity()->getErrors()->get('CODE')->shouldBe(['asdasd']);
	}
	function it_clear()
	{
		$this->clear(10)->shouldBe(true);
		$count = $this->db()->query('SELECT * FROM tbl_captcha')->count();
		if ($count > 0){
			throw new ErrorException('Is not clear');
		}
	}
}