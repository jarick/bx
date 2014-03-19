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
		$sSql = "CREATE TABLE {$schema['TABLE']}(\n";
		$sSql .= implode(",\n",$schema['COLUMNS']);
		$sSql .= "\n);";
		return $this->execute($sSql);
	}
	public function length($name)
	{
		return "length($name)";
	}
	public function upper($value)
	{
		return "upper($value)";
	}
	public function showColumns($table)
	{
		$result = null;
		$columns = $this->pdo()->query("PRAGMA table_info($table)")->fetchAll();
		foreach ($columns as $column){
			$result[] = [
				'NAME'	 => $column['name'],
				'TYPE'	 => $column['type'],
				'NN'	 => $column['notnull'],
				'DEF'	 => trim(str_replace("''","'",$column['dflt_value']),"'"),
			];
		}
		return $result;
	}
}