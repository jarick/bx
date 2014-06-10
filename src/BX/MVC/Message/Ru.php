<?php namespace BX\MVC\Message;

class Ru
{
	static public function get()
	{
		return [
			'mvc.entity.site.title'							 => 'Заголовок сайта',
			'mvc.entity.site.name'							 => 'Символьный код сайта',
			'mvc.entity.site.keywords'						 => 'Описание сайта',
			'mvc.entity.site.folder'						 => 'Корневая папка сайта',
			'mvc.entity.site.regex'							 => 'Адрес сайта',
			'mvc.widgets.admin_settings.success'			 => 'Данные сохранены',
			'mvc.entity.site.error_layout_rule'				 => 'Поле «#LABEL#» заполнено некорректно',
			'mvc.entity.site.error_url_rewrite'				 => 'Поле «#LABEL#» заполнено некорректно',
			'mvc.widgets.admin_settings.error_session_token' => 'Ваша сессия просрочена пересохраните данные',
		];
	}
}