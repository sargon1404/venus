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
	* {@inheritdoc}
	*/
	public function parse(string $text) : string
	{
		$text = str_replace("\r\n", "\n", $text);

		$text = nl2br(App::e($text));

		return $this->app->plugins->filter('markup_text_parse', $text, $this);

		return $text;
	}

	/**
	* @see \Venus\Markup\MarkupInterface::convert()
	* {@inheritdoc}
	*/
	public function convert(string $text) : string
	{
		return $this->app->plugins->filter('markup_text_convert', $text, $this);
	}

	/**
	* @see \Venus\Markup\MarkupInterface::quote()
	* {@inheritdoc}
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
