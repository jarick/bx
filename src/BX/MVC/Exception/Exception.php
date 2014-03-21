<?php namespace BX\MVC\Exception;

class Exception extends \Exception
{
	public $code;
	public $headers;
	public function __construct($message = null,$code = 500,$headers = [])
	{
		$this->code = $code;
		$this->headers = $headers;
		parent::__construct($message,$code);
	}
	/**
	 * @param Controller $controller
	 * @return Response
	 */
	public function render($controller)
	{
		$controller->getView()->buffer()->flush();
		var_dump($this->getMessage());
		var_dump($this->getFile());
		var_dump($this->getLine());
		$path = $controller->getSiteFolder().DIRECTORY_SEPARATOR.$controller->getSiteName().
			DIRECTORY_SEPARATOR.'error'.DS.$this->code;
		$content = $controller->getView()->render($path,['error' => $this]);
		$controller->getView()->send($content,$this->code,$this->headers);
		return $controller->getView()->response();
	}
}