<?php namespace BX\DB\Filter\Rule;

class Number extends BaseRule
{
	/**
	 * Get Sql for null
	 * @param type $column
	 * @return type
	 */
	private function getNull($column)
	{
		return '('.$column." IS NULL OR ".$this->adaptor()->length($column).'=0)';
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
			if (!is_numeric($value)){
				throw new \InvalidArgumentException("Filter `$field` must be numeric input `$value`");
			}
			return $column.' '.$operation.' '.$this->bindParam($field,$value);
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