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
	public string $prefix = 'admin_block_login';

	/**
	* @internal
	*/
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
		if ($this->model->bruteforce->isIpBlocked($this->app->ip)) {
			$this->outputIpError();
		}

		$this->plugins->run($this->prefix . 'index', $this);

		$this->view->render();
	}

	/**
	* Logins the user
	*/
	public function login() : bool
	{
		$username = $this->request->post('username');
		$password = $this->request->post('password');
		$user_id = $this->app->user->getIdByUsername($username);
		var_dump($user_id);die;
		$ip = $this->app->ip;

		if ($this->model->bruteforce->isIpBlocked($ip)) {
			$this->outputIpError();
		}
		if ($uid) {
			if ($this->model->bruteforce->isUserBlocked($uid)) {
				$this->outputUserError();
			}
		}

		$this->plugins->run($this->prefix . 'login', $username, $password, $this);

		if (!$this->validate()) {
			return false;
		}

		$user = $this->model->login($username, $password);
		if (!$user) {
			//the login failed
			if ($this->model->bruteforce->isIpBlocked($ip)) {
				$this->outputIpError();
			}
			if ($this->model->bruteforce->isUserBlocked($uid)) {
				$this->outputUserError();
			}

			$error = $this->getError($this->model->bruteforce->getAttempts(), $this->model->bruteforce->getMaxAttempts(), $this->model->bruteforce->getBlockSeconds());

			$this->errors->add($error);

			$this->plugins->run($this->prefix . 'login_failed', $this);

			return false;
		}

		//the login was succesfull. Set the language and redirect
		$this->app->session->set('admin_language', $this->getLanguage());

		$redirect_url = $this->getRedirectUrl();

		$this->plugins->run($this->prefix . 'login_success', $user, $redirect_url, $this);

		$this->app->redirect($redirect_url);

		return true;
	}

	/**
	* Returns the language to be used
	* @return string The language
	*/
	protected function getLanguage() : string
	{
		$language = $this->request->post('language');
		if (!is_dir($this->app->admin_extensions_dir . App::EXTENSIONS_DIRS['languages'] . $language)) {
			$language = '';
		}

		return $language;
	}

	/**
	* Returns the redirect url
	* @return string The redirect url
	*/
	protected function getRedirectUrl() : string
	{
		$redirect_url = $this->app->session->get('admin_referrer');
		if (!$redirect_url) {
			$redirect_url = $this->request->post('referrer_url', 'url');
		}

		if ($redirect_url) {
			$redirect_url = $this->app->uri->addHttp($redirect_url);

			if (!$this->app->uri->isLocal($redirect_url)) {
				$redirect_url = $this->app->admin_index;
			}
		} else {
			$redirect_url = $this->app->admin_index;
		}

		if ($redirect_url == $this->app->uri->getAdminBlock('login')) {
			$redirect_url = $this->app->admin_index;
		}

		$this->app->session->set('admin_referrer', '');

		$redirect_url = $this->plugins->filter($this->prefix . 'get_redirect_url', $redirect_url);

		return $redirect_url;
	}

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

		$error = App::__($lang_index, ['{MAX_ATTEMPTS}' => $max_attempts, '{BLOCK_SECONDS}' => $block_seconds, '{MINUTES}' => $time['minutes'], '{SECONDS}' => $time['seconds']]);

		$this->app->error($error);
	}

	/**
	* Returns the error
	* @param int $attempts The number of failed attempts
	* @param int $max_attempts The maximum number of attempts
	* @param int $seconds The number of seconds the user will be blocked
	* @return string The error
	*/
	protected function getError(int $attempts, int $max_attempts, int $seconds) : string
	{
		$error = App::__('login_err6');
		if ($attempts > 1) {
			$error = App::__('login_err7', ['{FAILED_ATTEMPTS}' => $attempts]);
		}

		$error.= App::__('login_err8', ['{MAX_ATTEMPTS}' => $max_attempts, '{BLOCK_INTERVAL}' => $this->app->format->timeInterval($seconds)]);

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

		$redirect_url = $this->plugins->filter($this->prefix . 'logout', $redirect_url);

		$this->app->redirectForce($redirect_url);
	}
}
