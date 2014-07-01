<?php namespace BX\User\Widget;
use BX\MVC\Widget;
use BX\User\Form\AccessForm;

class AdminUserLoginWidget extends Widget
{
	public function run()
	{
		$form = new AccessForm();
		if ($form->auth()){
			$url = $this->request()->query()->get('back_url');
			if ($url === null){
				$url = $this->getCurPageParam([],['post']);
			}
			$this->redirect($url);
		}
		$this->render('admin/user/login',compact('form'));
	}
}