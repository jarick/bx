<?php namespace BX\User;
use BX\Base\Registry;
use BX\User\Store\IAccessStore;
use BX\User\Store\TableConfirmRegistrationStore;

class ConfirmRegistrationManager extends RememberPasswordManager
{
	/**
	 * @return IAccessStore
	 */
	protected function store()
	{
		if ($this->store === null){
			if (Registry::exists('user','confirm_registration','store')){
				$store = Registry::get('user','confirm_registration','store');
				switch ($store){
					case 'db': $this->store = new TableConfirmRegistrationStore();
						break;
					default : throw new \RuntimeException('Store `$store` is not found');
				}
			}else{
				$this->store = new TableConfirmRegistrationStore();
			}
		}
		return $this->store;
	}
}