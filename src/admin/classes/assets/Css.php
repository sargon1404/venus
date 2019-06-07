<?php
/**
* The Css Admin Cache Class
* @package Venus
*/

namespace Venus\Admin\Assets;

use Venus\Admin\App;
use Venus\Admin\Themes;
use Venus\Theme;

/**
* The Css Admin Assets Class
* Combines, parses, minifies and caches the css admin assets
*/
class Css extends \Venus\Assets\Css
{
	/**
	* Builds the javascript cache object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->parser = new \Venus\Assets\Parsers\Css;

		$this->extension = App::FILE_EXTENSIONS['css'];
		$this->base_cache_dir = $this->app->cache_dir . App::CACHE_DIRS['css'];
		$this->cache_dir = $this->app->admin_cache_dir . App::CACHE_DIRS['css'];
		$this->minify = $this->app->config->getFromScope('css_minify', 'admin');
	}

	/**
	* @see \Venus\Assets\Css::buildCache()
	* {@inheritDoc}
	*/
	public function buildCache()
	{
		$theme = new \Venus\Admin\Theme($this->app);

		$this->cacheTheme($theme);
	}

	/**
	* @see \Venus\Assets\Css::cacheThemeInline()
	* {@inheritDoc}
	*/
	protected function cacheThemeInline(Theme $theme)
	{
		$minify = $theme->params->css_inline_minify ?? ($theme->params->css_minify ?? $this->minify);

		$inline_code = $this->getInline($theme->dir . App::EXTENSIONS_DIRS['css'], $minify);

		$this->app->cache->update('css_inline', $inline_code, true, 'admin');
	}
}
