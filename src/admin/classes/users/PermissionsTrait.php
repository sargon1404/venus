<?php
/**
* The Permissions Trait
* @package Venus
*/

namespace Venus\Admin\Users;

/**
* The Permissions Trait
* Implements the user's items permissions system. Determines if the user can perform certain actions
*/
trait PermissionsTrait
{
	/**
	* @var array default_permissions  The admin's permissions, if no permissions are set for the admin for a certain block
	*/
	protected array $default_permissions = [
		'view' => 1,
		'add' => 1,
		'publish' => 1,
		'publish_own' => 1,
		'edit' => 1,
		'edit_own' => 1,
		'delete' => 1,
		'delete_own' => 1
	];

	/**
	* @var array $permissions The permissions of this user
	*/
	protected array $permissions = ['view' => 1, 'comment' => 1, 'rate' => 1, 'add' => 1, 'publish' => 0, 'publish_own' => 0, 'edit' => 0, 'edit_own' => 0, 'delete' => 0, 'delete_own' => 0];

	/**
	* @var array $permissions_own The defined own permissions
	*/
	protected array $permissions_own = ['publish' => 'publish_own', 'edit' => 'edit_own', 'delete' => 'delete_own'];

	/**
	* @var array $own_item_ids Array storing the ids of the items the current user has created. Used to determine if the user owns the item and has the edit_own/publish_own/delete_own permissions
	*/
	protected array $own_item_ids = [];

	/**
	* Returns the default permissions
	* @return array
	*/
	public function getDefaultPermissions() : array
	{
		return $this->default_permissions;
	}

	/**
	* Returns the permissions
	* @return array
	*/
	public function getPermissions() : array
	{
		return $this->permissions;
	}

	/**
	* Sets the permissions
	* @param array $permissions The permissions
	* @return $this
	*/
	public function setPermissions(array $permissions)
	{
		$this->permissions = $permissions;

		return $this;
	}

	/**
	* Adds an item to the list of items created by the current user. Will add it only if $created_by equals the user_id of the current user
	* @param string $item_id The item's id
	* @param int $created_by The user who created the item
	* @return $this
	*/
	public function addOwnItem(string $item_id, int $created_by)
	{
		if ($this->app->user->id == $created_by) {
			$this->own_item_ids[] = $item_id;
		}

		return $this;
	}

	/**
	* Returns true if the user has the permission to perform an action
	* Eg: Returns true if the user has the 'edit' permissions. If he doesn't, but has the 'edit_own' permission and $item_id is specified, will return true if $item_id was declared as an own item using addOwnItem()
	* @param string $permission The permission to check for: Eg: add/edit/delete
	* @param string $item_id If specified will check if the user can perform the action on the item. The item must be marked as belonging to the user with addOwnItem()
	* @return bool Returns true if the user can perform the action, false otherwise
	*/
	public function can(string $permission, string $item_id = '') : bool
	{
		if ($item_id) {
			return $this->canDoOnOwnItem($permission, $item_id);
		} else {
			return $this->canDo($permission);
		}
	}

	/**
	* Checks if the user has a certain permission
	* @param string $permission The permission to check for: Eg: add/edit/delete
	* @param bool $check_own If true, will also check the own permissions
	*/
	public function hasPermission(string $permission, bool $check_own = true) : bool
	{
		if (!empty($this->permissions[$permission])) {
			return true;
		}

		if ($check_own && isset($this->permissions_own[$permission])) {
			if (!empty($this->permissions[$this->permissions_own[$permission]]) && $this->own_item_ids) {
				return true;
			}
		}

		return false;
	}

	/**
	* Checks if the user has the permission to perform an action. If false, it will output a permission denied screen
	* @param string $permission The permission to check for: Eg: add/edit/edit_own/delete etc...
	* @param int $created_by The id of the user who created the item
	*/
	public function checkPermission(string $permission, int $created_by = 0)
	{
		if ($created_by) {
			if (!$this->canDoOnCreatedItem($permission, $created_by)) {
				$this->app->permissionDenied();
			}
		} elseif (!$this->canDo($permission)) {
			$this->app->permissionDenied();
		}
	}

	/**
	* Checks if the user has the permission to perform an action on multiple items
	* @param string $permission The permission to check for: Eg: add/edit/edit_own/delete etc...
	* @param array $item_ids Array with the ids of the items to check permissions for
	* @param string $table The database table
	* @param string $id_field The database id column
	* @param string $created_by_field The created by column.
	* @return array The item ids the user has permission to perform the action
	*/
	public function checkItemsPermission(string $permission, array &$item_ids, string $table, string $id_field, string $created_by_field = 'created_by') : array
	{
		$item_ids = $this->canDoOnCreatedItems($permission, $item_ids, $table, $id_field, $created_by_field);
		if ($item_ids === false) {
			$this->app->permissionDenied();
		}

		return $item_ids;
	}

	/**
	* Returns true if the user has the permission to perform an action
	* @param string $permission The permission to check for: Eg: add/edit/edit_own/delete etc...
	* @return bool
	*/
	protected function canDo(string $permission) : bool
	{
		if (empty($this->permissions[$permission])) {
			return false;
		}

		return true;
	}

	/**
	* Returns true if the user has the permission to perform an action on an item
	* @param string $permission The permission to check for: Eg: add/edit/edit_own/delete etc...
	* @param string $item_id The item's id
	* @return bool
	*/
	protected function canDoOnOwnItem(string $permission, string $item_id) : bool
	{
		if ($this->canDo($permission)) {
			return true;
		}

		if (isset($this->permissions_own[$permission])) {
			if (!empty($this->permissions[$this->permissions_own[$permission]])) {
				return in_array($item_id, $this->own_item_ids);
			}
		}

		return false;
	}

	/**
	* Returns true if user has the permission to perform an action on an item. An item is considered to belong to the user if it was created by him
	* @param string $permission The permission to check for: Eg: add/edit/edit_own/delete etc...
	* @param int $created_by The id of the user who created the item
	* @return bool
	*/
	protected function canDoOnCreatedItem(string $permission, int $created_by) : bool
	{
		if ($this->canDo($permission)) {
			return true;
		}

		if (isset($this->permissions_own[$permission])) {
			if (!empty($this->permissions[$this->permissions_own[$permission]])) {
				if ($created_by == $this->app->user->id) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	* Returns the list of item ids for which the user can perform an action
	* @param string $permission The permission to check for: Eg: add/edit/edit_own/delete etc...
	* @param array $item_ids Array listing the item ids for which to check the user's permissions. It's passed by reference and will be populated with the item ids which the user has permissions to
	* @param string $table The database table
	* @param string $id_field The database id column
	* @param string $created_by_field The created by columnOnly those items with $created_by_field matching the user's id will be kept in $item_ids
	* @return bool|array Returns false,if the user doesn't have the permission set, the list of IDs if he has
	*/
	protected function canDoOnCreatedItems(string $permission, array &$item_ids, string $table, string $id_field, string $created_by_field = 'created_by')
	{
		if ($this->canDo($permission)) {
			return $item_ids;
		}

		if (isset($this->permissions_own[$permission])) {
			if (!empty($this->permissions[$this->permissions_own[$permission]])) {
				if ($item_ids) {
					$item_ids = $this->getCreatedItemIds($item_ids, $table, $id_field, $created_by_field);
				}

				return $item_ids;
			}
		}

		$item_ids = [];

		return false;
	}

	/**
	* Returns the item_ids which were created by the current user
	* @param array $item_ids Array listing the item ids to return the data for
	* @param string $table The database table
	* @param string $id_field The database id column
	* @param string $created_by_field The created by column
	* @return array
	*/
	protected function getCreatedItemIds(array $item_ids, $table, $id_field, $created_by_field) : array
	{
		if (!$item_ids) {
			return [];
		}

		return $this->app->db->sql->select($id_field)->from($table)->whereIn($id_field, $item_ids)->where([$created_by_field => $this->app->user->id])->getFields();
	}
}
