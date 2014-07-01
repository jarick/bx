<?php namespace BX\User\Form;
use BX\User\User;
use BX\Error\Error;
use BX\Validator\Exception\ValidateException;

class UserEditForm
{
	use \BX\Form\FormEntityTrait,
	 \BX\Translate\TranslateTrait;
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
					#$this->getEntity()->addError(false,$this->trans('user.form.edit.unknow_error'));
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
					#$this->getEntity()->addError(false,$this->trans('user.form.edit.unknow_error'));
				}
			}else{
				return true;
			}
		}
		return false;
	}
}