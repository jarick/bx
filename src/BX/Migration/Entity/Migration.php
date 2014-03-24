<?php namespace BX\Migration\Entity;
use BX\DB\ActiveRecord;
use BX\Validator\Manager\String;
use BX\Validator\Manager\Setter;
use BX\DB\Column\StringColumn;
use BX\DB\Column\TimestampColumn;
use BX\DB\Column\NumberColumn;

class Migration extends ActiveRecord
{
	use \BX\Date\DateTrait;
	const C_PACKAGE = 'PACKAGE';
	const C_SERVICE = 'SERVICE';
	const C_FUNCTION = 'FUNCTION';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_GUID = 'GUID';
	const C_ID = 'ID';
	public function settings()
	{
		return [
			self::DB_TABLE => 'tbl_migrate',
		];
	}
	public function rules()
	{
		return [
			[[self::C_PACKAGE,self::C_SERVICE,self::C_FUNCTION],String::create()->setMax(100)->notEmpty()],
			[self::C_GUID,String::create()->setMax(30)->onAdd()->notEmpty()],
			[self::C_TIMESTAMP_X,Setter::create()->setValue($this->date()->convertTimeStamp())],
		];
	}
	public function labels()
	{
		return [
			self::C_PACKAGE		 => $this->trans('migration.entity.migration.package'),
			self::C_SERVICE		 => $this->trans('migration.entity.migration.service'),
			self::C_FUNCTION	 => $this->trans('migration.entity.migration.function'),
			self::C_TIMESTAMP_X	 => $this->trans('migration.entity.migration.timestamp'),
			self::C_GUID		 => $this->trans('migration.entity.migration.guid'),
		];
	}
	public function columns()
	{
		return [
			self::C_ID			 => NumberColumn::create('T.ID',true),
			self::C_PACKAGE		 => StringColumn::create('T.PACKAGE'),
			self::C_SERVICE		 => StringColumn::create('T.SERVICE'),
			self::C_FUNCTION	 => StringColumn::create('T.FUNCTION'),
			self::C_GUID		 => StringColumn::create('T.GUID'),
			self::C_TIMESTAMP_X	 => TimestampColumn::create('T.TIMESTAMP_X'),
		];
	}
}