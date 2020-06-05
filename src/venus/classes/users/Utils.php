<?php
/**
* The Users Utils Class
* @package Venus
*/

namespace Venus\Users;

use Venus\App;
use Venus\User;

/**
* The Users Utils Class
* Class containing user utils functionality
*/
class Utils
{
	use \Venus\AppTrait;

	/**
	* Returns the url from where the user can unsubscribe from receiving emails
	* @param User $user The user
	* @return string The unsubscribe url
	*/
	public function getUnsubscribeUrl(User $user) : string
	{
		return $this->app->uri->build($this->app->utils_url . 'unsubscribe.php', ['user_id' => $user->id, 'key' => $user->secret_key]);
	}

	/**
	* Returns the autologin url of an user
	* @param User $user The user
	* @param string $redirect_url The url where the user will be redirected after autologin
	* @param int $valid_timestamp The interval[timestamp] until the autologin will be considered valid. If 0, the default setting is applied
	* @return string The autologin url
	*/
	public function getAutologinUrl(User $user, string $redirect_url = '', int $valid_timestamp = 0) : string
	{
		$key = App::randStr(30);

		if (!$valid_timestamp) {
			$valid_timestamp = time() + ($this->app->user_autologin_expires * 3600 *24);
		}

		$insert_array = [
			'user_id' => $user->id,
			'key' => $key,
			'valid_timestamp' => $valid_timestamp
		];

		$this->app->db->insert('venus_users_autologin', $insert_array);

		return $this->app->uri->build($this->app->utils_url . 'autologin.php', ['user_id' => $user->id, 'key' => $key, 'url' => $redirect_url]);
	}
}
