<?php
namespace BX\MVC\Exception;

class Abort extends Exception
{
	public function __construct()
	{
		parent::__construct();
	}

	public function render($controller)
	{
		return $controller->getView()->response();
	}
}