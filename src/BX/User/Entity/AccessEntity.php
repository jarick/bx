<?php namespace BX\User\Entity;

/**
 * @property-read string $id
 * @property integer $user_id
 * @property string $guid
 * @property string $token
 * @property-read string $timestamp_x
 */
class AccessEntity implements \BX\Validator\IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Date\DateTrait,
	 \BX\Translate\TranslateTrait;
	const C_ID = 'ID';
	const C_USER_ID = 'USER_ID';
	const C_GUID = 'GUID';
	const C_TOKEN = 'TOKEN';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	/**
	 * Get labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_ID			 => $this->trans('user.entity.access.id'),
			self::C_USER_ID		 => $this->trans('user.entity.access.user_id'),
			self::C_GUID		 => $this->trans('user.entity.access.guid'),
			self::C_TOKEN		 => $this->trans('user.entity.access.login'),
			self::C_TIMESTAMP_X	 => $this->trans('user.entity.access.timestamp_x'),
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
			[self::C_USER_ID],
			$this->rule()->number()->integer()->setMin(1)->notEmpty(),
			[self::C_GUID,self::C_TOKEN],
			$this->rule()->string()->notEmpty()->setMax(100),
			[self::C_TOKEN],
			$this->rule()->setter()->setFunction([$this,'filterToken'])->setValidators([
				$this->rule()->string()->setMax(100)->notEmpty(),
			]),
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp()),
		];
	}
	/**
	 * Filter token
	 *
	 * @return string
	 */
	public function filterToken()
	{
		return password_hash($this->token,PASSWORD_BCRYPT);
	}
}