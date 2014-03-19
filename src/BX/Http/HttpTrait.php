<?php namespace BX\Http;
use BX\Http\Manager\Request;
use BX\Http\Manager\Response;
use BX\Http\Manager\Flash;
use BX\Http\Manager\Session;
use BX\DI;

trait HttpTrait
{
	/**
	 * @return Request
	 */
	public function request()
	{
		$key = 'request';
		if (DI::get($key) === null){
			DI::set($key,Request::getManager());
		}
		return DI::get($key);
	}
	/**
	 * @return Response
	 */
	public function response()
	{
		$key = 'response';
		if (DI::get($key) === null){
			DI::set($key,Response::getManager());
		}
		return DI::get($key);
	}
	public function flash()
	{
		$key = 'flash';
		if (DI::get($key) === null){
			DI::set($key,Flash::getManager(false,['session' => $this->session()]));
		}
		return DI::get($key);
	}
	public function session()
	{
		$key = 'session';
		if (DI::get($key) === null){
			DI::set($key,Session::getManager());
		}
		return DI::get($key);
	}
}