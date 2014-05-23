<?php namespace BX\Http;
use BX\Http\Request;
use BX\Http\Response;
use BX\Config\DICService;

trait HttpTrait
{
	/**
	 * Return request manager
	 *
	 * @return Request
	 */
	public function request()
	{
		$key = 'request';
		if (DICService::get($key) === null){
			$manager = function(){
				return new Request();
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
	/**
	 * Return response manager
	 *
	 * @return Response
	 */
	public function response()
	{
		$key = 'response';
		if (DICService::get($key) === null){
			$manager = function(){
				return new Response();
			};
			DICService::set($key,$manager);
		}
		return DICService::get($key);
	}
}