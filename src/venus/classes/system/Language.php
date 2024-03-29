<?php
/**
* The System's Language Class
* @package Venus
*/

namespace Venus\System;

use Venus\App;

/**
* The System's Language Class
*/
class Language extends \Venus\Language
{
	/**
	* Builds the system's language object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$language = $this->get();

		parent::__construct($language);

		$this->loadFiles();

		$this->app->plugins->run('system_language_construct', $language, $this);
	}

	/**
	* Returns the data of a language
	* @return object The language
	*/
	public function get() : object
	{
		$id = $this->getLanguageId();

		$language = null;
		if ($id == $this->app->config->language_default) {
			return $this->getDefault();
		} else {
			return $this->getRow($id);
		}
	}

	/**
	* Returns the id of the language the user is using.
	* @return int
	*/
	protected function getLanguageId() : int
	{
		if ($this->app->user->id && $this->app->user->language_id) {
			return $this->app->user->language_id;
		}

		return $this->app->config->language_default;
	}

	/**
	* Returns the data of the default language
	* @return object The default language
	*/
	public function getDefault() : ?object
	{
		return $this->app->cache->get('language_default');
	}

	/**
	* Returns the url of the language's flag
	* @param string $name The name of the language. If empty,the current language is used
	* @return string The flag's url
	*/
	public function getFlagUrl(string $name = '') : string
	{
		return $this->getPath($name) . 'flag.png';
	}
}
