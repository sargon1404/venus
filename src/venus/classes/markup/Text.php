<?php
/**
* The Text Markup Class
* @package Venus
*/

namespace venus\markup;

use venus\App;

/**
* The Text Markup Class
* Basic text 'markup' driver
*/
class Text extends Markup implements MarkupInterface
{
	/**
	* @see \Venus\Markup\MarkupInterface::parse()
	* {@inheritDoc}
	*/
	public function parse(string $text) : string
	{
		$text = str_replace("\r\n", "\n", $text);

		$text = nl2br(App::e($text));

		return $this->app->plugins->filter('markupTextParse', $text, $this);

		return $text;
	}

	/**
	* @see \Venus\Markup\MarkupInterface::convert()
	* {@inheritDoc}
	*/
	public function convert(string $text) : string
	{
		return $this->app->plugins->filter('markupTextConvert', $text, $this);
	}

	/**
	* @see \Venus\Markup\MarkupInterface::quote()
	* {@inheritDoc}
	*/
	public function quote(string $text, string $cite = '') : string
	{
		$code = '';
		if ($cite) {
			$code.= $cite . '<br>';
		}

		$code.= $text . '<hr>';

		return $code;
	}
}
