<?php
namespace BX\gii\managers;
use BX\Manager;
use BX;

class ManagerManager extends Manager
{
	public function behaviors()
	{
		return array(
			'entity' => 'main:entity',
		);
	}
	
	public function getDir()
	{
		 return $_SERVER['DOCUMENT_ROOT'].\BX::getSystemDir().'/BX/services';
	}

	public function getManagers()
	{
		$arResult = array();
		foreach (new \DirectoryIterator($this->getDir()) as $fileInfo)
		{
			if($fileInfo->isDot())
				continue;
			$fileInfo->isDir();
			$arResult[] = $fileInfo->getFilename();
		}
		return $arResult;
	}

	public function getModules()
	{
		$arResult = array();
		$oModule = new \CModule();
		$db = $oModule->GetList();
		while($el = $db->fetch())
			$arResult[] = $el['ID'];
		return $arResult;
	}
	
	public function getTables()
	{
		global $DB,$DBName;
		$arResult = array();
		$db = $DB->Query('SHOW TABLES');
		while($el = $db->Fetch())
			$arResult[] = $el["Tables_in_$DBName"];
		return $arResult;
	}

	public function create($POST)
	{
		global $DB,$APPLICATION,$USER;
		$arFields = array();
		if(!$this->checkFields($POST))
			return false;
		$strTmp = $this->getDir().DIRECTORY_SEPARATOR.$POST['SERVICE'];
		if(!is_dir($strTmp))
			mkdir($strTmp,BX_DIR_PERMISSIONS, true);
		$strTmpManager = $strTmp.DIRECTORY_SEPARATOR.'managers';
		$strTmpEntity = $strTmp.DIRECTORY_SEPARATOR.'entities';
		CheckDirPath($strTmpManager);
		$strFileManager = $strTmpManager.DIRECTORY_SEPARATOR.ucwords($POST['MANAGER']).'Manager.php';
		$strFileEntity = $strTmpEntity.DIRECTORY_SEPARATOR.ucwords($POST['MANAGER']).'Entity.php';
		$value = $this->getValueByKey($POST,'IS_PERMISSION');
		$ID = 0;
		if($value === 'on')
		{
			$operation = new \COperation();
			$words = array('X','W','R');
			foreach ($words as $word)
			{
				if(
					!$DB->Query(
						"SELECT ID FROM b_operation WHERE NAME='".$DB->ForSql($POST["PERMISSION_{$word}"]).
						"' AND MODULE_ID='".$DB->ForSql($POST['MODULE']).
						"' AND BINDING='".$DB->ForSql($POST['PERMISSION_BINDING']).
					"'")->Fetch()
				)
				{
					$ID = $DB->Add('b_operation', array(
						'NAME' => $POST["PERMISSION_{$word}"],
						'MODULE_ID' => $POST['MODULE'],
						'DESCRIPTION' => '',
						'BINDING' => $POST['PERMISSION_BINDING'],
					));
				}
			}
			$DB->Query("CREATE TABLE IF NOT EXISTS `{$POST['PERMISSION_TABLE']}`(
				`ID` int(11) NOT NULL AUTO_INCREMENT,
			    `GROUP_CODE` varchar(50) NOT NULL,
  				`TASK_ID` int(11) NOT NULL,
  				`ENTITY_ID` INT(11) NOT NULL DEFAULT 0,
  				PRIMARY KEY (`ID`)
			)");
			if( $ID > 0)	
			{
				
				$DB->Add($POST['PERMISSION_TABLE'], array(
					'SUBJECT_ID' => 'G1',
					'TASK_ID' => $ID, 
					'ENTITY_ID' => 0,
				));
			}
		}
		$arColumns = array();
		$db = $DB->Query("SHOW COLUMNS FROM `".$DB->ForSql($POST['DB_TABLE'])."`");
		while($el = $db->Fetch())
			$arColumns[] = $el['Field'];
		$entity = BX::entity('gii:manager');
		$entity->setDbTable($POST['DB_TABLE']);
		$entity->setData($POST);
		while($el = $db->Fetch())
			$arColumns[] = $el['Field'];
		ob_start();
		$APPLICATION->IncludeFile('widgets/gii/ManagerAdd.tpl.php',array(
				'COLUMNS' => $arColumns,
				'entity' => $entity, 
			),
			array('SHOW_BORDER' => false)
		);
		$strFile = ob_get_contents();
		ob_end_clean();
		list($strContentManager , $strContentEntity) = explode('#ENTITY#', $strFile); 
		$APPLICATION->SaveFileContent($strFileManager, "<?php".PHP_EOL.$strContentManager);
		if(strlen($strContentEntity) > 0)
			$APPLICATION->SaveFileContent($strFileEntity, "<?php".PHP_EOL.$strContentEntity);
		return true;
	}

	public function attributeLabels()
	{
		return array(
			'SERVICE' => 'Сервис',
			'MANAGER' => 'Класс',
			'MODULE' => 'Модуль',
			'EVENT' => 'Событие',
			'TAG' => 'Тег кеширования',
			'U_FIELD' => 'Пользовательское поле',
			'PERMISSION_X' => 'Право на модерацию',
			'PERMISSION_W' => 'Право на запись',
			'PERMISSION_R' => 'Право на чтение',
			'PERMISSION_TABLE' => 'Таблица прав',
			'PERMISSION_BINDING' => 'Группа прав',
			'RELATION' => 'Связь',
			'IS_ENTITY' => 'Генерировать объект',
			'DB_TABLE' => 'Таблица БД',
		);
	}

	public function rules()
	{
		return array(
			array('SERVICE,MANAGER,MODULE,DB_TABLE','string','allowEmpty'=>false,'min'=>3),
			array('IS_ENTITY','boolean'),
			array('EVENT,TAG,U_FIELD','gii_manager_ischeck','allowEmpty'=>false,'min'=>3,'prefix' => 'IS_','check_value' => 'on'),
			array('PERMISSION_TABLE','gii_manager_permission'),
			array('DB_TABLE','gii_manager_dbtable'),
			array('MODULE','gii_manager_module'),
		);
	}
	
	public function isEmpty($value,$trim=false)
	{
		return BX::manager('main:checker')->isEmpty($value,$trim);
	}
	
	public function getValueByKey($value,$key)
	{
		return BX::manager('main:checker')->getValueByKey($value,$key);
	}
	
	public function addError($manager,$key,$message,$params=array())
	{
		return BX::manager('main:checker')->addError($manager,$key,$message,$params);
	}
	
	public function checkIsCheck($manager,$key,&$arFields,$arParams)
	{
		$default = array(
			'prefix' => 'IS_',
			'check_value' => 'on',
		);
		$arParams = array_merge($default,$arParams);
		$value = $this->getValueByKey($arFields,$arParams['prefix'].$key);
		if($value !== $arParams['check_value'])
		{
			$arFields[$key] = false;
			return true;
		}
		return BX::manager('main:checker')->checkString($manager,$key,$arFields,$arParams);
	}
	
	public function checkDBTable($manager,$key,&$arFields,$arParams)
	{
		global $DB;
		$value = $this->getValueByKey($arFields,$key);
		if($this->isEmpty($value))
		{
			$this->addError($manager,$key,'Необходимо заполнить поле «#ATTRIBUTE#».');
			return false;
		}
		if(!in_array($value,$this->getTables()))
		{
			$this->addError($manager,$key,'#ATTRIBUTE# "#VALUE#" не существует.',array('#VALUE#' => $arFields[$key]));
			return false;
		}
		$db = $DB->Query("SHOW COLUMNS FROM `".$DB->ForSql($value)."`");
		$success = true;
		while($el = $db->Fetch())
		{
			$value = $this->getValueByKey($arFields,"LABEL_{$el['Field']}");
			if($this->isEmpty($value))
			{
				$this->addError($manager,$key,'Не введено название поля "#FIELD#".',array('#FIELD#' => $el['Field']));
				$success = false;
			}	
		}
		return $success;
	}
	
	public function checkPermission($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,'IS_PERMISSION');
		$return = true;
		if($value === 'on')
		{
			$value = $this->getValueByKey($arFields,'PERMISSION_X');
			if($this->isEmpty($value))
			{
				$this->addError($manager,$key,'Необходимо заполнить поле «Право на модерацию».');
				$return = false;
			}
			$value = $this->getValueByKey($arFields,'PERMISSION_W');
			if($this->isEmpty($value) || strlen($value) <= 3)
			{
				$this->addError($manager,$key,'Необходимо заполнить поле «Право на запись»(более 3 символов).');
				$return = false;
			}
			$value = $this->getValueByKey($arFields,'PERMISSION_R');
			if($this->isEmpty($value) || strlen($value) <= 3)
			{
				$this->addError($manager,$key,'Необходимо заполнить поле «Право на чтение»(более 3 символов).');
				$return = false;
			}
			$value = $this->getValueByKey($arFields,'PERMISSION_BINDING');
			if($this->isEmpty($value) || strlen($value) <= 3)
			{
				$this->addError($manager,$key,'Необходимо заполнить поле «Группа прав»(более 3 символов).');
				$return = false;
			}
			$value = $this->getValueByKey($arFields,'PERMISSION_TABLE');
			if($this->isEmpty($value) || strlen($value) <= 3)
			{
				$this->addError($manager,$key,'Необходимо заполнить поле «Таблица прав»(более 3 символов).');
				$return = false;
			}
		}
		return $return;
	}
	
	public function checkModule($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,'MODULE');
		if(!in_array($value, $this->getModules()))
		{
			$this->addError($manager,$key,'#ATTRIBUTE# "#VALUE#" не найден.',array('#VALUE#' => $value));
			return false;
		}
		return true;
	}
	
	public function __OnBuildValidatorMap(&$map)
	{
		$map['gii_manager_module'] = array($this,'checkModule');
		$map['gii_manager_ischeck'] = array($this,'checkIsCheck');
		$map['gii_manager_permission'] = array($this,'checkPermission');
		$map['gii_manager_dbtable'] = array($this,'checkDBTable');
	}

	public function checkFields(&$arFields)
	{
		global $DB,$APPLICATION;
		return \BX::manager('main:checker')->checkFields($this,$arFields,true);
		RemoveEventHandler('main', 'OnBuildValidatorMap', $iEventHandlerKey);
	}
}