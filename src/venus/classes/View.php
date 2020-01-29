<?php
/**
* The View Class
* @package Venus
*/

namespace Venus;

use \Mars\Controller; //for preloading

/**
* The View Class
* Implements the View functionality of the MVC pattern
*/
abstract class View extends \Mars\View
{
	/**
	* @var string $base_url The base url of the controller to which the view belongs.
	*/
	public string $base_url = '';

	/**
	* @var Document $document The document the view belongs to
	*/
	public Document $document;

	/**
	* @var string $images_url Alias for $this->app->images_url
	*/
	public string $images_url = '';

	/**
	* @var string $prefix Prefix to be used when calling plugins
	*/
	public string $prefix = '';

	/**
	* @var string $prefix_output Prefix to be used when calling plugins in the templates
	*/
	public string $prefix_output = '';

	/**
	* @var string $item_id The name of the id param, when building the item's url
	*/
	public string $item_id = 'id';

	/**
	* @var string $item_id The name of the id param, when building the item's url. Brackets will be appended to this param
	*/
	public string $item_ids = 'ids';

	/**
	* @var string $controller_name The controller's name
	*/
	protected string $controller_name = '';

	/**
	* @see \Mars\View::prepare()
	* {@inheritDoc}
	*/
	protected function prepare(Controller $controller)
	{
		parent::prepare($controller);

		$this->controller_name = $this->controller->name;
		$this->document = $this->controller->document;

		$this->url = $this->controller->url;
		$this->base_url = $this->controller->base_url;

		$this->prefix = $this->getPrefix('view');
		$this->prefix_output = str_replace('_view_', '', $this->prefix) . '_output_';

		$this->images_url = $this->app->images_url;
	}

	/**
	* @see \Mars\View::getTemplate()
	* {@inheritDoc}
	*/
	public function getTemplate(string $template = '', string $layout = '') : string
	{
		if (!$template) {
			$template = $this->current_method;
		}
		if ($layout) {
			$layout = $this->layout;
		}
		//todo
		var_dump($this->layout);
		die;
		return $this->controller->document->getTemplate($template, $layout);
	}

	/**
	* @see \Mars\View::renderTemplate()
	* {@inheritDoc}
	*/
	public function renderTemplate(string $template = '', string $layout = '')
	{
		if (!$template) {
			$template = $this->current_method;
		}
		if ($layout) {
			$layout = $this->layout;
		}

		//set the plugin's output data
		$this->app->plugins->setOutputData($this->prefix_output . $template . '_', $this);

		$content = $this->controller->document->renderTemplate($template, $layout);

		$this->app->plugins->clearOutputData();

		return $content;
	}

	/**
	* @internal
	*/
	protected function sendPrepare()
	{
		$this->app->dialogs->outputContent();
	}

	/**
	* Builds an url appending $action and $params to the view's $url
	* @param string $action The action to perform, if any
	* @param array $params Array containing the values to be appended. Specified as name => value
	* @param bool $convert If true, will convert the returned url to http/https based on the ssl_enable setting
	* @param bool $remove_empty_params If true, will remove from the query params the empty values
	* @return string The url
	*/
	public function getUrl(string $action = '', array $params = [], bool $convert = false, bool $remove_empty_params = true) : string
	{
		if ($action) {
			$params[$this->app->config->action_param] = $action;
		}

		return $this->app->uri->build($this->url, $params, $convert, $remove_empty_params);
	}

	/**
	* Builds the url of a controller
	* @param string $controller The name of the controller
	* @param string $action The action to perform, if any
	* @param array $params Array containing the values to be appended. Specified as name => value
	* @param bool $convert If true, will convert the returned url to http/https based on the ssl_enable setting
	* @param bool $remove_empty_params If true, will remove from the query params the empty values
	* @return string The url
	*/
	public function getControllerUrl(string $controller, string $action = '', array $params = [], bool $convert = false, bool $remove_empty_params = true) : string
	{
		$params[$this->app->config->controller_param] = $controller;
		if ($action) {
			$params[$this->app->config->action_param] = $action;
		}

		return $this->app->uri->build($this->url, $params, $convert, $remove_empty_params);
	}

	/**
	* Builds the url of an item appending $action and $params to the view's $url
	* @param string $item_id The item's id
	* @param string $action The action to perform, if any
	* @param bool $is_multi If false, the item's name will be read from $this->_id. If true, it will be read from $this->_ids and '[]' will be appended.
	* @param string $return_route The return route, if any
	* @param array $params Array containing the values to be appended. Specified as name => value
	* @param bool $convert If true, will convert the returned url to http/https based on the ssl_enable setting
	* @param bool $remove_empty_params If true, will remove from the query params the empty values
	* @return string The item's url
	*/
	public function getItemUrl(string $item_id, string $action = '', bool $is_multi = false, string $return_route = '', array $params = [], bool $convert = false, bool $remove_empty_params = true) : string
	{
		$item_name = $this->item_id;
		if ($is_multi) {
			$item_name = $this->item_ids . '[]';
		}

		$item_params = [];
		if ($action) {
			$item_params[$this->app->config->action_param] = $action;
		}

		$item_params[$item_name] = $item_id;

		if ($return_route) {
			$item_params[$this->app->config->return_route_param] = $return_route;
		}


		return $this->app->uri->build($this->url, $item_params + $params, $convert, $remove_empty_params);
	}
}
