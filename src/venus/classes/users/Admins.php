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

		$ugid = (int)App::USERGROUPS['admins'];

		$this->app->db->readQuery("
			SELECT username, email
			FROM venus_users_usergroups as ug
			LEFT JOIN venus_users as u USING(uid)
			WHERE ug.ugid = {$ugid} AND u.receaive_admin_emails = 1");

		return $this->app->db->getList('username', 'email');
	}
}
