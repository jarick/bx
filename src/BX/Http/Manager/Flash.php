<?php
namespace BX\Http\Manager;
use BX\Manager;
use BX\Http\Manager\Session;

class Flash extends Manager
{
	const VALUE = 'value';
	const IS_MULTY = 'is_multy';
	const FLASH_KEY = 'BX.Http.Manager.Flash';
	protected static  $aData = [];
	protected static $aSaveData = [];
	protected static $bStart = false;
	
	protected $oSession = false;
	
	public function setSession($oSession)
	{
		$this->oSession = $oSession;
	}
	
	/**
	 * @return Session
	 */
	public function getSession()
	{
		return $this->oSession;
	}
	
	public function init()
	{
		if($this->getSession() === false){
			$this->setSession(Session::getManager());
		}
		if(!self::$bStart){
			$arSave = get_session(self::FLASH_KEY);
			if(!empty($arSave))
			{
				foreach($arSave as $strKey => $aFlash)
				{
					self::$aData[$strKey] = $aFlash[self::VALUE];
					if($aFlash[self::IS_MULTY] === true){
						self::$aSaveData[$strKey] = $aFlash;
					}
				}
			}
			set_session(self::FLASH_KEY, self::$aSaveData);
			self::$bStart = true;
		}
	}

	public function set($strKey,$strValue,$bMultyHits = false)
	{
		self::$aData[$strKey] = $strValue;
		self::$aSaveData[$strKey] = array(
			self::VALUE => $strValue,
			self::IS_MULTY=>$bMultyHits
		);
		$this->getSession()->set(self::FLASH_KEY, self::$aSaveData);
	}
	
	public function get($strKey)
	{
		if (array_key_exists($strKey, self::$aData)){
			return self::$aData[$strKey];
		}
		return null;
	}
}