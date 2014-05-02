<?php namespace BX\User\Entity;

/**
 * @property-read string $id
 * @property-read string $guid
 * @property string $active
 * @property-read string $timestamp_x
 * @property string $name
 * @property string $description
 * @property integer $sort
 */
class UserGroupEntity implements \BX\Validator\IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Date\DateTrait,
	 \BX\Translate\TranslateTrait;
	const C_ID = 'ID';
	const C_GUID = 'GUID';
	const C_ACTIVE = 'ACTIVE';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	const C_NAME = 'NAME';
	const C_DESCRIPTION = 'DESCRIPTION';
	const C_SORT = 'SORT';
	/**
	 * Get labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_ID			 => $this->trans('user.entity.access.id'),
			self::C_GUID		 => $this->trans('user.entity.access.guid'),
			self::C_ACTIVE		 => $this->trans('user.entity.access.active'),
			self::C_TIMESTAMP_X	 => $this->trans('user.entity.access.timestamp_x'),
			self::C_NAME		 => $this->trans('user.entity.access.name'),
			self::C_DESCRIPTION	 => $this->trans('user.entity.access.description'),
			self::C_SORT		 => $this->trans('user.entity.access.sort'),
		];
	}
	/**
	 * Get rules
	 *
	 * @return array
	 */
	protected function rules()
	{
		return [
			[self::C_GUID],
			$this->rule()->setter()->setValidators([
				$this->rule()->string()->notEmpty()->setMax(100),
			])->setValue(uniqid('user_group'))->onAdd(),
			[self::C_ACTIVE],
			$this->rule()->boolean()->setDefault('Y'),
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp()),
			[self::C_NAME],
			$this->rule()->string()->setMax(255)->notEmpty(),
			[self::C_DESCRIPTION],
			$this->rule()->string()->setMax(1000),
			[self::C_SORT],
			$this->rule()->number()->integer()->setMin(0)->setDefault(500),
		];
	}
}