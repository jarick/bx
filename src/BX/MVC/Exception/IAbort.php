<?php namespace BX\MVC\Exception;
use BX\MVC\SiteController;

interface IAbort
{
	public function render(SiteController $controller);
}