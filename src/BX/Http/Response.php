<?php namespace BX\Http;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class Response implements \ArrayAccess
{
	/**
	 * @var array
	 */
	public $headers = [];
	/**
	 * @var integer
	 */
	public $code = 200;
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
	 *
	 * @param string $content
	 */
	public function send($content)
	{
		foreach($this->headers as $key => $value){
			if ($value === null){
				unset($this->headers[$key]);
			}
		}
		$this->setResponse(new SymfonyResponse($content,$this->code,$this->headers));
	}
	/**
	 * Send stream response
	 *
	 * @param callable $stream
	 */
	public function stream($stream)
	{
		foreach($this->headers as $key => $value){
			if ($value === null){
				unset($this->headers[$key]);
			}
		}
		$this->setResponse(new StreamedResponse($stream,$this->code,$this->headers));
	}
	/**
	 * Send json response
	 *
	 * @param array $data
	 */
	public function json(array $data)
	{
		foreach($this->headers as $key => $value){
			if ($value === null){
				unset($this->headers[$key]);
			}
		}
		$this->setResponse(new JsonResponse($data,$this->code,$this->headers));
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
	public function &offsetGet($offset)
	{
		if (!isset($this->headers[$offset])){
			$this->headers[$offset] = null;
		}
		return $this->headers[$offset];
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