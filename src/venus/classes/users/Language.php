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
	protected User $user;

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
		return $this->user->lang;
	}

	/**
	* Returns the language
	* @param int $language_id The id of the language
	* @return object The language
	*/
	protected function get(int $language_id) : object
	{
		if ($this->user->id == $this->app->user->id) {
			return parent::get($language_id);
		}

		if ($language_id == $this->app->config->language_default) {
			return $this->getDefault();
		} else {
			return $this->getRow($language_id);
		}
	}
}
