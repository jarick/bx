<?php namespace spec\BX\News;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NewsManagerSpec extends ObjectBehavior
{
	use \BX\Http\HttpTrait;
	private $filename;
	function let()
	{
		\BX\DB\Schema::loadFromYamlFile();
		$files = [
			'PICTURE' => [
				'name'		 => '5885_1.jpeg',
				'type'		 => 'image/jpeg',
				'tmp_name'	 => '/tmp/phpbQ4Z7l',
				'error'		 => 0,
				'size'		 => 40231,
			],
		];
		$this->request()->setFiles($files);
		$server = $this->request()->server()->all();
		$server['DOCUMENT_ROOT'] = realpath(__DIR__.'/data');
		$this->request()->setServer($server);
		$file = realpath(__DIR__.'/../../../test/data/5885_1.jpeg');
		copy($file,'/tmp/phpbQ4Z7l');
	}
	function it_is_initializable()
	{
		$this->shouldHaveType('BX\News\NewsManager');
	}
	function it_add()
	{
		$save = [
			'ACTIVE'		 => 'Y',
			'NAME'			 => 'News 2',
			'PREVIEW_TEXT'	 => 'PREVIEW_TEXT',
			'PICTURE'		 => new \BX\Validator\Upload\UploadFile('PICTURE','image'),
			'DETAIL_TEXT'	 => 'DETAIL_TEXT',
			'USER_ID'		 => 1,
		];
		$this->add($save)->shouldBeLike(2);
		$save['CODE'] = 'news-2';
		$save['PICTURE'] = 'news/59daa7ab2dd0106a4a8b24a5f409480162007912.png';
		$save['SORT'] = 500;
		$this->finder()->filter(['=NAME' => 'News 2'])->get()->getData()
			->shouldDbResult($save);
		$this->filename = $this->request()->server()->get('DOCUMENT_ROOT').
			'/upload/news/59daa7ab2dd0106a4a8b24a5f409480162007912.png';
		if (!file_exists($this->filename)){
			throw new \RuntimeException('Test fall');
		}
	}
	function it_update()
	{
		$save = [
			'ACTIVE'		 => 'Y',
			'NAME'			 => 'News 1',
			'PREVIEW_TEXT'	 => 'PREVIEW_TEXT',
			'PICTURE'		 => new \BX\Validator\Upload\UploadFile('PICTURE','image'),
			'DETAIL_TEXT'	 => 'DETAIL_TEXT',
			'USER_ID'		 => 1,
		];
		$this->update(1,$save)->shouldBe(true);
		$save['CODE'] = 'news-1';
		$save['PICTURE'] = 'news/59daa7ab2dd0106a4a8b24a5f409480162007912.png';
		$save['SORT'] = 500;
		$this->finder()->filter(['ID' => '1'])->get()->getData()
			->shouldDbResult($save);
	}
	function it_delete()
	{
		$this->delete(1)->shouldBe(true);
		$this->finder()->count()->shouldBe(0);
		if (\BX\News\NewsCategoryLink::finder()->count() > 0){
			throw new \RuntimeException('Test fall');
		}
		if (file_exists($this->filename)){
			throw new \RuntimeException('Test fall');
		}
	}
	function getMatchers()
	{
		return [
			'dbResult' => function($object,$array){
			foreach($array as $key => $value){
				if ($object[$key] != $value){
					return false;
				}
			}
			return true;
		}
		];
	}
}