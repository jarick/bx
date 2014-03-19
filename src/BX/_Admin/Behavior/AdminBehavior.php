<?php
namespace BX\behaviors;
use BX\Behavior;

class AdminBehavior extends Behavior
{
	public function renderAdminListHeads()
	{
		$arHeads = [];
		if(method_exists($this->getOwner(), 'renderAdminListHeads'))
			$arHeads = $this->getOwner()->renderAdminListHeads();
		foreach($this->getOwner()->getBehaviorAll() as $eBehavior)
			if(method_exists($eBehavior, 'renderAdminListHeads'))
				$arHeads = array_merge($arHeads , $eBehavior->renderAdminListHeads());
		return $arHeads;
	}
	
	public function renderAdminFilter($oFilterWidget)
	{
		if(method_exists($this->getOwner(), 'renderAdminFilter'))
			$this->getOwner()->renderAdminFilter();
		foreach($this->getOwner()->getBehaviorAll() as $eBehavior)
			if(method_exists($eBehavior, 'renderAdminFilter'))
				$eBehavior->renderAdminFilter();
	}
	
	public function renderAdminList(&$row)
	{
		if(method_exists($this->getOwner(), 'renderAdminList'))
			$this->getOwner()->renderAdminList(&$row);
		foreach($this->getOwner()->getBehaviorAll() as $eBehavior)
			if(method_exists($eBehavior, 'renderAdminList'))
				$eBehavior->renderAdminList(&$row);
	}
}