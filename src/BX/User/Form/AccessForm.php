<?php namespace BX\User\Form;
use BX\User\User;
use BX\User\Auth;

class AccessForm
{
	use \BX\Form\FormTrait,
	 \BX\Translate\TranslateTrait;
	const C_LOGIN = 'NEW';
	const C_PASSWORD = 'OLD';
	const C_SAVE = 'USER_ID';
	/**
	 * @var \BX\User\Entity\UserEntity
	 */
	private $user = null;
	/**
	 * Return labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
			self::C_LOGIN	 => 'Логин',
			self::C_PASSWORD => 'Пароль',
			self::C_SAVE	 => 'Запомнить меня',
		];
	}
	/**
	 * Return array of fields
	 *
	 * @return array
	 */
	protected function fields()
	{
		return[
			self::C_PASSWORD => $this->field()->password(),
			self::C_LOGIN	 => $this->field()->text()->required(),
			self::C_SAVE	 => $this->field()->checkbox(),
		];
	}
	/**
	 * Filter password
	 *
	 * @param string $value
	 */
	public function validateLogin($value)
	{
		$user = User::finder()
			->filter(['LOGIN' => $value])
			->limit(1)
			->get();
		if ($user === false){
			return $this->trans('user.form.access_form.login_not_found');
		}else{
			$this->user = $user;
		}
	}
	/**
	 * Check old password
	 *
	 * @param string $value
	 */
	public function validatePassword(&$value)
	{
		if ($this->user !== null){
			$hash = User::getHashPasswordByUserID($this->user->id);
			if ($hash === false){
				return $this->trans('user.form.access_form.bad_password');
			}
			$value = User::getHashPassword($value);
			if (!User::checkPasswordByHash($value,$hash)){
				return $this->trans('user.form.access_form.bad_password');
			}
		}
	}
	/**
	 * Auth user
	 *
	 * @return boolean
	 */
	public function auth()
	{
		if ($this->isValid()){
			if (Auth::login($this->user->guid)){
				$this->addError(false,$this->trans('user.form.access_form.unknow_error'));
			}else{
				return true;
			}
		}
		return false;
	}
}