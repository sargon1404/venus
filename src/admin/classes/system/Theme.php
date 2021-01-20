<?php
/**
* The System's Admin Theme Class
* @package Venus
*/

namespace Venus\Admin\System;

use Mars\Templates;
use Venus\Admin\App;
use Venus\Admin\Menu;

/**
* The System's Admin Theme Class
* The system's admin theme extension
*/
class Theme extends \Venus\Admin\Theme
{
	/**
	* @var string $tab_id The tab id of the currently selected tab
	*/
	public string $tab_id = '1';

	/**
	* Builds the system's theme object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		parent::__construct($app);

		$this->engine = new Templates;

		$this->app->plugins->run('admin_system_theme_construct', $this);
	}

	/**
	* @see \Venus\Theme::prepare()
	* {@inheritdoc}
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareDevelopment();
		$this->prepareProperties();
		$this->prepareForDialog();
		$this->prepareVars();
		$this->prepareLibraries();
		$this->prepareMainUrls();
		$this->prepareTemplates();
		$this->prepareTab();

		//include the init.php file if one exists
		if (is_file($this->dir . $this->init_file)) {
			include($this->dir . $this->init_file);
		}

		$this->cleanup();
	}

	/**
	* @see \Venus\System\Theme::prepareJquery()
	* {@inheritdoc}
	*/
	protected function prepareJquery()
	{
		$this->app->javascript->loadLibrary('jquery');
		//$this->app->javascript->loadLibrary('jquery-ui-admin');
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
	* {@inheritdoc}
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

		$this->app->plugins->run('admin_system_theme_get_banner_positions', $this, $positions);

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

		$this->app->plugins->run('admin_system_theme_get_widget_positions', $this, $positions);

		return $positions;
	}

	/**************** OUTPUT *************************************/

	/**
	* @see \Venus\System\Theme::getTitle()
	* {@inheritdoc}
	*/
	protected function getTitle() : string
	{
		$title = $this->app->title->get();

		return $this->app->plugins->filter('admin_system_theme_get_title', $title, $this);
	}

	/**************** CSS & JAVASCRIPT *************************************/

	/**
	* @see \Mars\Theme::outputHead()
	* {@inheritdoc}
	*/
	public function outputHead()
	{
		$this->outputTitle();
		$this->outputEncoding();
		$this->outputFavicon($this->app->admin_url . 'favicon.png');

		$this->outputCssUrls('head');
		$this->outputJavascriptUrls('head');
		$this->outputJavascriptInHeader();

		$this->outputHeadExtra();

		$this->app->plugins->run('admin_system_theme_output_head', $this);
	}

	/**
	* Outputs the javascript init code
	*/
	protected function outputJavascriptInit()
	{
		echo 'venus.init();' . "\n";
		echo 'venus.initAdmin();' . "\n";

		$this->app->plugins->run('admin_system_theme_output_javascript_init', $this);
	}

	/**
	* @see \Venus\System\Theme::ouputJavascriptConfig()
	* {@inheritdoc}
	*/
	protected function ouputJavascriptConfig()
	{
		parent::ouputJavascriptConfig();

		echo "venus.html.current_tab = '{$this->tab_id}';\n";
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

	/**************** OUTPUT HTML *************************************/

	/**
	* @see \Venus\System\Theme::outputMenu()
	* {@inheritdoc}
	*/
	public function outputMenu(string $menu_name = 'main')
	{
		$menu = new Menu($menu_name);
		$menu->output();
	}

	/**
	* Outputs the help link
	*/
	public function outputHelpLink()
	{
		if (!$this->app->help_url) {
			return;
		}

		$html = '<a href="javascript:venus.dialog.open_url(\'' . App::ejs($this->app->help_url) . '\', \'' . App::ejsstr('help') . '\')" data-tooltip="' . App::e(App::estr('tooltip_help')) . '"><img src="' . App::e($this->images_url . 'help-link.png') . '" alt="' . estr('tooltip_help') . '" /></a>';

		return $this->app->plugins->run('admin_system_theme_output_help_link', $html, $this);
	}

	/**
	* Outputs the link in the config area corresponding to the currently loaded block
	*/
	public function outputConfigLink()
	{
		if (!$this->app->config_url) {
			return;
		}

		$html = '<a href="' . App::e($this->app->config_url) . '" data-tooltip="' . App::e(App::estr('tooltip_config')) . '"><img src="' . App::e($this->images_url . 'config-link.png') . '" alt="' . App::estr('tooltip_config') . '" /></a>';

		return $this->app->plugins->run('admin_system_theme_output_config_link', $html, $this);
	}

	/**
	* Outputs the logout link
	*/
	public function outputLogoutLink()
	{
		$html = '<a href="javascript:venus.logout()" data-tooltip="' . App::e(App::estr('tooltip_logout')) . '" class="logout"><img src="' . App::e($this->images_url . 'logout-link.png') . '" alt="' . App::estr('tooltip_logout') . '" /></a>';

		$html = $this->app->plugins->filter('admin_system_theme_output_logout_link', $html, $this);

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
