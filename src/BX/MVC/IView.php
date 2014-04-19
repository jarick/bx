<?php namespace BX\MVC;

interface IView
{
	public function buffer();
	public function exists($path);
	public function render($path,array $params = []);
	public function abort();
	public function redirect($url,$status = 302);
	public function send($content,$status = false,array $headers = []);
}