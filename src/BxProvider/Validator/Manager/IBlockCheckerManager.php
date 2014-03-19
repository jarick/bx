<?php
namespace BX\main\managers;

class CIBlockCheckerManager extends CManager
{
	public function checkExistIBlockSection($manager,$key,&$arFields,$arParams)
	{
		$self = \BX::getManager('main:Checker');
		$oSection = new \CIBlockSection();
		$value = $self->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'IBLOCK_ID' => false,
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
		if($arParams['IBLOCK_ID'] === false)
			throw new \Exception('IBLOCK_ID not found.');
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
		$self = \BX::getManager('main:Checker');
		$value = $self->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'IBLOCK_ID' => false,
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
		if($arParams['IBLOCK_ID'] === false)
			throw new \Exception('IBLOCK_ID not found.');
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
		$self = \BX::getManager('main:Checker');
		$value = $self->getValueByKey($arFields,$key);
		$default = array(
			'allowEmpty' => false,
			'IBLOCK_ID' => false,
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
		if($arParams['IBLOCK_ID'] === false)
			throw new \Exception('IBLOCK_ID not found.');
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
}
