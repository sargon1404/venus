<?php
/**
* The System's Theme Class
* @package Venus
*/

namespace Venus\System;

use Mars\Templates;
use Venus\App;
use Venus\Menu;
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
	public bool $is_homepage = false;

	/**
	* @var string $admin_url Alias for $this->app->admin_url
	*/
	public string $admin_url = '';

	/**
	* @var string $tab_id The id of the currently selected tab (ui element)
	*/
	public string $tab_id = '1';

	/**
	* @var bool $css_merge If true, the css stylesheets will be merged into one file
	*/
	public bool $css_merge = true;

	/**
	* @var string $css_location The location where the merged css stylesheets will be outputted in the document [head|footer]
	*/
	public string $css_location = '';

	/**
	* @var bool $javascript_merge If true, the js scripts will be merged into one file
	*/
	public bool $javascript_merge = true;

	/**
	* @var string $javascript_location The location where the merged js scripts will be outputted in the document [head|footer]
	*/
	public string $javascript_location = '';

	/**
	* @var string|array $libraries The javascript libraries loaded by this theme
	*/
	protected string|array $libraries = '';

	/**
	* @var bool $init Will include the theme's init file if $init is set to true
	*/
	protected bool $init = false;

	/**
	* @var string $init_file The name of the init file
	*/
	protected string $init_file = 'javascript.js';

	/**
	* Builds the system's theme object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->engine = new Templates;

		$theme = $this->get();

		parent::__construct($theme);

		$this->app->plugins->run('system_theme_construct', $this, $theme);
	}

	/**
	* Returns the data of a theme
	* @return object The theme
	*/
	protected function get()
	{
		$tid = $this->getThemeId();

		if ($tid == $this->app->config->theme_default) {
			return $this->getDefault();
		} else {
			return $this->getRow($tid);
		}
	}

	/**
	* Returns the id of the theme the user is using.
	* @return int
	*/
	protected function getThemeId() : int
	{
		if ($this->app->user->id && $this->app->user->theme_id) {
			return $this->app->user->theme_id;
		}

		return $this->app->config->theme_default;
	}

	/**
	* Returns the data of the default theme
	* @return object The default theme
	*/
	public function getDefault() : object
	{
		static $default_theme = null;
		if ($default_theme === null) {
			$default_theme = $this->app->cache->get('theme_default');
		}

		return $default_theme;
	}

	/**************PREPARE METHODS**************************/

	/**
	* @see \Venus\Theme::prepare()
	* {@inheritdoc}
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
			include($this->path . $this->init_file);
		}

		$this->cleanup();
	}

	/**
	* @see \Mars\Extensions\Body::prepareDevelopment()
	* {@inheritdoc}
	*/
	protected function prepareDevelopment()
	{
		parent::prepareDevelopment();

		if (!$this->app->is_cli) {
			if ($this->app->config->development) {
				//rebuild the javascript code if the site is running in development mode
				$this->app->cache->buildMainJavascript();
			}

			if ($this->development) {
				//rebuild the theme's css and js cache
				$this->app->cache->buildForTheme($this);
			}
		}
	}

	/**
	* @see \Mars\Theme::prepareProperties()
	* {@inheritdoc}
	*/
	protected function prepareProperties()
	{
		$this->css_merge = $this->params->css_merge ?? $this->app->config->css_merge;
		$this->css_location = $this->params->css_location ?? $this->app->config->css_location;

		$this->javascript_merge = $this->params->javascript_merge ?? $this->app->config->javascript_merge;
		$this->javascript_location = $this->params->javascript_location ?? $this->app->config->javascript_location;

		//don't merge css/js files in development mode
		if ($this->development) {
			$this->css_merge = false;
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
	* {@inheritdoc}
	*/
	protected function prepareVars()
	{
		parent::prepareVars();

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
		$this->libraries = $this->app->serializer->unserialize($this->libraries);

		foreach ($this->libraries as $library) {
			$this->app->javascript->loadLibrary($library);
		}
	}

	/**
	* Prepares jquery for loading
	*/
	protected function prepareJquery()
	{
		$this->app->javascript->loadLibrary('jquery');
	}

	/**
	* Loads the main js/css urls
	*/
	protected function prepareMainUrls()
	{
		App::pp($this->app->javascript);
		die;
		//load the main and theme's js code
		$this->app->javascript->loadMain($this->javascript_location, 50000);
		//$this->app->javascript->loadProperties($this->javascript_location, 49000);
		//$this->app->javascript->loadLanguageStrings($this->javascript_location, 48000);
		$this->app->javascript->loadTheme($this->name, $this->javascript_location, 47000, $this->development ? time() : true);

		//load the theme's css code
		$this->app->css->loadMain($this->name);
	}

	/**
	* Cleans some unused properties
	*/
	protected function cleanup()
	{
		//unset the params_data and parent_params_data arrays to free some ram
		unset($this->params_data);
		unset($this->parent_params_data);
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
			return $this->templates_path . $filename . '.' . App::FILE_EXTENSIONS['templates'];
		}

		if ($this->parent) {
			if ($this->parentTemplateExists($filename)) {
				return $this->parent_templates_path . $filename . '.' . App::FILE_EXTENSIONS['templates'];
			}
		}

		return '';
	}

	/**
	* @see \Mars\Theme::getTemplateFilename()
	* {@inheritdoc}
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
		} else {
			throw new \Exception("Template {$template} not found");
		}

		return $this->templates_path . $filename;
	}

	/**
	* @see \Mars\Theme::getTemplateFromFilename()
	* {@inheritdoc}
	*/
	public function getTemplateFromFilename(string $filename, bool $debug = false) : string
	{
		$content = parent::getTemplateFromFilename($filename, $debug);

		$this->app->plugins->run('system_theme_get_template_from_filename', $this, $content, $filename, $debug);

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

		$this->app->plugins->run('system_theme_get_extension_template', $this, $content, $filename, $cache_filename, $debug);

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
		$device_dir = $this->app->device->getSubdir($device);

		if ($layout) {
			$filename = $this->findTemplateFilename(App::EXTENSIONS_DIRS['layouts'] . App::sl($layout) . App::DIRS['extensions'] . '/' . $dir . $name . $device_dir . $template);
			if ($filename) {
				return $filename;
			}
		}

		return $this->findTemplateFilename(App::DIRS['extensions'] . '/' . $dir . $name . $device_dir . $template);
	}

	/**
	* @see \Mars\Theme::includeTemplate()
	* {@inheritdoc}
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

		$this->app->plugins->run('system_theme_get_title', $this, $title);

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

		$menu = new Menu($this->app);
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

		$breadcrumbs = new Breadcrumbs($this->app);
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

		$announcements = new Announcements($this->app);
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

		$banners = new Banners($this->app);
		$banners->output($position);
	}

	/**
	* Outputs all the widgets associated with a widget position
	* @param string $position The position for which to output the widgets
	*/
	public function outputWidgets(string $position)
	{
		return;
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
	* @param int|array|object $category Either the category's id or the category's data (array/object).
	* @param int $page_no The category's page number
	*/
	public function outputCategoryUrl(int|array|object $category, int $page_no = 0)
	{
		echo App::e($this->app->uri->getCategory($category, $page_no));
	}

	/**
	* Outputs the url of a block
	* @param int|string|array|object $block The block's id (int) or the block's name (string) or the block's data (array/object).
	* @param int $action The action to perform, if any
	* @param array $params Array containing the params to append to the url, if any, specified as name => value
	* @param array $seo_extra Extra seo parts to be inclued in the url
	* @param int $page_no The page number, if any
	*/
	public function outputBlockUrl(int|string|array|object $block, string $action = '', array $params = [], array $seo_extra = [], int $page_no = 0)
	{
		echo App::e($this->app->uri->getBlock($block, $action, $params, $seo_extra, $page_no));
	}

	/**
	* Outputs the url of a page
	* @param int|array|object $page The page's id (int) or the page's data (array/object)
	* @param int $page_no The page's page number
	*/
	public function outputPageUrl(int|array|object $page, int $page_no = 0)
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
		echo $this->app->html->formOpen($this->app->uri->getLogout(), ['id' => 'logout-form']);
		$this->app->html->token();
		echo $this->app->html->formClose();
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

	/**************** OUTPUT *************************************/

	/**
	* @see \Mars\Theme::outputHead()
	* {@inheritdoc}
	*/
	public function outputHead()
	{
		$this->outputTitle();
		$this->outputEncoding();
		$this->outputMeta();
		$this->outputRss();

		$this->outputCssUrls('head');
		$this->outputJavascriptUrls('head');
		$this->outputJavascriptInHeader();

		$this->outputHeadExtra();

		if ($this->app->device->isTablet()) {
			$this->outputTabletsExtra();
		} elseif ($this->app->device->isSmartphone()) {
			$this->outputSmartphonesExtra();
		} else {
			$this->outputDesktopExtra();
		}

		$this->app->plugins->run('system_theme_output_head', $this);
	}

	/**
	* Outputs the javascript urls loaded in a certain location
	* @param string $location The location
	*/
	public function outputCssUrls(string $location)
	{
		if ($this->css_merge && $location == $this->css_location) {
			$splitter = new \Venus\Assets\Splitter($this->app->css->getUrls($location));

			$this->app->css->outputUrls($splitter->getExternalUrls());
			$this->app->css->outputMergedUrls($splitter->getLocalUrls());
		} else {
			$this->app->css->output($location);
		}
	}

	/**
	* Outputs the javascript urls loaded in a certain location
	* @param string $location The location
	*/
	public function outputJavascriptUrls(string $location)
	{
		if ($this->javascript_merge && $location == $this->javascript_location) {
			$splitter = new \Venus\Assets\Splitter($this->app->javascript->getUrls($location));

			$this->app->javascript->outputUrls($splitter->getExternalUrls());
			$this->app->javascript->outputMergedUrls($splitter->getLocalUrls());
		} else {
			$this->app->javascript->output($location);
		}
	}

	/**
	* Outputs extra code in the head, for desktop devices
	*/
	public function outputDesktopExtra()
	{
		$this->app->plugins->run('system_theme_output_desktop_extra', $this);
	}

	/**
	* Outputs extra code in the head, for tablet devices
	*/
	public function outputTabletsExtra()
	{
		$this->outputViewport();

		$this->app->plugins->run('system_theme_output_tablets_extra', $this);
	}

	/**
	* Outputs extra code in the head, for smartphones devices
	*/
	public function outputSmartphonesExtra()
	{
		$this->outputViewport();

		$this->app->plugins->run('system_theme_output_smartphones_extra', $this);
	}

	/**
	* Outputs the meta viewport tag, if the theme's params allow it
	*/
	public function outputViewport()
	{
		if (!$this->params->viewport_output) {
			return;
		}

		$meta = '<meta name="viewport" content="width=' . App::e($this->params->viewport_width) . ', initial-scale=' . App::e($this->params->viewport_initial_scale) . '">' . "\n";

		$meta = $this->app->plugins->filter('system_theme_output_viewport', $meta, $this);

		echo $meta;
	}

	/**
	* Outputs the required javascript code in the header
	*/
	public function outputJavascriptInHeader()
	{
		$this->outputJavascriptCodeStart();
		$this->ouputJavascriptConfig();
		$this->outputJavascriptCodeEnd();
	}

	/**
	* Outputs the javascript config code
	*/
	protected function ouputJavascriptConfig()
	{
		echo 'venus.device = \'' . App::ejs($this->app->device->type) . '\';' . "\n";

		if ($this->app->user->editor != 'bbcode') {
			echo 'venus.editor.type = \'' . App::ejs($this->app->user->editor) . '\';' . "\n";
		}

		$this->app->plugins->run('system_theme_output_javascript_config', $this);
	}

	/**
	* Outputs the extra head code
	*/
	public function outputHeadExtra()
	{
		$this->outputJavascriptCode($this->app->extra_javascript['head']);
		$this->outputCssCode($this->app->extra_css['head']);

		echo $this->app->extra_html['head'];

		$this->app->plugins->run('system_theme_output_head_extra', $this);
	}

	/**
	* Outputs the extra body code
	*/
	public function outputBodyExtra()
	{
		$this->outputJavascriptCode($this->app->extra_javascript['body']);

		echo $this->app->extra_html['body'];

		$this->app->plugins->run('system_theme_output_body_extra', $this);
	}

	/**
	* Outputs the extra footer code
	*/
	public function outputFooterExtra()
	{
		$this->outputJavascriptCode($this->app->extra_javascript['footer']);

		echo $this->app->extra_html['footer'];

		$this->app->plugins->run('system_theme_output_footer_extra', $this);
	}

	/**
	* Outputs the dialogs content
	*/
	public function outputDialogs()
	{
		//echo $this->app->dialogs->content;
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
