<?php
namespace  BxProvider;

trait EntityTrait 
{
	public function getDataForIBlock()
	{
		$aData = $this->arData;
		$aProp = [];
		$aIBlock = []; 
		foreach($this->arData as $key => $value)
		{
			if($value !== null)
			{
				if($this->startWith($key,'PROPERTY_'))
					$aProp[substr($key, 9)] = $value;
				else 
					$aIBlock[$key] = $value;
			}
		}
		return $aIBlock['PROPERTY_VALUES'] = $aProp;
	}
	
	public function setDataForIBlock($values)
	{
		foreach($values as $strKey => $strValue)
		{
			if($strKey === 'PROPERTY_VALUES')
			{
				foreach($strValue as $strPropKey => $strPropValue)
				if(array_key_exists('PROPERTY_'.$this->toUpper($strPropKey), $this->arData))
					$this->arData['PROPERTY_'.$this->toUpper($strPropKey)] = $strPropValue;
			}
			else
			{
				if(array_key_exists($this->toUpper($strKey), $this->arData))
					$this->arData[$this->toUpper($strKey)] = $strValue;
			}
		}
	}
}