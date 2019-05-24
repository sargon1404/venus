<?php
/**
* The User's Language Class
* @package Venus
*/

namespace Venus\Users;

use venus\User;

/**
* The User's Language Class
* Encapsulates the language a certain user uses
*/
class Language extends \Venus\Language
{
	/**
	* @var User $user The user
	*/
	protected $user = null;

	/**
	* Builds the user's language
	* @param User $user The user
	*/
	public function __construct(User $user)
	{
		$this->user = $user;

		parent::__construct();
	}

	/**
	* Returns the id of the language the user uses
	* @return int
	*/
	protected function getLanguageId() : int
	{
		return (int)$this->user->lang;
	}

	/**
	* Returns the language
	* @param int $lid The id of the language
	* @return object The language
	*/
	protected function get(int $lid) : object
	{
		if ($this->user->uid == $this->app->user->uid) {
			return parent::get($lid);
		}

		if ($lid == $this->app->config->language_default) {
			return $this->getDefault();
		} else {
			return $this->getRow($lid);
		}
	}
}
