<?php namespace BX\DB;

interface IDbResult
{

	public function setResult($aResult);

	public function getData();

	public function count();

	public function fetch();

	public function getNext();

	public function rewind();

	public function current();

	public function key();

	public function next();

	public function valid();
}