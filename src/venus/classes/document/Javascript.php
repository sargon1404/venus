<?php
/**
* The Javascript Urls Class
* @package Venus
*/

namespace Venus\Document;

use Venus\App;

/**
* The Document's Javascript Urls Class
* Class containing the javascript urls/stylesheets used by a document
*/
class Javascript extends \Mars\Document\Javascript
{
	use CacheTrait;

	/**
	* @var array $dialogs The dialogs data
	*/
	protected $dialogs = [];

	/**
	* @internal
	*/
	protected $extension = '.js';

	/**
	* Loads the javascript code
	* @param string $location The location of the url [head|footer]
	* @param int $priority The url's output priority. The higher, the better
	* @return $this
	*/
	public function loadMain(string $location = 'head', int $priority = 100)
	{
		return $this;
	}

	/**
	* Stores the content as as inline dialog for faster display
	* @param string $name The dialog's name
	* @param string $alias The dialog's alias
	* @param string $title The dialog's title
	* @param string $content The dialog's content
	* @return $this
	*/
	public function loadDialog(string $name, string $alias, string $title, string $content)
	{
		$this->dialogs[$name] = [
			'alias' => $alias,
			'title' => $title,
			'cnt' => $content
		];

		return $this;
	}

	/**
	* Builds the code for the inline editors
	* @return string The dialog's javascript code
	*/
	public function buildDialogs() : string
	{
		global $venus;
		if (!$this->dialogs) {
			return '';
		}

		$code = '';
		foreach ($this->dialogs as $name => $dialog) {
			$name = App::ejs($name);
			$alias = App::ejs($dialog['alias']);
			$title = App::ejs($dialog['title']);
			$content = App::ejs($dialog['content'], false);

			$code.= "venus.dialog.load_inline('{$name}', '{$alias}', '{$title}', '{$content}');\n";
		}

		$code.= "\n";

		$this->dialogs = [];

		return $code;
	}
}
