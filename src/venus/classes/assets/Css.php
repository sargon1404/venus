<?php
/**
* The Css Cache Class
* @package Venus
*/

namespace Venus\Assets;

use Venus\App;
use Venus\Assets\Generators\Css\Generators;
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
	protected string $libraries_dir = 'css';

	/**
	* @internal
	*/
	//protected $parser;

	/**
	* @var string $merge_separator The separator between merged files
	*/
	protected string $merge_separator = "\n";

	/**
	* Returns the generators handler
	* @return
	*/
	public function getGeneratorsHandler()
	{
		return new Generators($this->app);
	}







	/**
	* Builds the css cache object
	*/
	/*public function __construct(App $app)
	{
		$this->app = $app;
		//$this->parser = new Css\Parser($this->app);

		$this->extension = App::FILE_EXTENSIONS['css'];
		$this->base_cache_path = $this->app->cache_path . App::CACHE_DIRS['css'];
		$this->cache_path = $this->base_cache_path;
		$this->minify = $this->app->config->getFromScope('css_minify', 'frontend');
	}*/

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
	* {@inheritdoc}
	*/
	/*public function minify(string $content) : string
	{
		$minifier = new Minifier;

		return $minifier->minifyCss($content);
	}*/

	/**
	* @see \Venus\Assets\Asset::parse()
	* {@inheritdoc}
	*/
	/*public function parse(string $content, array $params = []) : string
	{
		$content = $this->parser->parse($content, $params);

		return $this->app->plugins->filter('asset_css_parse', $content, $params, $this);
	}*/

	/**
	* @see \Venus\Assets\Asset::parseInline()
	* {@inheritdoc}
	*/
	/*public function parseInline(string $content, string $device) : string
	{
		$this->parser->setVars($this->vars[$device] ?? []);

		return $content;
	}*/

	/**
	* Returns the name of the file where a theme's css vars will be cached
	* @param string $name The name of the theme
	* @param string $device The device
	* @return string
	*/
	/*public function getThemeVarsFile(string $name, string $device = '') : string
	{
		return $this->getFile('theme', [$name, $device]) . '.vars';
	}*/

	/**
	* Caches the css code of a theme
	* @param Theme $theme The theme
	*/
	/*public function cacheTheme(Theme $theme)
	{
		$this->app->output->message("Building css code for theme {$theme->title}");

		$this->vars = [];
		$this->parser->setTheme($theme);

		$dir = $theme->dir . App::EXTENSIONS_DIRS['css'];
		$code = $this->readFromDir($dir);

		$this->storeCode($theme, $code);

		//store the mobile css code, if any
		$devices = $this->app->device->getDevices();
		foreach ($devices as $device) {
			if ($device == 'desktop') {
				continue;
			}

			$code = $this->readFromDeviceDir($dir, $device);

			$this->storeCode($theme, $code, $device);
		}

		$this->cacheThemeInline($theme);
	}*/

	/**
	* Stores the css code
	* @param Theme $theme The current theme
	* @param string $code The css code to store
	* @param string $device The device to store the code for
	*/
	/*protected function storeCode(Theme $theme, string $code, string $device = '')
	{
		$vars = $this->getParentVars($theme, $device);
		if ($device) {
			$vars = array_merge($vars, $this->vars['desktop']);
		}

		$this->parser->setVars($vars);

		//store the main css code
		$cache_file = $this->getFile('theme', [$theme->name, $device]);
		$this->store($cache_file, $code);

		//store the vars
		$this->storeVars($theme, $device);

		//store the parsed vars so we can use it when parsing inline code
		if (!$device) {
			$device = 'desktop';
		}

		$this->vars[$device] = $this->parser->getVars();
	}*/

	/**
	* Returns the parent's theme vars
	* @param Theme $theme The current theme
	* @param string $device The device to return the vars for
	* @return array The vars
	*/
	/*protected function getParentVars(Theme $theme, string $device = '') : array
	{
		$vars = [];
		if (!$theme->parent) {
			return $vars;
		}

		$vars_file = $this->cache_path . $this->getThemeVarsFile($theme->parent_name, $device);
		if (is_file($vars_file)) {
			$vars = serialize(file_get_contents($vars_file));
		}

		return $vars;
	}*/

	/**
	* Stores the vars
	* @param Theme $theme The current theme
	* @param string $device The device to store the vars for
	*/
	/*protected function storeVars(Theme $theme, string $device = '')
	{
		$vars_file = $this->cache_path . $this->getThemeVarsFile($theme->name, $device);
		file_put_contents($vars_file, serialize($this->parser->getVars()));
	}*/

	/**
	* Caches the inline css code of a theme
	* @param Theme $theme The theme
	*/
	/*protected function cacheThemeInline(Theme $theme)
	{
		$minify = $theme->params->css_inline_minify ?? ($theme->params->css_minify ?? $this->minify);

		$inline_code = $this->getInline($theme->dir . App::EXTENSIONS_DIRS['css'], $minify);

		$theme->updateInlineCss($inline_code);
	}*/
}
