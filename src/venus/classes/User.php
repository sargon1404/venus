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
	* @var int $uid The user's id. 0 if he's a guest
	*/
	public int $uid = 0;

	/**
	* @var int $username The username of the user
	*/
	public string $username = '';

	/**
	* @var string $email The email of the user
	*/
	public string $email = '';

	/**
	* @var int $ugid The id of the primary usergroup the user belongs to
	*/
	public array $ugid = App::USERGROUPS['guests'];

	/**
	* @var Usergroup $usergroup The primary usergroup of the user
	*/
	public Usergroup $usergroup;

	/**
	* @var array $usergroups The usergroups the user belongs to
	*/
	public array $usergroups = [];

	/**
	* @var array $ugid The ids of the usergroups the user belongs to
	*/
	public array $ugids = [];

	/**
	* @var int $timezone User's timezone
	*/
	public string $timezone = '';

	/**
	* @var int $timezone_offset The difference in seconds between user's timezone and UTC
	*/
	public int $timezone_offset = 0;

	/**
	* @var int $lang The user's language
	*/
	public int $lang = 0;

	/**
	* @var int $theme The user's theme
	*/
	public int $theme = 0;

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
	* @ignore
	*/
	protected static string $id_name = 'uid';

	/**
	* @ignore
	*/
	protected static string $table = 'venus_users';

	/**
	* @ignore
	*/
	protected static string $usergroups_table = 'venus_users_usergroups';

	/**
	* @var array $_ignore Custom properties which won't be inserted into the database
	*/
	protected static array $_ignore = ['url', 'usergroup', 'ugids', 'usergroups', 'avatar_url', 'avatar_width', 'avatar_height', 'avatar_wh', 'avatar_html', 'avatar_type'];

	/**
	* Builds the user
	* @param mixed $user The user's id/data
	*/
	public function __construct($user = 0)
	{
		parent::__construct($user);

		$this->app->plugins->run('userConstruct', $this, $uid);
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
	* @param mixed $fields The fields to return (string,array)
	* @param array $where Sql where conditions
	*/
	protected function loadUser($fields = '*', array $where = [])
	{
		$this->db->sql->select($fields)->from($this->getTable())->where($where)->limit(1);

		$this->loadBySql();
	}

	/**
	* Loads the user by uid
	* @param int $uid The user's uid
	* @param mixed $fields The fields to return (string,array)
	* @return $this
	*/
	public function loadByUid(int $uid, $fields = '*')
	{
		$this->loadUser($fields, ['uid' => (int)$uid]);

		return $this;
	}

	/**
	* Loads the user by username
	* @param string $username The username
	* @param mixed $fields The fields to return (string,array)
	* @return $this
	*/
	public function loadByUsername(string $username, $fields = '*')
	{
		$this->loadUser($fields, ['username' => $username]);

		return $this;
	}

	/**
	* Loads the user by email
	* @param string $email The email
	* @param mixed $fields The fields to return (string,array)
	* @return $this
	*/
	public function loadByEmail(string $email, $fields = '*')
	{
		$this->loadUser($fields, ['email' => $email]);

		return $this;
	}

	/**
	* Returns an user, by uid
	* @param int $uid The user's uid
	* @param mixed $fields The fields to return (string|array)
	* @return User The user object or null if not found
	*/
	public function getByUid(int $uid, $fields = '*') : ?User
	{
		$user = new User;
		$user->loadByUid($uid, $fields);

		if (!$user->uid) {
			return null;
		}

		return $user;
	}

	/**
	* Returns an user, by username
	* @param string $username The user's username
	* @param mixed $fields The fields to return (string|array)
	* @return User The user object or null if not found
	*/
	public function getByUsername(string $username, string $fields = '*') : ?User
	{
		$user = new User;
		$user->loadByUsername($username, $fields);

		if (!$user->uid) {
			return null;
		}

		return $user;
	}

	/**
	* Returns an user, by email
	* @param string $email The user's email
	* @param mixed $fields The fields to return (string|array)
	* @return User The user object or null if not found
	*/
	public function getByEmail(string $email, string $fields = '*') : ?User
	{
		$user = new User;
		$user->loadByEmail($email, $fields);

		if (!$user->uid) {
			return null;
		}

		return $user;
	}

	/**
	* Returns the user id, from username
	* @param string $username The user's username
	* @return int The user's id
	*/
	public function getUidByUsername(string $username) : int
	{
		$user = new User;
		$user->loadByUsername($username);

		return (int)$user->uid;
	}

	/**
	* Prepares the user
	* @param string $avatar_type The avatar type: image/thumb/small_thumb
	*/
	protected function prepare(string $avatar_type = 'image')
	{
		if (isset($this->ugid)) {
			$usergroups = new Usergroups;

			$this->ugids = [$this->ugid];
			$this->usergroup = $usergroups->get($this->ugid);
			$this->usergroups = [$this->ugid => $this->usergroup];
		}

		$this->url = $this->app->uri->getUser($this);
		$this->avatar_type = $avatar_type;

		$this->app->plugins->run('userPrepare', $this);
	}

	/**
	* Loads all the usergroups the user belongs to
	* @param bool $include_primary_ugid If true will include the primary usergroup in the list of usergroups
	* @return $this
	*/
	public function loadUsergroups(bool $include_primary_ugid = true)
	{
		static $loaded = false;
		if ($loaded) {
			return $this;
		}

		$this->usergroups = new Usergroups;

		if ($this->uid) {
			$this->usergroups->loadByUser($this, $include_primary_ugid);
		} else {
			$this->usergroups->loadGuests();
		}

		$this->ugids = $this->usergroups->getUgids();

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
		if ($this->uid == VENUS_SUPER_ADMIN_UID) {
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
		if ($this->uid == VENUS_SUPER_ADMIN_UID) {
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
		if (in_array(App::USERGROUPS['admins'], $this->ugids)) {
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
		if (in_array(App::USERGROUPS['moderators'], $this->ugids)) {
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
		} elseif ($this->ugid) {
			if ($this->usergroup->avatar) {
				return $this->usergroup->getAvatarUrl($this->ugid, $orig_avatar_type);
			}
		}

		$url = $this->app->theme->images_url . $avatar_type . 'avatar.png';

		return $this->app->plugins->filter('userGetAvatarUrl', $url, $this);
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
	* Outputs the user's uid
	*/
	public function outputUid()
	{
		echo App::e($this->uid);
	}

	/**
	* Alias for output_uid
	*/
	public function outputId()
	{
		$this->outputUid();
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
		echo App::e($this->usergroup->ugid);
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
