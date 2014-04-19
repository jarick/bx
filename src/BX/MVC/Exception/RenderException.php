<?php namespace BX\MVC\Exception;
use BX\MVC\SiteController;

class RenderException
{
	/**
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
		$path .= $controller->getSiteName().DIRECTORY_SEPARATOR.'error'.DS.'500';
		if ($controller->view()->exists($path)){
			$params = [
				'hanlder'	 => $this,
				'error'		 => $exception,
			];
			$content = $controller->view()->render($path,$params);
			$controller->view()->send($content,'500');
		}else{
			if (\BX\Base\Registry::isDevMode()){
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