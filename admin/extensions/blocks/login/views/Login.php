<?php
/**
* The Admin Login View Class
* @author Venus-CMS
* @package Cms\Admin\Blocks\Login
*/

namespace Cms\Admin\Blocks\Login\Views;

if(!defined('VENUS')) die;


/**
* The Admin Login View Class
*/
class Login extends \Venus\Admin\View
{

	/**
	* @internal
	*/
	public string $prefix = 'admin_block_login';


	/**
	* Displays the login form
	*/
	public function index()
	{
		$this->referrer_url = '';
		if(!empty($_SERVER['HTTP_REFERER']))
		{
			$this->referrer_url = $_SERVER['HTTP_REFERER'];
			if(!$this->app->uri->isLocal($this->referrer_url))
				$this->referrer_url = '';
		}

		$this->default_language = $this->app->config->lang;
		$this->languages = $this->model->getLanguages();

		$this->app->plugins->run($this->prefix . 'index', $this);
	}

}