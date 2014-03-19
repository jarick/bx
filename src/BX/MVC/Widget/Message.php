<?php
namespace BX\MVC\Widget;
use BX\MVC\Widget;

class Message extends Widget
{
	const TYPE_DANGER = 'danger';
	const TYPE_WARNING = 'warning';
	const TYPE_INFO = 'info';
	const TYPE_SUCCESS = 'success';
	
	protected $sMessage;
	public function setMessage($sMessage)
	{
		$this->sMessage = $sMessage; 
	}
	public $sType = self::TYPE_DANGER;
	public function setType($sType)
	{
		$this->sType = $sType;
	}
	
	public function run()
	{
  		echo '<span class="message-'.$this->sType.'">'.$this->sMessage.'</span>';
	}	
}