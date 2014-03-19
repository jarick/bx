<?php
namespace BX\gii\entities;
/**
 * @property string $service
 * @property string $manager
 * @property string $module
 * @property string $is_event
 * @property string $event
 * @property string $is_tag
 * @property string $tag
 * @property string is_u_field
 * @property string $u_field
 * @property string $is_permission
 * @property string $permission_x
 * @property string $permission_w
 * @property string $permission_r
 * @property string $permission_table
 * @property string $permission_binding
 * @property string $db_table
 **/
class ManagerEntity
{
	protected $arData;
	public function __construct()
	{
		$this->arData = array(
			'SERVICE' => null,
			'MANAGER' => null,
			'MODULE' => null,
			'IS_EVENT' => null,
			'EVENT' => null,
			'IS_TAG' => null,
			'TAG' => null,
			'IS_U_FIELD' => null,
			'U_FIELD' => null,
			'IS_PERMISSION' => null,
			'PERMISSION_X' => null,
			'PERMISSION_W' => null,
			'PERMISSION_R' => null,
			'PERMISSION_TABLE' => null,
			'PERMISSION_BINDING' => null,
			'RELATION' => null,
			'IS_ENTITY' => null,
			'DB_TABLE' => null,
		);
	}

	public function __isset($key)
	{
		$key = ToUpper($key);
		return array_key_exists($key, $this->arData);
	}

	public function __get($key)
	{
		$key = ToUpper($key);
		if(array_key_exists($key, $this->arData))
			return $this->arData[$key];
		else
			throw new \Exception("`$key` is not field ".get_class($this));
	}

	public function __set($key,$value)
	{
		$key = ToUpper($key);
		if(array_key_exists($key, $this->arData))
			$this->arData[$key] = $value;
		else
			throw new \Exception("`$key` is not field ".get_class($this));
	}

	public function getData()
	{
		foreach($this->arData as $key => $value)
		{
			if($value !== null)
			{
				$result[$key] = $value;
			}
		}
		return $result;
	}

	public function setData($values)
	{
		foreach($this->arData as $key => &$value)
		{
			$value = (isset($values[$key])) ? $values[$key] : null;
		}
	}
	
	public function setDbTable($strTable)
	{
		global $DB;
		$dbColumn = $DB->Query("SHOW COLUMNS FROM `".$DB->ForSql($strTable)."`");
		while($arColumn = $dbColumn->Fetch())
			$this->arData['LABEL_'.ToUpper($arColumn['Field'])] = null;
	}
}