<?php namespace BX\User\Widget;
use BX\User\Widget\UserGroup;
use BX\User\Entity\UserGroupEntity;

class AdminUserGroupListWidget extends BaseAdminListWidget
{
	/**
	 * Delete group
	 *
	 * @param integer $id
	 * @return integer
	 */
	protected function delete($id)
	{
		return UserGroup::delete($id);
	}
	/**
	 * Return filter entity
	 *
	 * @return \BX\User\Entity\UserGroupEntity
	 */
	protected function getFilterEntity()
	{
		return new UserGroupEntity();
	}
	/**
	 * Return flash key
	 *
	 * @return string
	 */
	protected function getFlashKey()
	{
		return 'admin_user_list_widgets';
	}
	/**
	 * Return template
	 *
	 * @return string
	 */
	protected function getTemplate()
	{
		return 'admin/user/group_list';
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
		$return = UserGroup::finder()->sort($sort)->filter($filter);
		if ($offset !== null){
			$return->offset($offset);
		}
		if ($limit !== null){
			$return->limit($limit);
		}
		return $return;
	}
}