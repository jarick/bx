<?php namespace BX\DB\Filter\Rule;

class Boolean extends BaseRule
{
	/**
	 * Get sql for null value
	 * @param type $column
	 * @return type
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
	 * @throws \InvalidArgumentException
	 */
	public function addCondition($field,$value)
	{
		$not = false;
		if (substr($field,0,1) === '!'){
			$field = substr($field,1);
			$not = true;
		}
		if ($this->string()->length($value) === 0){
			$sql = $this->getNull($this->getColumn($field));
		} elseif (in_array($value,[0,false,'N'],true) || strlen($value) === 0){
			$sql = $this->getColumn($field).' = 0';
		} elseif (in_array($value,[1,true,'Y'])){
			$sql = $this->getColumn($field).' = 1';
		} else{
			throw new \InvalidArgumentException("Condition must be 0|1|false|true|Y|N set `$value`");
		}
		if ($not){
			$sql = "NOT($sql)";
		}
		return $sql;
	}
}