<?php
/**
* The System's Admin Language Class
* @package Venus
*/

namespace Venus\Admin\System;

use Venus\Admin\App;

/**
* The Admin Language Class
* The system's admin language extension
*/
class Language extends \Venus\Admin\Language
{
	/**
	* Builds the system's language object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$name = $this->get();

		$this->load($name);

		$this->loadFile('index');
		$this->loadFile('errors');
		$this->loadFile('admin');

		$this->app->plugins->run('admin_system_language_construct', $this);
	}

	/**
	* @see \Venus\Language::get()
	* {@inheritDoc}
	*/
	public function get()
	{
		$name = $this->app->session->get('language');
		if (!$name) {
			$name = $this->app->config->lang;
		}

		return $name;
	}
}
