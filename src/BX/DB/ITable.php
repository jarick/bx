<?php namespace BX\DB;

interface ITable
{
	public function getPkColumn();
	public function getDbTable();
	public function getCacheTag();
	public function getEvent();
	public function getRelations();
	public function getColumns();
}