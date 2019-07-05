<?php
/**
* The Javascript Urls Class
* @package Venus
*/

namespace Venus\Document;

use Venus\App;
use Venus\Assets\Asset;

/**
* The Document's Javascript Urls Class
* Class containing the javascript urls/stylesheets used by a document
*/
class Javascript extends \Mars\Document\Javascript
{
	use \Venus\Assets\CacheTrait;
	use \Venus\Assets\MergeTrait;

	/**
	* @var array $dialogs The dialogs data
	*/
	protected $dialogs = [];

	/**
	* Builds the javascript object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->extension = App::FILE_EXTENSIONS['javascript'];
		$this->base_cache_url = $this->app->cache_url . App::CACHE_DIRS['javascript'];
		$this->cache_url = $this->base_cache_url;
		$this->merge_key = 'javascript_merged';

		$this->version = $this->app->cache->javascript_version;
		if ($this->app->development) {
			$this->version = time();
		}
	}

	/**
	* Returns the javascript assets class
	* @return Asset The assets class
	*/
	protected function getAssetsObj() : Asset
	{
		return new \Venus\Assets\Javascript($this->app);
	}

	/**
	* Loads a javascript library. Alias for $app->library->loadJavascript()
	* @param string $name The name of the library. Eg: jquery
	* @return $this
	*/
	public function loadLibrary(string $name)
	{
		$this->app->library->loadJavascript($name);

		return $this;
	}

	/**
	* Unloads a javascript library. Alias for $app->library->unloadJavascript()
	* @param string $name The name of the library. Eg: jquery
	* @return $this
	*/
	public function unloadLibrary(string $name)
	{
		$this->app->library->unloadJavascript($name);

		return $this;
	}

	/**
	* Returns the name under which the main javascript code will be cached
	* @param string $device The device
	* @param string $language The language's name
	* @return string
	*/
	public function getMainFile(string $device, string $language = '') : string
	{
		return $this->getFile('main', [$language], $device);
	}

	/**
	* Loads the 'main' javascript code (code found in the /javascript folder)
	* @param string $location The location of the url [head|footer]
	* @param int $priority The url's output priority. The higher, the better
	* @return $this
	*/
	public function loadMain(string $location = 'head', int $priority = 50000)
	{
		$async = false;
		$defer = false;

		$url = $this->cache_url . $this->getMainFile($this->app->device->get(), $this->app->lang->name);

		$this->app->plugins->run('documentJavascriptLoadMain', $url, $location, $priority, $async, $defer);

		$this->load($url, $location, $priority, $async, $defer);

		return $this;
	}

	/**
	* Merges and outputs the merged urls
	* @param array $urls The urls to merge. Must be local
	* @return $this
	*/
	public function outputMergedUrls(array $urls)
	{
		$url = $this->cache_url . $this->getMergedFile($urls);

		$this->outputUrl($url);

		return $this;
	}

	/**
	* Loads the theme's javascript code
	* @oaram string $name The name of the heme
	* @param string $location The location of the url [head|footer]
	* @param int $priority The url's output priority. The higher, the better
	* @param mixed $version If string, will add the specified version. If true, will add the configured version param to the url
	* @return $this
	*/
	public function loadTheme(string $name, string $location = 'head', int $priority = 5000, $version = true)
	{
		$url = $this->cache_url . $this->getThemeFile($name, $this->app->device->get());

		$this->load($url, $location, $priority, $version);

		return $this;
	}

	/**
	* Stores the content as as inline dialog for faster display
	* @param string $name The dialog's name
	* @param string $alias The dialog's alias
	* @param string $title The dialog's title
	* @param string $content The dialog's content
	* @return $this
	*/
	public function loadDialog(string $name, string $alias, string $title, string $content)
	{
		$this->dialogs[$name] = [
			'alias' => $alias,
			'title' => $title,
			'cnt' => $content
		];

		return $this;
	}

	/**
	* Builds the code for the inline editors
	* @return string The dialog's javascript code
	*/
	public function buildDialogs() : string
	{
		global $venus;
		if (!$this->dialogs) {
			return '';
		}

		$code = '';
		foreach ($this->dialogs as $name => $dialog) {
			$name = App::ejs($name);
			$alias = App::ejs($dialog['alias']);
			$title = App::ejs($dialog['title']);
			$content = App::ejs($dialog['content'], false);

			$code.= "venus.dialog.loadInline('{$name}', '{$alias}', '{$title}', '{$content}');\n";
		}

		$code.= "\n";

		$this->dialogs = [];

		return $code;
	}
}
