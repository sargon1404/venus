<?php
/**
* The Admin Login Controller Class
* @package Cms\Admin\Blocks\Login
*/
namespace Cms\Admin\Blocks\Login\Controllers;

use Venus\Admin\App;


/**
* The Admin Login Controller Class
*/
class Login extends \Venus\Admin\Controller
{

	/**
	* @internal
	*/
	public string $prefix = 'admin_block_login_';

	protected array $validation_rules = [
		'username' => ['login_err4' => 'required'],
		'password' => ['login_err5' => 'required']
	];

	/**
	* Inits the controller
	*/
	protected function init()
	{
		$this->model = $this->getModel();
		$this->view = $this->getView();
	}

	/**
	* Displays the login form
	*/
	public function index()
	{
		if($this->model->bruteforce->isIpBlocked($this->app->ip)) {
			$this->outputIpError();
		}

		$this->app->plugins->run($this->prefix . 'index', $this);

		$this->view->render();
	}

	/**
	* Logins the user
	*/
	public function login() : bool
	{
		$username = $this->request->post('username');
		$password = $this->request->post('password');
		$uid = $this->app->user->getUidByUsername($username);
		$ip = $this->app->ip;

		if($this->model->bruteforce->isIpBlocked($ip))
			$this->outputIpError();
		if($uid) {
			if($this->model->bruteforce->isUserBlocked($uid))
				$this->outputUserError();
		}

		$this->app->plugins->run($this->prefix . 'login', $username, $password, $this);

		if(!$this->validate()) {
			return false;
		}

		$user = $this->model->login($username, $password);
var_dump($user);die;
		if(!$user)
		{
			//the login failed
			if($this->model->bruteforce->ipIsBlocked($ip))
				$this->outputIpError();
			if($this->model->bruteforce->userIsBlocked($uid))
				$this->outputUserError();

			$error = $this->getError($this->model->bruteforce->getAttempts(), $this->model->bruteforce->getMaxAttempts(), $this->model->bruteforce->getBlockSeconds());

			$this->errors->add($error);

			$this->app->plugins->run($this->prefix . 'login_failed', $this);

			return false;
		}

		//the login failed was succesfull. Set the language and redirect

		$this->app->session->set('admin_language', $this->getLanguage());

		$redirect_url = $this->getRedirectUrl();

		$this->app->plugins->run($this->prefix . 'login_success', $user, $redirect_url, $this);

		$this->app->redirectForce($redirect_url);

		return true;
	}

	/**
	* Returns the language to be used
	* @return string The language
	*/
	protected function getLanguage() : string
	{
		$language = $this->request->post('language');
		if(!is_dir(VENUS_ADMIN_LANGUAGES_DIR . $language))
			$language = '';

		return $language;
	}

	/**
	* Returns the redirect url
	* @return string The redirect url
	*/
	protected function getRedirectUrl() : string
	{
		global $venus;
		$redirect_url = $this->app->session->get('admin_referrer');
		if(!$redirect_url)
			$redirect_url = $this->request->post('referrer_url', 'url');

		if($redirect_url)
		{
			$redirect_url = $this->app->uri->addHttp($redirect_url);

			if(!$this->app->uri->isLocal($redirect_url))
				$redirect_url = $this->app->admin_index;
		}
		else
			$redirect_url = $this->app->admin_index;

		if($redirect_url == $this->app->admin_url . 'login.php')
			$redirect_url = $this->app->admin_index;

		$this->app->session->set('admin_referrer', '');

		$redirect_url = $this->app->plugins->filter($this->prefix . 'get_redirect_url', $redirect_url);

		return $redirect_url;
	}

	/**
	* Validates the username & password
	* @param string $username The username
	* @param string $password The password
	*/
	/*protected function validate($username, $password)
	{
		if(!$username)
			$this->errors->add(l('login_err4'));
		if(!$password)
			$this->errors->add(l('login_err5'));
	}*/

	/**
	* Outputs the error screen if the IP can't login
	*/
	protected function outputIpError()
	{
		$this->outputError('login_err1');
	}

	/**
	* Outputs the error screen if the user can't login
	*/
	protected function outputUserError()
	{
		$this->outputError('login_err9');
	}

	/**
	* Outputs the error screen
	* @param string $lang_index The language index of the error to output
	*/
	protected function outputError(string $lang_index)
	{
		$max_attempts = $this->model->bruteforce->getMaxAttempts();
		$last_attempt = $this->model->bruteforce->getLastAttempt();
		$block_seconds = $this->model->bruteforce->getBlockSeconds();
		$seconds = $block_seconds - $last_attempt;

		$time = $this->app->time->getMinutes($seconds);

		$error = App::str($lang_index, ['{MAX_ATTEMPTS}' => $max_attempts, '{BLOCK_SECONDS}' => $block_seconds, '{MINUTES}' => $time['minutes'], '{SECONDS}' => $time['seconds']]);

		$this->app->error($error);
	}

	/**
	* Returns the error
	* @param int $attempts The number of failed attempts
	* @param int $max_attempts The maximum number of attempts
	* @param int $seconds The number of seconds the user will be blocked
	*/
	protected function getError(int $attempts, int $max_attempts, int $seconds) : string
	{
		global $venus;
		$error = l('login_err6');
		if($attempts > 1)
			$error = l('login_err7', '{FAILED_ATTEMPTS}', $attempts);

		$error.= l('login_err8', ['{MAX_ATTEMPTS}', '{BLOCK_INTERVAL}'], [$max_attempts, $this->app->format->timeInterval($seconds)]);

		return $error;
	}

	/**
	* Logouts the user from admin
	*/
	public function logout()
	{
		$this->app->user->checkToken(false);
		$this->app->user->logout();

		$redirect_url = $this->app->site_index;

		$redirect_url = $this->app->plugins->filter($this->prefix . 'logout', $redirect_url);

		$this->app->redirectForce($redirect_url);
	}
}