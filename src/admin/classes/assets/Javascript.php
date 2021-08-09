<?php
/**
* The Javascript Admin Cache Class
* @package Venus
*/

namespace Venus\Admin\Assets;

use Venus\Admin\App;
use Venus\Admin\Languages;
use Venus\Theme;

/**
* The Javascript Admin Assets Class
* Combines,parses,minifies and caches the javascript admin assets
*/
class Javascript extends \Venus\Assets\Javascript
{
	/**
	* @var array $config_array Array listing the config options to load
	*/
	protected array $config_array = ['tags_separator' => '', 'response_param' => ''];

	/**
	* @var array $paths_array Array listing the paths to output
	*/
	protected array $admin_paths_array = [
		'admin_url', 'admin_utils_url'
	];

	/**
	* @var string $scope The scope from where the config/properties are read
	*/
	protected string $scope = 'admin';

	/**
	* Builds the javascript cache object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->dir = $this->app->admin_javascript_dir;
		$this->extension = App::FILE_EXTENSIONS['javascript'];

		$this->base_cache_path = $this->app->cache_path . App::CACHE_DIRS['javascript'];
		$this->cache_path = $this->app->admin_cache_path . App::CACHE_DIRS['javascript'];
		$this->minify = $this->app->config->getFromScope('javascript_minify', 'admin');

		$this->paths_array = array_merge($this->paths_array, $this->admin_paths_array);
	}

	/**
	* @see \Venus\Assets\Javascript::buildCache()
	* {@inheritdoc}
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
	* Caches the javascript code of the admin theme
	*/
	public function cacheThemes()
	{
		$theme = new \Venus\Admin\Theme($this->app);

		$this->cacheTheme($theme);
	}

	/**
	* @see \Venus\Assets\Javascript::cacheTheme()
	* {@inheritdoc}
	*/
	public function cacheTheme(Theme $theme)
	{
		$this->app->output->message("Building javascript code for theme {$theme->title}");

		$javascript_dir = $theme->dir . App::EXTENSIONS_DIRS['javascript'];
		$has_javascript_dir = is_dir($javascript_dir);
		$main_code = '';

		//get the javascript code of the theme, if it has a javascript dir
		if ($has_javascript_dir) {
			$main_code = $this->readFromDir($javascript_dir);
		}

		//generate the theme's javascript code for each device
		$devices = $this->app->device->getDevices();
		foreach ($devices as $device) {
			$code = $this->getTheme($theme, $device, false);
			$code.= $main_code;

			if ($device != 'desktop') {
				//read the javascript code for devices
				if ($has_javascript_dir) {
					$code.= $this->readFromDeviceDir($javascript_dir, $device);
				}
			}

			$cache_file = $this->getFile('theme', [$theme->name]);
			$this->store($cache_file, $code);
		}
	}

	/**
	* Returns the init code
	*/
	protected function getInit() : string
	{
		$code = "var venus = new VenusAdmin;\n";
		$code.= "venus.init();\n\n";

		return $code;
	}

	/**
	* @see \Venus\Assets\Javascript::getLanguagesObj()
	* {@inheritdoc}
	*/
	protected function getLanguagesObj()
	{
		return new Languages;
	}
}
