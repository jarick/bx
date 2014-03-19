<?php
namespace BX\Event;

interface IEvent
{
	public function on($sName,$oFunc,$iSort = 500);
	public function fire($sName,$aParams,$bHalt = true);
}