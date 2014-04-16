<?php namespace BX\DB\Filter\Rule;
use BX\DB\Filter\SqlBuilder;
use BX\DB\Adaptor\IAdaptor;

abstract class BaseRule implements IRule
{
	use \BX\String\StringTrait;
	/**
	 * @var SqlBuilder
	 */
	private $filter;
	/**
	 * Constructor
	 * @param \BX\DB\Filter\SqlBuilder $filter
	 */
	public function __construct(SqlBuilder $filter)
	{
		$this->filter = $filter;
	}
	/**
	 * Prepare name column
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
	 * Get sql builder
	 * @return SqlBuilder
	 */
	protected function builder()
	{
		return $this->filter;
	}
	/**
	 * Get adaptor
	 * @return IAdaptor
	 */
	protected function adaptor()
	{
		return $this->builder()->adaptor();
	}
	/**
	 * Escape column name
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