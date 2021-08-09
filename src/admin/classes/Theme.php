<?php
/**
* The Theme Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Theme Class
* Object corresponding to an admin theme extension
*/
class Theme extends \Venus\System\Theme
{
	use \Venus\Admin\Extensions\Body;

	/**
	* Builds the system's theme object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$name = $this->get();

		$this->load($name);

		$this->app->plugins->run('admin_theme_construct', $this);
	}

	/**
	* Returns the name of the theme
	* @return string
	*/
	protected function get() : string
	{
		return $this->app->config->theme;
	}

	/**
	* Loads the theme
	* @param string $name The name of the theme
	*/
	public function load($name) : bool
	{
		$this->name = $name;
		$this->title = $name;

		$this->prepare();

		return true;
	}

	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareDevelopment();
		$this->prepareProperties();
		$this->prepareTemplates();

		//include the init.php file if one exists
		if (is_file($this->path . $this->init_file)) {
			include($this->path . $this->init_file);
		}
	}

	/**
	* @see \Venus\Theme::preparePaths()
	* {@inheritdoc}
	*/
	protected function preparePaths()
	{
		parent::preparePaths();

		$this->cache_path = $this->app->admin_cache_path;
		$this->cache_url = $this->app->admin_cache_url;
		$this->templates_cache_path = $this->cache_path . App::CACHE_DIRS['templates'];
	}

	/**
	* @see \Venus\Theme::prepareImagePaths()
	* {@inheritdoc}
	*/
	protected function prepareImagePaths()
	{
		$this->has_images_dir = true;
		$this->has_tablets_images_dir = is_dir($this->images_path . $this->app->device->getSubdir('tablet'));
		$this->has_smartphones_images_dir = is_dir($this->images_path . $this->app->device->getSubdir('smartphone'));
		$this->has_mobile_images_dir = is_dir($this->images_path . $this->app->device->getSubdir('mobile'));

		parent::prepareImagePaths();
	}

	/**
	* @see \Mars\Theme::prepareProperties()
	* {@inheritdoc}
	*/
	protected function prepareProperties()
	{
		parent::prepareProperties();

		//output all css & js scripts in the head, on admin
		$this->css_location = 'head';
		$this->javascript_location = 'head';
	}

	/**
	* @see \Venus\Theme::prepareTemplates()
	* {@inheritdoc}
	*/
	protected function prepareTemplates()
	{
		$this->templates = $this->app->cache->get('theme_templates');
		if ($this->templates) {
			return;
		}

		$this->app->file->listDir($this->templates_path, $dirs, $templates, false, true);

		$this->templates = array_fill_keys($templates, '');

		$this->app->cache->set('theme_templates', $this->templates);
	}
}
