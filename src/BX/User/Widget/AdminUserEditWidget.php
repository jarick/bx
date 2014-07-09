<?php namespace BX\User\Widget;
use BX\MVC\Widget;
use BX\User\User;
use BX\MVC\Exception\PageNotFound;
use BX\User\UserGroup;
use BX\User\UserGroupMember;
use BX\User\Form\PasswordForm;
use BX\User\Form\UserEditForm;

class AdminUserEditWidget extends Widget
{
	const FLASH_KEY = 'admin_user_widgets';
	const ACTION_SAVE = 0;
	const ACTION_UPDATE = 1;
	const ACTION_SAVE_AND_ADD_NEW = 2;
	/**
	 * @var string
	 */
	private $path_to_list = '/admin/user/';
	/**
	 * Return groups
	 *
	 * @return array
	 */
	private function getGroups()
	{
		$return = [];
		$groups = UserGroup::finder()->filter(['ACTIVE' => 'Y'])->all();
		foreach($groups as $group){
			$return[$group->id] = $group;
		}
		return $return;
	}
	/**
	 * Return groups id by user id
	 *
	 * @param integer $user_id
	 * @return array
	 */
	private function getGroupsIdByUserId($user_id)
	{
		$return = [];
		$members = UserGroupMember::finder()->filter(['USER_ID' => $user_id])->all();
		foreach($members as $member){
			$return[] = $member->group_id;
		}
		return $return;
	}
	/**
	 * Return edit form
	 *
	 * @param array $groups
	 * @param integer $id
	 * @param integer $copy
	 * @return \BX\User\Form\UserEditForm
	 * @throws PageNotFound
	 */
	private function getForm(array $groups,$id = 0,$copy = 0)
	{
		$form = new UserEditForm($groups);
		if ($id > 0){
			$user = User::GetByID($id);
			if ($user === false){
				throw new PageNotFound($this->trans('user.widgets.edit.user_not_found'));
			}
			if ($form->update($id)){
				$this->session()->setFlash(self::FLASH_KEY,$this->trans('user.widgets.edit.update_success'));
				$this->onLocalRedirect($id);
			}
		}else{
			if ($copy > 0){
				$user = User::GetByID($copy);
				if ($user === false){
					throw new PageNotFound($this->trans('user.widgets.edit.user_not_found'));
				}else{
					$form->setDefault($user->getData());
				}
			}
			if ($form->add()){
				$this->session()->setFlash(self::FLASH_KEY,$this->trans('user.widgets.edit.add_success'));
				$this->onLocalRedirect($id);
			}
		}
		return $form;
	}
	/**
	 * Return action by request
	 *
	 * @return integer
	 */
	private function getAction()
	{
		$action = intval($this->request()->post()->get('action'));
		if ($action > 2){
			$action = 0;
		}
		return $action;
	}
	/**
	 * Redirect on after save user
	 *
	 * @param integer $id
	 */
	private function onLocalRedirect($id)
	{
		switch ($this->getAction()){
			case self::ACTION_SAVE:
				$this->redirect($this->path_to_list);
				break;
			case self::ACTION_UPDATE:
				$this->redirect($this->getCurPageParam(['id' => $id],['id','post']));
				break;
			case self::ACTION_SAVE_AND_ADD_NEW:
				$this->redirect($this->getCurPageParam([],['id','post']));
				break;
		}
	}
	/**
	 * Recursive trim array values
	 *
	 * @param array $value
	 * @return array
	 */
	protected function trim(array $value)
	{
		foreach($value as &$item){
			if (is_array($item)){
				$item = $this->trim($item);
			}else{
				$item = trim($item);
			}
		}
		return $value;
	}
	/**
	 * Change password
	 */
	private function actionChangePassword()
	{
		$form = new PasswordForm();
		if ($this->request()->post()->get($form->getFormName()) !== null){
			if ($form->save()){
				$error = implode('<br/> ',$form->getErrors()->all());
				$this->view->json(['status' => 0,'message' => $error]);
			}else{
				$error = $this->trans('user.widgets.user_edit.password_change_success');
				$this->view->json(['status' => 1,'message' => $error]);
			}
		}
	}
	/**
	 * Delete user
	 *
	 * @param integer $id
	 */
	public function actionDelete($id)
	{
		$post = $this->request()->post()->get('DELETE');
		if ($id > 0 && $post !== null){
			if (intval($post['SESSION_ID']) === $this->session()->getId()){
				if (User::delete($post['ID'])){
					$this->setFlash(self::FLASH_KEY,$this->trans('user.widgets.user_edit.delete_success'));
					$this->redirect($this->path_to_list);
				}
			}
		}
	}
	/**
	 * Run
	 */
	public function run()
	{
		$id = intval($this->request()->query()->get('id'));
		$copy = intval($this->request()->query()->get('copy'));
		$groups = $this->getGroups();
		if ($id > 0){
			$this->actionChangePassword($id);
			$this->actionDelete($id);
			$form = $this->getForm($groups,$id);
		}elseif ($copy > 0){
			$form = $this->getForm($groups,$id,$copy);
		}else{
			$form = $this->getForm($groups);
		}
		$message = $this->session()->getFlash(self::FLASH_KEY);
		$members = $this->getGroupsIdByUserId($id);
		$params = compact('form','message','id','groups','members');
		$this->render('admin/user/edit',$params);
	}
}