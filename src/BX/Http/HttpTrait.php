<?php namespace BX\Http;
use BX\Http\RequestManager;
use BX\Http\ResponseManager;
use BX\Http\FlashManager;
use BX\Base\DI;

trait HttpTrait
{
	/**
	 * Get request manager
	 * @return Request
	 */
	public function request()
	{
		$key = 'request';
		if (DI::get($key) === null){
			DI::set($key,new RequestManager());
		}
		return DI::get($key);
	}
	/**
	 * Get response manager
	 * @return Response
	 */
	public function response()
	{
		$key = 'response';
		if (DI::get($key) === null){
			DI::set($key,new ResponseManager());
		}
		return DI::get($key);
	}
	/**
	 * Get flash manager
	 * @return Flash
	 */
	public function flash()
	{
		$key = 'flash';
		if (DI::get($key) === null){
			DI::set($key,new FlashManager());
		}
		return DI::get($key);
	}
}