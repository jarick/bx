<?php namespace BX\Console\Writer;

interface IWriter
{
	public function write($message);
	public function read();
	public function error($message);
	public function color($message,$color,$background_color = false);
}