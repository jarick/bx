<?php namespace BX\Captcha\Entity;
use \BX\Validator\IEntity;

/**
 * @property string $sid
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
	const C_SID = 'SID';
	const C_CODE = 'CODE';
	const C_UNIQUE_ID = 'UNIQUE_ID';
	const C_TIMESTAMP_X = 'TIMESTAMP_X';
	protected function labels()
	{
		return [
			self::C_ID			 => $this->trans('captcha.entity.id'),
			self::C_SID			 => $this->trans('captcha.entity.sid'),
			self::C_CODE		 => $this->trans('captcha.entity.code'),
			self::C_UNIQUE_ID	 => $this->trans('captcha.entity.unique_id'),
			self::C_TIMESTAMP_X	 => $this->trans('captcha.entity.timestamp_x'),
		];
	}
	protected function rules()
	{
		return[
			[self::C_SID,self::C_CODE,self::C_UNIQUE_ID],
			$this->rule()->string()->setMax(32)->setMin(6)->notEmpty(),
			[self::C_TIMESTAMP_X],
			$this->rule()->setter()->setValidators([
				$this->rule()->datetime()->withTime()->notEmpty()
			])->setValue($this->date()->convertTimeStamp()),
		];
	}
	public function check($sid,$code)
	{
		$check_code = $this->string()->toUpper($this->code) !== $this->string()->toUpper($code);
		if ($this->sid !== $sid || $check_code){
			$this->addError(self::C_CODE,$this->trans('captcha.entity.error_check'));
			return false;
		}
		return true;
	}
}