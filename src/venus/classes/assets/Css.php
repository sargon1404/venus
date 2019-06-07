<?php
/**
* The Css Cache Class
* @package Venus
*/

namespace Venus\Assets;

use Venus\App;
use Venus\Themes;
use Venus\Theme;
use Venus\Helpers\Minifier;

/**
* The Css Cache Class
* Combines, parses, minifies and caches the css assets
*/
class Css extends Asset
{
	/**
	* @internal
	*/
	protected $libraries_dir = 'css';

	/**
	* @internal
	*/
	protected $parser = null;

	/**
	* @internal
	*/
	protected $vars = [];

	/**
	* Builds the css cache object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->parser = new Parsers\Css;

		$this->extension = App::FILE_EXTENSIONS['css'];
		$this->base_cache_dir = $this->app->cache_dir . App::CACHE_DIRS['css'];
		$this->cache_dir = $this->base_cache_dir;
		$this->minify = $this->app->config->getFromScope('css_minify', 'frontend');
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
	* Returns the asset responsible for handling the dependencies for libraries of this type
	* @return Asset The asset handling the dependencies
	*/
	public function getDependenciesHandler() : Asset
	{
		return new Javascript($this->app);
	}

	/**
	* @see \Venus\Assets\Asset::minify()
	* {@inheritDoc}
	*/
	public function minify(string $content) : string
	{
		$minifier = new Minifier;

		return $minifier->minifyCss($content);
	}

	/**
	* @see \Venus\Assets\Asset::parse()
	* {@inheritDoc}
	*/
	public function parse(string $content) : string
	{
		$content = $this->parser->parse($content);

		return $this->app->plugins->filter('assetCssOarse', $content);
	}

	/**
	* @see \Venus\Assets\Asset::parseInline()
	* {@inheritDoc}
	*/
	public function parseInline(string $content, string $device) : string
	{
		$this->parser->setVars($this->vars[$device] ?? []);

		return $content;
	}

	/**
	* Returns the name of the file where a theme's css vars will be cached
	* @param string $name The name of the theme
	* @param string $device The device
	* @return string
	*/
	public function getThemeVarsFile(string $name, string $device) : string
	{
		return $this->getFile('theme', [$name], $device) . '.vars';
	}

	/**
	* Combines & minifies & caches the javascript code
	*/
	public function buildCache()
	{
		$themes = new Themes;
		$themes->load([], 'parent');

		foreach ($themes as $theme) {
			$this->cacheTheme($theme);
		}
	}

	/**
	* Caches the css code of a theme
	* @param Theme $theme The theme
	*/
	public function cacheTheme(Theme $theme)
	{
		$this->parser->setTheme($theme);

		$dir = $theme->dir . App::EXTENSIONS_DIRS['css'];
		$main_code = $this->readFromDir($dir);

		$devices = $this->app->device->getDevices();

		$this->vars = [];
		foreach ($devices as $device) {
			$code = $main_code;
			if ($device != 'desktop') {
				$code.= $this->readFromDeviceDir($dir, $device);
			}

			//read the parent's theme vars
			$parent_vars = [];
			if ($theme->parent) {
				//read the parent theme's vars
				$vars_file = $this->cache_dir . $this->getThemeVarsFile($theme->parent_name, $device);
				if (is_file($vars_file)) {
					$parent_vars = serialize(file_get_contents($vars_file));
				}
			}

			$this->parser->setVars($parent_vars);

			$cache_file = $this->getThemeFile($theme->name, $device);
			$this->store($cache_file, $code);

			//store the parsed vars so we can use it when parsing inline code
			$this->vars[$device] = $this->parser->getVars();

			//store the vars in a cache file for fast access
			$vars_file = $this->cache_dir . $this->getThemeVarsFile($theme->name, $device);
			file_put_contents($vars_file, serialize($this->parser->getVars()));
		}

		$this->cacheThemeInline($theme);
	}

	/**
	* Caches the inline css code of a theme
	* @param Theme $theme The theme
	*/
	protected function cacheThemeInline(Theme $theme)
	{
		$minify = $theme->params->css_inline_minify ?? ($theme->params->css_minify ?? $this->minify);

		$inline_code = $this->getInline($theme->dir . App::EXTENSIONS_DIRS['css'], $minify);

		$theme->updateInlineCss($inline_code);
	}
}
