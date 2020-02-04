<?php
/**
* The Users Email Class
* @package Venus
*/

namespace Venus\Users;

use Venus\User;

/**
* The Users Email Class
* Class which sends various user related emails
*/
class Email
{
	use \Venus\AppTrait;

	/**
	* @internal
	*/
	public array $search = [];

	/**
	* @internal
	*/
	public array $replace = [];

	/**
	* @var User $user The user
	*/
	protected User $user;

	/**
	* @var Language $lang The language used to send the emails
	*/
	protected Language $lang;

	/**
	* Builds the User Email class
	* @param User $user The user who will recieve the email
	*/
	public function __construct(User $user)
	{
		$this->app = App::getApp();
		$this->user = $user;

		$this->buildSearchReplace();

		$this->loadStrings();
	}

	/**
	* Loads the user's language, so we can send emails using his selected language rather than the current language
	*/
	protected function loadStrings()
	{
		$this->lang = new Language($this->user);
		$this->lang->loadPackage('emails');
	}

	/**
	* Processes and returns a language string
	* @param string $key The string key
	* @return string The string
	*/
	protected function getString(string $key) : string
	{
		$str = '';
		if (isset($this->lang->strings[$key])) {
			$str = $this->lang->strings[$key];
		}

		$str = str_replace($this->search, $this->replace, $str);

		return $str;
	}

	/**
	* Builds the search & replace variables used in mails
	*/
	protected function buildSearchReplace()
	{
		$user = $this->user;

		$profile_url = $this->app->uri->getUser($user);
		$admin_profile_url = $this->app->uri->getAdminUser($user->uid);

		$this->search = ['{USERNAME}', '{USERNAME_RAW}', '{UID}', '{EMAIL}', '{PROFILE_URL}', '{ADMIN_PROFILE_URL}', '{LOGIN_URL}', '{SITE_NAME}'];
		$this->replace = [App::e($user->username), $user->username, $user->uid, App::e($user->email), App::e($profile_url), App::e($admin_profile_url), App::e($this->app->uri->getLogin()), App::e($this->app->config->site_name)];
	}

	/**
	* Adds search & replace variables to the ones already existing
	* @param array $search The search vars
	* @param array $replace The replace vars
	*/
	protected function addSearchReplace(array $search, array $replace)
	{
		$this->search = array_merge($this->search, $search);
		$this->replace = array_merge($this->replace, $replace);
	}

	/**
	* Sends the email informing the user his account is activated
	* @return $this
	*/
	public function sendIsActivated()
	{
		$this->app->mail($this->user->email, $this->getString('email_user_is_activated_subject'), $this->getString('email_user_is_activated_message'));

		return $this;
	}

	/**
	* Sends to the admins the email informing them a new user has registered
	* @param array $emails The emails where the mail will be sent. If empty, it'll be sent to all admins
	* @return $this
	*/
	public function sendIsRegisteredToAdmins(array $emails = [])
	{
		$user = $this->user;

		if (!$emails) {
			$admins = new Admins;
			$emails = $admins->getEmails();
		}

		$this->app->mail($this->user->email, $this->getString('email_admins_user_is_registered_subject'), $this->getString('email_admins_user_is_registered_message'));

		return $this;
	}

	/**
	* Sends the email informing the user his account is enabled
	* @return $this
	*/
	public function sendIsEnabled()
	{
		$this->app->mail($this->user->email, $this->getString('email_user_is_enabled_subject'), $this->getString('email_user_is_enabled_message'));

		return $this;
	}

	/**
	* Sends the email with the activation link/code to an user
	* @return $this
	*/
	public function sendActivationCode()
	{
		$user = $this->user;

		$activation_url = $this->app->uri->getRegister('activate', ['uid' => (int)$user->uid, 'code' => $user->activation_code]);
		$activation_form_url = $this->app->uri->getRegister('activate_form');

		$search = ['{ACTIVATION_URL}', '{ACTIVATION_FORM_URL}', '{ACTIVATION_CODE}'];
		$replace = [App::e($activation_url), App::e($activation_form_url), App::e($user->activation_code)];

		$this->addSearchReplace($search, $replace);

		$this->app->mail($this->user->email, $this->getString('email_user_activation_code_subject'), $this->getString('email_user_activation_code_message'));

		return $this;
	}

	/**
	* Sends to the admins the email informing them a new user has registered and needs to be activated
	* @param array $emails The emails where the mail will be sent. If empty, it'll be sent to all admins
	* @return $this
	*/
	public function sendActivationNeededToAdmins(array $emails = [])
	{
		if (!$emails) {
			$admins = new Admins;
			$emails = $admins->getEmails();
		}

		$awaiting_activation_url = $this->app->uri->getAdminBlock('admin_users', '', [$this->app->config->controller_param => 'deactivated']);

		$search = ['{AWAITING_ACTIVATION_URL}'];
		$replace = [App::e($awaiting_activation_url)];

		$this->addSearchReplace($search, $replace);

		$this->app->mail($this->user->email, $this->getString('email_admins_activation_needed_subject'), $this->getString('email_admins_activation_needed_message'));

		return $this;
	}

	/**
	* Sends to the user an email with his forgotten username
	* @return $this
	*/
	public function sendForgottenUsername()
	{
		$this->app->mail($this->user->email, $this->getString('email_user_forgot_username_subject'), $this->getString('email_user_forgot_username_message'));

		return $this;
	}

	/**
	* Sends to the user the email with the confirmation he has requested the forgotten password
	* @param string $code The confirmation code
	* @return $this
	*/
	public function sendForgottenPasswordConfirmation(string $code)
	{
		$search = ['{RESET_PASSWORD_URL}'];
		$replace = [e($this->app->uri->getLogin('reset_password', ['uid' => (int)$this->user->uid, 'code' => $code]))];

		$this->addSearchReplace($search, $replace);

		$this->app->mail($this->user->email, $this->getString('email_user_forgot_password_confirmation_subject'), $this->getString('email_user_forgot_password_confirmation_message'));

		return $this;
	}

	/**
	* Sends to the user an email with his forgotten password
	* @param string $password The user's new password
	* @return $this
	*/
	public function sendForgottenPassword(string $password)
	{
		$search = ['{PASSWORD}'];
		$replace = [$password];

		$this->addSearchReplace($search, $replace);

		$this->app->mail($this->user->email, $this->getString('email_user_forgot_password_subject'), $this->getString('email_user_forgot_password_message'));

		return $this;
	}

	/**
	* Sends to the user an email informing him he received a new private message
	* @return $this
	*/
	public function sendPrivateMessage()
	{
		$search = ['{URL}'];
		$replace = [e($this->app->uri->getPrivateMessages('inbox'))];

		$this->addSearchReplace($search, $replace);

		$this->app->mail($this->user->email, $this->getString('email_user_pm_new_subject'), $this->getString('email_user_pm_new_message'));

		return $this;
	}

	/**
	* Sends to the user an email informing him there are new comments on a page where he subscribed
	* @param string $url The page's url
	* @param string $unsubscribe_url The unsubscribe url
	* @return $this
	*/
	public function sendCommentsNotification(string $url, string $unsubscribe_url)
	{
		$search = ['{URL}', '{UNSUBSCRIBE_URL}'];
		$replace = [e($url), e($unsubscribe_url)];

		$this->addSearchReplace($search, $replace);

		$this->app->mail($this->user->email, $this->getString('email_user_comment_notification_subject'), $this->getString('email_user_comment_notification_message'));

		return $this;
	}
}
