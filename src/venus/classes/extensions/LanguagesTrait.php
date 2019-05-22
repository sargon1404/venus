<?php
/**
* The Extension's Languages Trait
* @package Venus
*/

namespace Venus\Extensions;

use Venus\App;

/**
* The Extension's Languages Trait
* Trait which allows extensions to load language files
* The protected static $languages_cache property must be declared in the classes using this trait
* @property string $languages_cache The config prefix
* The languages are cached, if $languages_cache = true, in the $languages field
*/
trait LanguagesTrait
{

	/**
	* @var bool $languages_cache If true, the list of available languges will be cached in the languages field
	*/
	//protected static $languages_cache = false;

	/**
	* Determines if the extension's language cache field can be used
	*/
	protected function canUseLanguageCache() : bool
	{
		if (empty(static::$languages_cache)) {
			return false;
		}

		return true;
	}

	/**
	* Determines if an extension has a language dir
	* @param string $language The language's name
	* @param string $name The name of the extension
	* @return bool
	*/
	protected function hasLanguage(string $language, string $name) : bool
	{
		$language_dir = $this->getDir($name) . App::EXTENSIONS_DIRS['languages'] . App::sl($language);
		if (!$this->canUseLanguageCache() || $this->app->lang->development || $this->development) {
			return is_dir($language_dir);
		}

		//check the list of cached languages, if any
		if ($name == $this->name) {
			$languages = $this->getLanguages();
			if (!$languages) {
				return false;
			}

			if (in_array($language, $languages)) {
				return true;
			}

			return false;
		}

		return is_dir($language_dir);
	}

	/**
	* Loads a file from the extension's languages dir
	* Workflow: Try to load the file from the language's extensions folder. If the file is not found:
	* Workflow: Load the file from the extension's languages folder. If the folder is not there, try to load it for the fallback language
	* $languages_cache property must be set to true, if the available languages are to be cached
	* @param string $file The name of the file to load (must not include the .php extension)
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	* @return $this
	*/
	public function loadLanguage(string $file = '', string $name = '')
	{
		if (!$name) {
			$name = $this->name;
		}
		if (!$file) {
			$file = 'index';
		}

		//check if we can load the file from the system language's folder
		if ($this->app->lang->loadExtensionFile($this->getBaseDir(), $name, $file)) {
			return;
		}

		//load the file from the extension's language dir
		if ($this->hasLanguage($this->app->lang->name, $name)) {
			$this->loadLanguageFile($this->app->lang->name, $file, $name);
		} else {
			//we don't have a folder for the current language. Use the fallback language
			if ($this->app->lang->name != $this->app->config->language_fallback) {
				if ($this->hasLanguage($this->app->config->language_fallback, $name)) {
					$this->loadLanguageFile($this->app->config->language_fallback, $file, $name);
				}
			}
		}

		return $this;
	}

	/**
	* Returns the list of available languages this extension has
	* @return array The available languages
	*/
	protected function getLanguages() : array
	{
		if ($this->languages == '-') {
			return [];
		}

		$languages = App::unserialize($this->languages);
		if (!$languages) {
			$languages = $this->readLanguages();
			$this->cacheLanguages($languages);
		}

		return $languages;
	}

	/**
	* Reads from the disk the list of available languages
	* @return array The list of available languages
	*/
	protected function readLanguages() : array
	{
		$language_dir = $this->dir . App::EXTENSIONS_DIRS['languages'];
		$this->app->file->listDir($language_dir, $languages, $files);

		return $languages;
	}

	/**
	* Caches the available languages by storing them in the 'languages' database field
	* @param array $languages The languages to cache
	*/
	protected function cacheLanguages(array $languages) : array
	{
		$id_name = $this->getIdName();
		$table = $this->getTable();

		$this->app->db->writeQuery("UPDATE {$table} SET languages = :languages WHERE {$id_name} = :id", ['languages' => App::serialize($languages, '-'), 'id' => $this->getId()]);

		return $languages;
	}

	/**
	* Loads an extension's language file
	* @param string $language The language's name
	* @param string $file The name of the file to load
	* @param string $name The name of the extension
	*/
	protected function loadLanguageFile(string $language, string $file, string $name)
	{
		$filename = $this->getDir($name) . App::EXTENSIONS_DIRS['languages'] . App::sl($language) . $file . '.php';

		$this->app->lang->loadFilename($filename);
	}
}
