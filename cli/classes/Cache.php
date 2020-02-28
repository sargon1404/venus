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
		'usergroups' => ['usergroups', 'Caches the usergroups data'],
	];

	/**
	* Caches everything
	* @param array $options Not used
	*/
	public function all($options)
	{
		$this->libraries($options);
		$this->newline();
		$this->css($options);
		$this->newline();
		$this->javascript($options);
		$this->newline();
		$this->languages($options);
		$this->newline();
		$this->themes($options);
		$this->newline();
		$this->usergroups($options);
	}

	/**
	* Caches the libraries
	* @param array $options Not used
	*/
	public function libraries(array $options)
	{
		$this->info('Building libraries...');

		$cache = new \Venus\Admin\Cache;
		$cache->buildLibraries();

		$this->done();
	}

	/**
	* Caches the frontend & admin css code
	* @param array $options Not used
	*/
	public function css(array $options)
	{
		$this->info('Building css code...');

		$this->app->cache->buildCss();

		$this->done();
	}

	/**
	* Caches the frontend css code
	* @param array $options Not used
	*/
	public function cssFrontend(array $options)
	{
		$this->info('Building frontend css code...');

		$this->app->cache->buildCssFrontend();

		$this->done();
	}

	/**
	* Caches the admin css code
	* @param array $options Not used
	*/
	public function cssAdmin(array $options)
	{
		$this->info('Building admin css code...');

		$this->app->cache->buildCssAdmin();

		$this->done();
	}

	/**
	* Caches the frontend & adminjavascript code
	* @param array $options Not used
	*/
	public function javascript(array $options)
	{
		$this->info('Building javascript code...');

		$this->app->cache->buildJavascript();

		$this->done();
	}

	/**
	* Caches the frontend javascript code
	* @param array $options Not used
	*/
	public function javascriptFrontend(array $options)
	{
		$this->info('Building frontend javascript code...');

		$this->app->cache->buildJavascriptFrontend();

		$this->done();
	}

	/**
	* Caches the admin javascript code
	* @param array $options Not used
	*/
	public function javascriptAdmin(array $options)
	{
		$this->info('Building admin javascript code...');

		$this->app->cache->buildJavascriptAdmin();

		$this->done();
	}

	/**
	* Caches the languages data
	* @param array $options Not used
	*/
	public function languages(array $options)
	{
		$this->info('Building the languages cache...');

		$this->app->cache->buildLanguages();

		$this->done();
	}

	/**
	* Caches the themes data
	* @param array $options Not used
	*/
	public function themes(array $options)
	{
		$this->info('Building the themes cache...');

		$this->app->cache->buildThemes();

		$this->done();
	}

	/**
	* Caches the usergroups data
	* @param array $options Not used
	*/
	public function usergroups(array $options)
	{
		$this->info('Building the usergroups cache...');

		$this->app->cache->buildUsergroups();

		$this->done();
	}
}
