<?php namespace BX\User\Message;
use BX\Translate\IMessageFile;

class Ru implements IMessageFile
{
	static public function get()
	{
		return [
			'user.entity.user.login'						 => 'Логин',
			'user.entity.user.id'							 => 'Номер',
			'user.entity.user.guid'							 => 'Внешний код',
			'user.entity.user.email'						 => 'E-mail',
			'user.entity.user.create_date'					 => 'Дата добавления',
			'user.entity.user.timestamp_x'					 => 'Дата изменения',
			'user.entity.user.registered'					 => 'Регистрация',
			'user.entity.user.active'						 => 'Активность',
			'user.entity.user.code'							 => 'Символьный код',
			'user.entity.user.display_name'					 => 'Имя на сайте',
			'user.entity.user.password'						 => 'Пароль',
			'user.widgets.user_edit.session_token_error'	 => 'Ваша сессия просрочена, пересохраните данные.',
			'user.widgets.edit.update_success'				 => 'Данные пользователя обновлены.',
			'user.widgets.user_edit.password_change_success' => 'Пароль изменен.',
			'user.entity.password_form.bad_old_password'	 => 'Не верно введен старый пароль.',
			'user.entity.user.error_password_min'			 => 'Пароль слишком короткий, минимальная длина #MIN# символов',
			'user.widgets.user_edit.delete_success'			 => 'Пользователь удален.',
			'user.widgets.user_edit.password_change_error'	 => 'Произошла ошибка при сохранении нового пароля, пожалуйста, пересохраните данные.',
			'user.widgets.edit.update_error'				 => 'Произошла ошибка при изменении пользователя, пожалуйста, пересохраните данные.',
			'user.widgets.edit.add_error'					 => 'Произошла ошибка при добавлении пользователя, пожалуйста, пересохраните данные.',
		];
	}
}