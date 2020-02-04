<?php
/**
* The Admin Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Main Venus Class
* The system's main object
*/
class App extends \Venus\App
{
	/**
	* @const array DIRS The locations of the used dirs
	*/
	public const ADMIN_DIRS = [
		'cache' => 'cache',
		'extensions' => 'extensions',
		'javascript' => 'javascript',
		'utils' => 'utils'
	];

	/**
	* @const array URLS The locations of the used urls
	*/
	public const ADMIN_URLS = [
		'extensions' => 'extensions',
		'utils' => 'utils'
	];

	/**
	* @const array ADMIN_URLS_STATIC The locations of the used static urls
	*/
	public const ADMIN_URLS_STATIC = [
		'cache' => 'cache'
	];

	/**
	* @var bool $is_admin True if the app is run from the admin
	*/
	public bool $is_admin = true;

	/**
	* @var string $help_url The link to the help section for the current loaded extension
	*/
	public string $help_url = '';

	/**
	* @var string $config_url The link to the config section for the current loaded extension
	*/
	public string $config_url = '';

	/**
	* @var string $admin_url_static The url from where static content is served
	*/
	public string $admin_url_static = '';

	/**
	* @var string $admin_url_rel The relative url of admin
	*/
	public string $admin_url_rel = '';

	/**
	* @see \Mars\App::getSiteUrl()
	* {@inheritDoc}
	*/
	protected function getSiteUrl() : string
	{
		$host = $_SERVER['HTTP_HOST'];
		$pi = pathinfo($_SERVER['PHP_SELF']);

		return $this->scheme . $host . dirname($pi['dirname']) . '/';
	}

	/**
	* @see \Mars\App::loadBooter()
	* {@inheritDoc}
	*/
	protected function loadBooter()
	{
		$this->boot = new AppBooter($this);
	}

	/**
	* @see \Mars\App::setProperties()
	* {@inheritDoc}
	*/
	public function setProperties()
	{
		parent::setProperties();

		$this->admin_url_static = $this->site_url_static . $this->config->admin_dir;
		$this->admin_url_rel = $this->site_url_rel . $this->config->admin_dir;

		$this->assignDirs(static::ADMIN_DIRS, $this->admin_dir, 'admin');
		$this->assignUrls(static::ADMIN_URLS, $this->admin_url, 'admin');
		$this->assignUrls(static::ADMIN_URLS_STATIC, $this->admin_url_static, 'admin');
	}

	/**
	* Don't do any checking in the admin
	*/
	public function checkOffline()
	{
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
	* @see \Mars\App::error()
	* {@inheritDoc}
	*/
	public function error(string $error, string $title = '', bool $escape_html = true)
	{
		$this->navbar->setTitle(static::str('error'));

		parent::error($error, $title, $escape_html);
	}

	/**
	* @see \Mars\App::message()
	* {@inheritDoc}
	*/
	public function message(string $message, string $title = '', bool $escape_html = true)
	{
		$this->navbar->setTitle(static::str('message'));

		parent::message($message, $title, $escape_html);
	}

	/**
	* @see \Mars\App::permissionDenied()
	* {@inheritDoc}
	*/
	public function permissionDenied()
	{
		$this->lang->loadPackage('messages');
		$this->navbar->setTitle(l('permission_denied'));

		parent::permissionDenied();
	}

	/**
	* @see \Mars\App::redirect404()
	* {@inheritDoc}
	*/
	public function redirect404()
	{
		$this->redirectForce($this->admin_url . '404.php');
	}

	/**
	* Sets the help page of the current extension
	* @param string $name The name of the help section
	* @param string $block The name of the block. If empty, the current block is used
	* @return $this
	*/
	public function setHelp(string $name = 'index', string $block = '')
	{
		if (!$block) {
			$block = $this->document->name;
		}

		$this->help_url = $this->uri->build($this->admin_url . VENUS_ASSETS_NAME . 'help.php', ['block' => $block, 'name' => $name]);

		return $this;
	}

	/**
	* Sets the help url of the current extension
	* @param string $url The help url
	* @return $this
	*/
	public function setHelpUrl(string $url)
	{
		$this->help_url = $url;

		return $this;
	}

	/**
	* Sets the config area of the current extension
	* @param string $area The config area
	* @return $this
	*/
	public function setConfigArea(string $area)
	{
		$this->config_url = $this->uri->adminBlock('admin_config', [$this->config->action_param => $area]);

		return $this;
	}

	/**
	* Sets the config url of the current extension
	* @param string $url The config url
	* @return $this
	*/
	public function setConfigUrl(string $url)
	{
		$this->config_url = $url;

		return $this;
	}
}
