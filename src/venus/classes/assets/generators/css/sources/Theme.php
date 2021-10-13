<?php
/**
* The Theme Source Class
* @package Venus
*/

namespace Venus\Assets\Generators\Css\Sources;

use Venus\App;
use Venus\Themes;
use Venus\Assets\Parsers\Css\Parsers;

/**
* The Theme Source Class
* Class generating the cache code for each theme
*/
class Theme extends Base
{
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

		$parsers = new Parsers($this->app);

		$path = $theme->path . App::EXTENSIONS_DIRS['css'];
		$base_code = $this->reader->get($path);
		$code = $parsers->parse($base_code, ['theme' => $theme]);
		$this->writer->store(['theme', $theme->name], $code);

		//store the mobile css code, if any
		$devices = $this->app->device->getDevices();
		foreach ($devices as $device) {
			if ($device == 'desktop') {
				continue;
			}

			$code = $this->reader->getForDevice($device, $path);
			$code = $base_code . "\n\n" . $code;
			$code = $parsers->parse($code, ['theme' => $theme]);
			$this->writer->store(['theme', $theme->name, $device], $code);
		}

		$this->cacheThemeInline($theme);
	}

	/**
	* Caches the inline css code of a theme
	* @param Theme $theme The theme
	*/
	protected function cacheThemeInline(\Venus\Theme $theme)
	{
		$inline_code = [];

		$parsers = new Parsers($this->app);

		$path = $theme->path . App::EXTENSIONS_DIRS['css'] . 'inline';
		$base_code = $this->reader->get($path);
		$inline_code['desktop'] = $parsers->parse($base_code, ['theme' => $theme]);

		$devices = $this->app->device->getDevices();
		foreach ($devices as $device) {
			if ($device == 'desktop') {
				continue;
			}

			$code = $this->reader->getForDevice($device, $path);
			$code = $base_code . "\n\n" . $code;
			$code = $parsers->parse($code, ['theme' => $theme]);

			$inline_code[$device] = $code;
		}

		$theme->updateInlineCss($inline_code);
	}
}
