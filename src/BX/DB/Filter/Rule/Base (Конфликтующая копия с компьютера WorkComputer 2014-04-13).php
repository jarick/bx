<?php
namespace BX\DB\Filter\Rule;
use BX\DB\Filter\SqlBuilder;
use BX\DB\Adaptor\IAdaptor;

abstract class Base implements IRule
{
	use \BX\String\StringTrait;
	/**
	 * @var IAdaptor
	 */
	private $adaptor;
	/**
	 * @var SqlBuilder
	 */
	private $filter;
	/**
	 * Constructor
	 * @param \BX\DB\Filter\SqlBuilder $filter
	 * @param \BX\DB\Adaptor\IAdaptor $adaptor
	 */
	public function __construct(SqlBuilder $filter,IAdaptor $adaptor)
	{
 		$this->filter = $filter;
		$this->adaptor = $adaptor;
	}
	/**
	 * Get column
	 * @param string $key
	 * @return string
	 */
	protected function getColumn($key)
	{
 		return $this->builder()->getColumn($key);
	}
	/**
	 * Bind param
	 * @param string $key
	 * @param string $value
	 * @return string
	 */
	protected function bindParam($key,$value)
	{
 		return $this->builder()->bindParam($key,$value);
	}
	/**
	 * Get SQL Builder
	 * @return SqlBuilder
	 */
	protected function builder()
 	{
 		return $this->filter;
	}
	/**
	 * Get DB adaptor
	 * @return IAdaptor
	 */
	protected function adaptor()
 	{
 		return $this->adaptor;
	}
	/**
	 * Escape string for sql
	 * @param string $string
	 * @return string
	 */
	protected function esc($string)
	{
		$quote = $this->adaptor()->getQuoteChar();
		return $quote.$this->string()->toUpper($string).$quote;
	}
	abstract public function addCondition($field,$value);
}