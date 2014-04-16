<?php namespace BX\DB\Adaptor;

interface IAdaptor
{
	public function execute($sql,$vars = []);
	public function getLastId();
	public function query($sql,$vars);
	public function getQuoteChar();
	public function setPK(&$column,&$schema);
	public function setAI(&$column,&$schema);
	public function setUQ(&$column,&$schema);
	public function setNN(&$column,&$schema);
	public function getColumnArray();
	public function getColumnType($column);
	public function createTable($schema);
	public function length($name);
	public function upper($value);
	public function showColumns($table);
	public function resetAI($table);
	/**
	 * @return \PDO
	 */
	public function pdo();
	public function lock($tables);
	public function unlock($tables);
}