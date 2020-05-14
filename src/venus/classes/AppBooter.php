<?php
/**
* The Booter Class
* @package Venus
*/

namespace Venus;

use Mars\{Memcache, Caching, Timer, Filter, Escape, Validator, Session, Device, Response};
use Mars\Document\{Title, Meta, Rss};
use Mars\Alerts\{Errors, Messages, Warnings, Notifications};

/**
* The Booter Class
* Initializes the system's required classes
*/
class AppBooter extends \Mars\AppBooter
{
	/**
	* @see \Mars\Booter::minimum()
	* {@inheritDoc}
	*/
	public function minimum()
	{
		$this->app->timer = new Timer($this->app);
		$this->app->uri = new Uri($this->app);

		$this->app->config = new Config($this->app);
		$this->app->config->read();

		$this->app->memcache = new Memcache($this->app);
		$this->app->caching = new Caching($this->app);

		return $this;
	}

	/**
	* @see \Mars\Booter::db()
	* {@inheritDoc}
	*/
	public function db()
	{
		$this->app->db = new Db($this->app);
		$this->app->sql = new Sql($this->app);

		return $this;
	}


	/**
	* @see \Mars\Booter::base()
	* {@inheritDoc}
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
	* {@inheritDoc}
	*/
	public function env()
	{
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



		return $this;
	}

	/**
	* @see \Mars\Booter::document()
	* {@inheritDoc}
	*/
	public function document()
	{
		$this->app->title = new Title;
		$this->app->meta = new Meta;

		$this->app->css = new document\Css($this->app);
		$this->app->javascript = new document\Javascript($this->app);

		$this->app->rss = new Rss;

		$this->app->errors = new Errors;
		$this->app->messages = new Messages;
		$this->app->warnings = new Warnings;
		$this->app->notifications = new Notifications;

		$this->app->breadcrums = new document\Breadcrumbs;
	}

	/**
	* @see \Mars\Booter::system()
	* {@inheritDoc}
	*/
	public function system()
	{
		$this->app->output = new system\Output($this->app);

		$this->app->plugins = new System\Plugins($this->app);
		$this->app->plugins->load();

		$this->app->user = new system\User($this->app);
		$this->app->lang = new system\Language($this->app);
		$this->app->theme = new system\Theme($this->app);

		return $this;
	}
}
