<?php namespace BX\Engine;

interface IEngine
{
	public function render($view,$path,array $params = []);
}