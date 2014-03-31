<?php namespace BX\Http\Manager;
use BX\Manager;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Response extends Manager implements \ArrayAccess
{
	/**
	 * @var array
	 */
	protected $headers;
	/**
	 * @var integer
	 */
	protected $code = 200;
	/**
	 * @var SymfonyResponse
	 */
	protected $response = false;
	/**
	 * Set http status
	 * @param integer $code
	 * @return \BX\Http\Manager\Response
	 */
	public function setStatus($code)
	{
		$this->code = $code;
		return $this;
	}
	/**
	 * Set http response
	 * @param SymfonyResponse $response
	 * @return Response
	 */
	protected function setResponse(SymfonyResponse $response)
	{
		$this->response = $response;
		return $this;
	}
	/**
	 * Get http response
	 * @return SymfonyResponse
	 */
	protected function getResponse()
	{
		return $this->response;
	}
	/**
	 * Send response
	 * @param type $sContent
	 */
	public function send($sContent)
	{
		$this->setResponse(new SymfonyResponse($sContent,$this->code,$this->headers));
	}
	/**
	 * Redirect
	 * @param string $url
	 * @param integer $status
	 */
	public function redirect($url,$status = 302)
	{
		$this->setResponse(new RedirectResponse($url,$status));
	}
	/**
	 * End response
	 */
	public function end()
	{
		$this->getResponse()->send();
	}
	/**
	 * Offset exists
	 * @param string $offset
	 * @return string
	 */
	public function offsetExists($offset)
	{
		return isset($this->headers[$offset]);
	}
	/**
	 * Offset get
	 * @param string $offset
	 * @return string
	 */
	public function offsetGet($offset)
	{
		return isset($this->headers[$offset]) ? $this->headers[$offset] : null;
	}
	/**
	 * Offset set
	 * @param string $offset
	 * @param string $value
	 */
	public function offsetSet($offset,$value)
	{
		$this->headers[$offset] = $value;
	}
	/**
	 * Offset unset
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->headers[$offset]);
	}
}