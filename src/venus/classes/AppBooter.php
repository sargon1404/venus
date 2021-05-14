<?php
/**
* The Booter Class
* @package Venus
*/

namespace Venus;

use Mars\Memcache;
use Mars\Caching;
use Mars\Timer;
use Mars\Filter;
use Mars\Escape;
use Mars\Validator;
use Mars\Accelerator;
use Mars\Session;
use Mars\Device;
use Mars\Response;
use Mars\Document\Title;
use Mars\Document\Meta;
use Mars\Document\Rss;
use Mars\Alerts\Errors;
use Mars\Alerts\Messages;
use Mars\Alerts\Warnings;
use Mars\Alerts\Notifications;

/**
* The Booter Class
* Initializes the system's required classes
*/
class AppBooter extends \Mars\AppBooter
{
	/**
	* @see \Mars\Booter::minimum()
	* {@inheritdoc}
	*/
	public function minimum()
	{
		$this->app->timer = new Timer($this->app);
		$this->app->uri = new Uri($this->app);

		$this->app->config = new Config($this->app);
		$this->app->config->read();

		$this->app->setData();

		$this->app->memcache = new Memcache($this->app);
		$this->app->caching = new Caching($this->app);

		return $this;
	}

	/**
	* @see \Mars\Booter::db()
	* {@inheritdoc}
	*/
	public function db()
	{
		$this->app->db = new Db($this->app);
		$this->app->sql = new Sql($this->app);

		$this->app->setDataAfterDb();

		return $this;
	}


	/**
	* @see \Mars\Booter::base()
	* {@inheritdoc}
	*/
	public function base()
	{
		$this->app->log = new Log($this->app);
		$this->app->time = new Time($this->app);
		$this->app->filter = new Filter($this->app);
		$this->app->escape = new Escape($this->app);
		$this->app->validator = new Validator($this->app);
		$this->app->format = new Format($this->app);
		$this->app->file = new File($this->app);
		$this->app->html = new Html($this->app);
		$this->app->ui = new Ui($this->app);
		$this->app->text = new Text($this->app);

		return $this;
	}

	/**
	* Loads the config settings from the database
	*/
	public function config()
	{
		$this->app->config->load();
	}

	/**
	* @see \Mars\Booter::env()
	* {@inheritdoc}
	*/
	public function env()
	{
		$this->app->accelerator = new Accelerator($this->app);

		$this->app->session = new Session($this->app);
		$this->app->session->start();

		$this->app->device = new Device($this->app);
		$this->app->request = new Request($this->app);
		$this->app->response = new Response($this->app);

		$this->app->cache = new Cache($this->app);
		$this->app->cache->load();

		$this->app->library = new Library($this->app);
		$this->app->env = new Environment($this->app);
		$this->app->media = new Media;

		$this->app->setDataAfterEnv();

		return $this;
	}

	/**
	* @see \Mars\Booter::document()
	* {@inheritdoc}
	*/
	public function document()
	{
		$this->app->title = new Title;
		$this->app->meta = new Meta;

		$this->app->css = new Document\Css($this->app);
		$this->app->javascript = new Document\Javascript($this->app);

		$this->app->rss = new Rss;

		$this->app->errors = new Errors;
		$this->app->messages = new Messages;
		$this->app->warnings = new Warnings;
		$this->app->notifications = new Notifications;

		$this->app->breadcrums = new Document\Breadcrumbs;
	}

	/**
	* @see \Mars\Booter::system()
	* {@inheritdoc}
	*/
	public function system()
	{
		$this->app->output = new system\Output($this->app);

		$this->app->plugins = new System\Plugins($this->app);
		$this->app->plugins->load();

		$this->app->user = new System\User($this->app);
		$this->app->lang = new System\Language($this->app);
		$this->app->theme = new System\Theme($this->app);

		return $this;
	}
}
