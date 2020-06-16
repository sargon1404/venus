<?php
/**
* The Admin Login Model Class
* @author Venus-CMS
* @package Cms\Admin\Blocks\Login
*/

namespace Cms\Admin\Blocks\Login\Models;

use Venus\App;
use Venus\Admin\Languages;
use Venus\Admin\Extensions\Info;
use Venus\Admin\Helpers\Bruteforce;

/**
* The Admin Login Model Class
*/
class Login extends \Venus\Admin\Model
{

	/**
	* @var Bruteforce $bruteforce The bruteforce object
	*/
	public Bruteforce $bruteforce;

	/**
	* @internal
	*/
	public string $prefix = 'admin_block_login';


	/**
	* Inits the model
	*/
	protected function init()
	{
		$this->bruteforce = new Bruteforce;
	}

	/**
	* Logins the user
	* @param string $username The username
	* @param string $password The password
	* @return bool|User The user object if the login is succesful, false otheriwse
	*/
	public function login($username, $password)
	{
		$user = $this->app->user->login($username, $password);

		if ($user) {
			//delete previous invalid login attempts
			$this->bruteforce->delete($this->app->ip, $user->id);

			//log the login
			$log_insert_array = [
				'user_id' => $user->id,
				'ip' => $this->app->ip,
				'useragent' => $this->app->useragent,
				'timestamp' => $this->app->db->unixTimestamp()
			];

			$this->app->db->insert('venus_administrators_logins', $log_insert_array);
		} else {
			//record the failed login attempt
			$this->bruteforce->insert($this->app->ip, $this->app->user->getIdByUsername($username));
		}

		return $user;
	}

	/**
	* Returns the available admin languages
	* @return array
	*/
	public function getLanguages() : array
	{
		$list = [];

		$languages = new Languages;
		foreach ($languages as $lang) {
			$info = $lang->getInfo();

			$list[$lang->name] = $info['title'];
		}

		return $list;
	}
}
