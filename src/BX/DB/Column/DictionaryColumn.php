<?php namespace BX\DB\Column;

class DictionaryColumn extends BX\Base\Dictionary
{
	public function __construct()
	{
		parent::__construct('BX\DB\Column\IColumn');
	}
}