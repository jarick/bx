<?php namespace BX\MVC\Exception;
use BX\MVC\SiteController;

class RenderException
{
	use \BX\Config\ConfigTrait;
	/**
	 * Render catch esception
	 *
	 * @param \Exception $exception
	 * @param SiteController $controller
	 * @return Response
	 */
	public function render(\Exception $exception,SiteController $controller)
	{
		if ($exception instanceof IAbort){
			return $exception->render($controller);
		}
		$controller->view()->buffer()->flush();
		$path = $controller->getSiteFolder().DIRECTORY_SEPARATOR;
		$path .= $controller->getSiteName().DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.'500';
		if ($controller->view()->exists($path)){
			$params = [
				'hanlder'	 => $this,
				'error'		 => $exception,
			];
			$content = $controller->view()->render($path,$params);
			$controller->view()->send($content,'500');
		}else{
			if ($this->config()->isDevMode()){
				$run = new \Whoops\Run();
				$run->pushHandler(new \Whoops\Handler\PrettyPageHandler());
				$run->handleException($exception);
			}else{
				echo 'Error on page';
			}
		}
		return $controller->view()->response();
	}
}