<?php
/**
* The Request Class
* @package Venus
*/

namespace Venus;

/**
* The Request Class
* Handles the input [$_GET, $_POST, $_COOKIE] interactions
*/
class Request extends \Mars\Request
{
	/**
	* Determines if the content can be posted
	* @return bool True if the content can be posted, false otherwise
	*/
	public function canPost() : bool
	{
		if ($this->method != 'post') {
			$this->app->lang->loadPackage('messages');
			$this->app->errors->add(App::__('invalid_method_text'));

			return false;
		}

		return $this->checkToken();
	}

	/**
	* Check if the request came from a valid user, preventing XCRF attacks.
	* @param bool $return If $return = true, will return false if the token is not valid. If the param is false, and the token isn't valid, will display an error screen
	* @return bool
	*/
	public function checkToken(bool $return = true) : bool
	{
		$token = $this->getToken();

		if ($token != $this->app->user->token) {
			$this->app->lang->loadPackage('messages');
			if ($return) {
				$this->app->errors->add(App::__('invalid_token_title'));
				return false;
			}

			$this->app->error(App::__('invalid_token_text'), App::__('invalid_token'));
		}

		return true;
	}

	/**
	* Returns the token value
	* @return string
	*/
	public function getToken() : string
	{
		return $this->value($this->app->config->token_field);
	}

	/**
	* @see \Mars\Request::getResponse()
	* {@inheritDoc}
	*/
	public function getResponse(string $response_param = '') : string
	{
		if ($this->app->is_api) {
			return 'ajax';
		}

		return parent::getResponse($this->app->config->response_param);
	}

	/**
	* Returns the name of the controller
	* @param string $default_controller The name of the default controller, if any
	* @return string
	*/
	public function getController(string $default_controller = '') : string
	{
		$name = $this->value($this->app->config->controller_param);
		if (!$name) {
			$name = $default_controller;
		}

		return $name;
	}

	/**
	* @see \Mars\Request::getAction()
	* {@inheritDoc}
	*/
	public function getAction(string $action_param = '') : string
	{
		return parent::getAction($this->app->config->action_param);
	}

	/**
	* Returns the return route
	* @param array $allowed_route List of allowed routes
	* @param string $default_route The default return route
	* @return string
	*/
	public function getReturnRoute(array $allowed_routes = [], string $default_route = '') : string
	{
		$route = $this->value($this->app->config->return_route_param);

		if ($allowed_routes) {
			if (in_array($route, $allowed_routes)) {
				return $route;
			} else {
				return $default_route;
			}
		} elseif (!$route) {
			$route = $default_route;
		}

		return $route;
	}

	/**
	* Returns the parsed and filtered text html code from an input variable
	* @param string $name The name of the variable. If empty, 'venus_editor' is used
	* @param bool $parse_links If true, will parse links
	* @param bool $parse_nofollow If true, will apply the rel="nofollow" attribute to links
	* @param bool $parse_smilies If true, will parse the smilies
	* @param bool $parse_badwords If true, will parse the text for badwords
	* @param bool $parse_media If true, will parse the media files
	* @param bool $parse_videos If true, will parse the videos
	* @return string The parsed and filtered text
	*/
	public function getText(string $name = '', bool $parse_links = true, bool $parse_nofollow = false, bool $parse_smilies = false, bool $parse_badwords = false, bool $parse_media = false, bool $parse_videos = false) : string
	{
		if (!$name) {
			$name = 'editor';
		}

		$text = $this->post($name);

		return $this->app->text->parse($text, $parse_links, $parse_nofollow, $parse_smilies, $parse_badwords, $parse_media, $parse_videos);
	}

	/**
	* Returns the datetime timestamp for $name
	* @param string $name The name of the variable
	* @param bool $return_timestamp If true will return a timestamp. If false, will return a mysql datetime
	* @param bool $adjust_timestamp If true and $return_timestamp is true, will return the timestamp adjusted to UTC
	* @return int|string The timestamp
	*/
	public function getDatetime(string $name, bool $return_timestamp = true, bool $adjust_timestamp = true)
	{
		$date = $this->value($name . '-date');
		$time = $this->value($name . '-time');

		return $this->app->time->toDatetime($date, $time, $return_timestamp, $adjust_timestamp);
	}

	/**
	* Returns the date timestamp for $name
	* @param string $name The name of the variable
	* @param bool $return_timestamp If true will return a timestamp. If false, will return a mysql datetime
	* @param bool $adjust_timestamp If true and $return_timestamp is true will return the timestamp adjusted to UTC
	* @return int|string The timestamp
	*/
	public function getDate(string $name, bool $return_timestamp = true, bool $adjust_timestamp = true)
	{
		$date = $this->value($name . '-date');

		return $this->app->time->toDate($date, $return_timestamp, $adjust_timestamp);
	}

	/**
	* @see \Mars\Request::getOrderBy()
	* {@inheritDoc}
	*/
	public function getOrderBy(array $fields = [], string $default_field = '', string $orderby_param = '') : string
	{
		if (!$orderby_param) {
			$orderby_param = $this->app->config->orderby_param;
		}

		return parent::getOrderBy($fields, $default_field, $orderby_param);
	}

	/**
	* @see \Mars\Request::getOrder()
	* {@inheritDoc}
	*/
	public function getOrder(string $order_param = '') : string
	{
		if (!$order_param) {
			$order_param = $this->app->config->order_param;
		}

		return parent::getOrder($order_param);
	}

	/**
	* @see \Mars\Request::getPage()
	* {@inheritDoc}
	*/
	public function getPage(string $page_param = '') : int
	{
		if (!$page_param) {
			$page_param = $this->app->config->page_param;
		}

		return parent::getPage($page_param);
	}

	/**
	* Gets the current page of the comments pagination system
	* @return int The current page
	*/
	public function getCommentsPage() : int
	{
		return $this->getPage($this->app->config->seo_comments_param);
	}

	/**
	* @see \Mars\Request::uploadExtensionIsAllowed()
	* {@inheritDoc}
	*/
	public function uploadExtensionIsAllowed(string $extension, $allowed_extensions = '*', ?array &$out_allowed_extensions = []) : bool
	{
		if (!$allowed_extensions) {
			if (!empty($this->app->config->upload_allow)) {
				$allowed_extensions = explode(',', $this->app->config->upload_allow);
				$allowed_extensions = $this->app->filter->trim($allowed_extensions);
			}
		}

		return parent::uploadExtensionIsAllowed($extension, $allowed_extensions, $out_allowed_extensions);
	}

	/**
	* @see \Mars\Request::uploadHandleErrorExtensionIsDisallowed()
	* {@inheritDoc}
	*/
	protected function uploadHandleErrorExtensionIsDisallowed(string $file)
	{
		$this->app->errors->add(App::__('upload_error3', ['{FILENAME}', '{DISALLOWED_TYPES}'], [$file, implode(', ', $this->upload_disallowed_extensions)]));
	}

	/**
	* @see \Mars\Request::uploadHandleErrorExtensionIsNotAllowed()
	* {@inheritDoc}
	*/
	protected function uploadHandleErrorExtensionIsNotAllowed(string $file, array $allowed_extensions)
	{
		$this->app->errors->add(App::__('upload_error2', ['{FILENAME}', '{ALLOWED_TYPES}'], [$file, implode(', ', $allowed_extensions)]));
	}

	/**
	* @see \Mars\Request::uploadHandleError()
	* {@inheritDoc}
	*/
	protected function uploadHandleError(string $error_code, string $file)
	{
		$error = App::__('upload_error1', '{FILE}', $file);

		switch ($error_code) {
			case UPLOAD_ERR_INI_SIZE:
				$error.= App::__('upload_error2') . ini_get('upload_max_filesize');
				break;
			case UPLOAD_ERR_PARTIAL:
				$error.= App::__('upload_error3');
				break;
			case UPLOAD_ERR_NO_FILE:
				$error.= App::__('upload_error4');
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$error = App::__('upload_error5');
				break;
		}

		$this->app->errors->add($error);
	}
}
