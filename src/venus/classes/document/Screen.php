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
	* @param string $extensions_path The folder where the extensions are located
	*/
	protected string $extensions_path = '';

	/**
	* Builds the screen object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->extensions_path = $this->app->extensions_path;
	}


	/**
	* Returns the filename of a screen template
	* @param string $template The template
	* @return string The filename
	*/
	protected function getTemplateFilename(string $template) : string
	{
		if (isset($this->app->theme)) {
			if ($this->app->theme->hasTemplate($template)) {
				return $this->app->theme->getTemplateFilename($template);
			}
		}

		return $this->extensions_path . Theme::getBaseDir() . '/' . $template . '.tpl';
	}

	/**
	* @see \Mars\Document\Screen::fatalError()
	* {@inheritdoc}
	*/
	public function fatalError(string $text, bool $escape_html = true)
	{
		if ($this->app->is_bin) {
			parent::fatalError($text, false);
			return;
		}

		if ($escape_html) {
			$text = App::e($text);
		}

		$template = $this->getTemplateFilename('fatal-error');

		$content = file_get_contents($template);
		$content = str_replace('{{ $error }}', nl2br($text), $content);

		echo $content;
		die;
	}

	/**
	* @see \Mars\Document\Screen::error()
	* {@inheritdoc}
	*/
	public function error(string $text, string $title = '', bool $escape_html = true)
	{
		if ($this->app->is_bin) {
			parent::error($text);
		}
		if ($this->app->response->isAjax()) {
			$this->sendError($text, $escape_html);
		}

		if (!isset($this->app->theme)) {
			$this->fatalError($text, $escape_html);
		}

		if (!$title) {
			$title = App::__('error');
		}

		$this->output($text, $title, $escape_html, 'error', 'error');
	}

	/**
	* @see \Mars\Document\Screen::message()
	* {@inheritdoc}
	*/
	public function message(string $text, string $title = '', bool $escape_html = true)
	{
		if ($this->app->is_bin) {
			parent::message($text);
		}
		if ($this->app->response->isAjax()) {
			$this->sendError($text, $escape_html);
		}

		if (!$title) {
			$title = App::__('message');
		}

		$this->output($text, $title, $escape_html, 'message', 'message');
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

		$this->app->content = $this->app->theme->getTemplate('alerts/' . $template);
		$this->app->output();
		die;
	}

	/**
	* @see \Mars\Document\Screen::permissionDenied()
	* {@inheritdoc}
	*/
	public function permissionDenied()
	{
		$this->app->lang->loadFile('messages');
		$title = App::__('permission_denied');
		$text = App::__('permission_denied_text');

		if ($this->app->is_bin) {
			parent::permissionDenied();
		}
		if ($this->app->response->isAjax()) {
			$this->sendError($title);
		}

		header('HTTP/1.0 401 Unauthorized');

		$this->output($text, $title, false, 'error', 'permission_denied');
	}

	/**
	* Displays the site's offline screen
	*/
	public function offline()
	{
		$template = $this->getTemplateFilename('offline');

		$content = file_get_contents($template);
		$content = str_replace('{{ $text }}', $this->app->config->offline_reason, $content);

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
	* @param string $text The error message
	* @param bool $escape_html If true will escape the error message
	*/
	protected function sendError(string $text, bool $escape_html)
	{
		$this->app->errors->add($text, '', $escape_html);

		$this->send();
	}

	/**
	* Sends a message using ajax
	* @param string $text The message
	* @param bool $escape_html If true will escape the message
	*/
	protected function sendMessage(string $text, bool $escape_html)
	{
		$this->app->messages->add($text, '', $escape_html);

		$this->send();
	}
}
