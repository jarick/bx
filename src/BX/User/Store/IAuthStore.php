<?php namespace BX\User\Store;

interface IAuthStore
{
	const ID = 'ID';
	const LOGIN = 'LOGIN';
	const EMAIL = 'EMAIL';
	const UNIQUE_ID = 'UNIQUE_ID';
	const STORE_KEY = 'user.store.auth.store_key';
	public function exits();
	public function save($id,$login,$email);
	public function delete();
}