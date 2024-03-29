<?php
/**
* The System's User Class
* @package Venus
*/

namespace Venus\System;

use Venus\App;
use Mars\Session;

/**
* The System's User Class
* Builds the current user
*/
class User extends \Venus\User
{
	/**
	* @var string $sid The session id
	*/
	public string $sid = '';

	/**
	* @var bool $is_admin True if the user has admin priviledges
	*/
	public bool $is_admin = false;

	/**
	* @var bool $is_moderator True if the user is a moderator
	*/
	public bool $is_moderator = false;

	/**
	* @var string $token The session token value. Used to prevent CSRF attacks
	*/
	public string $token = '';

	/**
	* @var array $moderator_permissions The moderator permissions of this user,if he's a moderator
	*/
	public array $moderator_permissions = [];

	/**
	* @var session $session The session object
	*/
	protected Session $session; //preload

	/**
	* @var int $session_regenerate_interval The interval - in minutes - after which the session id will be regenerated
	*/
	protected int $session_regenerate_interval = 0;

	/**
	* @var string $cookie_name The name of the user cookie
	*/
	protected string $cookie_name = '';

	/**
	* @var int $cookie_expires The interval - in minutes - after which the user cookie will expire
	*/
	protected string $cookie_expires = '';

	/**
	* @internal
	*/
	protected static string $login_keys_table = 'venus_users_login_keys';

	/**
	* @internal
	*/
	protected static string $login_keys_scope = 'frontend';

	/**
	* @internal
	*/
	protected static string $notifications_table = 'venus_users_notifications';

	/**
	* @internal
	*/
	protected static string $moderator_permissions_table = 'venus_moderators_permissions';

	/**
	* Builds the current system user
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->session = $this->app->session;

		$this->prepareProperties();
		$this->prepareSession();
		$this->prepareToken();

		$this->prepareUser();
		$this->prepareUsergroups();

		$this->prepareConfig();

		$this->app->plugins->run('system_user_construct', $this);
	}

	/**
	* Returns the login keys table
	* @return string
	*/
	public function getLoginKeysTable() : string
	{
		return static::$login_keys_table;
	}

	/**
	* Returns the user notifications table
	* @return string
	*/
	public function getNotificationsTable() : string
	{
		return static::$notifications_table;
	}

	/**
	* Returns the moderators table
	* @return string
	*/
	public function getModeratorPermissionsTable() : string
	{
		return static::$moderator_permissions_table;
	}

	/***************PREPARE METHODS******************************/

	/**
	* Prepares the required properties
	*/
	protected function prepareProperties()
	{
		$this->cookie_name = $this->app->config->user_cookie_name;
		$this->cookie_expires = $this->app->config->user_cookie_expires;
	}

	/**
	* Prepares the user's session data
	*/
	protected function prepareSession()
	{
		$this->sid = $this->session->getId();
		$this->session_regenerate_interval = $this->app->config->session_regenerate_interval;

		//generate a new session id if we have to
		$timestamp = (int)$this->session->get('session_timestamp');
		$timestamp_cutoff = time() - ($this->session_regenerate_interval * 60);

		if ((!$timestamp || $timestamp < $timestamp_cutoff) && !$this->app->type) {
			$this->sid = $this->session->regenerateId();

			$this->session->set('session_timestamp', time());
		}

		$this->app->plugins->run('system_user_prepare_session', $this);
	}

	/**
	* Deletes the user's session data
	*/
	protected function deleteSession()
	{
		$this->session->unset('user');
		$this->session->unset('user_id');
		$this->session->unset('usergroups');
		$this->session->unset('usergroups_timestamp');
		$this->session->unset('session_timestamp');

		$this->session->delete();
	}

	/**
	* Prepares the token
	*/
	protected function prepareToken()
	{
		if (!$this->session->get('token')) {
			$this->session->set('token', $this->app->random->getString(64));
		}

		$this->token = $this->session->get('token');

		$this->app->plugins->run('system_user_prepare_token', $this);
	}

	/**
	* Prepares the user
	*/
	protected function prepareUser()
	{
		if (!$this->app->config->users_enable) {
			return;
		}

		$user = null;

		if ($this->session->get('user_id')) {
			$user = $this->session->get('user', true);
		} elseif ($this->app->config->login_remember_me) {
			$user_data = $this->app->request->readCookie($this->cookie_name);

			if ($user_data) {
				$user = $this->getById($user_data['user_id']);

				if ($user) {
					//check if the keys match
					if ($this->checkLoginKey($user->id, $user_data['key'])) {
						//reset the login key
						$new_key = $this->updateLoginKey($user->id, $user_data['key']);

						$this->writeUserCookie($user->id, $new_key);

						$this->session->set('user_id', $user->id);
						$this->session->set('user', $user, true);
					} else {
						$user = null;
					}
				} else {
					$this->logout();
				}
			}
		}

		if (!$user) {
			return;
		}

		$this->assign($user);

		//check if the user account is enabled/activated
		if (!$this->isEnabled()) {
			$this->logout();
			return;
		}

		//check for user notifications
		if ($this->app->cache->users_notifications) {
			$this->processNotifications();
		}

		$this->prepareAdmin();
		$this->prepareModerator();

		$this->app->plugins->run('system_user_prepare_user', $this);
	}

	/**
	* Prepares the usergroups
	*/
	protected function prepareUsergroups()
	{
		//check if we have the usergroups data in session. If so, check when the last time when the data changed was
		$from_session = true;
		$timestamp = (int)$this->session->get('usergroups_timestamp');
		$cached_timestamp = (int)$this->app->cache->usergroups_timestamp;
		$usergroups = $this->session->get('usergroups', true);

		if ($cached_timestamp > $timestamp || !$usergroups || !$timestamp || !$cached_timestamp) {
			$from_session = false;
		}

		if ($from_session) {
			$this->usergroups = $usergroups;
		} else {
			parent::loadUsergroups();

			$this->session->set('usergroups', $this->usergroups, true);
			$this->session->set('usergroups_timestamp', time());
		}

		$this->usergroup = $this->usergroups->find($this->usergroup_id);

		$this->usergroup_ids = $this->usergroups->getIds();

		if (!$this->isUsergroupEnabled()) {
			$this->logout();
			return;
		}

		if ($this->usergroup_id == APP::USERGROUPS['guests']) {
			$this->username = $this->usergroup->username;
		}
	}

	/**
	* Do nothing if called, the usergroups are already loaded in prepare_usergroups
	* @see \Venus\User::loadUsergroups()
	* @param bool $include_primary_usergroup_id
	* @return $this
	*/
	public function loadUsergroups(bool $include_primary_usergroup_id = true)
	{
		return $this;
	}

	/**
	* Prepares the user's config options [editor/timezone etc..]
	*/
	protected function prepareConfig()
	{
		$this->editor = $this->app->config->editor;
		//$this->markup_language =
		$this->markup_tags = $this->usergroup->markup_tags;

		if (!$this->timezone) {
			$this->timezone = $this->app->config->timezone_default;
		}

		$this->timezone_offset = $this->app->time->getTimezoneOffset($this->timezone);
	}

	/**
	* Determines if the user is an admin
	*/
	protected function prepareAdmin()
	{
		$this->is_admin = $this->isAdmin();
	}

	/**
	* Prepares the moderator permissions, if the user is a moderator/admin
	*/
	protected function prepareModerator()
	{
		if (!$this->id) {
			return;
		}

		if (!$this->is_moderator) {
			//if the user is an admin, grant him all moderator permissions automatically
			if ($this->is_admin) {
				$this->is_moderator = true;
			}

			return;
		}

		$this->moderator_permissions = $this->session->get('moderator_permissions', true);
		if ($this->moderator_permissions) {
			return;
		}

		$permissions_array = $this->app->db->select($this->getModeratorPermissionsTable(), ['user_id' => $this->id])->all();
		foreach ($permissions_array as $perm) {
			$data = ['publish' => $perm->publish, 'edit' => $perm->edit, 'delete' => $perm->delete];
			if ($perm->bid) {
				$this->moderator_permissions['block'][$perm->bid] = $data;
			} elseif ($perm->cid) {
				$this->moderator_permissions['category'][$perm->cid] = $data;
			}
		}

		$this->session->set('moderator_permissions', $this->moderator_permissions, true);
	}

	/*************** LOGIN METHODS ******************************/

	/**
	* Determines if the username can login
	* @param string $username The user's username
	* @param string $password The user' password
	* @param object $user The user object. Is written even if the login fails [out]
	* @return bool True if the user can login, false otherwise
	*/
	protected function canLogin(string $username, string $password, &$user = null) : bool
	{
		$user = $this->getByUsername($username);
		if (!$user) {
			return false;
		}

		if (!password_verify($password, $user->password)) {
			return false;
		}

		//allow the super admin account to login even if his account is disabled
		if ($this->isSuperAdmin()) {
			return true;
		}

		if (!$user->isEnabled()) {
			return false;
		}

		if (!$user->isUsergroupEnabled()) {
			return false;
		}

		return true;
	}

	/**
	* Logs an user
	* @param string $username The username of the user
	* @param string $password The password of the user
	* @param bool $remember_me If true the login will remember the user
	* @param object $user The user object. Is written even if the login fails [out]
	* @return bool|User Returns an User object with the user's data if he can login, false otherwise
	*/
	public function login($username, $password, $remember_me = false, &$user = null)
	{
		if (!$this->canLogin($username, $password, $user)) {
			return false;
		}

		$this->loginUser($user, $remember_me);

		return $user;
	}

	/**
	* Logs the user
	* @param int $id The id of the user to login
	* @param bool $remember_me If true the login will remember the user by writing the user cookie
	* @param object $user The user object. Is written even if the login fails [out]
	* @return bool|User Returns an User object with the user's data if he can login, false otherwise
	*/
	public function loginById(int $id, bool $remember_me = false, &$user = null)
	{
		$user = $this->getById($id);
		if (!$user) {
			return false;
		}

		if (!$user->isEnabled()) {
			return false;
		}
		if (!$user->isUsergroupEnabled()) {
			return false;
		}

		$this->loginUser($user, $remember_me);

		return $user;
	}

	/**
	* Logins the user by setting the session data and the user cookie, if $remember_me is true
	* @param \Venus\User $user The user to login
	* @param bool $remember_me If true the login will remember the user
	*/
	protected function loginUser(\Venus\User $user, bool $remember_me = false)
	{
		//reset the session id
		$this->session->regenerateId();

		//generate a new login key
		if ($remember_me) {
			$this->resetLoginKey($user->id);
		}

		//set the session data
		$this->session->set('user', $user, true);
		$this->session->set('user_id', $user->id);
		$this->session->set('session_timestamp', time());
	}

	/**
	* Logouts the current user
	*/
	public function logout()
	{
		if (!$this->id) {
			return;
		}

		$this->deleteLoginKey($this->id);

		$this->deleteSession();
		$this->deleteUserCookie();
	}

	/****************LOGIN KEYS METHODS**************************/

	/**
	* Generates a login key
	* @return string The generated login key
	*/
	protected function getLoginKey() : string
	{
		$key = $this->app->random->getString(64);

		return $key;
	}

	/**
	* Hashes the login key
	* @param string $key The key
	* @return string The hash
	*/
	protected function hashLoginKey(string $key) : string
	{
		return sha1($key);
	}

	/**
	* Checks if a login key is valid
	* @param int $id The user's id
	* @param string $key The login key to check for
	* @return bool
	*/
	protected function checkLoginKey(int $id, string $key) : bool
	{
		$table = $this->getLoginKeysTable();
		$key = $this->hashLoginKey($key);

		$sql = "SELECT COUNT(*) FROM {$table} WHERE user_id = :id AND key_crc = CRC32(:key) AND `key` = :key AND scope = :scope AND valid_timestamp > UNIX_TIMESTAMP()";

		$this->app->db->readQuery($sql, ['id' => $id, 'key' => $key, 'scope' => static::$login_keys_scope]);
		$count = $this->app->db->getCount();

		if ($count) {
			return true;
		}

		return false;
	}

	/**
	* Writes a login key to the database
	* @param int $id The user's id
	* @param string $key The key
	*/
	protected function writeLoginKey(int $id, string $key)
	{
		$this->deleteLoginKey($id);

		$key = $this->hashLoginKey($key);

		$insert_array = [
			'user_id' => $id,
			'key' => $key,
			'key_crc' => $this->app->db->crc32($key),
			'ip' => $this->app->ip,
			'timestamp' => $this->app->db->unixTimestamp(),
			'valid_timestamp' => time() + (60 * $this->cookie_expires),
			'scope' => static::$login_keys_scope
		];

		$this->app->db->insert($this->getLoginKeysTable(), $insert_array);
	}

	/**
	* Generates a new login key, updates the database, and returns it
	* @param int $id The user's id
	* @param string $old_key The old login key
	* @return string The new login key
	*/
	protected function updateLoginKey(int $id, string $old_key) : string
	{
		$key = $this->getLoginKey();
		$new_key = $this->hashLoginKey($key);
		$old_key = $this->hashLoginKey($old_key);

		$update_array = [
			'key' => $new_key,
			'key_crc' => $this->app->db->crc32($new_key),
			'ip' => $this->app->ip,
			'timestamp' => $this->app->db->unixTimestamp(),
			'valid_timestamp' => time() + (60 * $this->cookie_expires)
		];

		$this->app->db->update($this->getLoginKeysTable(), $update_array, ['user_id' => $id, 'key_crc' => $this->app->db->crc32($old_key), 'key' => $old_key, 'scope' => static::$login_keys_scope], 1);

		return $key;
	}

	/**
	* Deletes a login key from the database
	* @param int $id The user's id
	* @param string $key The key to delete. If empty, deletes the key found in the user cookie
	*/
	protected function deleteLoginKey(int $id, string $key = '')
	{
		if (!$key) {
			$user_data = $this->app->request->readCookie($this->cookie_name);
			if ($user_data) {
				if ($user_data['user_id'] == $id) {
					$key = $user_data['key'];
				}
			}
		}

		if (!$key) {
			return;
		}

		$this->app->db->delete($this->getLoginKeysTable(), ['user_id' => $id, 'key_crc' => $this->app->db->crc32($key), 'key' => $key, 'scope' => static::$login_keys_scope], 1);
	}

	/**
	* Resets the login key of an user
	* @param int $id The user's id
	*/
	protected function resetLoginKey(int $id)
	{
		$key = $this->getLoginKey();

		$this->writeLoginKey($id, $key);
		$this->writeUserCookie($id, $key);
	}

	/****************USER COOKIE METHODS**************************/

	/**
	* Reads the user cookie
	* @return array The user cookie data
	*/
	protected function readUserCookie()
	{
		return $this->app->request->readCookie($this->cookie_name);
	}

	/**
	* Writes the user cookie
	* @param int $id The user's id
	* @param string $key The login key
	*/
	protected function writeUserCookie(int $id, string $key)
	{
		$user_data = [
			'user_id' => $id,
			'key' => $key
		];

		$this->app->request->setCookie($this->cookie_name, $user_data, time() + (60 * $this->cookie_expires));
	}

	/**
	* Deletes the user cookie
	*/
	protected function deleteUserCookie()
	{
		$this->app->request->unsetCookie($this->cookie_name);
	}

	/**
	* Reloads the current user's data
	* @return $this
	*/
	public function reload()
	{
		if (!$this->id) {
			return $this;
		}

		$user = $this->getById($this->id);

		if (!$user || !$this->isEnabled() || !$this->isUsergroupEnabled()) {
			$this->logout();
			return;
		}

		$this->assign($user);

		$this->session->set('user', $user, true);

		return $this;
	}

	/***************NOTIFICATION METHODS******************************/

	/**
	* Checks if there are any status changes etc.., and process it accordingly
	*/
	protected function processNotifications()
	{
		if (!$this->id) {
			return;
		}

		$notifications_array = $this->app->db->selectCol($this->getNotificationsTable(), 'type', ['user_id' => $this->id]);
		if (!$notifications_array) {
			return;
		}

		$this->app->db->deleteByIds($this->getNotificationsTable(), $this->id, 'user_id');

		foreach ($notifications_array as $code) {
			$this->processNotification($code);
		}
	}

	/**
	* Processes a user notification
	* @param int $code The notification code
	*/
	protected function processNotification(int $code)
	{
		switch ($code) {
			case VENUS_USERS_NOTIFICATION_RELOAD_DATA:
				$this->reload();
				break;
			case VENUS_USERS_NOTIFICATION_RELOAD_MODERATOR_DATA:
				$this->reload();
				$this->session->unset('moderator_permissions');
				$this->prepareModerator();
				break;
		}
	}

	/*************** MISC METHODS ******************************/

	/**
	* Assigns the values of the properties from object $user to the current object
	* @param object $user The object to assign
	* @return $this
	*/
	public function assign($user)
	{
		unset($user->password);

		parent::assign($user);

		return $this;
	}
}
