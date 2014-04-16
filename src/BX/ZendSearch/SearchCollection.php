<?php namespace BX\ZendSearch;
use BX\Base\Collection;

class SearchCollection extends Collection
{
	public function __construct()
	{
		parent::__construct('ZendSearch\Lucene\Document\Field');
	}
}