<?php namespace BX\DB\Manager;
use BX\Manager;
use BX\Registry;
use BX\DB\Exception\DbException;
use BX\DB\Adaptor\Sqlite;
use BX\DB\Adaptor\Mysql;
use BX\DB\IDatabase;
use BX\DI;

class Database extends Manager implements IDatabase
{
	use \BX\String\StringTrait;
	const AI = 'AI';
	const PK = 'PK';
	const NN = 'NN';
	const UQ = 'UQ';
	const BIN = 'BIN';
	const UN = 'UN';
	const ZF = 'ZF';
	/**
	 * Get adaptor
	 * @return \BX\DB\Adaptor\IAdaptor
	 * @throws \InvalidArgumentException
	 */
	public function adaptor()
	{
		if (DI::get('adaptor') === null){
			if (!Registry::exists('db')){
				$adaptor = new Sqlite();
			} elseif (is_string($adaptor_str = Registry::get('db'))){
				switch ($adaptor_str){
					case 'sqlite': $adaptor = new Sqlite();break;
					case 'mysql': $adaptor = new Mysql();break;
					default:
						if (class_exists($adaptor_str)){
							$adaptor = new $adaptor_str();
						} else{
							throw new \InvalidArgumentException("DB adaptor `$adaptor_str` is not exists");
						}
						break;
				}
			} else{
				$adaptor = $adaptor_str;
			}
			DI::set('adaptor',$adaptor);
		}
		return DI::get('adaptor');
	}
	/**
	 * Init table vars for edit
	 * @param string $table
	 * @return array
	 */
	public function initTableVarsForEdit($table)
	{
		$result = [];
		$columns = $this->adaptor()->showColumns($table);
		foreach ($columns as $column){
			$result[$column['NAME']] = $column['DEF'];
		}
		return $result;
	}
	protected function check($struct)
	{
		if (!preg_match('/^[a-zA-Z0-9_]+$/',$struct)){
			throw new DbException('Identifier does not conform to security policies.');
		}
		return $struct;
	}
	public function esc($sString,$dontQuote = false)
	{
		$sString = $this->check($sString);
		if ($dontQuote){
			return $sString;
		} else{
			$sQuote = $this->adaptor()->getQuoteChar();
			return $sQuote.$sString.$sQuote;
		}
	}
	/**
	 * Create table
	 * @param string $table
	 * @param array $fields
	 * @return boolean
	 */
	public function createTable($table,$fields)
	{
		$schema = [
			'PK'		 => [],
			'FK'		 => [],
			'UQ'		 => [],
			'COLUMNS'	 => [],
			'TABLE'		 => '',
		];
		$schema['TABLE'] = $this->esc($table);
		foreach ($fields as $field){
			$column = $this->esc($field[0]).' '.$this->adaptor()->getColumnType($this->string()->toUpper($field[1]));
			$length = intval($field[2]);
			if ($length > 0){
				$column .= "($length)";
			}
			if (array_key_exists(3,$field)){
				$keys = explode(',',$field[3]);
				if (in_array(self::PK,$keys)){
					$column .= ' ';
					$this->adaptor()->setPK($column,$schema);
				}
				if (in_array(self::AI,$keys)){
					$column .= ' ';
					$this->adaptor()->setAI($column,$schema);
				}
				if (in_array(self::UQ,$keys)){
					$column .= ' ';
					$this->adaptor()->setUQ($column,$schema);
				}
				if (in_array(self::NN,$keys)){
					$column .= ' ';
					$this->adaptor()->setNN($column,$schema);
				}
			}
			if (isset($field['def'])){
				$column .= ' DEFAULT '.$this->adaptor()->pdo()->quote($field['def']);
			} elseif (isset($field['~def'])){
				$column .= " DEFAULT {$field['~def']}";
			}
			$schema['COLUMNS'][] = $column;
		}
		return false !== $this->adaptor()->createTable($schema);
	}
	/**
	 * Drop table
	 * @param string $table
	 * @return boolean
	 */
	public function dropTable($table)
	{
		return false !== $this->adaptor()->dropTable($this->esc($table));
	}
	/**
	 * Add row
	 * @param string $table
	 * @param array $fields
	 * @return integer|boolean
	 */
	public function add($table,$fields)
	{
		$this->log()->debug("Insert row into table `$table`");
		$sql = 'INSERT INTO '.$this->esc($table).'(';
		$columns = [];
		foreach (array_keys($fields) as $key){
			$columns[] = $this->esc($key);
		}
		$sql .= implode(',',$columns).') VALUES (';
		$values = [];
		$vars = [];
		foreach ($fields as $key => $value){
			if ($this->string()->startsWith($key,'~')){
				$values[] = "'$value'";
			} else{
				$key = $this->string()->toLower($this->esc($key,true));
				$values[] = ":$key";
				$vars[$key] = $value;
			}
		}
		$sql .= implode(',',$values).')';
		if ($this->execute($sql,$vars)){
			return $this->getLastId();
		} else{
			return false;
		}
	}
	/**
	 * Update row
	 * @param string $table
	 * @param array $fields
	 * @param string $where
	 * @param array $where_params
	 * @return boolean
	 */
	public function update($table,$fields,$where,array $where_params = [])
	{
		$this->log()->debug("Update row in table `$table`");
		$sql = 'UPDATE '.$this->esc($table).' SET ';
		$columns = [];
		$vars = [];
		foreach ($fields as $key => $value){
			if ($this->string()->startsWith($key,'~')){
				$columns[] = $this->esc($key)." = '$value'";
			} else{
				$param = $this->string()->toLower($key);
				$columns[] = $this->esc($key).'=:'.$param;
				$vars[$param] = $value;
			}
		}
		$sql .= implode(',',$columns).' WHERE '.$where;
		return $this->execute($sql,array_merge($vars,$where_params));
	}
	/**
	 * Delete rows
	 * @param string $table
	 * @param string $where
	 * @param array $where_params
	 * @return type
	 */
	public function delete($table,$where,array $where_params = [])
	{
		$this->log()->debug("Delete row in table `$table`");
		$sql = 'DELETE FROM '.$this->esc($table).' WHERE '.$where;
		return $this->execute($sql,$where_params);
	}
	/**
	 * Execute sql
	 * @param string $sql
	 * @param array $vars
	 * @return boolean
	 */
	public function execute($sql,array $vars = [])
	{
		return false !== $this->adaptor()->execute($sql,$vars);
	}
	/**
	 * Get last insert id
	 * @return integer
	 */
	public function getLastId()
	{
		return $this->adaptor()->getlastId();
	}
	/**
	 * Query sql
	 * @param string $sql
	 * @param array $vars
	 * @return DBResult
	 */
	public function query($sql,array $vars = [])
	{
		return DBResult::getManager(false,['result' => $this->adaptor()->query($sql,$vars)]);
	}
}