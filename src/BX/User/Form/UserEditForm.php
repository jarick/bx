<?php namespace BX\User\Form;
use BX\User\User;
use BX\Error\Error;
use BX\Validator\Exception\ValidateException;

class UserEditForm
{
	use \BX\Form\FormTrait,
	 \BX\Translate\TranslateTrait,
	 \BX\Logger\LoggerTrait;
	const C_SESSID = 'SESSID';
	const C_LOGIN = 'LOGIN';
	const C_PASSWORD = 'PASSWORD';
	const C_EMAIL = 'EMAIL';
	const C_CODE = 'CODE';
	const C_REGISTERED = 'REGISTERED';
	const C_ACTIVE = 'ACTIVE';
	const C_DISPLAY_NAME = 'DISPLAY_NAME';
	const C_GROUP_ID = 'GROUP_ID';
	/**
	 * @var array
	 */
	private $enums = [];
	/**
	 * Return form name
	 *
	 * @return string
	 */
	public function getFormName()
	{
		return 'FORM';
	}
	/**
	 * Return labels
	 *
	 * @return array
	 */
	protected function labels()
	{
		return [
		 self::C_LOGIN		 => $this->trans('user.form.edit.login'),
		 self::C_PASSWORD	 => $this->trans('user.form.edit.password'),
		 self::C_EMAIL		 => $this->trans('user.form.edit.email'),
		 self::C_CODE		 => $this->trans('user.form.edit.code'),
		 self::C_REGISTERED	 => $this->trans('user.form.edit.registered'),
		 self::C_ACTIVE		 => $this->trans('user.form.edit.active'),
		 self::C_DISPLAY_NAME => $this->trans('user.form.edit.display_name'),
		 self::C_GROUP_ID	 => $this->trans('user.form.edit.group_id'),
		];
	}
	/**
	 * Constructor
	 *
	 * @param array $enums
	 */
	public function __construct(array $enums)
	{
		$this->enums = $enums;
	}
	/**
	 * Return fields
	 *
	 * @return array
	 */
	protected function fields()
	{
		return [
		 self::C_LOGIN		 => $this->field()->text(true),
		 self::C_PASSWORD	 => $this->field()->text(),
		 self::C_EMAIL		 => $this->field()->text(true),
		 self::C_CODE		 => $this->field()->text(),
		 self::C_REGISTERED	 => $this->field()->checkbox(),
		 self::C_ACTIVE		 => $this->field()->checkbox(),
		 self::C_DISPLAY_NAME => $this->field()->text(),
		 self::C_GROUP_ID	 => $this->field()->selectbox($this->enums)->multy(),
		];
	}
	/**
	 * Add user
	 *
	 * @return boolean
	 */
	public function add()
	{
		if ($this->isValid(true)){
			if (!User::add($this->getData())){
				$error = Error::get();
				if ($error instanceof ValidateException){
					foreach($error->all() as $key => $mess){
						$this->fields[$key]->error[] = $mess;
					}
				}else{
					$mess = 'Message: '.$error->getMessage().'.Trace: '.$error->getTraceAsString();
					$this->log('user.form.edit')->err($mess);
					$this->addError($this->trans('user.form.edit.unknow_error'));
				}
			}else{
				return true;
			}
		}
		return false;
	}
	/**
	 * Update user
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function update($id)
	{
		if ($this->isValid(false)){
			if (!User::update($id,$this->getData())){
				$error = Error::get();
				if ($error instanceof ValidateException){
					foreach($error->all() as $key => $mess){
						$this->fields[$key]->error[] = $mess;
					}
				}else{
					$mess = 'Message: '.$error->getMessage().'.Trace: '.$error->getTraceAsString();
					$this->log('user.form.edit')->err($mess);
					$this->addError($this->trans('user.form.edit.unknow_error'));
				}
			}else{
				return true;
			}
		}
		return false;
	}
}