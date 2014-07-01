<?php namespace BX\Http\Message;
use BX\Translate\IMessageFile;

class Ru implements IMessageFile
{
	public static function get()
	{
		return[
			'validator.form_trait.session_error' => 'Ваша сессия просрочена, пересохраните данные.',
		];
	}
}