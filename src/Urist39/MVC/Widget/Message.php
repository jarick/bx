<?php
namespace Urist39\MVC\Widget;
use BX\MVC\Widget\Message as BaseMessage;

class Message extends BaseMessage
{
	public function run($arParams)
	{
		echo '<div class="alert alert-'.$this->sType.' alert-dismissable">';
  		echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
  		echo $this->sMessage;
		echo "</div>";
	}	
}