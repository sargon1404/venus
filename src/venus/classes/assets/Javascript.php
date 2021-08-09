<?php
/**
* The Javascript Cache Class
* @package Venus
*/

namespace Venus\Assets;

use Venus\App;
use Venus\Themes;
use Venus\Theme;
use Venus\Languages;
use Venus\Helpers\Minifier;

/**
* The Javascript Cache Class
* Combines, parses, minifies and caches the javascript assets
*/
class Javascript extends Asset
{
	/**
	* @var array $config_array Array listing the config options to load
	*/
	protected array $config_array = ['tags_separator' => '', 'editor' => ''];

	/**
	* @var array $properties_array Array listing the properties to output
	*/
	protected array $properties_array = ['debug' => [], 'development' => ''];

	/**
	* @var array $paths_array Array listing the paths to output
	*/
	protected array $paths_array = [
		'url', 'url_static',
		'images_url', 'media_url', 'utils_url'
	];

	/**
	* @var string $scope The scope from where the config/properties are read
	*/
	protected string $scope = 'frontend';

	/**
	* @internal
	*/
	protected string $extension = 'js';

	/**
	* @internal
	*/
	protected string $cache_dir = 'javascript';

	/**
	* @internal
	*/
	protected string $libraries_dir = 'javascript';

	/**
	* @var string $merge_separator The separator between merged files
	*/
	protected string $merge_separator = ";\n";

	/**
	* Builds the javascript cache object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->dir = $this->app->javascript_dir;
		$this->extension = App::FILE_EXTENSIONS['javascript'];
		$this->base_cache_path = $this->app->cache_path . App::CACHE_DIRS['javascript'];
		$this->cache_path = $this->base_cache_path;

		$this->minify = $this->app->config->getFromScope('javascript_minify', 'frontend');
	}

	/**
	* Returns the asset responsible for handling the dependencies for libraries of this type
	* @return Asset The asset handling the dependencies
	*/
	public function getDependenciesHandler() : Asset
	{
		return new Css($this->app);
	}

	/**
	* @see \Venus\Assets\Asset::minify()
	* {@inheritdoc}
	*/
	public function minify(string $content) : string
	{
		$minifier = new Minifier;
		return $minifier->minifyJavascript($content);
	}

	/**
	* @see \Venus\Assets\Asset::parse()
	* {@inheritdoc}
	*/
	public function parse(string $content, array $params = []) : string
	{
		return $this->app->plugins->filter('asset_javascript_parse', $content, $params, $this);
	}

	/**
	* Combines & minifies & caches the javascript code
	*/
	public function buildCache()
	{
		$this->cacheMain();
		$this->cacheProperties();
		$this->cacheLanguages();
		$this->cacheThemes();
		$this->cacheInline();
	}

	/**
	* Caches the javascript code from the /javascript folder and the plugins
	*/
	public function cacheMain()
	{
		$this->app->output->message("Building main javascript code");

		$code = $this->readFromDir($this->dir);
		$code.= $this->readFromDir($this->dir . App::EXTENSIONS_DIRS['plugins']);

		$cache_file = $this->getFile('main');
		$this->store($cache_file, $code);
	}

	/**
	* Caches init, config, theme, paths properties
	*/
	public function cacheProperties()
	{
		$code = $this->getInit();
		$code.= $this->getConfig();
		$code.= $this->getProperties();
		$code.= $this->getPaths();

		$cache_file = $this->getFile('properties');
		$this->store($cache_file, $code);
	}

	/**
	* Caches, for each language, the javascript language strings
	*/
	public function cacheLanguages()
	{
		$strings = $this->getStrings();

		foreach ($strings as $lang => $code) {

			$cache_file = $this->getFile('language', [$lang]);
			$this->store($cache_file, $code);
		}
	}

	/**
	* Caches the javascript code of the themes
	*/
	public function cacheThemes()
	{
		$themes = new Themes;
		$themes->load();

		foreach ($themes as $theme) {
			$this->cacheTheme($theme);
		}
	}

	/**
	* Caches the javascript code of a theme
	* @param Theme $theme The theme for which to generate the cache code
	*/
	public function cacheTheme(Theme $theme)
	{
		$this->app->output->message("Building javascript code for theme {$theme->title}");

		//use the javascript_minify theme param, if set
		$minify = $theme->params->javascript_minify ?? null;
		if (!$theme->has_javascript_dir) {
			$theme->updateInlineJs();
			return;
		}

		$dir = $theme->dir . App::EXTENSIONS_DIRS['javascript'];

		//get the theme's config and javascript code
		$code = $this->getTheme($theme);
		$code.= $this->readFromDir($dir);

		$cache_file = $this->getFile('theme', [$theme->name]);
		$this->store($cache_file, $code, $minify);

		//cache the theme's inline javascript code
		$minify = $theme->params->javascript_inline_minify ?? ($theme->params->javascript_minify ?? $this->minify);

		$inline_code = $this->getInline($theme->dir . App::EXTENSIONS_DIRS['javascript'], $minify);

		$theme->updateInlineJs($inline_code);
	}

	/**
	* Caches the inline code
	*/
	public function cacheInline()
	{
		$this->app->output->message("Building inline javascript code");

		$inline_code = $this->getInline($this->dir, $this->minify);

		$this->app->cache->set('js_inline', $inline_code, $this->scope);
	}

	/**
	* Returns the init code
	*/
	protected function getInit() : string
	{
		$code = "var venus = new Venus;\n";
		$code.= "venus.init();\n\n";

		return $code;
	}

	/**
	* Returns the config output
	* @return string
	*/
	protected function getConfig() : string
	{
		$code = '';
		foreach ($this->config_array as $name => $data) {
			$value = $this->getConfigValue($name, $data);

			$code.= "venus.config.{$name} = '" . App::ejs($value) . "';\n";
		}

		return $code . "\n\n";
	}

	/**
	* Returns a config value
	* @param string The name of the config value
	* @param mixed $data The value's data
	* @return string The config value
	*/
	protected function getConfigValue(string $name, $data) : string
	{
		if (is_array($data)) {
			$filter = $data['filter'] ?? '';
			$scope = $data['scope'] ?? $this->scope;

			$value = $this->app->config->getFromScope($name, $scope);
			return $this->app->filter->value($value, $filter);
		} else {
			$filter = $data;

			$value = $this->app->config->get($name);
			return $this->app->filter->value($value, $filter);
		}
	}

	/**
	* Returns the properties output
	* @return string
	*/
	protected function getProperties() : string
	{
		$code = '';
		foreach ($this->properties_array as $name => $data) {
			$value = $this->getConfigValue($name, $data);

			$code.= "venus.{$name} = '" . App::ejs($value) . "';\n";
		}

		return $code . "\n\n";
	}

	/**
	* Returns the paths output
	* @return string
	*/
	protected function getPaths() : string
	{
		$code = '';
		foreach ($this->paths_array as $name) {
			$value = App::ejsc($this->app->$name);

			$code.= "venus.{$name} = '{$value}';\n";
		}

		return $code . "\n\n";
	}

	/**
	* Returns the theme's name & path & params
	* @param Theme The theme
	* @return string
	*/
	protected function getTheme(Theme $theme) : string
	{
		$code = "venus.theme.name = '" . App::ejs($theme->name) . "';\n";
		$code.= "venus.theme.url = '" . App::ejs($theme->base_url) . "';\n";
		$code.= "venus.theme.images_url = '" . App::ejs($theme->images_url) . "';\n";

		return $code . "\n\n";
	}

	/**
	* Returns the language strings for all languages
	* @return array
	*/
	protected function getStrings()
	{
		$strings = [];

		$languages = $this->getLanguagesObj();
		$languages->load();

		foreach ($languages as $lang) {
			$strings[$lang->name] = $this->getStringsCode($lang->getStringsFromFile('javascript'));
		}

		return $strings;
	}

	/**
	* Returns the languages object
	*/
	protected function getLanguagesObj()
	{
		return new Languages;
	}

	/**
	* Generates the string javascript code
	* @param array $strings The strings to generate the code from
	* @return string The generated code
	*/
	protected function getStringsCode(array $strings) : string
	{
		$code = '';
		foreach ($strings as $name => $string) {
			$name = App::ejs($name);
			$string = App::ejs($string, false);

			$code.= "venus.lang.strings['{$name}'] = '{$string}';\n";
		}

		return $code . "\n";
	}
}
