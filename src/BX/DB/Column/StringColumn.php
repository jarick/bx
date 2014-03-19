<?php namespace BX\DB\Column;

class StringColumn extends BaseColumn
{
	public function getFilterRule()
	{
		return 'string';
	}
}
