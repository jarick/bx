<?php
namespace BX\main\behaviors;
use BX;
use BX\Behavior;
use BX\main\behaviors\EntityBehavior;

class EntityBehavior extends Behavior
{
	public $clActive = 'ACTIVE';
	public $clName = 'NAME';
	public $sNameLabel = 'Название';
	
	public function init()
	{ 
		global $USER;
		$oEntity = $this->owner;
		$oEntity->appendAttributeLabels(array(
			'ACTIVE' => 'Активность',
			'NAME' => $this->sNameLabel,
		));
		$oEntity->appendRules(array(
			array('ACTIVE','boolean'),
			array('NAME','string','allowEmpty' => false,'max'=>255),		
		));
		$oEntity->appendFilterRules(array(
			'ACTIVE' => 'boolean',
			'NAME' => 'string',				
		));
		$oEntity->appendFilterFields(array(
			'ACTIVE' => 'T.ACTIVE',
			'NAME' => 'T.NAME',
		));
	}
	
	public function __onRenderAdminListHeads(&$arHeads)
	{
		return array(
			array("id"=>'ACTIVE', "content"=>$this->owner->getLabel('ACTIVE'), "sort"=>'ACTIVE', "default"=>true),
			array("id"=>'NAME', "content"=>$this->owner->getLabel('NAME'), "sort"=>'NAME', "default"=>true),
		);
	}
	
	public function __onRenderAdminList(&$row)
	{
		$row->AddCheckField('ACTIVE');
		$row->AddInputField('NAME');
	}
	
	/*public function __onRenderAdminFilter($oFilterWidget)
	{
		$oFilterWidget->getText('CREATE_BY',get_request('filter_create_by'),$this->owner->getLabel('CREATE_BY'));
		$oFilterWidget->getText('CHANGE_BY',get_request('filter_change_by'),$this->owner->getLabel('CHANGE_BY'));
	}*/
}
