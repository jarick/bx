<?php namespace BX\User\Widget;
use BX\MVC\Widget\BaseAdminListWidget;
use BX\User\User;
use BX\User\Form\AdminFilterForm;

class AdminUserListWidget extends BaseAdminListWidget
{
	/**
	 * Delete group
	 *
	 * @param integer $id
	 * @return integer
	 */
	protected function delete($id)
	{
		return User::delete($id);
	}
	/**
	 * Return filter form
	 *
	 * @return \BX\User\Form\AdminFilterForm
	 */
	protected function getFilterForm()
	{
		return new AdminFilterForm();
	}
	/**
	 * Return flash key
	 *
	 * @return string
	 */
	protected function getFlashKey()
	{
		return 'admin_user_widgets';
	}
	/**
	 * Return template
	 *
	 * @return string
	 */
	protected function getTemplate()
	{
		return 'admin/user/list';
	}
	/**
	 * Return SQL Builder
	 *
	 * @param array $sort
	 * @param array $filter
	 * @param type $offset
	 * @param type $limit
	 * @return \BX\DB\Filter\SqlBuilder
	 */
	protected function getList(array $sort,array $filter,$offset = null,$limit = null)
	{
		$return = User::finder()->sort($sort)->filter($filter);
		if ($offset !== null){
			$return->offset($offset);
		}
		if ($limit !== null){
			$return->limit($limit);
		}
		return $return;
	}
}