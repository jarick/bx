<?php
namespace  BX\DB;
use BX\DB\Manager\DBResult;

interface IDatabase
{
	public function initTableVarsForEdit($sTable);
	public function createTable($sTable,$aFields);
	public function dropTable($sTable);
	public function add($sTable,$aFields);
	public function update($sTable,$iId,$aFields);
	public function delete($sTable,$iId);
	public function adaptor();
	public function execute($sSql,array $aVars = []);
	public function getLastId();
	/**
	 * @return DBResult
	 **/
	public function query($sSql,array $aVars = []);
}