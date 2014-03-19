<?php namespace BX\DB\Manager;
use BX\Manager;
use BX\DB\IDbResult;

class DBResult extends Manager implements \Iterator, IDbResult
{
	use \BX\String\StringTrait;
	protected $aResult = [];

	public function setResult($aResult)
	{
		$this->aResult = $aResult;
	}

	public function getData()
	{
		return $this->aResult;
	}

	public function count()
	{
		return count($this->aResult);
	}

	public function fetch()
	{
		return next($this->aResult);
	}

	public function getNext()
	{
		$this->next();
	}

	public function rewind()
	{
		rewind($this->aResult);
	}

	public function current()
	{
		$aResult = current($this->aResult);
		foreach ($aResult as $sKey => $sValue) {
			$aResult['~'.$sKey] = $sValue;
			$aResult[$sKey] = $this->string()->escape($sValue);
		}
		return $aResult;
	}

	public function key()
	{
		return key($this->aResult);
	}

	public function next()
	{
		$aResult = next($this->aResult);
		foreach ($aResult as $sKey => $sValue) {
			$aResult['~'.$sKey] = $sValue;
			$aResult[$sKey] = $this->string()->escape($sValue);
		}
		return $aResult;
	}

	public function valid()
	{
		return key($this->aResult) !== null;
	}
}