<?php namespace BX\Http;

interface IRequest
{
	public function server();
	public function files();
	public function query();
	public function post();
}