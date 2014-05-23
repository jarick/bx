<?php namespace BX\DB;

interface IDatabaseManager
{
	public function initTableVarsForEdit($table);
	public function createTable($table,$fields);
	public function dropTable($table);
	public function add($table,$fields);
	public function update($table,$id,$fields);
	public function delete($table,$id);
	public function adaptor();
	public function execute($sql,array $vars = []);
	public function getLastId();
	/**
	 * @return DBResult
	 * */
	public function query($sql,array $vars = []);
}