<?php namespace BX\User\Entity;

/**
 * @property-read integer $id
 * @property integer $user_id
 * @property integer $group_id
 * @property-read strign $timestamp_x
 */
class UserGroupMemberEntity implements \BX\Validator\IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Date\DateTrait,
	 \BX\Translate\TranslateTrait;
	const C_ID = 'ID';
	const C_USER_ID = 'USER_ID';
	const C_GROUP_ID = 'GROUP_ID';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	/**
	 * Return labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_ID			 => $this->trans('user.entity.access.id'),
			self::C_USER_ID		 => $this->trans('user.entity.access.user_id'),
			self::C_GROUP_ID	 => $this->trans('user.entity.access.group_id'),
			self::C_TIMESTAMP_X	 => $this->trans('user.entity.access.timestamp_x'),
		];
	}
	/**
	 * Return rules
	 *
	 * @return array
	 */
	protected function rules()
	{
		return [
			[self::C_USER_ID,self::C_GROUP_ID],
			$this->rule()->number()->integer()->setMin(1)->notEmpty(),
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp()),
		];
	}
}