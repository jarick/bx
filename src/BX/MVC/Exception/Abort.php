<?php namespace BX\MVC\Exception;
use BX\MVC\SiteController;

class Abort extends \Exception implements IAbort
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Render
	 * @param \BX\MVC\SiteController $controller
	 * @return type
	 */
	public function render(SiteController $controller)
	{
		return $controller->view()->response();
	}
}