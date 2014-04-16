<?php namespace BX\DB\Filter\Rule;
use BX\DB\Filter\SqlBuilder;

class DateTime extends BaseRule
{
	use \BX\Date\DateTrait;
	/**
	 * @var string
	 */
	private $format;
	/**
	 * Get sql for null
	 * @param string $column
	 * @return string
	 */
	private function getNull($column)
	{
		return '('.$column." IS NULL OR ".$this->adaptor()->length($column).'=0)';
	}
	/**
	 * Create date rule
	 * @param \BX\DB\Filter\SqlBuilder $filter
	 * @return \BX\DB\Filter\Rule\DateTime
	 */
	public static function short(SqlBuilder $filter)
	{
		$date = new self($filter);
		return $date->setFormat('short');
	}
	/**
	 * Create datetime rule
	 * @param \BX\DB\Filter\SqlBuilder $filter
	 * @return \BX\DB\Filter\Rule\DateTime
	 */
	public static function full(SqlBuilder $filter)
	{
		$date = new self($filter);
		return $date->setFormat('full');
	}
	/**
	 * Set format date
	 * @param string $format
	 * @return \BX\DB\Filter\Rule\DateTime
	 */
	public function setFormat($format = 'short')
	{
		$this->format = $format;
		return $this;
	}
	/**
	 * Get sql
	 * @param string $field
	 * @param string $value
	 * @param string $operation
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	private function getSql($field,$value,$operation)
	{
		$column = $this->getColumn($field);
		if (strlen($value) > 0){
			if (!$this->date()->checkDateTime($value,$this->format)){
				throw new \InvalidArgumentException("Filter `$field` must be date input `$value`");
			}
			$time = $this->date()->makeTimeStamp($value,$this->format) + $this->date()->getOffset();
			return $column.' '.$operation.' '.$this->bindParam($field,$time);
		} else{
			return $this->getNull($column);
		}
	}
	/**
	 * Add condition sql
	 * @param string $field
	 * @param string $value
	 * @return string
	 */
	public function addCondition($field,$value)
	{
		$not = false;
		if (substr($field,0,1) === '!'){
			$field = substr($field,1);
			$not = true;
		}
		if (substr($field,0,2) === '>='){
			$sql = $this->getSql(substr($field,2),$value,'>=');
		} elseif (substr($field,0,2) === '<='){
			$sql = $this->getSql(substr($field,2),$value,'<=');
		} elseif (substr($field,0,1) === '>'){
			$sql = $this->getSql(substr($field,1),$value,'>');
		} elseif (substr($field,0,1) === '<'){
			$sql = $this->getSql(substr($field,1),$value,'<');
		} else{
			$sql = $this->getSql($field,$value,'=');
		}
		if ($not){
			$sql = "NOT($sql)";
		}
		return $sql;
	}
}