<?php namespace BX\User\Form;
use BX\User\User;
use BX\Validator\IEntity;
use BX\Form\IForm;

/**
 * @property string $new
 * @property string $old
 * @property integer $user_id
 */
class PasswordForm implements IForm
{
	use \BX\Form\FormTrait;
	const C_NEW = 'NEW';
	const C_OLD = 'OLD';
	const C_USER_ID = 'USER_ID';
	/**
	 * @var string
	 */
	public function getFormName()
	{
		return 'PASSWORD';
	}
	/**
	 * Return labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_NEW		 => 'Новый пароль',
			self::C_OLD		 => 'Старый пароль',
			self::C_USER_ID	 => 'ID пользователя',
		];
	}
	/**
	 * Return fields
	 *
	 * @return array
	 */
	protected function fields()
	{
		return[
			self::C_USER_ID	 => $this->field()->hidden()->required(),
			self::C_OLD		 => $this->field()->text()->required(),
			self::C_NEW		 => $this->field()->text()->required(),
		];
	}
	/**
	 * Filter password
	 *
	 * @param string $value
	 */
	public function validateNew(&$value)
	{
		$value = User::getHashPassword($value);
		if ($value === false){
			return $this->trans('user.form.password.error_password_min',['#MIN#' => 6]);
		}
	}
	/**
	 * Check old password
	 *
	 * @param string $value
	 */
	public function validateOld($value)
	{
		$hash = User::getHashPasswordByUserID($this->getValue(self::C_USER_ID));
		if ($hash === false){
			return $this->trans('user.form.password.bad_old_password');
		}
		if (!User::checkPasswordByHash($value,$hash)){
			return $this->trans('user.form.password.bad_old_password');
		}
	}
	/**
	 * Change password
	 *
	 * @return boolean
	 */
	public function save()
	{
		if ($this->isValid()){
			if (!User::updatePassword($this->getValue(self::C_USER_ID),$this->getValue(self::C_NEW))){
				$error = $this->trans('user.form.password.password_change_error');
				$this->addError(false,$error);
			}else{
				return true;
			}
		}
		return false;
	}
}