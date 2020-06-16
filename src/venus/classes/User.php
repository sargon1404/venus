<?php
/**
* The User Class
* @package Venus
*/

namespace Venus;

/**
* The User Class
* Encapsulates the functionality of an user
*/
class User extends Item
{
	/**
	* @var int $id The user's id. 0 if he's a guest
	*/
	public int $id = 0;

	/**
	* @var string $username The username of the user
	*/
	public string $username = '';

	/**
	* @var string $password The user's password
	*/
	public string $password = '';

	/**
	* @var string $password_clear The user's clear password
	*/
	public string $password_clear = '';

	/**
	* @var string $email The user's email
	*/
	public string $email = '';

	/**
	* @var int $usergroup_id The id of the primary usergroup the user belongs to
	*/
	public int $usergroup_id = App::USERGROUPS['guests'];

	/**
	* @var Usergroup $usergroup The primary usergroup of the user
	*/
	public Usergroup $usergroup;

	/**
	* @var Usergroups $usergroups The usergroups the user belongs to
	*/
	public Usergroups $usergroups;

	/**
	* @var array $usergroup_ids The ids of the usergroups the user belongs to
	*/
	public array $usergroup_ids = [];

	/**
	* @var int $timezone User's timezone
	*/
	public string $timezone = '';

	/**
	* @var int $timezone_offset The difference in seconds between user's timezone and UTC
	*/
	public int $timezone_offset = 0;

	/**
	* @var int $language_id The user's language
	*/
	public int $language_id = 0;

	/**
	* @var int $theme_id The user's theme
	*/
	public int $theme_id = 0;

	/**
	* @var string $editor The editor type: bbcode/html/textarea
	*/
	public string $editor = 'bbcode';

	/**
	* @var string $markup_language The markup language used by the editor. bbcode/html/text
	*/
	public string $markup_language = 'bbcode';

	/**
	* @var string $markup_tags Comma delimited list with the markup tags the user is allowed to use when parsing text
	*/
	public string $markup_tags = '';

	/**
	* @var string $avatar_type The avatar type: image/thumb/small_thumb
	*/
	protected string $avatar_type = 'image';

	/**
	* @var string $username_pattern The username pattern
	*/
	protected static string $username_pattern = '/[^a-z0-9\-_\. \(\)\'@äàâèéêôöœßùûüÿ]+/i';

	/**
	* @ignore
	*/
	protected static string $table = 'venus_users';

	/**
	* @ignore
	*/
	protected static string $usergroups_table = 'venus_users_usergroups';

	protected static array $store = ['username', 'email', 'avatar'];

	/**
	* @var array $_ignore Custom properties which won't be inserted into the database
	*/
	protected static array $ignore = [
		'url', 'password_clear' ,'usergroup', 'usergroup_ids', 'usergroups', 'timezone_offset',
		'editor', 'markup_language', 'markup_tags',
		'avatar_url', 'avatar_width', 'avatar_height', 'avatar_wh', 'avatar_html', 'avatar_type'
	];

	/**
	* Builds the user
	* @param int|array|object $user The user's id/data
	*/
	public function __construct($user = 0)
	{
		parent::__construct($user);

		$this->app->plugins->run('user_construct', $this);
	}

	/**
	* {@inheritDocs}
	*/
	protected function getValidationRules() : array
	{
		return [
			'username' => [
								 'user_username_missing' => 'required', 'user_username_exists' => 'unique',
								 'user_username_short' => ['min_chars', $this->app->config->users_min_username],
								 'user_username_invalid' => ['pattern', static::$username_pattern]
							  ],
			'password_clear' => ['user_password_missing' => 'required', 'user_password_short' => ['min_chars', $this->app->config->users_min_password]],
			'email' => ['user_email_missing' => 'required', 'user_email_invalid' => 'email', 'user_email_exists' => 'unique'],
			'usergroup_id' => ['user_usergroup_doesnt_exist' => [$this, 'validateUsergroup']],
			'registration_ip' => ['user_ip_to_many' => [$this, 'validateIp']]
		];
	}

	/**
	* {@inheritDocs}
	*/
	protected function validate() : bool
	{
		//don't validate the password_clear field, if empty
		if (!$this->password_clear) {
			$this->skipValidationRule('password_clear');
		}

		return parent::validate();
	}

	/**
	* Validates the usergroup
	* @param int $usergroup_id The usergroup id
	* @return bool
	*/
	public function validateUsergroup(int $usergroup_id) : bool
	{
		$usergroup = new Usergroup($usergroup_id);
		return $usergroup->isValid();
	}

	/**
	* Validates the ip
	* @param string $ip The ip
	* @return bool
	*/
	public function validateIp(string $ip) : bool
	{
		if (!$ip || !$this->app->config->registration_per_ip) {
			return true;
		}

		$count = $this->app->db->count($this->getTable(), ['registration_ip' => $ip, 'registration_ip_crc' => $this->app->db->crc32($ip)]);
		if ($count >= $this->app->config->registration_per_ip) {
			return false;
		}

		return true;
	}

	/**
	* {@inheritDocs}
	*/
	protected function getDefaultsArray() : array
	{
		return [
			'usergroup_id' => App::USERGROUPS['registered'],
			'secret_key' => App::randStr(),

			'status' => 1,
			'activated' => 1,

			'language_id' => 0,
			'theme_id' => 0,
			'timezone' => '',

			'receaive_pms' => 1,
			'receaive_emails' => 1,
			'receaive_admin_emails' => 1,

			'registration_type' => 'venus',
			'registration_timestamp' => time(),
			'registration_ip' => $this->app->ip,
			'registration_ip_crc' => $this->app->db->crc32($this->app->ip)
		];
	}

	/**
	* {@inheritDocs}
	*/
	protected function process()
	{
		if ($this->password_clear) {
			$this->password = $this->hashPassword($this->password_clear);
		}

		$this->seo_alias = $this->app->filter->slug($this->username);
	}

	/**
	* {@inheritDocs}
	*/
	public function insert(bool $process = true, bool $keep_old_id = false) : int
	{
		$user_id = parent::insert($process, $keep_old_id);
		if ($user_id) {
			$this->insertUsergroups();
		}

		return $user_id;
	}

	/**
	* {@inheritDocs}
	*/
	public function update(bool $process = true) : bool
	{
		//don't use the username/email validation rules if the username/email hasn't changed
		if (!$this->canUpdate('username')) {
			$this->skipValidationRule('username');
		}
		if (!$this->canUpdate('email')) {
			$this->skipValidationRule('email');
		}

		return parent::update($process);
	}

	public function insertUsergroups()
	{
	}

	/**
	* {@inheritDocs}
	*/
	public function delete() : int
	{
		$rows = parent::delete();

		$this->deleteUsergroups();

		return $rows;
	}

	public function deleteUsergroups()
	{
	}

	/**
	* Returns the usergroups table
	* @return string
	*/
	protected function getUsergroupsTable() : string
	{
		return static::$usergroups_table;
	}

	/**
	* Loads a user
	* @param array $where Sql where conditions
	*/
	protected function loadUser(array $where = [])
	{
		$this->db->sql->select($this->fields)->from($this->getTable())->where($where)->limit(1);

		$this->loadBySql();
	}

	/**
	* Loads the user by id
	* @param int $id The user's id
	* @return bool
	*/
	public function loadById(int $id) : bool
	{
		$this->loadUser(['id' => $id]);

		return true;
	}

	/**
	* Loads the user by username
	* @param string $username The username
	* @return $this
	*/
	public function loadByUsername(string $username)
	{
		$this->loadUser(['username' => $username]);

		return $this;
	}

	/**
	* Loads the user by email
	* @param string $email The email
	* @return $this
	*/
	public function loadByEmail(string $email)
	{
		$this->loadUser(['email' => $email]);

		return $this;
	}

	/**
	* Returns an user, by id
	* @param int $id The user's id
	* @return User The user object or null if not found
	*/
	public function getById(int $id) : ?User
	{
		$user = new User;
		$user->loadById($id);

		if (!$user->id) {
			return null;
		}

		return $user;
	}

	/**
	* Returns an user, by username
	* @param string $username The user's username
	* @return User The user object or null if not found
	*/
	public function getByUsername(string $username) : ?User
	{
		$user = new User;
		$user->loadByUsername($username);

		if (!$user->id) {
			return null;
		}

		return $user;
	}

	/**
	* Returns an user, by email
	* @param string $email The user's email
	* @return User The user object or null if not found
	*/
	public function getByEmail(string $email) : ?User
	{
		$user = new User;
		$user->loadByEmail($email);

		if (!$user->id) {
			return null;
		}

		return $user;
	}

	/**
	* Returns the user id, from username
	* @param string $username The user's username
	* @return int The user's id
	*/
	public function getIdByUsername(string $username) : int
	{
		$user = new User;
		$user->loadByUsername($username);

		return $user->id;
	}

	/**
	* Prepares the user
	* @param string $avatar_type The avatar type: image/thumb/small_thumb
	*/
	protected function prepare(string $avatar_type = 'image')
	{
		if (isset($this->usergroup_id)) {
			$usergroups = new Usergroups;

			$this->usergroup_ids = [$this->usergroup_id];
			$this->usergroup = $usergroups->get($this->usergroup_id);
			//var_dump("usergroups!!!!");
			//$this->usergroups = [$this->ugid => $this->usergroup];
		}

		$this->url = $this->app->uri->getUser($this);
		$this->avatar_type = $avatar_type;

		$this->app->plugins->run('user_prepare', $this);
	}

	/**
	* Loads all the usergroups the user belongs to
	* @param bool $include_primary_usergroup_id If true will include the primary usergroup in the list of usergroups
	* @return $this
	*/
	public function loadUsergroups(bool $include_primary_usergroup_id = true)
	{
		static $loaded = false;
		if ($loaded) {
			return $this;
		}

		$this->usergroups = new Usergroups;

		if ($this->id) {
			$this->usergroups->loadByUser($this, $include_primary_usergroup_id);
		} else {
			$this->usergroups->loadGuests();
		}

		$this->usergroup_ids = $this->usergroups->getIds();

		$loaded = true;

		return $this;
	}

	/**************************IS METHODS******************************/

	/**
	* Checks the user's status. Returns tue if the user is enabled and activated
	* @return bool
	*/
	public function isEnabled() : bool
	{
		if ($this->id == VENUS_SUPER_ADMIN_ID) {
			return true;
		}

		if (!$this->status || !$this->activated) {
			return false;
		}

		return true;
	}

	/**
	* Checks the user's usergroups status. Returns true if ANY usergroup is activated, false otherwise. If the main usergroup is not activated, will return false
	* @return bool
	*/
	public function isUsergroupEnabled() : bool
	{
		//load the usergroups, if not already loaded
		$this->loadUsergroups();
		if (!$this->usergroups) {
			return false;
		}

		//is the main usergroup active?
		if (!$this->usergroup->status) {
			return false;
		}

		$status = false;
		foreach ($this->usergroups as $usergroup) {
			if ($usergroup->status) {
				$status = true;
				break;
			}
		}

		return $status;
	}

	/**
	* Returns true if the user is the super admin
	* @return bool
	*/
	public function isSuperAdmin() : bool
	{
		if ($this->id == VENUS_SUPER_ADMIN_ID) {
			return true;
		}

		return false;
	}

	/**
	* Returns true if the user belongs to the admin usergroups.
	* @return bool
	*/
	public function isAdmin() : bool
	{
		if ($this->isSuperAdmin()) {
			return true;
		}

		$this->loadUsergroups();
		if (in_array(App::USERGROUPS['admins'], $this->usergroup_ids)) {
			return true;
		}

		return false;
	}

	/**
	* Returns true if the user belongs to the moderators usergroups.
	* @return bool
	*/
	public function isModerator() : bool
	{
		if ($this->isSuperAdmin()) {
			return true;
		}

		$this->loadUsergroups();
		if (in_array(App::USERGROUPS['moderators'], $this->usergroup_ids)) {
			return true;
		}

		return false;
	}

	/**************************HELPER METHODS******************************/

	/**
	* Hashes a password
	* @param string $password The password to hash
	* @return string The hashed password
	*/
	public function hashPassword(string $password) : string
	{
		return \password_hash($password, PASSWORD_DEFAULT);
	}

	/**
	* Returns the url from where the user can unsubscribe from receiving emails
	* @return string The unsubscribe url
	*/
	public function getUnsubscribeUrl() : string
	{
		$utils = $this->getUtilsObj();

		return $utils->getUnsubscribeUrl($this);
	}

	/**
	* Returns the autologin url of an user
	* @param object $user The user. If empty, the currently logged user is used
	* @param string $redirect_url The url where the user will be redirected after autologin
	* @param int $valid_timestamp The interval[timestamp] until the autologin will be considered valid. If 0, the default setting is applied
	* @return string The autologin url
	*/
	public function getAutologinUrl(string $redirect_url = '', int $valid_timestamp = 0) : string
	{
		$utils = $this->getUtilsObj();

		return $utils->getAutologinUrl($this, $redirect_url, $valid_timestamp);
	}

	/**
	* @internal
	*/
	protected function getUtilsObj() : object
	{
		return new \Venus\Users\Utils($this->app);
	}

	/**************************AVATAR METHODS******************************/

	/**
	* Prepares the avatar data: url, width, height etc..
	* @param string $avatar_type The avatar type: image/thumb/small_thumb
	* @return $this
	*/
	public function prepareAvatar(string $avatar_type = '')
	{
		if (!$avatar_type) {
			$avatar_type = $this->avatar_type;
		}

		$this->avatar_url = $this->getAvatarUrl($avatar_type);
		$this->avatar_width = $this->getAvatarWidth($avatar_type);
		$this->avatar_height = $this->getAvatarHeight($avatar_type);
		$this->avatar_wh = $this->app->html->imgWh($this->avatar_width, $this->avatar_height);
		$this->avatar_html = $this->app->html->img($this->avatar_url, $this->avatar_width, $this->avatar_height, $this->username);

		return $this;
	}

	/**
	* Returns the avatar url of an user
	* @param string $avatar_type The avatar's type: image/thumb/small_thumb
	* @return string The avatar url
	*/
	public function getAvatarUrl(string $avatar_type = 'image') : string
	{
		$orig_avatar_type = $avatar_type;
		if ($avatar_type == 'image') {
			$avatar_type = '';
		}
		if ($avatar_type) {
			$avatar_type = $avatar_type . '_';
		}

		if ($this->avatar) {
			return $this->app->uploads_url . 'avatars/' . $this->app->file->getSubdir($this->avatar, true) . $avatar_type . rawurlencode($this->avatar);
		} elseif ($this->usergroup_id) {
			if ($this->usergroup->avatar) {
				return $this->usergroup->getAvatarUrl($this->usergroup_id, $orig_avatar_type);
			}
		}

		$url = $this->app->theme->images_url . $avatar_type . 'avatar.png';

		return $this->app->plugins->filter('user_get_avatar_url', $url, $this);
	}

	/**
	* Returns the avatar filename of an user
	* @return string
	*/
	public function getAvatarFilename() : string
	{
		if (!$this->avatar) {
			return '';
		}

		return $this->app->uploads_url . 'avatars/' . $this->app->file->getSubdir($this->avatar) . $this->avatar;
	}

	/**
	* Returns the avatar's width
	* @param string $avatar_type The avatar's type: image/thumb/small_thumb
	* @return int
	*/
	public function getAvatarWidth(string $avatar_type = 'image') : int
	{
		return $this->usergroup->getAvatarWidth($avatar_type);
	}

	/**
	* Returns the avatar's width
	* @param string $avatar_type The avatar's type: image/thumb/small_thumb
	* @return int
	*/
	public function getAvatarHeight($avatar_type = 'image') : int
	{
		return $this->usergroup->getAvatarHeight($avatar_type);
	}

	/**************************OUTPUT METHODS******************************/

	/**
	* Outputs the user's id
	*/
	public function outputId()
	{
		echo App::e($this->id);
	}

	/**
	* Outputs the user's username
	*/
	public function outputUsername()
	{
		echo App::e($this->username);
	}

	/**
	* Outputs the user's profile url
	*/
	public function outputUrl()
	{
		echo App::e($this->url);
	}

	/**
	* Outputs the usergroup id
	*/
	public function outputUsergroupId()
	{
		echo App::e($this->usergroup->id);
	}

	/**
	* Outputs the usergroup title
	*/
	public function outputUsergroupTitle()
	{
		echo App::e($this->usergroup->title);
	}

	/**
	* Outputs the user's avatar
	* @param bool $show_link If true will show the avatar, inside the link
	*/
	public function outputAvatar(bool $show_link = false)
	{
		if ($show_link) {
			$this->outputLink(true, false);
		} else {
			$this->outputAvatarImage();
		}
	}

	/**
	* Outputs the user's avatar image
	*/
	public function outputAvatarImage()
	{
		echo $this->app->html->img($this->avatar_url, $this->avatar_width, $this->avatar_height, $this->username);
	}

	/**
	* Outputs the user link
	* @param bool $show_avatar If true will show
	* @param bool $show_username If true will show the username
	* @param array $attributes Extra attributes in the format name => value
	*/
	public function outputLink(bool $show_avatar = false, bool $show_username = true, array $attributes = [])
	{
		echo '<a href="' . App::e($this->url) . '"' . $this->app->html->getAttributes('user', '', $attributes) . '>';

		if ($show_avatar) {
			$this->outputAvatarImage();
		}
		if ($show_username) {
			$this->outputUsername();
		}

		echo '</a>';
	}
}
