<?php namespace spec\BX\Validator\Collection;
use BX\Validator\Upload\UploadFile;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FileSpec extends ObjectBehavior
{
	use \BX\Http\HttpTrait;
	function let()
	{
		$files = [
			'FILE' => [
				'name'		 => '5885_1.jpeg',
				'type'		 => 'image/jpeg',
				'tmp_name'	 => '/tmp/phpbQ4Z7l',
				'error'		 => 0,
				'size'		 => 40231,
			],
		];
		$this->request()->setFiles($files);
		$file = realpath(__DIR__.'/../../../../test/data/5885_1.jpeg');
		copy($file,'/tmp/phpbQ4Z7l');
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\Validator\Collection\File');
	}
	function it_exists_file()
	{
		$values = ['FILE' => new UploadFile('test','image')];
		$labels = ['FILE' => 'file'];
		if ($this->getWrappedObject()->validateField('FILE',$values,$labels) !== true){
			throw new \RuntimeException('Test fall');
		}
	}
}