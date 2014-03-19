<?php
namespace BX\gii\entities;
use BX;
use BX\ActiveRecord;

/**
 * @property string $is_action_list
 * @property string $is_action_edit
 * @property string $manager
 * @property string $module
 * @property string $file
 * @property string $mess_title
 **/
class CrudEntity extends ActiveRecord
{
	public function getDir()
	{
		 return $_SERVER['DOCUMENT_ROOT'].BX::getServiceDir();
	}
	
	public function create($arField)
	{
		global $DB,$APPLICATION,$USER;
		$arFields = array();
		if(!$this->checkFields($arField))
			return false;
		$this->setData($arField);
		$arLabels = $this->getAttributeLabels();
		if($entity->is_action_list === 'Y')
		{
			ob_start();
			$APPLICATION->IncludeFile('widgets/gii/CrudAdmin.tpl.php',
				array(
					'ENTITY' => $this,
					'FIELDS' => array_keys($arLabels),
					'LABELS' => array_values($arLabels),
				),
				array(
					'SHOW_BORDER' => false,
				)
			);
			$strFile = ob_get_contents();
			ob_clean();
			$strFile = str_replace(array('#PHP_B#','#PHP_E#'), array('<?php','?>'), $strFile);
			$strFilePath = '/bitrix/modules/'.$this->module.'/admin/'.$this->file.'_admin.php';
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].$strFilePath, $strFile);
			$APPLICATION->SaveFileContent(
				$_SERVER["DOCUMENT_ROOT"].'/bitrix/admin/'.$this->file.'_admin.php', 
				'<?require($_SERVER["DOCUMENT_ROOT"]."'.$strFilePath.'");?>'
			);
			$strLang = "<?php \n";
			$strLang .= "\$MESS['SAVE_ERROR'] = 'Ошибка сохранения'; \n";
			$strLang .= "\$MESS['DELETE_ERROR'] = 'При удалении элемента произошла ошибка'; \n";
			$strLang .= "\$MESS['CONFIRM_DEL_MESSAGE'] = 'Удалить безвозвратно?'; \n";
			$strLang .= "\$MESS['MAIN_ADMIN_SAVE'] = 'Успешено сохранено'; \n";
			$strLang .= "\$MESS['ADMIN_TITLE'] = '{$this->mess_title}'; \n";
			$strFilePath = '/bitrix/modules/'.$this->module.'/lang/ru/admin/'.$this->file.'.php';
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].$strFilePath, $strLang);
		}
		if($this->is_action_edit === 'Y')
		{
			$arColumns = array();
			$db = $DB->Query('SHOW COLUMNS FROM `'.$manager->getDbTable().'`');
			while($el = $db->Fetch())
				$arColumns[$el['Field']] = $el;
			ob_start();
			$APPLICATION->IncludeFile('widgets/gii/CrudEdit.tpl.php',
				array(
					'ENTITY' => $entity,
					'FIELDS' => array_keys($arLabels),
					'COLUMNS' => $arColumns,
				),
				array(
					'SHOW_BORDER' => false,
				)
			);
			$strFile = ob_get_contents();
			ob_clean();
			$strFile = str_replace(array('#PHP_B#','#PHP_E#'), array('<?php','?>'), $strFile);
			$strFilePath = '/bitrix/modules/'.$entity->module.'/admin/'.$entity->file.'_edit.php';
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].$strFilePath, $strFile);
			$APPLICATION->SaveFileContent(
				$_SERVER["DOCUMENT_ROOT"].'/bitrix/admin/'.$entity->file.'_edit.php',
				'<?require($_SERVER["DOCUMENT_ROOT"]."'.$strFilePath.'");?>'
			);
			$strLang = "<?php \n";
			$strLang .= "\$MESS['SAVE_ERROR'] = 'Ошибка сохранения'; \n";
			$strLang .= "\$MESS['BACK_TO'] = 'Назад в список'; \n";
			$strLang .= "\$MESS['NOT_FOUND'] = 'Элемент не найден'; \n";
			$strLang .= "\$MESS['ADMIN_TAB1'] = 'Редактирование'; \n";
			$strLang .= "\$MESS['MAIN_ADMIN_SAVE'] = 'Успешено сохранено'; \n";
			$strLang .= "\$MESS['ADMIN_TITLE'] = '{$entity->mess_title}'; \n";
			$strFilePath = '/bitrix/modules/'.$entity->module.'/lang/ru/admin/'.$entity->file.'_edit.php';
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].$strFilePath, $strLang);
		}
		return true;
	}

	public function attributeLabels()
	{
		return array(
			'FILE' => 'Страница',
			'MANAGER' => 'Класс',
			'MODULE' => 'Модуль',
			'MESS_TITLE' => 'Заголовок страницы',
			'IS_ACTION_LIST' => 'Cтраница списка',
			'IS_ACTION_EDIT' => 'Cтраница редактирования',
		);
	}

	public function rules()
	{
		return array(
			array('MODULE,FILE,MANAGER','string','allowEmpty'=>false,'min'=>3),
			array('MESS_TITLE','string','allowEmpty'=>false),
			array('IS_ACTION_LIST,IS_ACTION_EDIT','boolean'),
			array('MANAGER','gii_crud_manager'),
			array('MODULE','gii_crud_module'),
		);
	}
	
	public function isEmpty($value,$trim=false)
	{
		return BX::getManager('main:checker')->isEmpty($value,$trim);
	}
	
	public function getValueByKey($value,$key)
	{
		return BX::getManager('main:checker')->getValueByKey($value,$key);
	}
	
	public function addError($manager,$key,$message,$params=array())
	{
		return BX::getManager('main:checker')->addError($manager,$key,$message,$params);
	}
	
	public function checkManager($manager,$key,&$arFields,$arParams)
	{
		$default = array(
			'messages_not_exist' => "«#ATTRIBUTE#» '#VALUE#' не найден.",
		);
		$arParams = array_merge($default,$arParams);
		$value = $this->getValueByKey($arFields,$key);
		if(!in_array($value, $this->getManagers()))
		{
			$message = strtr($arParams['messages_not_exist'],array('#VALUE#'=>$value));
			$this->addError($manager,$key,$message);
			return false;
		}
		return true;
	}
	
	public function checkModule($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,'MODULE');
		if(!in_array($value, $this->getModules()))
		{
			$this->addError($manager,$key,'#ATRIBUTE# "#VALUE#" не найден.',array('#VALUE#' => $value));
			return false;
		}
		return true;
	}
	
	public function __OnBuildValidatorMap(&$map)
	{
		$map['gii_crud_manager'] = array($this,'checkManager');
		$map['gii_crud_module'] = array($this,'checkModule');
	}

	public function checkFields(&$arFields)
	{
		global $DB,$APPLICATION;
		return BX::getManager('main:checker')->checkFields($this,$arFields,true);
	}
	
	public function getManagers()
	{
		$arReturn = array();
		$services = new \DirectoryIterator($_SERVER['DOCUMENT_ROOT'].BX::getSystemDir().'/BX');
		foreach($services as $service)
		{
			if($service->isDir() && !$service->isDot())
			{
				$managers = new \DirectoryIterator($service->getPathname());
				foreach($managers as $manager)
			  	{
			 		if($managers->getFilename() === 'managers')
			 		{
			 			$files = new \DirectoryIterator($manager->getPathname());
			 			foreach($files as $file)
			 			{
			  				if(preg_match("/(?<manager>\S+)Manager/", $file->getFilename(),$match))
			  				{
			  					$data = explode(' ',$match['manager']);
			  					foreach ($data as &$value)
			  					{
			  						$value[0] = ToLower($value[0]);
			  					}
			  					$match['manager'] = implode(' ',$data);
			  					$arReturn[] = $service->getFileName().':'.$match['manager'];
			  				}
			 			}
			 		}
			  	}
			}
		}
		return $arReturn;
	}
	
	public function getModules()
	{
		$arResult = array();
		$oModule = new \CModule();
		$dbModule = $oModule->GetList();
		while($arModule = $dbModule->fetch())
			$arResult[] = $arModule['ID'];
		return $arResult;
	}
}