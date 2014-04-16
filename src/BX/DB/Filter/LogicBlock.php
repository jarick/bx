<?php namespace BX\DB\Filter;
use BX\Base\Registry;
use BX\DB\Filter\Rule\String;
use BX\DB\Filter\Rule\Number;
use BX\DB\Filter\Rule\DateTime;
use BX\DB\Filter\Rule\Boolean;

class LogicBlock
{
	/**
	 * @var SqlBuilder
	 */
	private $sql_builder;
	/**
	 * @var array
	 */
	private $filter_rule;
	/**
	 * Constructor
	 * @param \BX\DB\Filter\SqlBuilder $sql_builder
	 * @param type $filter_rule
	 * @throws \InvalidArgumentException
	 */
	public function __construct(SqlBuilder $sql_builder,array $filter_rule)
	{
		if (empty($filter_rule)){
			throw new \InvalidArgumentException("Filter rules is not set");
		}
		$this->sql_builder = $sql_builder;
		$this->filter_rule = $filter_rule;
	}
	/**
	 * Get special chars array
	 * @return array
	 */
	private function getSpecialCharsArray()
	{
		return ['!','=','>','<','%'];
	}
	/**
	 * Get type param
	 * @param string $key
	 * @return IParam
	 * */
	private function getType($key)
	{
		while (in_array(substr($key,0,1),$this->getSpecialCharsArray())){
			$key = substr($key,1);
		}
		if (array_key_exists($key,$this->filter_rule)){
			$type = $this->filter_rule[$key];
			switch ($type){
				case 'string': return new String($this->sql_builder);
				case 'number': return new Number($this->sql_builder);
				case 'date': return DateTime::short($this->sql_builder);
				case 'datetime': return DateTime::full($this->sql_builder);
				case 'boolean': return new Boolean($this->sql_builder);
				default:
					if (Registry::exists('filter_rule',$type)){
						if (is_string($class = Registry::get('filter_rule',$type))){
							if (class_exists($class)){
								return new $class($this->sql_builder);
							} else{
								throw new \InvalidArgumentException("Filter rule class `$class` is not exists");
							}
						} else{
							throw new \InvalidArgumentException("Filter rule .$type must be string");
						}
					} else{
						throw new \InvalidArgumentException("Filter rule `$type` is not exists");
					}
			}
		} else{
			throw new \InvalidArgumentException("Unknow field `$key`");
		}
	}
	/**
	 * Get sql
	 * @param array $filter
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function toSql(array $filter)
	{
		$return = [];
		$logic = 'AND';
		foreach($filter as $key => $value){
			if (is_numeric($key)){
				$block = new LogicBlock($this->sql_builder,$this->filter_rule);
				$return[] = '('.$block->toSql((array)$value).')';
			} elseif ($key === 'LOGIC'){
				if (in_array($value,['OR','AND'])){
					$logic = $value;
				} else{
					throw new \InvalidArgumentException("Logic must be AND or OR set `$value`");
				}
			} else{
				$return[] = $this->getType($key)->addCondition($key,$value);
			}
		}
		return implode(' '.$logic.' ',$return);
	}
}