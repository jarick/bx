<?php
namespace BX\MVC\Exception;

class Exception extends \Exception
{
	public $iCode;
	public $aHeaders;
	public function __construct($message = null, $code = 500,$aHeaders = [])
	{
		$this->iCode = $code;
		$this->aHeaders = $aHeaders;
		parent::__construct($message, $code);
	}

	/**
	 * @param Controller $oController
	 * @return Response
	 */
	public function render($oController)
	{
		$oController->getView()->buffer()->flush();
 		$sPath = $oController->getSiteFolder().DS.$oController->getSiteName().DS.'error'.DS.$this->iCode;
		$sContent = $oController->getView()->render($sPath,['error' => $this]);
		$oController->getView()->send($sContent,$this->iCode,$this->aHeaders);
		return $oController->getView()->response();
	}
}