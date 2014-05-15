<?php namespace BX\Error;

interface IErrorManager
{
	public static function reset();
	public static function set(\Exception $ex);
	public static function get();
}