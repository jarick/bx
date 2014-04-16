<?php namespace BX\DB\Adaptor;

class Sqlite extends DbAdaptor implements IAdaptor
{
	public function getQuoteChar()
	{
		return '`';
	}
	public function setPK(&$column,&$schema)
	{
		$column .= 'PRIMARY KEY';
	}
	public function setAI(&$column,&$schema)
	{
		$column .= 'AUTOINCREMENT';
	}
	public function setUQ(&$column,&$schema)
	{
		$column .= 'UNIQUE';
	}
	public function setNN(&$column,&$schema)
	{
		$column .= 'NOT NULL';
	}
	public function getColumnArray()
	{
		return [
			'INTEGER'	 => 'INTEGER',
			'STRING'	 => 'VARCHAR',
			'TIMESTAMP'	 => 'TIMESTAMP',
			'TEXT'		 => 'TEXT',
			'BOOLEAN'	 => 'INT',
		];
	}
	public function getColumnType($column)
	{
		$aColumns = $this->getColumnArray();
		return $aColumns[$column];
	}
	public function createTable($schema)
	{
		$sql = "CREATE TABLE {$schema['TABLE']}(\n";
		$sql .= implode(",\n",$schema['COLUMNS']);
		$sql .= "\n);";
		return $this->execute($sql);
	}
	public function length($name)
	{
		return "LENGTH($name)";
	}
	public function upper($value)
	{
		return "UPPER($value)";
	}
	public function showColumns($table)
	{
		$result = null;
		$columns = $this->pdo()->query("PRAGMA table_info($table)")->fetchAll();
		foreach($columns as $column){
			$result[] = [
				'NAME'	 => $column['name'],
				'TYPE'	 => $column['type'],
				'NN'	 => $column['notnull'],
				'DEF'	 => trim(str_replace("''","'",$column['dflt_value']),"'"),
			];
		}
		return $result;
	}
	public function resetAI($table)
	{
		$this->pdo()->exec("delete from sqlite_sequence where name='$table'");
	}
	public function lock($tables)
	{
		$name = md5(implode('|',(array)$tables));
		$this->pdo()->exec("BEGIN EXCLUSIVE TRANSACTION $name");
	}
	public function unlock($tables)
	{
		$name = md5(implode('|',(array)$tables));
		$this->pdo()->exec("END TRANSACTION $name");
	}
}