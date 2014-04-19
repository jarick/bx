<?php namespace BX\MVC\Exception;

class PageNotFound extends \Exception
{
	public function __construct($message = 'Page not found',$previous = null)
	{
		parent::__construct($message,404,$previous);
	}
}