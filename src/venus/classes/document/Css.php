<?php
/**
* The Css Urls Class
* @package Venus
*/

namespace Venus\Document;

use Venus\App;
use Venus\Assets\Asset;

/**
* The Document's Css Urls Class
* Class containing the css urls/stylesheets used by a document
*/
class Css extends \Mars\Document\Css
{
	use \Venus\Assets\CacheTrait;
	use \Venus\Assets\MergeTrait;

	/**
	* Builds the css object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->extension = App::FILE_EXTENSIONS['css'];
		$this->base_cache_url = $this->app->cache_url . App::CACHE_DIRS['css'];
		$this->cache_url = $this->base_cache_url;
		$this->merge_key = 'javascript_merged';

		$this->version = $this->app->cache->css_version;
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
		return new \Venus\Assets\Css($this->app);
	}

	/**
	* Loads a css library. Alias for $app->library->loadCss()
	* @param $name The name of the library. Eg: bootstrap
	* @return $this
	*/
	public function loadLibrary(string $name)
	{
		$this->app->library->loadCss($name);

		return $this;
	}

	/**
	* Unloads a css library. Alias for $app->library->unloadCss()
	* @param $name The name of the library. Eg: bootstrap
	* @return $this
	*/
	public function unloadLibrary(string $name)
	{
		$this->app->library->unloadcss($name);

		return $this;
	}

	/**
	* Loads the 'main' css code
	* @param string $name The name of the theme
	* @param string $location The location of the url [head|footer]
	* @param int $priority The url's output priority. The higher, the better
	* @return $this
	*/
	public function loadMain(string $name, string $location = 'head', int $priority = 50000)
	{
		$url = $this->cache_url . $this->getThemeFile($name, $this->app->device->get());

		$this->app->plugins->run('documentCssLoadMain', $url, $location, $priority);

		$this->load($url, $location, $priority);

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
}
