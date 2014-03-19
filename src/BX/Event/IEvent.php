<?php
namespace BX\Event;

interface IEvent
{
	public function on($name,$func,$sort = 500);
	public function fire($name,$params,$halt = true);
}