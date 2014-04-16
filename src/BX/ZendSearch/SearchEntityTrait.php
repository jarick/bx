<?php namespace BX\ZendSearch;

trait SearchEntityTrait
{
	/**
	 * Get search fileds
	 * @return array
	 */
	protected function search()
	{
		return [];
	}
	/**
	 * Get search fileds
	 * @return array
	 */
	protected function getSearch()
	{
		return $this->search();
	}
}