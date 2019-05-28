<?php
/**
* The System's Admin Theme Class
* @package Venus
*/

namespace Venus\Admin\System;

use Venus\Admin\App;
use Venus\Admin\Output\Menu;

/**
* The System's Admin Theme Class
* The system's admin theme extension
*/
class Theme extends \Venus\System\Theme
{
	use \Venus\Admin\Extensions\Body;

	/**
	* @var string $tab_id The tab id of the currently selected tab
	*/
	public $tab_id = 1;

	/**
	* Builds the system's theme object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$name = $this->get();

		$this->load($name);

		$this->app->plugins->run('adminSystemThemeConstruct', $this);
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

	/**
	* @see \Venus\Theme::prepare()
	* {@inheritDoc}
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareDevelopment();
		$this->prepareProperties();
		$this->prepareVars();
		$this->prepareLibraries();
		$this->prepareMainUrls();
		die("oooo");
		$this->prepareTemplates();
		$this->prepareForDialog();
		$this->prepareTab();

		//include the init.php file if one exists
		if (is_file($this->dir . $this->init_file)) {
			include($this->dir . $this->init_file);
		}
	}

	/**
	* @see \Venus\Theme::preparePaths()
	* {@inheritDoc}
	*/
	protected function preparePaths()
	{
		parent::preparePaths();

		$this->cache_dir = $this->app->admin_cache_dir;
		$this->cache_url = $this->app->admin_cache_url;
		$this->templates_cache_dir = $this->cache_dir . App::CACHE_DIRS['templates'];
	}

	/**
	* @see \Venus\Theme::prepareImagePaths()
	* {@inheritDoc}
	*/
	protected function prepareImagePaths()
	{
		$this->has_images_dir = true;
		$this->has_tablets_images_dir = is_dir($this->images_dir . $this->app->device->getSubdir('tablet'));
		$this->has_smartphones_images_dir = is_dir($this->images_dir . $this->app->device->getSubdir('smartphone'));
		$this->has_mobile_images_dir = is_dir($this->images_dir . $this->app->device->getSubdir('mobile'));

		parent::prepareImagePaths();
	}

	/**
	* @see \Mars\Theme::prepareProperties()
	* {@inheritDoc}
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
	* {@inheritDoc}
	*/
	protected function prepareTemplates()
	{
		$this->templates = $this->app->cache->get('theme_templates', true);

		if ($this->templates) {
			return;
		}

		$this->app->file->listDir($this->templates_dir, $dirs, $templates, false, true);

		$this->templates = array_fill_keys($templates, '');

		$this->app->cache->update('theme_templates', $this->templates, true, 'admin');
	}

	/**
	* @see \Venus\System\Theme::prepareJquery()
	* {@inheritDoc}
	*/
	protected function prepareJquery()
	{
	$this->app->javascript->load('//www.domain.com/script1.js', 'head');
	$this->app->javascript->load('http://www.domain.com/script3.js', 'head');
	$this->app->javascript->load('https://localhost/venus/admin/qqqq.js');
	//$this->app->javascript->load('https://localhost/venus/admin/aaaa/qqqq.js', 'head');
	$this->app->javascript->load('http://www.domain.com/script3_footer.js', 'footer');
	//$this->app->javascript->load('https://localhost/venus/admin/qqqq_footer.js', 'footer');

		$this->app->library->loadJavascript('jquery');
		//$this->app->library->loadJavascript('jquery-ui-admin');
		$this->app->library->loadJavascript('jquery-ui');

		$this->app->library->loadCss('bootstrap');
		//$this->app->library->unloadJavascript('jquery-ui');
		$this->app->library->unloadCss('bootstrap');
	}

	/**
	* @see \Venus\System\Theme::prepareMainUrls()
	* {@inheritDoc}
	*/
	protected function prepareMainUrls()
	{
		//javascript
		//var_dump($this->app->cache->getFrontendJavascriptUrl($this->app->device->type, $this->app->lang->name));die;

		//load the main js code
		$this->app->javascript->loadMain($this->javascript_location, $this->javascript_priority);
		//load the theme's js code
		$this->app->javascript->load($this->app->cache->getThemeJavascriptUrl($this->name, $this->app->device->type), $this->theme_javascript_location, $this->theme_javascript_priority);


		//$this->app->javascript->load($this->app->cache->getJavascriptUrl($this->app->device->type, $this->app->lang->name), $this->javascript_location, $this->javascript_priority);
		//$this->app->javascript->load($this->app->cache->getThemeJavascriptUrl($this->name, $this->app->device->type), $this->theme_javascript_location, $this->theme_javascript_priority);

		App::pp($this->app->javascript);
		die;
	}

	/**
	* Sets the currently active tab, if any
	*/
	protected function prepareTab()
	{
		$this->tab_id = $this->app->request->value('tab-id');
		if (!$this->tab_id) {
			$this->tab_id = 1;
		}
	}

	/**
	* @see \Venus\System\Theme::prepareVars()
	* {@inheritDoc}
	*/
	protected function prepareVars()
	{
		parent::prepareVars();

		$this->addVar('controls', $this->app->controls);
		$this->addVar('navbar', $this->app->navbar);
	}

	/**************POSITION METHODS***********************************/

	public function getBannerPositions()
	{
		var_dump("to do");
		die;
		$positions = [];

		$theme = $this->getDefaultTheme();
		$theme_positions = unserialize_field($theme->banner_positions);

		foreach ($theme_positions as $pos => $name) {
			$positions[$pos] = l($name);
		}

		$this->app->plugins->run('adminSystemThemeGetBannerPositions', $this, $positions);

		return $positions;
	}

	public function getWidgetPositions()
	{
		var_dump("to do");
		die;
		$positions = [];

		$theme = $this->getDefaultTheme();
		$theme_positions = unserialize_field($theme->widget_positions);

		foreach ($theme_positions as $pos => $name) {
			$positions[$pos] = l($name);
		}

		$this->app->plugins->run('adminSystemThemeGetWidgetPositions', $this, $positions);

		return $positions;
	}

	/**************** OUTPUT *************************************/

	/**
	* @see \Venus\System\Theme::getTitle()
	* {@inheritDoc}
	*/
	protected function getTitle() : string
	{
		$title = $this->app->title->get();

		return $this->app->plugins->filter('adminSystemThemeGetTitle', $title, $this);
	}

	/**************** CSS & JAVASCRIPT *************************************/

	/**
	* @see \Venus\System\Theme::buildCssCache()
	* {@inheritDoc}
	*/
	protected function buildCssCache()
	{
		$this->app->cache->css();
	}

	/**
	* @see \Venus\System\Theme::buildJavascriptCache()
	* {@inheritDoc}
	*/
	protected function buildJavascriptCache()
	{
		$this->app->cache->javascript();
	}

	/**
	* @see \venus\Theme::getCssUrl()
	* {@inheritDoc}
	*/
	public function getCssUrl() : string
	{
		$url = $this->app->uri->build($this->app->admin_url_static . VENUS_ASSETS_NAME . 'css.php', ['dateline' => $this->css_dateline]);

		$this->app->plugins->run('adminSystemThemeGetCssUrl', $this, $url);

		return $url;
	}

	/**
	* @see \Mars\Theme::outputHead()
	* {@inheritDoc}
	*/
	public function outputHead()
	{
		$this->outputTitle();
		$this->outputEncoding();
		$this->outputFavicon($this->app->admin_url_rel . 'favicon.png');

		$this->app->plugins->run('adminSystemThemeOutputHead1', $this);

		//output the css files


		$this->app->plugins->run('adminSystemThemeOutputHead2', $this);

		//output the js files
		if ($this->javascript_location == 'head') {
			$this->outputJavascriptUrl();
		}

		$this->outputJavascriptUrls('head');
		die('vcbvc');

		$this->app->plugins->run('adminSystemThemeOutputHead3', $this);

		$this->outputHeadExtra();
	}

	/**
	* Outputs the javascript urls loaded in a position
	* @param string $location The location
	*/
	public function outputJavascriptUrls(string $location)
	{
		if ($this->javascript_merge) {
			$this->app->javascript->outputUrls($this->getJavascriptExternalUrls($location));
		} else {
			$this->app->javascript->output($location);
		}
	}

	/**
	* Outputs the inline javascript code. It will output it only if the javascript_location is not set to 'head'!
	*/
	public function outputJavascriptInline()
	{
		if ($this->javascript_location == 'head') {
			return;
		}
	}

	/**
	* @see \Venus\System\Theme::outputBodyExtra()
	* {@inheritDoc}
	*/
	public function outputBodyExtra()
	{
		parent::outputBodyExtra();

		//save the current tab id
		$this->outputJavascriptCodeStart();

		echo "venus.html.current_tab = '{$this->tab_id}';\n";

		$this->outputJavascriptCodeEnd();
	}

	/**************** OUTPUT HTML *************************************/

	/**
	* @see \Venus\System\Theme::outputMenu()
	* {@inheritDoc}
	*/
	public function outputMenu(string $menu_name = 'main')
	{
		$menu = new Menu;
		$menu->output($menu_name);
	}

	/**
	* Outputs the help link
	*/
	public function outputHelpLink()
	{
		if (!$this->app->help_url) {
			return;
		}

		$html = '<a href="javascript:venus.dialog.open_url(\'' . App::ejs($this->app->help_url) . '\', \'' . App::ejsstr('help') . '\')" data-tooltip="' . App::e(App::estr('tooltip_help')) . '"><img src="' . App::e($this->images_url . 'help_link.png') . '" alt="' . estr('tooltip_help') . '" /></a>';

		return $this->app->plugins->run('adminSystemOutputHelpLink', $html, $this);
	}

	/**
	* Outputs the link in the config area corresponding to the currently loaded block
	*/
	public function outputConfigLink()
	{
		if (!$this->app->config_url) {
			return;
		}

		$html = '<a href="' . App::e($this->app->config_url) . '" data-tooltip="' . App::e(App::estr('tooltip_config')) . '"><img src="' . App::e($this->images_url . 'config_link.png') . '" alt="' . App::estr('tooltip_config') . '" /></a>';

		return $this->app->plugins->run('adminSystemOoutputConfigLink', $html, $this);
	}

	/**
	* Outputs the logout link
	*/
	public function outputLogoutLink()
	{
		$html = '<a href="javascript:venus.logout()" data-tooltip="' . App::e(App::estr('tooltip_logout')) . '" class="logout"><img src="' . App::e($this->images_url . 'logout_link.png') . '" alt="' . App::estr('tooltip_logout') . '" /></a>';

		$html = $this->app->plugins->run('adminSystemoutputLogoutLink', $html, $this);

		$this->outputLogoutForm();

		echo $html;
	}

	/**
	* Returns the logout url
	* @return string
	*/
	protected function getLogoutUrl() : string
	{
		return $this->app->admin_url . 'logout.php';
	}
}
