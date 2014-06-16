<?php namespace BX\User\Entity;
use BX\Error\Error;
use BX\User\User;
use BX\Validator\IEntity;

class PasswordForm implements IEntity
{
	use \BX\Validator\EntityTrait,
	 \BX\Config\ConfigTrait,
	 \BX\Translate\TranslateTrait;
	const C_NEW = 'NEW';
	const C_OLD = 'OLD';
	const C_USER_ID = 'USER_ID';
	protected function labels()
	{
		return [
			self::C_NEW		 => 'Новый пароль',
			self::C_OLD		 => 'Старый пароль',
			self::C_USER_ID	 => 'ID пользователя',
		];
	}
	protected function rules()
	{
		return [
			[self::C_OLD],
			$this->rule()->custom([$this,'checkOldPassword'])->notEmpty(),
			[self::C_NEW],
			$this->rule()->custom([$this,'filterPassword'])->notEmpty(),
			[self::C_USER_ID],
			$this->rule()->custom([$this,'checkUserID'])->notEmpty(),
		];
	}
	/**
	 * Filter password
	 *
	 * @param string $value
	 */
	public function filterPassword(&$value)
	{
		$value = User::getHashPassword($value);
		if ($value === false){
			return $this->trans('user.entity.user.error_password_min',['#MIN#' => 6]);
		}
	}
	/**
	 * Check old password
	 *
	 * @param string $value
	 */
	public function checkOldPassword(&$value)
	{
		$hash = User::getHashPasswordByUserID($this->getValue(self::C_USER_ID));
		if ($hash === false){
			return $this->trans('user.entity.password_form.bad_old_password');
		}
		if (!User::checkPasswordByHash($value,$hash)){
			return $this->trans('user.entity.password_form.bad_old_password');
		}
	}
	/**
	 * Return is exists user
	 *
	 * @param integer $user_id
	 */
	public function checkUserId($user_id)
	{
		$user = User::GetByID($user_id);
		if ($user === false){
			return $this->trans('user.entity.password_form.user_is_not_found');
		}
	}
}