<?php namespace BX\Http\Manager;
use BX\Manager;
use Symfony\Component\HttpFoundation\Response as SymfonyResponce;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\HeaderBag;

class Response extends Manager
{
	/**
	 * @var HeaderBag
	 */
	protected $oHeaders;
	public function init()
	{
		$this->oHeaders = new HeaderBag();
	}
	protected $iCode = 200;
	public function setStatus($iCode)
	{
		$this->iCode = $iCode;
		return $this;
	}
	public function addHeader($aHeader)
	{
		$this->oHeaders->add($aHeader);
		return $this;
	}
	public function hasHeader($sKey)
	{
		return $this->oHeaders->has($sKey);
	}
	public function getHeader($sKey,$mDefault = null,$bFirst = true)
	{
		return $this->oHeaders->get($sKey,$mDefault,$bFirst);
	}
	public function setHeader($sKey,$sValues,$bReplace = true)
	{
		$this->oHeaders->set($sKey,$sValues,$bReplace);
		return $this;
	}
	public function removeHeader($sKey)
	{
		$this->oHeaders->remove($sKey);
		return $this;
	}
	/**
	 * @var SymfonyResponce
	 */
	protected $oResponce = false;
	protected function setResponce($oResponce)
	{
		$this->oResponce = $oResponce;
	}
	protected function getResponce()
	{
		return $this->oResponce;
	}
	public function send($sContent)
	{
		$this->setResponce(new SymfonyResponce($sContent,$this->iCode,$this->oHeaders->all()));
	}
	public function redirect($sUrl,$iStatus = 302)
	{
		$this->setResponce(new RedirectResponse($sUrl,$iStatus));
	}
	public function end()
	{
		$this->getResponce()->send();
	}
}