<?php
namespace BX\main\behaviors;
use BX;
use BX\Behavior;
use BX\main\behaviors\EntityBehavior;
/* @var $oEntity EntityBehavior */
class AutorBehavior extends Behavior
{
	public $clDateCreate = 'DATE_CREATE';
	public $clTimestamp = 'TIMESTAMP_X';
	public $clCreateBy = 'CREATE_BY';
	public $clChangeBy = 'CHANGE_BY';
	
	public function init()
	{ 
		global $USER;
		$oEntity = $this->owner;
		$oEntity->appendAttributeLabels(array(
			'DATE_CREATE' => 'Дата создания',
			'TIMESTAMP_X' => 'Дата изменения',
			'CREATE_BY' => 'Создатель',
			'CHANGE_BY' => 'Последние изменение',
		));
		$oEntity->appendRules(array(
			array('DATE_CREATE,TIMESTAMP_X','date','type'=>'FULL','allowEmpty'=>false),
			array('CREATE_BY,CHANGE_BY','user','allowEmpty' => false),
			array('CHANGE_BY','set', 'value' => $USER->GetID()),
			array('TIMESTAMP_X','set', 'value' => ConvertTimeStamp(false,'FULL')),
			array('DATE_CREATE','set','value' => ConvertTimeStamp(false,'FULL'),'new' => true),
			array('CREATE_BY','set','value' => $USER->GetID(), 'new' => true),			
		));
		$oEntity->appendRelation(
			' LEFT OUTER JOIN b_user UA ON UA.ID = T.CREATE_BY '.
			' LEFT OUTER JOIN b_user UM ON UM.ID = T.CHANGE_BY '
		);
		$oEntity->appendFilterRules(array(
			'CREATE_BY,CHANGE_BY' => 'numeric',
			'DATE_CREATE,TIMESTAMP_X'=>'date',
			'CREATE_BY_NAME,CHANGE_BY_NAME,CREATE_BY_LOGIN,CHANGE_BY_LOGIN,'.
			'CREATE_BY_LAST_NAME,CHANGE_BY_LAST_NAME' => 'string',
		));
		$oEntity->appendFilterFields(array(
			'TIMESTAMP_X' => 'T.TIMESTAMP_X',
			'CREATE_BY' => 'T.CREATE_BY',
			'CHANGE_BY' => 'T.CHANGE_BY',
			'DATE_CREATE' => 'T.DATE_CREATE',
			'CREATE_BY_NAME' => 'UA.NAME',
			'CREATE_BY_LOGIN' => 'UA.LOGIN',
			'CREATE_BY_LAST_NAME' => 'UA.LAST_NAME',
			'CHANGE_BY_NAME' => 'UM.NAME',
			'CHANGE_BY_LOGIN' => 'UM.LOGIN',
			'CHANGE_BY_LAST_NAME' => 'UM.LAST_NAME',
		));
	}
	
	public function __onRenderAdminListHeads(&$arHeads)
	{
		return array(
			array("id"=>'DATE_CREATE', "content"=>$this->owner->getLabel('DATE_CREATE'), "sort"=>'DATE_CREATE', "default"=>true),
			array("id"=>'TIMESTAMP_X', "content"=>$this->owner->getLabel('TIMESTAMP_X'), "sort"=>'TIMESTAMP_X', "default"=>true),
			array("id"=>'CREATE_BY', "content"=>$this->owner->getLabel('CREATE_BY'), "sort"=>'CREATE_BY', "default"=>true),
			array("id"=>'CHANGE_BY', "content"=>$this->owner->getLabel('CHANGE_BY'), "sort"=>'CHANGE_BY', "default"=>true),
		);
	}
	
	public function __onRenderAdminList(&$row)
	{
		$oUser = new \CUser();
		$arUser = $oUser->GetList($by, $order,array('ID' => $this->owner->create_by, 'ACTIVE' => 'Y'))->Fetch();
		if($arUser)
			$sCreateUser ='<a href="/bitrix/admin/user_edit.php?ID='.$arUser['ID'].'">'.
		 		"[{$arUser['ID']}]({$arUser['LOGIN']}) {$arUser['NAME']} {$arUser['LAST_NAME']}".
		 		'</a>';
		else
			$sCreateUser = $this->owner->owner->create_by;
		$arUser = $oUser->GetList($by, $order,array('ID' => $this->owner->change_by, 'ACTIVE' => 'Y'))->Fetch();
		if($arUser)
			$sChangeUser ='<a href="/bitrix/admin/user_edit.php?ID='.$arUser['ID'].'">'.
			"[{$arUser['ID']}]({$arUser['LOGIN']}) {$arUser['NAME']} {$arUser['LAST_NAME']}".
			'</a>';
		else
			$sChangeUser = $this->owner->owner->change_by;
		$row->AddViewField("DATE_CREATE", $this->owner->date_create);
		$row->AddViewField("TIMESTAMP_X", $this->owner->timestamp_x);
		$row->AddViewField("CREATE_BY", $sCreateUser);
		$row->AddViewField("CHANGE_BY", $sChangeUser);
	}
	
	public function __onRenderAdminFilter($oFilterWidget)
	{
		#$oFilterWidget->getText('DATE_CREATE',get_request('filter_date_create'),$this->owner->getLabel('DATE_CREATE'));
		#$oFilterWidget->getText('TIMESTAMP_X',get_request('filter_timestamp_x'),$this->owner->getLabel('TIMESTAMP_X'));
		$oFilterWidget->getText('CREATE_BY',get_request('filter_create_by'),$this->owner->getLabel('CREATE_BY'));
		$oFilterWidget->getText('CHANGE_BY',get_request('filter_change_by'),$this->owner->getLabel('CHANGE_BY'));
	}
}
