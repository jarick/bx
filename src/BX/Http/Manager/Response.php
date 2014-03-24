<?php namespace BX\Http\Manager;
use BX\Manager;
use Symfony\Component\HttpFoundation\Response as SymfonyResponce;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\HeaderBag;

class Response extends Manager implements \ArrayAccess
{
	/**
	 * @var array
	 */
	protected $headers;
	protected $iCode = 200;
	public function setStatus($iCode)
	{
		$this->iCode = $iCode;
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
		$this->setResponce(new SymfonyResponce($sContent,$this->iCode,$this->headers));
	}
	public function redirect($sUrl,$iStatus = 302)
	{
		$this->setResponce(new RedirectResponse($sUrl,$iStatus));
	}
	public function end()
	{
		$this->getResponce()->send();
	}
	public function offsetExists($offset)
	{
		return isset($this->headers[$offset]);
	}
	public function offsetGet($offset)
	{
		return isset($this->headers[$offset]) ? $this->headers[$offset] : null;
	}
	public function offsetSet($offset,$value)
	{
		$this->headers[$offset] = $value;
	}
	public function offsetUnset($offset)
	{
		unset($this->headers[$offset]);
	}
}