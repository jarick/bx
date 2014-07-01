<?php namespace BX\Validator\Exception;
use Illuminate\Support\MessageBag;

class ValidateException extends \Exception
{
	/**
	 * @var Illuminate\Support\MessageBag
	 */
	private $error = null;
	/**
	 * Construct
	 *
	 * @param \Illuminate\Support\MessageBag $error
	 */
	public function __construct(MessageBag $error)
	{
		$this->error = $error;
	}
	/**
	 * Return all errros
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->error->all();
	}
}