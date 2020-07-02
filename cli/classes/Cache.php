<?php

namespace Cli;

class Cache extends Command
{
	/**
	* @param array $actions Array defining the available actions, in the format: [method, description, params(optional)]
	*/
	protected array $actions = [
		'all' => ['all', 'Caches all frontend and admin assets & data'],
		'libraries' => ['libraries', 'Caches the css and javascript libraries'],
		'css' => ['css', 'Caches the css code'],
		'css-frontend' => ['cssFrontend', 'Caches the frontend css code'],
		'css-admin' => ['cssAdmin', 'Caches the admin css code'],
		'javascript' => ['javascript', 'Caches the javascript code'],
		'javascript-frontend' => ['javascriptFrontend', 'Caches the frontend javascript code'],
		'javascript-admin' => ['javascriptAdmin', 'Caches the admin javascript code'],
		'languages' => ['languages', 'Caches the languages data'],
		'themes' => ['themes', 'Caches the themes data'],
		'plugins' => ['plugins', 'Caches the plugins data'],
		'usergroups' => ['usergroups', 'Caches the usergroups data'],
		'menus' => ['menus', 'Caches the menus data'],
		'menus-frontend' => ['menusFrontend', 'Caches the frontend menus'],
		'menus-admin' => ['menusAdmin', 'Caches the admin menus'],
	];

	/**
	* Caches everything
	*/
	public function all()
	{
		$this->libraries();
		$this->newline();
		$this->css();
		$this->newline();
		$this->javascript();
		$this->newline();
		$this->languages();
		$this->newline();
		$this->themes();
		$this->newline();
		$this->plugins();
		$this->usergroups();
		$this->menus();
	}

	/**
	* Caches the libraries
	*/
	public function libraries()
	{
		$this->printInfo('Building libraries...');

		$cache = new \Venus\Admin\Cache;
		$cache->buildLibraries();

		$this->done();
	}

	/**
	* Caches the frontend & admin css code
	*/
	public function css()
	{
		$this->printInfo('Building css code...');

		$this->app->cache->buildCss();

		$this->done();
	}

	/**
	* Caches the frontend css code
	*/
	public function cssFrontend()
	{
		$this->printInfo('Building frontend css code...');

		$this->app->cache->buildCssFrontend();

		$this->done();
	}

	/**
	* Caches the admin css code
	*/
	public function cssAdmin()
	{
		$this->printInfo('Building admin css code...');

		$this->app->cache->buildCssAdmin();

		$this->done();
	}

	/**
	* Caches the frontend & admin javascript code
	*/
	public function javascript()
	{
		$this->printInfo('Building javascript code...');

		$this->app->cache->buildJavascript();

		$this->done();
	}

	/**
	* Caches the frontend javascript code
	*/
	public function javascriptFrontend()
	{
		$this->printInfo('Building frontend javascript code...');

		$this->app->cache->buildJavascriptFrontend();

		$this->done();
	}

	/**
	* Caches the admin javascript code
	*/
	public function javascriptAdmin()
	{
		$this->printInfo('Building admin javascript code...');

		$this->app->cache->buildJavascriptAdmin();

		$this->done();
	}

	/**
	* Caches the languages data
	*/
	public function languages()
	{
		$this->printInfo('Building the languages cache...');

		$this->app->cache->buildLanguages();

		$this->done();
	}

	/**
	* Caches the themes data
	*/
	public function themes()
	{
		$this->printInfo('Building the themes cache...');

		$this->app->cache->buildThemes();

		$this->done();
	}

	/**
	* Caches the plugins data
	*/
	public function plugins()
	{
		$this->printInfo('Building the plugins cache...');

		$this->app->cache->buildPlugins();

		$this->done();
	}

	/**
	* Caches the usergroups data
	*/
	public function usergroups()
	{
		$this->printInfo('Building the usergroups cache...');

		$this->app->cache->buildUsergroups();

		$this->done();
	}

	/**
	* Caches the frontend & admin menus
	*/
	public function menus()
	{
		$this->printInfo('Building the menus cache...');

		$this->app->cache->buildMenus();

		$this->done();
	}

	/**
	* Caches the frontend javascript code
	*/
	public function menusFrontend()
	{
		$this->printInfo('Building the frontend menus cache...');

		$this->app->cache->buildMenusFrontend();

		$this->done();
	}

	/**
	* Caches the admin javascript code
	*/
	public function menusAdmin()
	{
		$this->printInfo('Building the admin menus cache...');

		$this->app->cache->buildMenusAdmin();

		$this->done();
	}
}
