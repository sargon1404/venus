<?php
/**
* The Javascript Urls Class
* @package Venus
*/

namespace Venus\Admin\Document;

use Venus\Admin\App;
use Venus\Assets\Asset;

/**
* The Document's Javascript Urls Class
* Class containing the javascript urls/stylesheets used by a document
*/
class Javascript extends \Venus\Document\Javascript
{
	/**
	* Builds the javascript object
	*/
	public function __construct(App $app)
	{
		parent::__construct($app);

		$this->cache_url = $this->app->admin_cache_url . App::CACHE_DIRS['javascript'];
	}

	/**
	* Returns the javascript assets class
	* @return Asset The assets class
	*/
	protected function getAssetsObj() : Asset
	{
		return new \Venus\Admin\Assets\Javascript($this->app);
	}

	/**
	* @see \Venus\Document\Javascript::loadMain()
	* @inheritdocs
	*/
	public function loadMain(string $location = 'head', int $priority = 50000)
	{
		$async = false;
		$defer = false;

		//load the frontend &admin javascript code
		$frontend_url = $this->base_cache_url . $this->getMainFile($this->app->device->get(), $this->app->lang->name);
		$admin_url = $this->cache_url . $this->getMainFile($this->app->device->get(), $this->app->lang->name);

		$this->app->plugins->run('admin_document_javascript_load_main', $frontend_url, $admin_url, $location, $priority, $async, $defer);

		$this->load($frontend_url, $location, $priority, $async, $defer);
		$this->load($admin_url, $location, $priority, $async, $defer);

		return $this;
	}
}
