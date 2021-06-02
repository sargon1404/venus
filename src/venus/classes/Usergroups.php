<?php
/**
* The Usergroups Class
* @package Venus
*/

namespace Venus;

/**
* The Usergroups Class. Container for usergroups
*/
class Usergroups extends Items
{
	/**
	* @internal
	*/
	protected static string $table = 'venus_usergroups';

	/**
	* @internal
	*/
	protected static string $user_usergroups_table = 'venus_users_usergroups';

	/**
	* @internal
	*/
	protected static string $class = '\Venus\Usergroup';

	/**
	* @internal
	*/
	protected static string $title_name = 'title';

	/**
	* Returns the user usergroups table
	* @return string
	*/
	protected function getUserUsergroupsTable() : string
	{
		return static::$user_usergroups_table;
	}

	/**
	* Returns the ids of the usergroups as array
	* @param array $ids Ignored
	* @return array
	*/
	public function getIds($ids = []) : array
	{
		return $this->ids;
	}

	/**
	* Finds a usergroup from the loaded groups. If nothing is found, returns the guests usergroup
	* @param int $id The id of the usergroup to find
	* @return Usergroup The usergroup
	*/
	public function find(int $id) : Usergroup
	{
		$usergroup = parent::find($id);
		if (!$usergroup) {
			$usergroup = $this->getGuests();
		}

		return $usergroup;
	}

	/**
	* Returns the data of an usergroup
	* @param int $id The usergroup for which to return the data
	* @return Usergroup The usergroup
	*/
	public function get(int $id) : Usergroup
	{
		if ($id == APP::USERGROUPS['guests']) {
			return $this->getGuests();
		}

		return $this->getObject($this->app->env->getUsergroup($id));
	}

	/**
	* Returns the data of the guests usergroup
	* @return object The guests usergroup
	*/
	public function getGuests() : Usergroup
	{
		static $usergroup_guests = [];

		if (!$usergroup_guests) {
			$usergroup_guests = $this->app->cache->get('usergroup_guests');
		}

		return $this->getObject($usergroup_guests);
	}

	/**
	* Loads the guests usergroup
	* @return $this
	*/
	public function loadGuests()
	{
		$this->setData([$this->getGuests()], false);

		return $this;
	}

	/**
	* Loads all the usergroups an user belongs to
	* @param User $user The user
	* @param bool $include_primary_usergroup_id If true will include the primary usergroup in the list of usergroups
	* @return $this
	*/
	public function loadByUser(User $user, bool $include_primary_usergroup_id = true)
	{
		$usergroups_table = $this->getUserUsergroupsTable();
		if (!$user->id) {
			return;
		}

		$usergroup_ids = $this->db->selectField($usergroups_table, 'usergroup_id', ['user_id' => $user->id]);
		if ($include_primary_usergroup_id) {
			$usergroup_ids = array_merge([$user->usergroup_id], $usergroup_ids);
		}
		$usergroup_ids = array_unique($usergroup_ids);
		if (!$usergroup_ids) {
			return;
		}

		$usergroups = [];
		foreach ($usergroup_ids as $usergroup_id) {
			$usergroups[] = $this->get($usergroup_id);
		}

		$this->setData($usergroups, false);

		return $this;
	}

	/**
	* Returns the default permissions of an item for all the usergroups
	* @param string $type The item's type
	* @return array The permissions
	*/
	public function getDefaultPermissions(string $type) : array
	{
		static $default_permissions = null;

		if ($default_permissions === null) {
			$default_permissions = $this->app->cache->get('usergroups_permissions');
		}

		return $default_permissions[$type];
	}
}
