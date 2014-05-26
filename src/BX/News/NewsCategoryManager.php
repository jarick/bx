<?php namespace BX\News;
use BX\News\Store\TableNewsCategoryStore;

class NewsCategoryManager
{
	/**
	 * @return TableNewsCategoryStore
	 */
	private function store()
	{
		if ($this->store === null){
			if ($this->config()->exists('news','category','store')){
				$store = $this->config()->get('news','category','store');
				switch ($store){
					case 'db': $this->store = new TableNewsCategoryStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableNewsCategoryStore();
			}
		}
		return $this->store;
	}
	public function add(array $category)
	{
		return $this->store()->add($category);
	}
	public function update($id,$category)
	{
		return $this->store()->update($id,$category);
	}
	public function delete($id)
	{
		return $this->store()->delete($id);
	}
	public function finder()
	{
		return $this->store()->getFinder();
	}
}