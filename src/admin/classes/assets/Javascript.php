<?php
/**
* The Javascript Admin Cache Class
* @package Venus
*/

namespace Venus\Admin\Assets;

use Venus\Admin\App;
use Venus\Admin\Languages;

/**
* The Javascript Admin Assets Class
* Combines,parses,minifies and caches the javascript admin assets
*/
class Javascript extends \Venus\Assets\Javascript
{
	/**
	* @var array $config_array Array listing the config options to load
	*/
	protected $config_array = ['tags_separator' => '', 'response_param' => ''];

	/**
	* @var array $paths_array Array listing the paths to output
	*/
	protected $paths_array = [
		'admin_url', 'admin_url_rel', 'admin_utils_url'
	];

	/**
	* @var string $scope The scope from where the config/properties are read
	*/
	protected $scope = 'admin';

	/**
	* Builds the javascript cache object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->dir = $this->app->admin_javascript_dir;
		$this->extension = App::FILE_EXTENSIONS['javascript'];

		$this->base_cache_dir = $this->app->cache_dir . App::CACHE_DIRS['javascript'];
		$this->cache_dir = $this->app->admin_cache_dir . App::CACHE_DIRS['javascript'];
		$this->minify = $this->app->config->getFromScope('javascript_minify', 'admin');
	}

	/**
	* @see \Venus\Assets\Javascript::buildCache()
	* {@inheritDoc}
	*/
	public function buildCache()
	{
		$this->cacheMain();
		$this->cacheThemes();
		$this->cacheInline();
	}

	/**
	* Caches the javascript code of the admin theme
	*/
	public function cacheThemes()
	{
		$devices = $this->app->device->getDevices();

		$theme = new \Venus\Admin\Theme($this->app);

		$javascript_dir = $theme->dir . App::EXTENSIONS_DIRS['javascript'];
		$has_javascript_dir = is_dir($javascript_dir);
		$main_code = '';

		//get the javascript code of the theme, if it has a javascript dir
		if ($has_javascript_dir) {
			$main_code = $this->readFromDir($javascript_dir);
		}

		//generate the theme's javascript code for each device
		foreach ($devices as $device) {
			$code = $this->getTheme($theme, $device, false);
			$code.= $main_code;

			if ($device != 'desktop') {
				//read the javascript code for devices
				if ($has_javascript_dir) {
					$code.= $this->readFromDeviceDir($javascript_dir, $device);
				}
			}

			$cache_file = $this->getThemeFile($theme->name, $device);
			$this->store($cache_file, $code);
		}
	}

	/**
	* @see \Venus\Assets\Javascript::getInit()
	* {@inheritDoc}
	*/
	protected function getInit() : string
	{
		$code = "venus.init();\n";
		$code.= "venus.initAdmin();\n";

		return $code . "\n\n";
	}

	/**
	* @see \Venus\Assets\Javascript::getLanguagesObj()
	* {@inheritDoc}
	*/
	protected function getLanguagesObj()
	{
		return new Languages;
	}

	/**
	* @see \Venus\Assets\Javascript::getMainMerge()
	* {@inheritDoc}
	*/
	protected function getMainMerge(string $device, string $language) : string
	{
		//merge the main/frontend javascript and the admin javascript
		$code = file_get_contents($this->base_cache_dir . $this->app->cache->getJavascriptFile($device, $language)) . "\n\n";
		$code.= file_get_contents($this->cache_dir . $this->app->cache->getJavascriptFile($device, $language)) . "\n\n";

		return $code;
	}
}