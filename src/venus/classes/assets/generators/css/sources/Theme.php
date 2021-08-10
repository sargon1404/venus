<?php
/**
* The Theme Source Class
* @package Venus
*/

namespace Venus\Assets\Generators\Css\Sources;

use Venus\App;
use Venus\Themes;

/**
* The Theme Source Class
* Class generating the cache code for each theme
*/
class Theme extends Base
{
	/**
	* @internal
	*/
	protected array $vars = [];

	/**
	* @internal
	*/
	protected $parser;

	/**
	* Generates the cache for each source
	*/
	public function cache()
	{
		$themes = new Themes;
		$themes->load([], 'parent');

		foreach ($themes as $theme) {
			$this->cacheTheme($theme);
		}
	}

	/**
	* Caches the css code of a theme
	* @param \Venus\Theme $theme The theme
	*/
	public function cacheTheme(\Venus\Theme $theme)
	{
		$this->app->output->message("Building css code for theme {$theme->title}");

		$this->vars = [];
		//$this->parser->setTheme($theme);

		$path = $theme->path . App::EXTENSIONS_DIRS['css'];
		print_r($this->app->dir->getFilesTree($path));
		die("xxxx");
		$code = $this->reader->get($path);
		die("xxxxaaaa");
		$this->storeCode($theme, $code);

		//store the mobile css code, if any
		$devices = $this->app->device->getDevices();
		foreach ($devices as $device) {
			if ($device == 'desktop') {
				continue;
			}

			$code = $this->readFromDeviceDir($path, $device);

			$this->storeCode($theme, $code, $device);
		}

		$this->cacheThemeInline($theme);
	}

	/**
	* Returns the list of urls which have been generated
	* @return array
	*/
	public function getUrls() : array
	{
		$urls = [];
		die("get urls");
		return $urls;
	}
}
