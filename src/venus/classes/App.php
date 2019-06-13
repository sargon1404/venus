<?php
/**
* The Main Venus Class
* @package Venus
*/

namespace Venus;

/**
* The Main Venus Class
* The system's main object
*/
class App extends \Mars\App
{
	/**
	* @var float $version The version
	*/
	public $version = '1.00';

	/**
	* @var bool $is_api True if the app is run as as an api call
	*/
	public $is_api = false;

	/**
	* @var bool $is_admin True if the app is run from the admin
	*/
	public $is_admin = false;

	/**
	* @var string $type True app type (dialog, ajax)
	*/
	public $type = '';

	/**
	* @var string The url of the main index file
	*/
	public $site_index = '';

	/**
	* @var array $categories The current categories the current document belongs to
	*/
	public $categories = [];

	/**
	* @var object $document The current document [block,page,category,tag]
	*/
	public $document = null;

	/**
	* @var string $namespace The root namespace
	*/
	public $namespace = "Cms\\";

	/**
	* @var string $extensions_namespace The root namespace for extensions
	*/
	public $extensions_namespace = "Cms\\Extensions\\";

	/**
	* @var string $images_dir The folder where the images are stored
	*/
	public $images_dir = '';

	/**
	* @var string $media_dir The folder where the media files are stored
	*/
	public $media_dir = '';

	/**
	* @var string $uploads_dir The folder where the uploaded files are stored
	*/
	public $uploads_dir = '';

	/**
	* @var string $javascript_dir The folder where the javascript files are stored
	*/
	public $javascript_dir = '';

	/**
	* @var string $images_url The url of the images folder. Will dynamically point to the regular/tablets/smartphones images folder, based on the theme type
	*/
	public $images_url = '';

	/**
	* @var string $images_url_base The url of the images folder. Will always point to the regular folder
	*/
	public $images_url_base = '';

	/**
	* @var string $media_url The url of the media folder
	*/
	public $media_url = '';

	/**
	* @var string $uploads_url The url of the uploads folder
	*/
	public $uploads_url = '';

	/**
	* @var string $javascript_url The url of the javascript folder
	*/
	public $javascript_url = '';

	/**
	* @var string $utils_url The url of the utils folder
	*/
	public $utils_url = '';

	/**
	* @var string $admin_url The url of the admin area
	*/
	public $admin_url = '';

	/**
	* @var string	$admin_dir The path for the admin area
	*/
	public $admin_dir = '';

	/**
	* @var bool $is_homepage Set to true if the homepage is currently displayed
	*/
	public $is_homepage = false;

	/**
	* @var bool $show_menu Determines if the menu is to be displayed
	*/
	public $show_menu = true;

	/**
	* @var bool $show_breadcrumbs Determines if the breadcrumbs are to be displayed
	*/
	public $show_breadcrumbs = true;

	/**
	* @var bool $show_announcements Determines if the announcements are to be displayed
	*/
	public $show_announcements = true;

	/**
	* @var bool $show_widgets Determines if the widgets are to be displayed
	*/
	public $show_widgets = true;

	/**
	* @var bool $show_banners Determines if the banners are to be displayed
	*/
	public $show_banners = true;

	/**
	* @var array $extra_html Extra html code to be placed in the head/body/footer
	*/
	public $extra_html = ['head' => '', 'body' => '', 'footer' => ''];

	/**
	* @var array $extra_javascript Extra javascript code to be placed in the head/body/footer
	*/
	public $extra_javascript = ['head' => '', 'body' => '', 'footer' => ''];

	/**
	* @var array $extra_css Extra css code to be placed in the head/body/footer
	*/
	public $extra_css = ['head' => '', 'body' => '', 'footer' => ''];

	/**
	* @const array DIRS The locations of the used dirs
	*/
	public const DIRS = [
		'temp' => 'temp',
		'log' => 'log',
		'cache' => 'cache',
		'libraries' => 'libraries',
		'extensions' => 'extensions',
		'images' => 'images',
		'media' => 'media',
		'uploads' => 'uploads',
		'javascript' => 'javascript',
		'utils' => 'utils'
	];

	/**
	* @const array URLS The locations of the used urls
	*/
	public const URLS = [
		'extensions' => 'extensions',
		'javascript' => 'javascript',
		'utils' => 'utils'
	];

	/**
	* @const array URLS_STATIC The locations of the used static urls
	*/
	public const URLS_STATIC = [
		'cache' => 'cache',
		'images' => 'images',
		'media' => 'media',
		'uploads' => 'uploads'
	];

	/**
	* @const array EXTENSIONS_DIR The locations of the used extensions subdirs
	*/
	public const EXTENSIONS_DIRS = [
		'languages' => 'languages/',
		'templates' => 'templates/',
		'layouts' => 'layouts/',
		'images' => 'images/',

		'css' => 'css/',
		'javascript' => 'javascript/',
		'functions' => 'functions/',
		'objects' => 'objects/',

		'controllers' => 'controllers/',
		'models' => 'models/',
		'views' => 'views/',

		'plugins' => 'plugins/',
		'inline' => 'inline/'
	];

	/**
	* @const array CACHE_DIRS The locations of the cache subdirs
	*/
	public const CACHE_DIRS = [
		'templates' => 'templates/',
		'css' => 'css/',
		'javascript' => 'javascript/',
		'libraries' => 'libraries/',
		'rss' => 'rss/'
	];

	/**
	* @const array FILE_EXTENSIONS Common file extensions
	*/
	public const FILE_EXTENSIONS = [
		'templates' => 'tpl',
		'css' => 'css',
		'javascript' => 'js'
	];

	/**
	* @const array USERGROUPS The id of the main usergroups
	*/
	public const USERGROUPS = [
		'guests' => 1,
		'registered' => 2,
		'spammers' => 3,
		'moderators' => 4,
		'admins' => 5
	];

	/**
	* @const array CATEGORIES The id of the main categories
	*/
	public const CATEGORIES = [
		'homepage' => 1
	];

	/**
	* Instantiates the App object
	* @return App The app instance
	*/
	public static function instantiate() : \Mars\App
	{
		parent::$instance = new static;

		return parent::$instance;
	}

	/**
	* Boots the App
	* @return App The app instance
	*/
	public function boot()
	{
		$this->setData();
		$this->setDirs();
		$this->setUrls();
		$this->setGzip();

		$this->loadBooter();
		$this->loadLibraries();

		$this->boot->minimum();

		//redirect to the installer, if venus hasn't yet been installed
		if (!defined('VENUS_INSTALLED')) {
			$this->redirect($this->site_url . 'install/');
		}

		$this->boot->db();
		$this->boot->config();
		$this->boot->base();
		$this->boot->properties();
		$this->boot->env();
		$this->boot->document();
		$this->boot->system();

		$this->checkOffline();
	}

	/**
	* @see \Mars\App::setUrls()
	* {@inheritDoc}
	*/
	protected function setUrls()
	{
		if ($this->is_cli) {
			return;
		}

		parent::setUrls();

		$this->site_index = $this->site_url . 'index.php';
	}

	/**
	* @see \Mars\App::setProperties()
	* {@inheritDoc}
	*/
	public function setProperties()
	{
		parent::setProperties();

		$this->assignUrls(static::URLS_STATIC, $this->site_url_static);

		$this->admin_dir = $this->site_dir . $this->config->admin_dir;
		$this->admin_url = $this->site_url . $this->config->admin_dir;
		$this->admin_index = $this->admin_url . 'index.php';

		$this->images_url_base = $this->images_url;
		$this->images_url = $this->images_url . $this->device->getDir();

		$this->show_menu = (bool)$this->config->menu_show;
		$this->show_breadcrumbs = (bool)$this->config->breadcrumbs_show;
	}

	/**
	* @see \Mars\App::loadBooter()
	* {@inheritDoc}
	*/
	protected function loadBooter()
	{
		$this->boot = new AppBooter($this);
	}

	/******************EXTRA METHODS*******************************************/

	/**
	* Adds html code to the output
	* @param string $code The code to add
	* @param string $position The position where the code will be added: head|body|footer
	* @return $this
	*/
	public function addHtml(string $code, string $position = 'head')
	{
		$this->extra_html[$position] .= $code . "\n";

		return $this;
	}

	/**
	* Adds javascript code to the output
	* @param string $code The code to add
	* @param string $position The position where the code will be added: head|body|footer
	* @return $this
	*/
	public function addJavascript(string $code, string $position = 'head')
	{
		$this->extra_javascript[$position] .= $code . "\n";

		return $this;
	}

	/**
	* Adds css code to the output
	* @param string $code The code to add
	* @param string $position The position where the code will be added: head|body|footer
	* @return $this
	*/
	public function addCss(string $code, string $position = 'head')
	{
		$this->extra_css[$position] .= $code . "\n";

		return $this;
	}

	/**********************SCREEN FUNCTIONS***************************************/

	/**
	* @internal
	*/
	protected function getScreenObj()
	{
		return new Document\Screen($this);
	}

	/**
	* Checks if the site is online. Displays the offline screen if it is
	*/
	public function checkOffline()
	{
		var_dump("check offline");die;
		$this->config->offline = true;
		if (!$this->config->offline) {
			return;
		}

		$screen = $this->getScreenObj();
		$screen->offline();
	}

	/**
	* @see \Mars\App::fatalError()
	* {@inheritDoc}
	*/
	public function fatalError(string $error, bool $escape_html = true)
	{
		$screen = $this->getScreenObj();
		$screen->fatalError($error, $escape_html);
	}

	/**
	* Redirects to the 404: Not Found page
	*/
	public function redirect404()
	{
		$this->redirect($this->site_url . '404.php');
	}

	/**********************OUTPUT FUNCTIONS***************************************/

	/**
	* @internal
	*/
	protected function getDebugObj()
	{
		return new Debug($this);
	}

	/**********************EMAIL FUNCTIONS***************************************/

	/**
	* Sends a mail
	* @param mixed $to The address(es) where the mail will be sent (string,array)
	* @param string $subject The subject of the mail
	* @param string $message The body of the mail
	* @param string $from The email adress from which the email will be send. By default $app->config->mail_from is used
	* @param string $from_name The from name field of the email. By default $app->config->mail_from_name is used
	* @param string $reply_to The email address to which to reply to
	* @param string $reply_to_name The reply name associated with the $reply_to email
	* @param bool $is_html If true the mail will be a html mail
	* @param array $attachments The attachments, if any,to the mail (string,array)
	* @return bool Returns true on success, false on failure
	*/
	public function mail($to, string $subject, string $message, string $from = '', string $from_name = '', string $reply_to = '', string $reply_to_name = '', bool $is_html = true, array $attachments = []) : bool
	{
		if (!$to) {
			return false;
		}

		$this->plugins->run('appMail1', $to, $subject, $message, $from, $from_name, $reply_to, $reply_to_name, $is_html, $attachments, $this);

		if (!$from) {
			$from = $this->config->mail_from;
		}
		if (!$from_name) {
			$from_name = $this->config->mail_from_name;
		}

		$mailer = $this->getMailer($to, $subject, $message, $from, $from_name, $reply_to, $reply_to_name, $is_html, $attachments);

		//set the smtp server
		if ($this->config->mail_type == 'smtp') {
			$mailer->isSmtp($this->config->mail_smtp_server, $this->config->mail_smtp_port, $this->config->mail_smtp_secure, $this->config->mail_smtp_auth_username, $this->config->mail_smtp_auth_password);
		}

		$this->plugins->run('appMail2', $mailer, $this);

		$result = $mailer->send();
		if (!$result) {
			if (!$this->is_cli && !$this->is_api) {
				$error = static::__('mail_error');
				if ($this->config->debug) {
					$error.= $mailer->getError();
				}

				$this->errors->add($error);

				$this->log->error('Error sending mail : ' . $mailer->getError(), __FILE__, __LINE__);
			}
		}

		return $result;
	}
}
