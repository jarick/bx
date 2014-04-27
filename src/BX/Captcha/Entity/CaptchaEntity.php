<?php namespace BX\Captcha\Entity;
use \BX\Validator\IEntity;

/**
 * @property string $guid
 * @property string $code
 * @property string $unique_id
 * @property integer $timestamp_x
 */
class CaptchaEntity implements IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Date\DateTrait,
	 \BX\Translate\TranslateTrait;
	const C_ID = 'ID';
	const C_GUID = 'GUID';
	const C_CODE = 'CODE';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	/**
	 * Labels
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_ID			 => $this->trans('captcha.entity.id'),
			self::C_GUID		 => $this->trans('captcha.entity.guid'),
			self::C_CODE		 => $this->trans('captcha.entity.code'),
			self::C_TIMESTAMP_X	 => $this->trans('captcha.entity.timestamp_x'),
		];
	}
	/**
	 * Rules
	 * @return array
	 */
	protected function rules()
	{
		return[
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp()),
			[self::C_GUID],
			$this->rule()->setter()->setValue(uniqid())->setValidators([
				$this->rule()->string()->setMax(32)->setMin(6)->notEmpty(),
			])->onAdd(),
			[self::C_CODE],
			$this->rule()->setter()->setValue($this->getRandomHash())->setValidators([
				$this->rule()->string()->setMax(32)->setMin(6)->notEmpty(),
			]),
		];
	}
	/**
	 * Return random hash
	 * @return string
	 */
	private function getRandomHash()
	{
		return md5($this->string()->getRandString(8));
	}
}