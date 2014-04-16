<?php namespace BX\Migration\Entity;
use BX\Validator\IEntity;

/**
 * @property string $package
 * @property string $service
 * @property string $function
 * @property-read string $timestamp
 * @property string $guid
 * @property-read integer $id
 */
class MigrationEntity implements IEntity
{
	use \BX\Date\DateTrait,
	 \BX\Validator\EntityTrait;
	const C_PACKAGE = 'PACKAGE';
	const C_SERVICE = 'SERVICE';
	const C_FUNCTION = 'FUNCTION';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_GUID = 'GUID';
	const C_ID = 'ID';
	/**
	 * Get rules
	 * @return array
	 */
	public function rules()
	{
		return [
			[self::C_PACKAGE,self::C_SERVICE,self::C_FUNCTION],
			$this->rule()->string()->setMax(100)->notEmpty()
			[self::C_GUID],
			$this->rule()->string()->setMax(30)->onAdd()->notEmpty()
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValue($this->date()->convertTimeStamp())
		];
	}
	/**
	 * Get labels
	 * @return array
	 */
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
}