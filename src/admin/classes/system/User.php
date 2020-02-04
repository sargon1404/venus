<?php
/**
* The System's Admin User Class
* @package Venus
*/

namespace Venus\Admin\System;

use Venus\admin\App;

/**
* The System's Admin User Class
* Builds the current user
*/
class User extends \Venus\System\User
{
	use \Venus\Admin\Users\PermissionsTrait;

	/**
	* @var object $config The admin's options
	*/
	public ?object $config;

	/**
	* @internal
	*/
	protected static string $administrators_table = 'venus_administrators';

	/**
	* @internal
	*/
	protected static string $login_keys_scope = 'admin';

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
		$this->prepareAdmin();
		$this->prepareConfig();

		$this->app->plugins->run('admin_system_user_construct', $this);
	}

	/**
	* Returns the name of the administrators table
	* @return string
	*/
	public function getAdministratorsTable() : string
	{
		return static::$administrators_table;
	}

	/***************PREPARE METHODS******************************/

	/**
	* @see \Venus\System\User::prepareSession()
	* {@inheritDoc}
	*/
	protected function prepareSession()
	{
		$this->session_regenerate_interval = $this->app->config->session_regenerate_interval;

		if (!$this->app->session->get('admin')) {
			$this->sid = $this->app->session->getId();
			return;
		}

		parent::prepareSession();
	}

	/**
	* @see \Venus\System\User::prepareUser()
	* {@inheritDoc}
	*/
	protected function prepareUser()
	{
		$user = null;

		if ($this->app->session->get('uid')) {
			$user = $this->getByUid($this->app->session->get('uid'));
			if (!$user) {
				$this->logout();
			}
		} else {
			$user_data = $this->app->request->readCookie($this->cookie_name);

			if ($user_data) {
				$user = $this->getByUid($user_data['uid']);

				if ($user) {
					//check if the keys match
					if ($this->checkLoginKey($user->uid, $user_data['key'])) {
						//reset the login key
						$new_key = $this->updateLoginKey($user->uid, $user_data['key']);

						$this->writeUserCookie($user->uid, $new_key);

						$this->app->session->set('uid', (int)$user->uid);
						$this->app->session->set('admin', 1);
					} else {
						$user = null;
					}
				}
			} else {
				$this->logout();
			}
		}

		if (!$user) {
			return;
		}

		$this->assign($user);

		if (!$this->isEnabled()) {
			$this->logout();
			return;
		}

		$this->app->plugins->run('admin_system_user_prepare_user', $this);
	}

	/**
	* @see \Venus\System\User::prepareAdmin()
	* {@inheritDoc}
	*/
	protected function prepareAdmin()
	{
		if (!$this->uid) {
			return;
		}

		parent::prepareAdmin();

		if (!$this->is_admin) {
			$this->logout();
			return;
		}

		$this->app->plugins->run('admin_system_user_prepare_admin', $this);
	}

	/**
	* @see \Venus\System\User::prepareConfig()
	* {@inheritDoc}
	*/
	protected function prepareConfig()
	{
		parent::prepareConfig();

		//$this->markup_language =
		$this->markup_tags = 'all';

		$this->config = null;
		if ($this->uid) {
			$this->config = $this->app->db->selectById($this->getAdministratorsTable(), 'uid', $this->uid);
		}

		if ($this->config) {
			$this->filter = $this->config->filter;
		}

		$this->app->plugins->run('admin_system_user_prepare_config', $this);
	}

	/*************** LOGIN METHODS ******************************/

	/**
	* @see \Venus\System\User::canLogin()
	* {@inheritDoc}
	*/
	protected function canLogin(string $username, string $password, &$user = null) : bool
	{
		if (!parent::canLogin($username, $password, $user)) {
			return false;
		}

		if (!$user->isAdmin()) {
			return false;
		}

		return true;
	}

	/**
	* @see \Venus\System\User::loginUser()
	* {@inheritDoc}
	*/
	protected function loginUser(\Venus\User $user, bool $remember_me = false)
	{
		//reset the session id
		$this->app->session->regenerateId();

		$this->resetLoginKey($user->uid);

		//set the session data
		$this->app->session->set('admin', 1);
		$this->app->session->set('uid', $user->uid);
		$this->app->session->set('session_timestamp', time());
	}

	/****************LOGIN KEYS METHODS**************************/

	/**
	* @see \Venus\System\User::writeLoginKey()
	* {@inheritDoc}
	*/
	protected function writeLoginKey(int $uid, string $key)
	{
		//delete all other login keys. Only allow one
		$this->app->db->delete($this->getLoginKeysTable(), ['uid' => $uid, 'scope' => static::$login_keys_scope], 1);

		parent::writeLoginKey($uid, $key);
	}

	/****************SESSION METHODS**************************/

	/**
	* @see \Venus\System\User::deleteSession()
	* {@inheritDoc}
	*/
	protected function deleteSession()
	{
		$this->app->session->unset('admin');

		parent::deleteSession();
	}

	/**
	* Adds a user notification to the queue
	* @param int $type The type of the notification. See file constans.php -> User notifications
	* @param array $uids Array with the user ids of the users who will receaive the notification. If empty the current user is used
	* @return $this
	*/
	public function addNotification(int $type, array $uids = [])
	{
		if (!$uids) {
			$uids = [$this->uid];
		}

		foreach ($uids as $uid) {
			$replace_array = [
				'uid' => (int)$uid,
				'type' => $type,
				'timestamp' => $this->app->db->unixTimestamp()
			];

			$this->app->db->replace($this->getNotificationsTable(), $replace_array);
		}

		$this->app->cache->buildUserNotifications();

		return $this;
	}
}
