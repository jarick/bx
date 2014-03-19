<?php
namespace BX\main\managers;
use BX\Manager;

class UrlManager extends Manager
{
	protected function checkGetRequestParams($arParams)
	{
		foreach($arParams['get'] as $strKey => $strValue)
			if(get_request($strKey,'GET') !== $strValue)
				return false;
		return true;
	}
	
	protected function checkPostRequestParams($arParams)
	{
		foreach($arParams['post'] as $strKey => $strValue)
			if(get_request($strKey,'POST') !== $strValue)
				return false;
		return true;
	}
	
	public function routing($arRules)
	{
		foreach($arRules['rules'] as &$params)
		{
			$params['method'] = (isset($params['method'])) ? $params['method'] : 'all';
			$tmp = array();
			if(isset($params['param']))
			{
				foreach($params['param'] as $val)
					$tmp[] = $val;
			}
			$params['param'] = $tmp;
		}
		unset($params);
				
		$folder = $arRules['folder'];
		$folder = trim($folder,'/');
		$folder = (strlen($folder) > 0) ? '/'.$folder.'/' : '/' ;
		$pathInfo = explode('?', $_SERVER['REQUEST_URI'], 2);
		$pathInfo = $pathInfo[0];
		$pathInfo = str_replace('index.php', '', $pathInfo);
		if(strpos($pathInfo, $folder) !== 0)
		{
			return false;
		}
		$pathInfo = substr($pathInfo, strlen($folder));
		if( strlen($pathInfo) === 0 )
			$pathInfo = '/';
		
		foreach($arRules['rules'] as $params)
		{
			$action = $params['action'];
			if( $params['method'] === 'get' )
				if($_SERVER['REQUEST_METHOD'] === 'POST')
					continue;
			if( $params['method'] === 'post' )
				if($_SERVER['REQUEST_METHOD'] !== 'POST')
					continue;
			if(array_key_exists('bzn', $params))
				if(!$params['bzn'])
					continue;
			if(array_key_exists('sessid', $params))
			{
				if(!check_bitrix_sessid())
					continue;
			}
			if(array_key_exists('get', $params))
			{
				if(!$this->checkGetRequestParams($params))
					continue;
			}
			if(array_key_exists('Post', $params))
			{
				if(!$this->checkPostRequestParams($params))
					continue;
			}
			if(array_key_exists('pattern', $params))
			{
				if(preg_match($params['pattern'], $pathInfo,$match))
				{
					$arParams = array();
					foreach($match as $key => $value)
					{
						if($key === 0)
							continue;
						$arParams['@'.$key] = $value;
					}
					if(is_array($action))
					{
						foreach($action as &$val)
						{
							if(is_string($val))
							{
								foreach($arParams as $key => $value)
								{
									$val = str_replace($key, $value, $val);
								}
							}
						}
						unset($val);
					}
					else 
					{
						if(is_string($action))
						{
							foreach($arParams as $key => $value)
							{
								$action = str_replace($key, $value, $action);
							}
						}
					}
					foreach($params['param'] as &$val)
					{
						if(is_string($val) && strpos($val, '@') !== false)
						{
							foreach($arParams as $key => $value)
							{
								$val = str_replace($key, $value, $val);
							}
						}
					}
					unset($val);
				}
				else 
					continue;
			}
			call_user_func_array($action,$params['param']);
			return true;
		}
		return false;
	}
}