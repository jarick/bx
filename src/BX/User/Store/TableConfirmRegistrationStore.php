<?php namespace BX\User\Store;

class TableConfirmRegistrationStore extends TableAuthStore
{
	/**
	 * @var string
	 */
	protected $repository_name = 'confirm_registration';
	/**
	 * Return settings
	 *
	 * @return array
	 */
	protected function settings()
	{
		return [
			'db_table' => 'tbl_confirm_registration',
		];
	}
}