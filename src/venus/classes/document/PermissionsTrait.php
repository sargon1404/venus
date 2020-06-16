<?php
/**
* The Permissions Trait
* @package Venus
*/

namespace Venus\Document;

use Venus\App;

/**
* The Permissions Trait
* Trait used by an object to prepare it's properties
*/
trait PermissionsTrait
{
	/**
	* @var array $permissions The item's permissions
	*/
	public array $permissions = [];

	/**
	* @internal
	*/
	protected static string $permissions_table = 'venus_items_permissions';

	/**
	* Returns the name of the permissions table
	* @return string
	*/
	public function getPermissionsTable() : string
	{
		return static::$permissions_table;
	}

	/**
	* Checks if the user has the permission to perform an action
	* @param string $permission The permission to check for. Eg: edit/delete etc..
	* @return bool True if the user has the permission to perform an action
	*/
	public function can(string $permission) : bool
	{
		if ($this->permissions[$permission]) {
			return true;
		}

		return false;
	}

	/**
	* Prepares the permissions of the document
	*/
	protected function preparePermissions()
	{
		if (!$this->app->config->users_enable) {
			$this->permissions = $this->getDefaultPermissions();
		} else {
			$permissions = $this->buildPermissions();
			$permissions = $this->mergePermissions($permissions);

			$this->permissions = App::arrayUnset($permissions, ['document_id', 'type', 'usergroup_id' , 'inherit']);
		}
	}

	/**
	* Builds the permissions for the current usergroups. If the inherit permission is checked, copies the default usergroup permissions
	* @return array The permissions
	*/
	protected function buildPermissions() : array
	{
		$default_permissions = null;
		$type = $this->getType();

		$permissions = $this->app->db->selectArrayWithKey($this->getPermissionsTable(), 'usergroup_id', '*', ['document_id' => $this->getId(), 'type' => $type, 'usergroup_id' => $this->app->user->usergroup_ids]);

		//are the permissions inherited? If so, use the default permissions
		foreach ($permissions as $i => $perm) {
			if (!$perm['inherit']) {
				if ($default_permissions === null) {
					$default_permissions = $this->app->user->usergroups->getDefaultPermissions($type);
				}

				$permissions[$i] = $default_permissions[$perm['usergroup_id']];
			}
		}

		return $permissions;
	}

	/**
	* Returns the default permissions of the item
	* @return array The permissions
	*/
	protected function getDefaultPermissions() : array
	{
		$default_permissions = $this->app->user->usergroups->getDefaultPermissions($this->getType());

		return $this->mergePermissions($default_permissions);
	}

	/**
	* Merges the permissions of multiple usergroups
	* @param array $permissions_array The permissions array
	* @return array The merged permissions
	*/
	protected function mergePermissions(array $permissions_array) : array
	{
		$permissions = [];

		if ($this->app->config->usergroup_multiple_permissions) {
			foreach ($this->app->user->usergroup_ids as $usergroup_id) {
				$perm = $permissions_array[$usergroup_id];
				if (!$permissions) {
					$permissions = $perm;
					continue;
				}

				foreach ($perm as $perm_type => $perm_value) {
					if ($perm_value) {
						$permissions[$perm_type] = $perm_value;
					}
				}
			}
		} else {
			$permissions = $permissions_array[$this->app->user->usergroup_id];
		}

		return $permissions;
	}
}
