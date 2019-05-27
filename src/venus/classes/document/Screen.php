<?php
/**
* The Screen Class
* @package Venus
*/

namespace Venus\Document;

use Venus\App;
use Venus\Theme;
use Mars\Alerts\Alert;

/**
* The Screen Class
* Contains 'Screen' functionality. Eg: error, message screens etc..
*/
class Screen extends \Mars\Document\Screen
{
	/**
	* Returns the filename of a screen template
	* @param string $template The template
	* @return string The filename
	*/
	protected function getTemplateFilename(string $template) : string
	{
		if (isset($this->app->theme)) {
		}

		return $this->app->extensions_dir . Theme::getBaseDir() . '/' . $template . '.tpl';
	}

	/**
	* @see \Mars\Document\Screen::fatalError()
	* {@inheritDoc}
	*/
	public function fatalError(string $error, bool $escape_html = true)
	{
		if ($this->app->is_cli) {
			parent::fatalError($error, $escape_html);
		}

		if ($escape_html) {
			$error = App::e($error);
		}

		echo '<h1>Fatal Error</h1><p>' . nl2br($error) . '</p>';
		die;
	}

	/**
	* @see \Mars\Document\Screen::error()
	* {@inheritDoc}
	*/
	public function error(string $error, string $title = '', bool $escape_html = true)
	{
		if ($this->app->is_cli) {
			parent::error($error);
		}
		if ($this->app->response->isAjax()) {
			$this->sendError($error, $escape_html);
		}

		if (!isset($this->app->theme)) {
			$this->fatalError($error, $escape_html);
		}

		if (!$title) {
			$title = App::__('error');
		}

		$this->output($error, $title, $escape_html, 'error', 'error');
	}

	/**
	* @see \Mars\Document\Screen::message()
	* {@inheritDoc}
	*/
	public function message(string $message, string $title = '', bool $escape_html = true)
	{
		if ($this->app->is_cli) {
			parent::message($message);
		}
		if ($this->app->response->isAjax()) {
			$this->sendError($message, $escape_html);
		}

		if (!$title) {
			$title = App::__('message');
		}

		$this->output($message, $title, $escape_html, 'message', 'message');
	}

	/**
	* Outputs an alert
	* @param string $text The text
	* @param string $title The title
	* @param bool $escape_html If true will escape the title and text
	* @param string $var_name The theme's var name under which the alert will be stored
	* @param string $template The messages template to load
	*/
	protected function output(string $text, string $title, bool $escape_html, string $var_name, string $template)
	{
		$this->app->title->set($title);

		$alert = new Alert($text, $title, $escape_html);

		$this->app->theme->addVar($var_name, $alert);

		$this->app->content = $this->app->theme->getTemplate('messages/' . $template);
		$this->app->output();
		die;
	}

	/**
	* @see \Mars\Document\Screen::permissionDenied()
	* {@inheritDoc}
	*/
	public function permissionDenied()
	{
		$this->app->lang->loadPackage('messages');
		$title = App::__('permission_denied');
		$error = App::__('permission_denied_text');

		if ($this->app->is_cli) {
			parent::permissionDenied();
		}
		if ($this->app->response->isAjax()) {
			$this->sendError($title);
		}

		header('HTTP/1.0 401 Unauthorized');

		$this->output($error, $title, false, 'error', 'permission_denied');
	}

	/**
	* Displays the site offline screen
	*/
	public function offline()
	{
		if ($this->app->theme->hasTemplate('offline')) {
			die("da");
		} else {
			die("nu");
		}

		$template = $this->getTemplateFilename('offline');

		$content = file_get_contents($template);
		$content = str_replace('{{ $message }}', $this->app->config->offline_reason, $content);

		echo $content;
		die;
	}

	/**
	* Sends with ajax
	*/
	protected function send()
	{
		$this->app->response->output();
	}

	/**
	* Sends an error using ajax
	* @param string $error The error message
	* @param bool $escape_html If true will escape the error message
	*/
	protected function sendError(string $error, bool $escape_html)
	{
		$this->app->errors->add($error, '', $escape_html);

		$this->send();
	}

	/**
	* Sends a message using ajax
	* @param string $message The message
	* @param bool $escape_html If true will escape the message
	*/
	protected function sendMessage(string $message, bool $escape_html)
	{
		$this->app->messages->add($error, '', $escape_html);

		$this->send();
	}
}