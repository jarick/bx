<?php
namespace BX\main\managers;
use BX\Manager;

class CheckerManager extends Manager
{
	public function init(){}
	protected $_errors;
	
	protected function getMap($oManager)
	{
	    $map = array(
	        'required' => array($this,'checkRequired'),
	        'length' => array($this,'checkString'),
	        'string' => array($this,'checkString'),
	        'number' => array($this,'checkNumber'),
	        'numerical' => array($this,'checkNumber'),
	        'boolean' => array($this,'checkBoolean'),
	        'safe' => array($this,'checkSafe'),
	        'unsafe' => array($this,'checkUnSafe'),
	        'date' =>  array($this,'checkDate'),
	        'default' => array($this,'checkSetDefault'),
	    	'set' => array($this,'checkSetValue'),
	    	'match' => array($this,'checkMatch'),
	    	'email' => array($this,'checkEmail'),
	    	'sessid' => array($this,'checkSessid'),
	    	'captcha' => array($this,'checkCaptcha'),
	    	'file' => array($this,'checkFile'),
	    	'custom' => array($this,'checkSetCustomFilter'),
	    );
	    foreach ($this->fireEvent('BuildValidatorMap') as $oFunction)
	    	$this->execEvent($oFunction, [&$map]);
		return $map;
	}
	
	public function addError($manager,$key,$message,$params=array())
	{
		$labels = $manager->getAttributeLabels();
		$params['#ATTRIBUTE#'] = $labels[$key];
		$this->_errors[] = array('id' => $key , 'text' => strtr($message,$params));
	}
	
	public function isEmpty($value,$trim=false)
	{
		return $value===null || $value===array() || $value==='' || $trim && is_scalar($value) && trim($value)==='';
	}
	
	public function getValueByKey($value,$key)
	{
		return (isset($value[$key])) ? $value[$key] : null;
	}
	
	public function clearFields($manager,&$arFields)
	{
		$result = array();
		$rules = $manager->getRules();
		foreach($rules as $rule)
		{
			foreach(explode(',', $rule[0]) as $key)
			{
				$tmpRule = $rule;
				unset($tmpRule[0],$tmpRule[1]);
				$result[$key][$rule[1]] = $tmpRule;
			}
		}
		foreach($arFields as $key => $field)
		{
			if(
			     !in_array($key,array('MIN_RIGHT','CHECK_RIGHT')) &&
			     !array_key_exists($key, $result)
			)
			{
				unset($arFields[$key]);
			}
		}
	}
	
	/**
	 * @param IEntity $manager
	 * @param array $arFields
	 * @return bool
	 **/
	public function checkFields($manager,&$arFields,$bNew)
	{
		global $APPLICATION;
		$this->_errors = array();
		$result = array();
		$rules = $manager->getRules();
		foreach($rules as $rule)
		{
			foreach(explode(',', $rule[0]) as $key)
			{
				$tmpRule = $rule;
				unset($tmpRule[0],$tmpRule[1]);
				if(array_key_exists('new',$tmpRule))
				{
					if($tmpRule['new'] !== $bNew)
						continue;
				}
				$result[$key][$rule[1]] = $tmpRule;
			}
		}
		$map = $this->getMap($manager);
		foreach($result as $key => $field)
		{
			if(array_key_exists('set', $field))
			{
				call_user_func_array($map['set'] , array($manager,$key,&$arFields,$field['set']));
			}
			else 
			{
				foreach($field as $action => $params)
				{
					if(!$map[$action])
					{
						throw new \Exception("��������� '$action' �� ������");
					}
					else
					{
						if($bNew || array_key_exists($key, $arFields))
							if(!call_user_func_array($map[$action] , array($manager,$key,&$arFields,$params)))
								break;
					}
				}
			}
		}
		if(!empty($this->_errors))
		{
			$e = new \CAdminException($this->_errors);
			$APPLICATION->ThrowException($e);
			return false;
		}
		return true;
	}
	
	public function checkRequired($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'requiredValue' => null,
			'strict' => false,
			'trim' => true,
			'messages_not_exist' =>	"#ATTRIBUTE# ������ ���� ����� #VALUE#.",
			'messages_is_not_required' =>	"���������� ��������� ���� �#ATTRIBUTE#�.",
		);
		$arParams = array_merge($default,$arParams);
		if($arParams['requiredValue'] !== null)
		{
			if(
				!$arParams['strict'] && $value!=$arParams['requiredValue'] ||
				 $arParams['strict'] && $value!==$arParams['requiredValue']
			)
			{
				$message = strtr($arParams['messages_not_exist'],array('#VALUE#'=>$arParams['requiredValue']));
				$this->addError($manager,$key,$message);
				return false;
			}
		}
		elseif($this->isEmpty($value,$arParams['trim']))
		{
			$message = $arParams['messages_is_not_required'];
			$this->addError($manager,$key,$message);
			return false;
		}
		return true;
	}
	
	public function checkString($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => true,
			'min' => null,
			'max' => null,
			'is' => null,
			'message_invalid' => "#ATTRIBUTE# ����� �� ������ ������.",
			'messages_is_not_required' =>	"���������� ��������� ���� �#ATTRIBUTE#�.",
			'message_min' => '#ATTRIBUTE# ������� �������� (�������: #MIN# #WORD#)',
			'message_max' => '#ATTRIBUTE# ������� ������� (��������: #MAX# #WORD#).',
			'message_is' => '#ATTRIBUTE# �������� ����� (������ ���� #LENGTH# #WORD#).',
			'words' => array('������','�������','��������'),
		);
		$arParams = array_merge($default,$arParams);
		if($arParams['allowEmpty'] && $this->isEmpty($value))
			return true;

		if(is_array($value))
		{
			$message = $arParams['message_invalid'];
			$this->addError($manager,$key,$message);
			return false;
		}
		if(!$arParams['allowEmpty'] && $this->isEmpty($value))
		{
			$message = $arParams['messages_is_not_required'];
			$this->addError($manager,$key,$message);
			return false;
		}
		$length=strlen($value);
		if($arParams['min'] !==null && $length < $arParams['min'])
		{
			$message = $arParams['message_min'];
			$this->addError($manager,$key,$message,array('#MIN#' => $arParams['min'] , '#WORD#' => convertNumber($arParams['min'] , $arParams['words']) ));
			return false;
		}
		if($arParams['max'] !== null && $length > $arParams['max'])
		{
			$message = $arParams['message_max'];
			$this->addError($manager,$key,$message,array('#MAX#' => $arParams['max'] , '#WORD#' => convertNumber($arParams['max'] , $arParams['words'])));
			return false;
		}
		if($arParams['is'] !== null && $length !== $arParams['is'])
		{
			$message = $arParams['message_is'];
			$this->addError($manager,$key,$message,array('#LENGTH#'=>$arParams['is'] , '#WORD#' => convertNumber($arParams['is'] , $arParams['words'])));
			return false;
		}
		return true;
	}
	
	public function checkNumber($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => true,
			'integerOnly' => false,
			'integerPattern' => '/^\s*[+-]?\d+\s*$/',
			'numberPattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/',
			'min' => null,
			'max' => null,
			'message_number' => "#ATTRIBUTE# ������ ���� ������.",
			'message_int' => '#ATTRIBUTE# ������ ���� ����� ������.',
			'message_min' => '#ATTRIBUTE# ������� ��� (�������: #MIN#).',
			'message_max' => '#ATTRIBUTE# ������� ����� (��������: #MAX#).',
		);
		$arParams = array_merge($default,$arParams);
		if($arParams['allowEmpty'] && $this->isEmpty($value))
			return true;
	
		if(!is_numeric($value))
		{
			$message = $arParams['message_number'];
			$this->addError($manager,$key,$message);
			return false;
		}
		if($arParams['integerOnly'])
		{
			if(!preg_match($arParams['integerPattern'],"$value"))
			{
				$message = $arParams['message_int'];
				$this->addError($manager,$key,$message);
				return false;
			}
		}
		else
		{
			if(!preg_match($arParams['numberPattern'],"$value"))
			{
				$message = $arParams['message_number'];
				$this->addError($manager,$key,$message);
				return false;
			}
		}
		if($arParams['min'] !==null && $value < $arParams['min'])
		{
			$message = $arParams['message_min'];
			$this->addError($manager,$key,$message,array('#MIN#' => $arParams['min']));
			return false;
		}
		if($arParams['max'] !==null && $value > $arParams['max'])
		{
			$message = $arParams['message_max'];
			$this->addError($manager,$key,$message,array('#MAX#' => $arParams['max']));
			return false;
		}
		return true;
	}
	
	public function checkDate($manager,$key,&$arFields,$arParams)
	{
		global $DB;
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => true,
			'format' => false,
			'format_type' => FORMAT_DATETIME,
			'site' => false,
			'type' => 'SHORT',
			'min' => null,
			'max' => null,
			'message_date' => "#ATTRIBUTE# ������ ���� �����.",
			'message_min' => '#ATTRIBUTE# ������ ���� ����� (�������: #MIN#).',
			'message_max' => '#ATTRIBUTE# ������ ���� ������ (��������: #MAX#).',
		);
		$arParams = array_merge($default,$arParams);
		if($arParams['allowEmpty'] && $this->isEmpty($value))
			return true;
	
		if(!$DB->IsDate($value,$arParams['format'],$arParams['site'],$arParams['type']))
		{
			$message = $arParams['message_date'];
			$this->addError($manager,$key,$message);
			return false;
		}
		if($arParams['min'] !==null && MakeTimeStamp($value,$arParams['format_type']) < $arParams['min'])
		{
			$message = $arParams['message_min'];
			$this->addError($manager,$key,$message,array('#MIN#' => ConvertTimeStamp($arParams['min'],'FULL')));
			return false;
		}
		if($arParams['max'] !==null && MakeTimeStamp($value,$arParams['format_type']) > $arParams['max'])
		{
			$message = $arParams['message_max'];
			$this->addError($manager,$key,$message,array('#MAX#' => ConvertTimeStamp($arParams['max'],'FULL')));
			return false;
		}
		return true;
	}
	
	public function checkBoolean($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => true,
			'strict' => false,
			'trueValue' => 'Y',
			'falseValue' => 'N',
			'message' => "#ATTRIBUTE# ������ ���� #TRUE# ��� #FALSE#.",
		);
		$arParams = array_merge($default,$arParams);
		if($arParams['allowEmpty'] && $this->isEmpty($value))
			return true;
		if(
			(!$arParams['strict'] && $value!=$arParams['trueValue'] && $value!=$arParams['falseValue']) || 
			($arParams['strict'] && $value!==$arParams['trueValue'] && $value!==$arParams['falseValue'])
		)
		{
			$message = $arParams['message'];
			$this->addError($manager,$key,$message,array(
				'#TRUE#' => $arParams['trueValue'],
				'#FALSE#' => $arParams['falseValue'],
			));
			return false;
		}
		return true;
	}
	
	public function checkSiteId($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'message_empty' => "���������� ��������� ���� �#ATTRIBUTE#�.",
			'message_invalid' => "���� #SITE# �� ������.",
		);
		$arParams = array_merge($default,$arParams);
		if($this->isEmpty($value))
		{
			if($arParams['allowEmpty'] === false)
			{
				$this->addError($manager, $key, $arParams['message_empty']);
				return false;
			}
			else
				return true;
		}
				
		$arSiteId = array();
		$oSite = new \CSite();
		$db = $oSite->GetList($by, $order,array('ACTIVE'=>'Y'));
		while($el = $db->Fetch())
			$arSiteId[] = $el['ID'];
		
		foreach((array)$value as $id)
		{
			if(!in_array($id, $arSiteId))
			{
				$this->addError($manager, $key, $arParams['message_invalid'] , array('#SITE#'=>$id));
				return false;
			}
		}
		return true;
	}
	
	public function checkUserId($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'message_empty' => "���������� ��������� ���� �#ATTRIBUTE#�.",
			'message_invalid' => "������������ �#USER_ID# �� ������.",
		);
		$arParams = array_merge($default,$arParams);
		if($this->isEmpty($value) || $value <= 0)
		{
			if($arParams['allowEmpty'] === false)
			{
				$this->addError($manager, $key, $arParams['message_empty']);
				return false;
			}
			else
				return true;
		}
			
		$arSiteId = array();
		$oSite = new \CUser();
		$db = $oSite->GetList($by, $order,array('ACTIVE'=>'Y' , 'ID' => $value));
		if($db->SelectedRowsCount() === 0)
		{
			$this->addError($manager, $key, $arParams['message_invalid'] , array('#USER_ID#'=>$value));
			return false;
		}
		return true;
	}

	public function checkSafe($manager,$key,&$arFields,$arParams)
	{
		return true;
	}
	
	public function setDefault($manager,$key,&$arFields,$arParams)
	{
		$default = array(
			'setOnEmpty' => true,
			'value' => null,
		);
		$arParams = array_merge($default,$arParams);
		if(!$arParams['setOnEmpty'])
			$arFields[$key] = $arParams['value'];
		else
		{
			$value = $this->getValueByKey($arFields,$key);
			if($value===null || $value==='')
				$arFields[$key] = $arParams['value'];
		}
		return true;
	}
	
	public function setValue($manager,$key,&$arFields,$arParams)
	{
		$arFields[$key] = $this->getValueByKey($arParams,'value');
		return true;
	}

	public function checkUnSafe($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		unset($arFields[$key]);
		$arFields['~'.$key] = $value;
		return true;
	}
	
	public function checkExistIBlockSection($manager,$key,&$arFields,$arParams)
	{
		$self = BX::manager('main:Cheker');
		$value = $self->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'IBLOCK_ID' => 17,
			'message_empty' => "���������� ��������� ���� �#ATTRIBUTE#�.",
			'message_invalid' => "������ �#ID# �� �������.",
		);
		$arParams = array_merge($default,$arParams);
		if($self->isEmpty($value) || $value <= 0)
		{
			if($arParams['allowEmpty'] === false)
			{
				$self->addError($manager, $key, $arParams['message_empty']);
				return false;
			}
			else
				return true;
		}
		$oSection = new \CIBlockSection();
		$db = $oSection->GetList(
			array(),
			array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => $value),
			false,
			array('IBLOCK_ID','ID'),
			array('nTopCount' => 1)
		); 
		if($db->SelectedRowsCount() === 0)
		{
			$self->addError($manager, $key, $arParams['message_invalid'] , array('#ID#'=>$value));
			return false;
		}
		return true;
	}
	
	public function checkExistIBlockElement($manager,$key,&$arFields,$arParams)
	{
		$self = BX::manager('main:Cheker');
		$value = $self->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'IBLOCK_ID' => 17,
			'message_empty' => "���������� ��������� ���� �#ATTRIBUTE#�.",
			'message_invalid' => "����� �#ID# �� ������.",
		);
		$arParams = array_merge($default,$arParams);
		if($self->isEmpty($value) || $value <= 0)
		{
			if($arParams['allowEmpty'] === false)
			{
				$self->addError($manager, $key, $arParams['message_empty']);
				return false;
			}
			else
				return true;
		}
		$oElement = new \CIBlockElement();
		$db = $oElement->GetList(
			array(),
			array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => $value),
			false,
			false,
			array('nTopCount' => 1),
			array('IBLOCK_ID','ID')
		);
		if($db->SelectedRowsCount() === 0)
		{
			$self->addError($manager, $key, $arParams['message_invalid'] , array('#ID#'=>$value));
			return false;
		}
		return true;
	}
	
	public function checkExistIBlockPropertyEnum($manager,$key,&$arFields,$arParams)
	{
		$self = BX::manager('main:Cheker');
		$value = $self->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'IBLOCK_ID' => 17,
			'message_empty' => "���������� ��������� ���� �#ATTRIBUTE#�.",
			'message_invalid' => "�������� �#ID# �� �������.",
		);
		$arParams = array_merge($default,$arParams);
		if($self->isEmpty($value) || $value <= 0)
		{
			if($arParams['allowEmpty'] === false)
			{
				$self->addError($manager, $key, $arParams['message_empty']);
				return false;
			}
			else
				return true;
		}
		$oEnum = new \CIBlockPropertyEnum();
		$db = $oEnum->GetList(
			array(),
			array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => $value)
		);
		if($db->SelectedRowsCount() === 0)
		{
			$self->addError($manager, $key, $arParams['message_invalid'] , array('#ID#'=>$value));
			return false;
		}
		return true;
	}
	
	public function checkMatch($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'message_empty' => "���������� ��������� ���� �#ATTRIBUTE#�.",
			'message_match' => "#ATTRIBUTE# ����� �� ������ ������.",
			'regex' => null,
		);
		$arParams = array_merge($default,$arParams);
		if($this->isEmpty($value) || $value <= 0)
		{
			if($arParams['allowEmpty'] === false)
			{
				$this->addError($manager, $key, $arParams['message_empty']);
					return false;
			}
			else
				return true;
		}
		if(!preg_match($arParams['regex'], $value))
		{
			$this->addError($manager, $key, $arParams['message_match']);
			return false;
		}
		return true;
	}
	
	public function checkSessid($manager,$key,&$arFields,$arParams)
	{
		$default = array(
			'message_invalid' => "���� ������ ���� ���������� ������������� ��������� ������.",
			'varname' => 'sessid',
		);
		$arParams = array_merge($default,$arParams);
		if(!check_bitrix_sessid($arParams['varname']))
		{
			$this->addError($manager, $key, $arParams['message_invalid']);
			return false;
		}
		unset($arFields[$key]);
		return true;
	}
	
	public function checkCaptcha($manager,$key,&$arFields,$arParams)
	{
		global $APPLICATION;
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'message_empty' => "���������� ������ ������� � ��������.",
			'message_invalid' => "������� � �������� ������� �������.",
			'sid' => null,
		);
		$arParams = array_merge($default,$arParams);
		if($this->isEmpty($value))
		{
			if($arParams['allowEmpty'] === false)
			{
				$this->addError($manager, $key, $arParams['message_empty']);
				return false;
			}
			else
				return true;
		}
		if(!$APPLICATION->CaptchaCheckCode($value,$arParams['sid']))
		{
			$this->addError($manager, $key, $arParams['message_invalid']);
			return false;
		}
		unset($arFields[$key]);
		return true;
	}
	
	public function checkFile($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => true,
			'is_image' => false,
			'extensions' => false,
			'max_size' => 0,
			'max_width' => 0,
			'max_height' => 0,
			'mime_types' => false,
			'message_empty' => "���������� ��������� ���� �#ATTRIBUTE#�.",
		);
		$arParams = array_merge($default,$arParams);
		if($this->isEmpty($value) || $value <= 0)
		{
			if($arParams['allowEmpty'] === false)
			{
				$this->addError($manager, $key, $arParams['message_empty']);
				return false;
			}
			else
				return true;
		}
		if($arParams['is_image'])
		{
			$strError = \CFile::CheckImageFile($value,$arParams['max_size'],$arParams['max_width'],$arParams['max_height']);
			if(strlen($strError))
			{
				$this->addError($manager, $key, $strError);
				return false;
			}
		}
		else
		{
			$strError = \CFile::CheckFile($value,$arParams['max_size'],$arParams['mime_types'],$arParams['extensions']);
			if(strlen($strError))
			{
				$this->addError($manager, $key, $strError);
				return false;
			}
		}
		return true;
	}
	
	public function customFilter($manager,$key,&$arFields,$arParams)
	{
		if(array_key_exists('this_function',$arParams))
		{
			$sFunction = array($manager,$arParams['this_function']);
			unset($arParams['self_function']);
		}
		if(array_key_exists('function',$arParams))
		{
			$sFunction = $arParams['function'];
			unset($arParams['function']);
			if(array_key_exists('class',$arParams))
			{
				$sFunction = array($arParams['class'],$sFunction);
				unset($arParams['class']);
			}
		}
		if(isset($sFunction))
			return call_user_func_array($sFunction,array($this,$key,&$arFields,$arParams));
		throw new \Exception("������� ��� �������� �� �������");
	}
	
	public function checkFile($manager,$key,&$arFields,$arParams)
	{
		$value = $this->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => true,
			'is_image' => false,
			'extensions' => false,
			'max_size' => 0,
			'max_width' => 0,
			'max_height' => 0,
			'mime_types' => false,
			'message_empty' => "���������� ��������� ���� �#ATTRIBUTE#�.",
		);
		$arParams = array_merge($default,$arParams);
		if($this->isEmpty($value) || $value <= 0)
		{
			if($arParams['allowEmpty'] === false)
			{
				$this->addError($manager, $key, $arParams['message_empty']);
				return false;
			}
			else
				return true;
		}
		if($arParams['is_image'])
		{
			$strError = \CFile::CheckImageFile($value,$arParams['max_size'],$arParams['max_width'],$arParams['max_height']);
			if(strlen($strError))
			{
				$this->addError($manager, $key, $strError);
				return false;
			}
		}
		else 
		{
			$strError = \CFile::CheckFile($value,$arParams['max_size'],$arParams['mime_types'],$arParams['extensions']);
			if(strlen($strError))
			{
				$this->addError($manager, $key, $strError);
				return false;
			}
		}
		return true;
	}
}
