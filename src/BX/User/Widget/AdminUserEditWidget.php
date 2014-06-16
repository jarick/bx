<?php namespace BX\User\Widget;
use BX\MVC\Widget;
use BX\User\User;
use BX\MVC\Exception\PageNotFound;
use BX\User\Entity\UserEntity;
use BX\User\UserGroup;
use BX\User\UserGroupMember;
use BX\User\Entity\PasswordForm;
use BX\Validator\IEntity;

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
	 * Return current object of UserEntity
	 *
	 * @param integer $id
	 * @return \BX\User\Entity\UserEntity
	 * @throws PageNotFound
	 */
	private function getUser($id)
	{
		$post = $this->request()->post()->get('FORM');
		if ($id > 0){
			$user = User::GetByID($id);
			if ($user === false){
				throw new PageNotFound($this->trans('user.widgets.edit.user_not_found'));
			}
			if ($post !== null){
				$save = array_merge($user->getData(),$this->trim($post));
				$user->setData($save);
				if ($this->checkFields($save,$user) && $user->checkFields($save,false)){
					if (User::update($id,$save)){
						$this->session()->setFlash(self::FLASH_KEY,$this->trans('user.widgets.edit.update_success'));
						$this->onLocalRedirect($id);
					}else{
						$user->addError(false,$this->trans('user.widgets.edit.update_error'));
					}
				}
			}
		}else{
			$user = new UserEntity();
			if ($post !== null){
				$user->setData($this->trim($post));
				if ($this->checkFields($post,$user) && $user->checkFields($post,true)){
					$id = User::add($post);
					if ($id !== false){
						$this->session()->setFlash(self::FLASH_KEY,$this->trans('user.widgets.edit.add_success'));
						$this->onLocalRedirect($id);
					}else{
						$user->addError(false,$this->trans('user.widgets.edit.add_error'));
					}
				}
			}
		}
		return $user;
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
	 * Return check fields
	 *
	 * @param array $post
	 * @param \BX\User\Entity\UserEntity $user
	 * @return boolean
	 */
	private function checkFields(array &$post,IEntity $user)
	{
		if (isset($post['SESSION_ID'])){
			$session_id = intval($post['SESSION_ID']);
		}else{
			$session_id = null;
		}
		if ($session_id !== $this->session()->getId()){
			$user->addError(false,$this->trans('user.widgets.user_edit.session_token_error'));
			return false;
		}
		return true;
	}
	/**
	 * Change password
	 *
	 * @param integer $id
	 */
	private function actionChangePassword($id)
	{
		$post = $this->request()->post()->get('PASSWORD');
		if ($id > 0 && $post !== null){
			$form = new PasswordForm();
			$form->setData($post);
			if ($this->checkFields($post,$form) && $form->checkFields($post)){
				if (!User::updatePassword($form->user_id,$form->new)){
					$error = $this->trans('user.widgets.user_edit.password_change_error');
					$form->addError(false,$error);
				}
			}
			if ($form->hasErrors()){
				$error = implode(', ',$form->getErrors()->all());
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
	 *
	 * @throws PageNotFound
	 */
	public function run()
	{
		$id = intval($this->request()->query()->get('id'));
		$this->actionChangePassword($id);
		$this->actionDelete($id);
		$user = $this->getUser($id);
		$message = $this->session()->getFlash(self::FLASH_KEY);
		if ($user->hasErrors()){
			$error = $user->getErrors();
		}else{
			$error = false;
		}
		$groups = $this->getGroups();
		$members = $this->getGroupsIdByUserId($id);
		$session_id = $this->session()->getId();
		$params = compact('user','message','id','error','groups','members','session_id');
		$this->render('admin/user/user_edit',$params);
	}
}