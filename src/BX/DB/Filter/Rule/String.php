<?php namespace BX\DB\Filter\Rule;

class String extends BaseRule
{
	/**
	 * Get null sql
	 * @param string $column
	 * @return string
	 */
	private function getNull($column)
	{
		return '('.$column." IS NULL OR ".$this->adaptor()->length($column).'=0)';
	}
	/**
	 * Add condition
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
		if (substr($field,0,1) === '%'){
			$field = substr($field,1);
			$column = $this->getColumn($field);
			if (strlen($value) > 0){
				$sql = $this->adaptor()->upper($column).' LIKE ';
				$sql .= $this->adaptor()->upper($this->bindParam($field,'%'.$value.'%'));
			} else{
				$sql = $this->getNull($column);
			}
		} elseif (substr($field,0,1) === '='){
			$field = substr($field,1);
			$column = $this->getColumn($field);
			if (strlen($value) > 0){
				$sql = $column.' = '.$this->bindParam($field,$value);
			} else{
				$sql = $this->getNull($column);
			}
		} elseif (strlen($value) <= 0){
			$column = $this->getColumn($field);
			$sql = $this->getNull($column);
		} else{
			$column = $this->getColumn($field);
			$sql = $column.' LIKE '.$this->bindParam($field,$value);
		}
		if ($not){
			$sql = "NOT($sql)";
		}
		return $sql;
	}
}