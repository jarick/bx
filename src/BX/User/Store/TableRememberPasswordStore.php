<?php namespace BX\User\Store;

class TableRememberPasswordStore extends TableAuthStore
{
	/**
	 * @var string
	 */
	protected $repository_name = 'remember_password';
	/**
	 * Return settings
	 *
	 * @return array
	 */
	protected function settings()
	{
		return [
			'db_table' => 'tbl_remember_password',
		];
	}
}