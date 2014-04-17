<?php namespace BX\Engine\Render;

interface IRender
{
	public function render($view,$path,array $params = []);
}