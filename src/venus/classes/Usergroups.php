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
	protected static $id_name = 'ugid';

	/**
	* @internal
	*/
	protected static $table = 'venus_usergroups';

	/**
	* @internal
	*/
	protected static $user_usergroups_table = 'venus_users_usergroups';

	/**
	* @internal
	*/
	protected static $class = '\Venus\Usergroup';

	/**
	* @internal
	*/
	protected static $title_name = 'title';

	/**
	* Returns the user usergroups table
	* @return string
	*/
	protected function getUserUsergroupsTable() : string
	{
		return static::$user_usergroups_table;
	}

	/**
	* Returns the ugids of the usergroups as array
	* @return array
	*/
	public function getUgids() : array
	{
		return $this->ids;
	}

	/**
	* Finds a usergroup from the loaded groups. If nothing is found, returns the guests usergroup
	* @param int $ugid The id of the usergroup to find
	* @return Usergroup The usergroup
	*/
	public function find(int $ugid) : Usergroup
	{
		$usergroup = parent::find($ugid);
		if (!$usergroup) {
			$usergroup = $this->getGuests();
		}

		return $usergroup;
	}

	/**
	* Returns the data of an usergroup
	* @param int $ugid The usergroup for which to return the data
	* @return Usergroup The usergroup
	*/
	public function get(int $ugid) : Usergroup
	{
		if ($ugid == APP::USERGROUPS['guests']) {
			return $this->getGuests();
		}

		return $this->getObject($this->app->env->getUsergroup($ugid));
	}

	/**
	* Returns the data of the guests usergroup
	* @return object The guests usergroup
	*/
	public function getGuests() : Usergroup
	{
		static $usergroup_guests = [];

		if (!$usergroup_guests) {
			$usergroup_guests = $this->app->cache->get('usergroup_guests', true);
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
	* @param bool $include_primary_ugid If true will include the primary usergroup in the list of usergroups
	* @return $this
	*/
	public function loadByUser(User $user, bool $include_primary_ugid = true)
	{
		$uid = (int)$user->uid;
		$usergroups_table = $this->getUserUsergroupsTable();
		if (!$uid) {
			return;
		}

		$ugids = $this->db->selectField($usergroups_table, 'ugid', ['uid' => $uid]);
		if ($include_primary_ugid) {
			$ugids = array_merge([(int)$user->ugid], $ugids);
		}
		$ugids = array_unique($ugids);
		if (!$ugids) {
			return;
		}

		$usergroups = [];
		foreach ($ugids as $ugid) {
			$usergroups[] = $this->get($ugid);
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
			$default_permissions = $this->app->cache->get('usergroups_permissions', true);
		}

		return $default_permissions[$type];
	}
}
