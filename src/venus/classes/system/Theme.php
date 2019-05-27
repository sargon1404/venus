<?php
/**
* The System's Theme Class
* @package Venus
*/

namespace Venus\System;

use Venus\App;
use Venus\Output\Menu;
use Venus\Output\Announcements;
use Venus\Output\Breadcrumbs;
use Venus\Output\Banners;

/**
* The System's Theme Class
*/
class Theme extends \Venus\Theme
{
	/**
	* @var bool $is_homepage Set to true if the homepage is currently displayed
	*/
	public $is_homepage = false;

	/**
	* @var string $admin_url Alias for $this->app->admin_url
	*/
	public $admin_url = '';

	/**
	* @var string $tab_id The id of the currently selected tab (ui element)
	*/
	public $tab_id = 1;

	/**
	* @var bool $css_merge If true, the css stylesheets will be merged into one file
	*/
	public $css_merge = true;

	/**
	* @var string $css_location The location where the merged css stylesheets will be outputted in the document [head|footer]
	*/
	public $css_location = '';

	/**
	* @var bool $javascript_merge If true, the js scripts will be merged into one file
	*/
	public $javascript_merge = true;

	/**
	* @var string $javascript_location The location where the merged js scripts will be outputted in the document [head|footer]
	*/
	public $javascript_location = '';

	/**
	* @var string $javascript_priority The priority of the main js file or the merged file, if javascript_merge = true
	*/
	public $javascript_priority = 50000;

	/**
	* @var string $theme_javascript_location The location where theme's javascript will be outputted in the document [head|footer]
	*/
	public $theme_javascript_location = 'head';

	/**
	* @var string $javascript_priority The priority of theme's javascript file
	*/
	public $theme_javascript_priority = 5000;

	/**
	* @var array $libraries The javascript libraries loaded by this theme
	*/
	protected $libraries = [];

	/**
	* @var array $css_urls The list of local/external css stylesheets to be loaded, if css_merge is true
	*/
	protected $css_urls = null;

	/**
	* @var array $javascript_urls The list of local/external js scripts to be loaded, if javascript_merge is true
	*/
	protected $javascript_urls = null;

	/**
	* @var bool $init Will include the theme's init file if $init is set to true
	*/
	protected $init = false;

	/**
	* @var string $init_file The name of the init file
	*/
	protected $init_file = 'javascript.js';

	/**
	* Builds the system's theme object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$theme = $this->get();

		parent::__construct($theme);

		$this->app->plugins->run('systemThemeConstruct', $this, $theme);
	}

	/**
	* Returns the data of a theme
	* @return object The theme
	*/
	protected function get()
	{
		$tid = $this->getTid();

		if ($tid == $this->app->config->theme_default) {
			return $this->getDefault();
		} else {
			$theme = $this->getRow($tid);
			if (!$theme) {
				$theme = $this->getDefault();
			}

			return $theme;
		}
	}

	/**
	* Returns the id of the theme the user is using.
	* @return int
	*/
	protected function getTid() : int
	{
		if ($this->app->user->uid && $this->app->user->theme) {
			return (int)$this->app->user->theme;
		}

		return (int)$this->app->config->theme_default;
	}

	/**
	* Returns the data of the default theme
	* @return object The default theme
	*/
	public function getDefault() : object
	{
		static $default_theme = null;
		if ($default_theme === null) {
			$default_theme = $this->app->cache->get('theme_default', true);
		}

		return $default_theme;
	}

	/**************PREPARE METHODS**************************/

	/**
	* @see \Venus\Theme::prepare()
	* {@inheritDoc}
	*/
	protected function prepare()
	{
		parent::prepare();

		$this->prepareProperties();
		$this->prepareForDialog();

		$this->prepareVars();
		$this->prepareLibraries();
		$this->prepareMainUrls();

		//include the init.php file if one exists
		if ($this->init) {
			include($this->dir . $this->init_file);
		}
	}

	/**
	* @see \Venus\Theme::prepareParams()
	* {@inheritDoc}
	*/
	protected function prepareParams()
	{
		parent::prepareParams();

		//unset the params_data and parent_params_data arrays to free some ram
		unset($this->params_data);
		unset($this->parent_params_data);
	}

	/**
	* @see \Mars\Theme::prepareProperties()
	* {@inheritDoc}
	*/
	protected function prepareProperties()
	{
		$this->css_dateline = $this->app->cache->css_dateline;
		$this->css_merge = $this->params->css_merge ?? $this->app->config->css_merge;
		$this->css_location = $this->params->css_location ?? $this->app->config->css_location;

		$this->javascript_dateline = $this->app->cache->javascript_dateline;
		$this->javascript_merge = $this->params->javascript_merge ?? $this->app->config->javascript_merge;
		$this->javascript_location = $this->params->javascript_location ?? $this->app->config->javascript_location;

		//don't merge css/js files in development mode
		$this->development = false;
		$this->javascript_merge = false;
		var_dump("remove development");
		if ($this->development) {
			$this->css_dateline = time();
			$this->css_merge = false;

			$this->javascript_dateline = time();
			$this->javascript_merge = false;
		}
	}

	/**
	* Sets the header/footer/content templates to the dialog versions
	*/
	public function prepareForDialog()
	{
		if ($this->app->type != 'dialog') {
			return;
		}

		$this->header_template = 'dialogs/header';
		$this->content_template = 'dialogs/content';
		$this->footer_template = 'dialogs/footer';
	}

	/**
	* @see \Mars\Theme::prepareVars()
	* {@inheritDoc}
	*/
	protected function prepareVars()
	{
		parent::prepareVars();

		$this->site_index = $this->app->site_index;
		$this->admin_url = $this->app->admin_url;

		$this->addVar('admin_url', $this->admin_url);
		$this->addVar('params', $this->params);
		$this->addVar('user', $this->app->user);
		$this->addVar('usergroup', $this->app->user->usergroup);
	}

	/**
	* Prepare the libraries the theme is using
	*/
	protected function prepareLibraries()
	{
		$this->prepareJquery();

		//load the libraries this theme is using
		$this->libraries = App::unserialize($this->libraries);

		foreach ($this->libraries as $library) {
			$this->app->library->loadJavascript($library);
		}
	}

	/**
	* Prepares jquery for loading
	*/
	protected function prepareJquery()
	{
		$this->app->library->loadJavascript('jquery');
	}

	/**
	* Loads the main js/css urls
	*/
	protected function prepareMainUrls()
	{
		//javascript
		$this->app->javascript->load($this->app->cache->getJavascriptUrl($this->app->device->type, $this->app->lang->name), $this->javascript_location, $this->javascript_priority);
		$this->app->javascript->load($this->app->cache->getThemeJavascriptUrl($this->name, $this->app->device->type), $this->theme_javascript_location, $this->theme_javascript_priority);
	}

	/**************TEMPLATES METHODS**************************/

	/**
	* Finds the filename of a template.
	* @param string $filename The filename
	* @return string The filename
	*/
	protected function findTemplateFilename(string $filename) : string
	{
		if ($this->templateExists($filename)) {
			return $this->templates_dir . $filename . '.' . App::FILE_EXTENSIONS['templates'];
		}

		if ($this->parent) {
			if ($this->parentTemplateExists($filename)) {
				return $this->parent_templates_dir . $filename . '.' . App::FILE_EXTENSIONS['templates'];
			}
		}

		return '';
	}

	/**
	* @see \Mars\Theme::getTemplateFilename()
	* {@inheritDoc}
	*/
	public function getTemplateFilename(string $template) : string
	{
		if ($this->layout) {
			$layout = App::EXTENSIONS_DIRS['layouts'] . App::sl($this->layout);

			if ($this->app->device->isMobile()) {
				$filename = $this->findTemplateFilename($layout . $this->app->device->getSubdir() . $template);
				if ($filename) {
					return $filename;
				}

				//check in the mobile folder
				$filename = $this->findTemplateFilename($layout . $this->app->device->getSubdir('mobile') . $template);
				if ($filename) {
					return $filename;
				}
			}

			$filename = $this->findTemplateFilename($layout . $template);
			if ($filename) {
				return $filename;
			}
		}

		if ($this->app->device->isMobile()) {
			//do we have the template in the tables/smartphones dir?
			$filename = $this->findTemplateFilename($this->app->device->getSubdir() . $template);
			if ($filename) {
				return $filename;
			}

			//check in the mobile folder
			$filename = $this->findTemplateFilename($this->app->device->getSubdir('mobile') . $template);
			if ($filename) {
				return $filename;
			}
		}

		$filename = $this->findTemplateFilename($template);
		if ($filename) {
			return $filename;
		}

		return $this->templates_dir . $filename;
	}

	/**
	* @see \Mars\Theme::getTemplateFromFilename()
	* {@inheritDoc}
	*/
	public function getTemplateFromFilename(string $filename, bool $debug = false) : string
	{
		$content = parent::getTemplateFromFilename($filename, $debug);

		$this->app->plugins->run('systemThemeGetTemplateFromFilename', $this, $content, $filename, $debug);

		return $content;
	}

	/**
	* Loads the template of an extension
	* @param string $filename The filename of the template
	* @param string $cache_filename The filename of file where the templates will be cached
	* @param bool $debug if true,the extension is run in debug mode
	* @return string The template's content
	*/
	public function getExtensionTemplate(string $filename, string $cache_filename, bool $debug = false) : string
	{
		if ($this->app->config->debug) {
			$this->templates_loaded[] = $this->app->file->getRel($filename, false);
		}

		$content = $this->getTemplateContent($filename, $cache_filename, $debug);

		$this->app->plugins->run('systemThemeGetExtensionTemplate', $this, $content, $filename, $cache_filename, $debug);

		return $content;
	}

	/**
	* Returns the filename of an extension's template
	* @param string $dir The folder where the templates for this extension are located
	* @param string $name The extension's name
	* @param string $layout The template's layout, if any
	* @param string $template The template's path
	* @param string $device The used device
	* @return string The template's filename
	*/
	public function getExtensionTemplateFilename(string $dir, string $name, string $layout, string $template, string $device = '') : string
	{
		var_dump("aici");
		die;
		$device_dir = $this->app->device->getSubdir($device);

		if ($layout) {
			return $this->getTemplateAbsoluteFilename(VENUS_THEMES_TEMPLATES_LAYOUTS_DIR . sl($layout) . VENUS_THEMES_TEMPLATES_EXTENSIONS_DIR . $dir . $name . $device_dir . $template);
		} else {
			return $this->getTemplateAbsoluteFilename(VENUS_THEMES_TEMPLATES_EXTENSIONS_DIR . $dir . $name . $device_dir . $template);
		}
	}

	/**
	* @see \Mars\Theme::includeTemplate()
	* {@inheritDoc}
	*/
	protected function includeTemplate(string $filename) : string
	{
		$app = $this->app;
		$strings = &$this->app->lang->strings;
		$vars = &$this->vars;
		$lang = $this->app->lang;
		$html = $this->app->html;
		$escape = $this->app->escape;
		$ui = $this->app->ui;
		$uri = $this->app->uri;
		$user = $this->app->user;
		$usergroup = $this->app->user->usergroup;

		ob_start();

		include($filename);

		return ob_get_clean();
	}

	/**************** OUTPUT HTML *************************************/

	/**
	* Returns the title of the page
	* @return string The title
	*/
	protected function getTitle() : string
	{
		$title = str_ireplace('{TITLE}', $this->app->title->get(), $this->app->config->title);

		$this->app->plugins->run('systemThemeGetTitle', $this, $title);

		return $title;
	}

	/**
	* Outputs the menu
	* @param string $name The name of the menu to output
	*/
	public function outputMenu(string $name = 'main')
	{
		if (!$this->app->show_menu || !$this->app->cache->menu_count || !$this->app->cache->menu_entries_count) {
			return;
		}

		$menu = new Menu;
		$menu->output($name);
	}

	/**
	* Outputs the breadcrumbs
	*/
	public function outputBreadcrumbs()
	{
		if (!$this->app->show_breadcrumbs || !$this->app->config->breadcrumbs_show) {
			return;
		}

		$breadcrumbs = new Breadcrumbs;
		$breadcrumbs->output();
	}

	/**
	* Outputs the announcements
	*/
	public function outputAnnouncements()
	{
		if (!$this->app->show_announcements || !$this->app->cache->announcements_count || !$this->app->config->announcements_show) {
			return;
		}

		$announcements = new Announcements;
		$announcements->output();
	}

	/**
	* Outputs the banners associated with a banner position
	* @param string $position The position for which to output the banners
	*/
	public function outputBanners(string $position)
	{
		if (!$this->app->show_banners || !$this->app->cache->banners_count || !$this->app->config->banners_show) {
			return;
		}

		$banners = new Banners;
		$banners->output($position);
	}

	/**
	* Outputs all the widgets associated with a widget position
	* @param string $position The position for which to output the widgets
	*/
	public function outputWidgets(string $position)
	{
		var_dump("output widgets");
		die;
		if (!$this->app->show_widgets ||  !$this->app->config->widgets_enable || defined('DISABLE_WIDGETS') || !$this->app->cache->widgets_count) {
			return;
		}

		$widgets = new Widgets;
		$widgets->output($position);
	}

	/**
	* Outputs a single widget
	* @param int $wid The widget's id or internal name
	*/
	public function outputWidget(int $wid)
	{
		if (!$this->app->show_widgets || !$this->app->config->widgets_enable || defined('DISABLE_WIDGETS') || !$this->app->cache->widgets_count) {
			return;
		}

		$widgets = new Widgets;
		$widgets->outputWidget($wid);
	}

	/****************OUTPUT URLS*************************************/

	/**
	* Returns true if a block is installed and enabled
	* @param string $name The name of the block
	* @return bool Returns true if the block is installed & enabled
	*/
	public function isBlock(string $name) : bool
	{
		return $this->app->env->isBlock($name);
	}

	/**
	* Outputs the url of a category
	* @param mixed $category Either the category's id or the category's data (array, object).
	* @param int $page_no The category's page number
	*/
	public function outputCategoryUrl($category, int $page_no = 0)
	{
		echo App::e($this->app->uri->getCategory($category, $page_no));
	}

	/**
	* Outputs the url of a block
	* @param mixed $block The block's id (int) or the block's name (string) or the block's data (array,object).
	* @param int $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @param array $seo_extra Extra seo parts to be inclued in the url
	* @param int $page_no The page number, if any
	*/
	public function outputBlockUrl($block, string $action = '', array $params = [], array $seo_extra = [], int $page_no = 0)
	{
		echo App::e($this->app->uri->getBlock($block, $action, $params, $seo_extra, $page_no));
	}

	/**
	* Outputs the url of a page
	* @param mixed $page The page's id (int) or the page's data (array, object)
	* @param int $page_no The page's page number
	*/
	public function outputPageUrl($page, int $page_no = 0)
	{
		echo App::e($this->app->uri->getPage($page, $page_no));
	}

	/**
	* Outputs the register url
	*/
	public function outputRegisterUrl()
	{
		echo App::e($this->app->uri->getRegister());
	}

	/**
	* Outputs the login url
	*/
	public function outputLoginUrl()
	{
		echo App::e($this->app->uri->getLogin());
	}

	/**
	* Outputs the logout form
	*/
	public function outputLogoutForm()
	{
		echo $this->app->html->formStart($this->app->uri->getLogout(), 'logout-form');
		$this->app->html->token();
		echo $this->app->html->formEnd();
	}

	/**
	* Outputs the current user's private messages url
	*/
	public function outputPrivateMessagesUrl()
	{
		echo App::e($this->app->uri->getPrivateMessages());
	}

	/**
	* Outputs the current user's control panel url
	*/
	public function outputControlPanelUrl()
	{
		echo App::e($this->app->uri->getControlPanel());
	}

	/**
	* Outputs the current user's profile url
	*/
	public function outputProfileUrl()
	{
		echo App::e($this->app->uri->getProfile());
	}

	/**************** CSS & JAVASCRIPT *************************************/

	/**
	* Returns the list of local css urls
	* @param string $location The location to return the urls for. If empty all urls are returned
	* @return array
	*/
	protected function getCssLocalUrls(string $location = '') : array
	{
		if ($this->css_urls === null) {
			$this->css_urls = $this->app->css->getSplitUrls();
		}

		return $this->getUrls($this->css_urls, 'local', $location);
	}

	/**
	* Returns the list of css javascript urls
	* @param string $location The location to return the urls for. If empty all urls are returned
	* @return array
	*/
	protected function getCssExternalUrls(string $location = '') : array
	{
		if ($this->css_urls === null) {
			$this->css_urls = $this->app->css->getSplitUrls();
		}

		return $this->getUrls($this->css_urls, 'external', $location);
	}

	/**
	* Returns the list of local javascript urls
	* @param string $location The location to return the urls for. If empty all urls are returned
	* @return array
	*/
	protected function getJavascriptLocalUrls(string $location = '') : array
	{
		if ($this->javascript_urls === null) {
			$this->javascript_urls = $this->app->javascript->getSplitUrls();
		}

		return $this->getUrls($this->javascript_urls, 'local', $location);
	}

	/**
	* Returns the list of external javascript urls
	* @param string $location The location to return the urls for. If empty all urls are returned
	* @return array
	*/
	protected function getJavascriptExternalUrls(string $location = '') : array
	{
		if ($this->javascript_urls === null) {
			$this->javascript_urls = $this->app->javascript->getSplitUrls();
		}

		return $this->getUrls($this->javascript_urls, 'external', $location);
	}

	/**
	* Returns urls of a certain type, from a certain location
	* @param array $urls The urls
	* @param string $type The urls type
	* @param string $location The location to return the urls for. If empty all urls are returned
	* @return array
	*/
	protected function getUrls(array $urls, string $type, string $location = '') : array
	{
		if ($location) {
			return $urls[$type][$location];
		} else {
			return $urls[$type];
		}
	}

	/**
	* Builds the css caches files, if in development mode
	*/
	protected function buildCssCache()
	{
		$this->app->cache->cssFrontend();
	}

	/**
	* Builds the javascript caches files, if in development mode
	*/
	protected function buildJavascriptCache()
	{
		$this->app->cache->javascriptFrontend();
	}

	/**
	* Returns the url of the main javascript file
	* @return string
	*/
	public function outputJavascriptUrl()
	{
		//rebuild the javascript cache in development mode
		if ($this->development) {
			$this->buildJavascriptCache();
		}
		var_dump($this->javascript_merge);
		die;
		$file = '';
		if ($this->javascript_merge) {
			$libraries = array_keys($this->app->library->getJavascript());
			$library_dependencies = array_keys($this->app->library->getJavascriptDependencies());
			$local_urls = array_keys(App::arrayMerge($this->getJavascriptLocalUrls()));

			$file = $this->app->cache->getJavascriptMergedFile($this->app->device->type, $this->app->lang->name, $libraries, $library_dependencies, $local_urls);
		} else {
			$file = $this->app->cache->getJavascriptFile($this->app->device->type, $this->app->lang->name);
		}

		$url = $this->app->uri->build($this->cache_url . 'javascript/' . $file, ['dateline' => $this->javascript_dateline]);

		$this->app->javascript->outputUrl($url);
	}

	/**
	* @see \Mars\Theme::outputCssMain()
	* {@inheritDoc}
	*/
	/*public function outputCssMain()
	{
		$this->outputCssUrl($this->getCssUrl());
	}*/

	/**
	* @see \Mars\Theme::getCssUrl()
	* {@inheritDoc}
	*/
	/*function getCssUrl() : string
	{
		global $venus;
		$url = $this->app->uri->build($this->app->site_url_static . VENUS_ASSETS_NAME . 'css.php', ['theme' => $this->name, 'dateline' => $this->css_dateline]);

		$this->app->plugins->run('systemThemeGetCssUrl', $this, $url);

		return $url;
	}*/

	/**
	* @see \Mars\Theme::getJavascriptUrl()
	* {@inheritDoc}
	*/
	/*protected function getJavascriptUrl() : string
	{
		global $venus;
		$url = $this->app->uri->build($this->app->site_url_static . VENUS_ASSETS_NAME . 'javascript.php', ['lang' => $this->app->lang->name, 'theme' => $this->name, 'dateline' => $this->js_dateline]);

		$this->app->plugins->run('systemThemeGetJsUrl', $this, $url);

		return $url;
	}*/

	/**************** OUTPUT *************************************/

	/**
	* @see \Mars\Theme::outputHead()
	* {@inheritDoc}
	*/
	public function outputHead()
	{
		$this->outputTitle();
		$this->outputEncoding();

		$this->app->plugins->run('systemThemeOutputHead1', $this);

		$this->outputCssMain();
		$this->outputCssUrls('head');

		$this->outputJavascriptMain();

		$this->outputJavascriptLibraries('head');
		$this->outputJavascriptUrls('head');

		$this->outputMeta();
		$this->outputRss();

		if ($this->app->device->isTablet()) {
			$this->outputTabletsExtra();
		} elseif ($this->app->device->isSmartphone()) {
			$this->outputSmartphonesExtra();
		} else {
			$this->outputDesktopExtra();
		}

		$this->app->plugins->run('systemThemeOutputHead2', $this);

		$this->outputHeadExtra();
	}

	/**
	* @see \Mars\Theme::outputFooter()
	* {@inheritDoc}
	*/
	public function outputFooter()
	{
		$this->outputCssUrls('footer');

		$this->outputJavascriptLibraries('footer_first');
		$this->outputJavascriptUrls('footer_first');

		$this->outputJavascriptLibraries('footer');
		$this->outputJavascriptUrls('footer');
	}

	/**
	* Outputs the loaded css libraries
	* @param string $location The location of the urls: first|head|footer
	*/
	/*public function outputCssLibraries(string $location = 'head')
	{
		global $venus;
		$libraries = $this->app->library->css->getLibraries($location);
		$dependencies = $this->app->library->javascript->getDependencies($location);
		if(!$libraries && !$dependencies)
			return;

		$url = $this->app->uri->build($this->app->site_url_static . VENUS_ASSETS_NAME . 'css.php', ['type' => 'libraries', 'names' => $libraries, 'dependencies' => $dependencies , 'dateline' => $this->css_dateline]);

		$this->app->css->outputUrl($url);
	}*/

	/**
	* Outputs the loaded javascript libraries
	* @param string $location The location of the urls: first|head|footer
	*/
	/*public function outputJavascriptLibraries(string $location = 'head')
	{
		global $venus;
		$libraries = $this->app->library->javascript->getLibraries($location);
		$dependencies = $this->app->library->css->getDependencies($location);
		if(!$libraries && !$dependencies)
			return;

		$url = $this->app->uri->build($this->app->site_url_static . VENUS_ASSETS_NAME . 'javascript.php', ['type' => 'libraries', 'names' => $libraries, 'dependencies' => $dependencies , 'dateline' => $this->js_dateline]);

		$this->app->javascript->outputUrl($url);
	}*/

	/**
	* Outputs extra code in the head, for desktop devices
	*/
	public function outputDesktopExtra()
	{
		$this->app->plugins->run('systemThemeOutputDesktopExtra', $this);
	}

	/**
	* Outputs extra code in the head, for tablet devices
	*/
	public function outputTabletsExtra()
	{
		$this->outputViewport();

		$this->app->plugins->run('systemThemeOutputTabletsExtra', $this);
	}

	/**
	* Outputs extra code in the head, for smartphones devices
	*/
	public function outputSmartphonesExtra()
	{
		$this->outputViewport();

		$this->app->plugins->run('systemThemeOutputSmartphonesExtra', $this);
	}

	/**
	* Outputs the meta viewport tag, if the theme's params allow it
	*/
	public function outputViewport()
	{
		if (!$this->params->viewport_output) {
			return;
		}

		echo '<meta name="viewport" content="width=' . App::e($this->params->viewport_width) . ', initial-scale=' . App::e($this->params->viewport_initial_scale) . '">' . "\n";

		$this->app->plugins->run('systemThemeOutputViewport', $this);
	}

	/**
	* Outputs the extra head code
	*/
	public function outputHeadExtra()
	{
		$this->outputJavascriptCode($this->app->extra_javascript['head']);
		$this->outputCssCode($this->app->extra_css['head']);

		echo $this->app->extra_html['head'];

		$this->app->plugins->run('systemThemeOutputHeadExtra', $this);
	}

	/**
	* Outputs the extra body code
	*/
	public function outputBodyExtra()
	{
		$this->outputJavascriptCodeStart();

		echo 'venus.device = \'' . App::ejs($this->app->device->type) . '\';' . "\n";

		if ($this->app->user->editor != 'bbcode') {
			echo 'venus.editor.type = \'' . App::ejs($this->app->user->editor) . '\';' . "\n";
		}

		$this->outputJavascriptCodeEnd();

		$this->outputJavascriptCode($this->app->extra_javascript['body']);

		echo $this->app->extra_html['body'];

		$this->app->plugins->run('systemThemeOutputBodyExtra', $this);
	}

	/**
	* Outputs the extra footer code
	*/
	public function outputFooterExtra()
	{
		$this->outputJavascriptCode($this->app->extra_javascript['footer']);

		echo $this->app->extra_html['footer'];

		$this->app->plugins->run('systemThemeOutputFooterExtra', $this);
	}

	/**
	* Outputs the dialogs content
	*/
	public function outputDialogs()
	{
		echo $this->app->dialogs->content;
	}

	/****************TABS*************************************/

	/**
	* Returns the currently selected tab id
	* @return int
	*/
	public function getTab() : int
	{
		return $this->tab_id;
	}

	/**
	* Sets the current tab id
	* @param int $tab_id The tab id
	* @return $this
	*/
	public function setTab(int $tab_id)
	{
		$this->tab_id = $tab_id;

		return $this;
	}
}
