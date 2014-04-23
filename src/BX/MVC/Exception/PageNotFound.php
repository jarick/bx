<?php namespace BX\MVC\Exception;
use \BX\MVC\SiteController;

class PageNotFound extends \Exception implements IAbort
{
	public $page = 'error/404';
	use \BX\Engine\EngineTrait;
	public function __construct($message = 'Page not found')
	{
		parent::__construct($message);
	}
	public function render(SiteController $controller)
	{
		$response = $controller->view()->response();
		$response->status = 404;
		$buffer = $controller->view()->buffer();
		$buffer->start();
		$path = $controller->getSiteFolder().DIRECTORY_SEPARATOR.$controller->getSiteName().
			DIRECTORY_SEPARATOR.$this->page;
		$found = $this->engine()->render($controller->view(),$path,['exception' => $this]);
		$return = $buffer->end();
		if (!$found){
			$response->send('Page not found');
		}else{
			$response->send($return);
		}
		return $response;
	}
}