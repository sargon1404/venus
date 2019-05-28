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
	protected $config_array = ['tags_separator' => '', 'editor' => ''];

	/**
	* @var array $properties_array Array listing the properties to output
	*/
	protected $properties_array = ['debug' => [], 'development' => ''];

	/**
	* @var array $paths_array Array listing the paths to output
	*/
	protected $paths_array = [
		'site_url', 'site_url_rel', 'site_url_static',
		'images_url', 'media_url', 'utils_url'
	];

	/**
	* @var string $scope The scope from where the config/properties are read
	*/
	protected $scope = 'site';

	/**
	* @internal
	*/
	protected $extension = 'js';

	/**
	* @internal
	*/
	protected $cache_dir = 'javascript';

	/**
	* @internal
	*/
	protected $libraries_dir = 'javascript';

	/**
	* Builds the javascript cache object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->dir = $this->app->javascript_dir;
		$this->extension = App::FILE_EXTENSIONS['javascript'];
		$this->base_cache_dir = $this->app->cache_dir . App::CACHE_DIRS['javascript'];
		$this->cache_dir = $this->base_cache_dir;

		$this->minify = $this->app->config->getFromScope('javascript_minify', 'site');
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
	* {@inheritDoc}
	*/
	public function minify(string $content) : string
	{
		$minifier = new Minifier;
		return $minifier->minifyJavascript($content);
	}

	/**
	* @see \Venus\Assets\Asset::parse()
	* {@inheritDoc}
	*/
	public function parse(string $content) : string
	{
		return $this->app->plugins->filter('assetJavascriptParse', $content);
	}

	/**
	* Combines & minifies & caches the javascript code
	*/
	public function cache()
	{
		$this->cacheMain();
		$this->cacheThemes();
		$this->cacheInline();
	}

	/**
	* Combines & minifies & caches the javascript code from the /javascript folder, the plugins, the config options, the paths and the strings
	*/
	public function cacheMain()
	{
		$main_code = $this->readFromDir($this->dir);
		$plugins_code = $this->readFromDir($this->dir . App::EXTENSIONS_DIRS['plugins']);
		$config_code = $this->getConfig();
		$properties_code = $this->getProperties();
		$path_code = $this->getPaths();
		$init_code = $this->getInit();
		$strings = $this->getStrings();

		//get javascript code for each device
		foreach ($this->app->device->devices as $device) {
			//build the main code
			$code = $main_code;
			if ($device != 'desktop') {
				$code.= $this->readFromDeviceDir($this->dir, $device);
			}

			//build the plugins code
			$code.= $plugins_code;
			if ($device != 'desktop') {
				$code.= $this->readFromDeviceDir($this->dir . App::EXTENSIONS_DIRS['plugins'], $device);
			}

			//build the config & paths
			$code.= $config_code;
			$code.= $properties_code;
			$code.= $path_code;

			//cache the js code, without any strings. We'll need it in the admin
			$cache_file = $this->app->cache->getJavascriptFile($device);
			$this->store($cache_file, $code);

			foreach ($strings as $lang => $strings_code) {
				$cache_code = $code;
				$cache_code.= $strings_code;
				$cache_code.= $init_code;

				$cache_file = $this->app->cache->getJavascriptFile($device, $lang);
				$this->store($cache_file, $cache_code);
			}
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
		//use the javascript_minify theme param, if set
		$minify = $theme->params->javascript_minify ?? null;

		if (!$theme->has_javascript_dir) {
			$theme->updateInlineJs();
			return;
		}

		//read the theme's main javascript code
		$dir = $theme->dir . App::EXTENSIONS_DIRS['javascript'];
		$main_code = $this->readFromDir($dir);

		//generate the theme's javascript code for each device
		$devices = $this->app->device->getDevices();

		foreach ($devices as $device) {
			$code = $this->getTheme($theme, $device);
			$code.= $main_code;

			if ($device != 'desktop') {
				$code.= $this->readFromDeviceDir($dir, $device);
			}

			$cache_file = $this->app->cache->getThemeJavascriptFile($theme->name, $device);
			$this->store($cache_file, $code, $minify);
		}

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
		$inline_code = $this->getInline($this->dir, $this->minify);

		$this->app->cache->update('js_inline', $inline_code, true, $this->scope);
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
	* @param string $device The device for which to return the info
	* @param bool $add_params If true, will also return the params
	* @return string
	*/
	protected function getTheme(Theme $theme, string $device, bool $add_params = true) : string
	{
		$image_paths = $theme->getImagePaths($theme, $device);

		$code = "venus.theme.name = '" . App::ejs($theme->name) . "';\n";
		$code.= "venus.theme.dir_url = '" . App::ejs($theme->dir_url_static) . "';\n";
		$code.= "venus.theme.images_url = '" . App::ejs($image_paths[1]) . "';\n";

		if ($add_params) {
			$code.= "venus.theme.params = venus.decode('" . $this->app->javascript->encode($theme->getParams($device)) . ")';\n";
		}

		return $code . "\n\n";
	}

	/**
	* Returns the init code
	* @return string
	*/
	protected function getInit() : string
	{
		$code = "venus.init();\n";

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
			$strings[$lang->name] = $this->getStringsCode($lang->getStrings('javascript'));
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

		$code.= "\n";

		return $code;
	}

	/**
	* Merges multiple javascript files into one
	* @param string $file The name of the file under which the files will be merged
	* @param string $device The name of the device for which to build the merge
	* @param string $language The name of the language for which to build the merge
	* @param array $libraries The libraries to include in the merge
	* @param array $library_dependencies The javascript dependencies of non-javascript libraries
	* @param array $local_urls The local urls to include in the merge
	* @return $this
	*/
	public function merge(string $file, string $device, string $language, array $libraries, array $library_dependencies, array $local_urls)
	{
		$code = $this->getLibrariesMerge($libraries);
		$code.= $this->getLibraryDependenciesMerge($library_dependencies);
		$code.= $this->getMainMerge($device, $language);
		$code.= $this->getLocalUrlsMerge($local_urls);

		$this->store($file, $code, false, false);

		return $this;
	}

	/**
	* Returns the merged code of the main javascript file
	* @param string $device The name of the device for which to build the merge
	* @param string $language The name of the language for which to build the merge
	* @return string The merged code
	*/
	protected function getMainMerge(string $device, string $language) : string
	{
		return file_get_contents($this->cache_dir . $this->app->cache->getJavascriptFile($device, $language)) . "\n\n";
	}
}
