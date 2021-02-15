<?php
/**
* The Css Urls Class
* @package Venus
*/

namespace Venus\Admin\Document;

use Venus\Admin\App;
use Venus\Assets\Asset;

/**
* The Document's Css Urls Class
* Class containing the css urls/stylesheets used by a document
*/
class Css extends \Venus\Document\Css
{
	/**
	* Builds the css object
	*/
	public function __construct(App $app)
	{
		parent::__construct($app);

		$this->cache_url = $this->app->admin_cache_url . App::CACHE_DIRS['css'];
	}

	/**
	* Returns the javascript assets class
	* @return Asset The assets class
	*/
	protected function getAssetsObj() : Asset
	{
		return new \Venus\Admin\Assets\Css($this->app);
	}

	/**
	* @see \Venus\Document\Css::loadMain()
	* {@inheritdoc}
	*/
	public function loadMain(string $name, string $location = 'head', int $priority = 50000)
	{
		$url = $this->cache_url . $this->getThemeFile($name, $this->app->device->get());

		$this->app->plugins->run('admin_document_css_load_main', $url, $location, $priority);

		$this->load($url, $location, $priority);

		return $this;
	}
}
