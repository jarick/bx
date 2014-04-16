<?php namespace BX\DB\Filter\Rule;

class Boolean extends Base
{
	/**
	 * Add condition
	 * @param type $field
	 * @param type $value
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
		if (in_array($value,[0,false,'N'],true) || strlen($value) === 0){
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