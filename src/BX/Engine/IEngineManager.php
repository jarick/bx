<?php namespace BX\Engine;

interface IEngineManager
{
	public function render($view,$path,array $params = []);
	public function exists($path);
}