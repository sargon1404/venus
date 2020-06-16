<?php
/**
* The Admins Class
* @package Venus
*/

namespace Venus\Users;

use Venus\App;

/**
* The Admins Class
* Admin related functionality
*/
class Admins
{
	use \Venus\AppTrait;

	/**
	* Returns the emails of the admins who can receaive admin notifications
	* @return array An array listing the emails of the admins, with the username as key
	*/
	public function getEmails() : array
	{
		static $emails = [];
		if ($emails) {
			return $emails;
		}

		$admin_usergroup_id = App::USERGROUPS['admins'];

		$this->app->db->readQuery("
			SELECT username, email
			FROM venus_users_usergroups as ug
			LEFT JOIN venus_users as u ON u.id = ug.user_id
			WHERE ug.usergroup_id = {$admin_usergroup_id} AND u.receaive_admin_emails = 1");

		return $this->app->db->getList('username', 'email');
	}
}
