<?php
/**
* The Markup Class
* @package Venus
*/

namespace Venus\Markup;

use Venus\App;
use Venus\Text\Parser;
use Venus\Text\Convertor;

/**
* The Markup Class
* Base class all markup drivers must extend
*/
abstract class Markup
{
	use \Venus\AppTrait;

	/**
	* @var bool $is_filtered If true, the results of parse() are considered filtered and won't be filtered for a second time
	*/
	public $is_filtered = false;

	/**
	* @var bool $url_nofollow If set to true, when parsing for urls, the rel="nofollow" will be set on each url
	*/
	public $url_nofollow = false;

	/**
	* @var bool $tags_all If true, all tags will be parses, iregardless of $tags_array
	*/
	public $tags_all = false;

	/**
	* @var array $tags_array The list of tags to parse
	*/
	public $tags_array = [];

	/**
	* Builds the markup object
	* @param string $tags_list Comma delimited string which specifies which tags to parse. If $tags_list == 'all', all tags will be parsed. If null, the tags list will be determined from the usergroup settings
	*/
	public function __construct(?string $tags_list = null)
	{
		$this->app = $this->getApp();

		if ($tags_list === null) {
			$tags_list = $this->app->user->markup_tags;
		}

		if (strtolower($tags_list) == 'all') {
			$this->tags_all = true;
		} else {
			$this->tags_array = explode(',', str_replace(' ', '', $tags_list));
		}
	}

	/**
	* @internal
	*/
	protected function getParserObj()
	{
		return new Parser;
	}

	/**
	* @internal
	*/
	protected function getConvertorObj()
	{
		return new Convertor;
	}

	/**
	* Determines if a tag can be parsed
	* @param string $tag The tag
	* @return bool
	*/
	protected function canParse(string $tag) : bool
	{
		if ($this->tags_all) {
			return true;
		}

		return in_array($tag, $this->tags_array);
	}

	/**
	* Trims and cleans the items of a list
	* @param array $items The items to trim
	* @return array The cleaned items list
	*/
	protected function trimList(array $items) : array
	{
		foreach ($items as $key => $val) {
			$items[$key] = trim($val);
			if (!$items[$key]) {
				unset($items[$key]);
			}
		}

		return $items;
	}

	/**
	* Returns the html code of a list
	* @param string $type The type of the list: ol|ul
	* @param array $items The items of the list
	* @return string
	*/
	protected function getList(string $type, array $items) : string
	{
		if (!$items) {
			return '';
		}

		$code = $this->app->html->list($type, $items);
		$code = str_replace("\n", '', $code);

		return $this->app->plugins->filter('markupMarkupGetList', $code, $type, $items, $this);
	}

	/**
	* Returns the html code of an image
	* @param string $url The image's url
	* @return string
	*/
	protected function getImage(string $url) : string
	{
		if (!$url) {
			return '';
		}

		$url = $this->escapeUrl($url);

		$code = $this->app->html->img($url, 0, 0, '', '', '', '', false);

		return $this->app->plugins->filter('markupMarkupGetImage', $code, $url, $this);
	}

	/**
	* Returns the html code of an url
	* @param string $url The url
	* @param string $title The title of the link
	* @return string
	*/
	protected function getUrl(string $url, string $title) : string
	{
		if (!$url && !$title) {
			return '';
		}

		$rel = '';
		if ($this->url_nofollow) {
			$rel = 'nofollow';
		}

		$url = $this->escapeUrl($url);

		$code = $this->app->html->a($url, $title, '', '', '', '', $rel, false);

		return $this->app->plugins->filter('markupMarkupGetUrl', $code, $url, $title, $this);
	}

	/**
	* Returns the html code of an email
	* @param string $email The email address
	* @param string $title The title of the email link
	* @return string
	*/
	protected function getEmail(string $email, string $title) : string
	{
		if (!$email && !$title) {
			return '';
		}

		$email = str_replace(' ', '', $email);

		$code = $this->app->html->a('mailto:' . $email, $title, 'email', '', '', '', '', false);

		return $this->app->plugins->run('markupMarkupGetEmail', $code, $email, $title, $this);
	}

	/**
	* Escapes an url
	* @param string $url The url
	* @return string The escaped url
	*/
	protected function escapeUrl(string $url) : string
	{
		$url = App::de($url);
		$url = $this->app->escape->url($url);
		$url = App::e($url);

		return $url;
	}

	/**
	* Returns the html code of a color span
	* @param string $color The color
	* @return string
	*/
	protected function getColor(string $color) : string
	{
		if (!$color) {
			return '<span>';
		}

		$color = substr($color, 0, 6);

		$code = '<span style="color:#' . $color . '">';

		return $this->app->plugins->filter('markupMarkupGetColor', $code, $color, $this);
	}

	/**
	* Returns the html code of a font family span
	* @param string $font The font family
	* @return string
	*/
	protected function getFont(string $font) : string
	{
		if (!$font) {
			return '<span>';
		}

		$code = '<span style="font-family:\'' . $font . '\'">';

		return $this->app->plugins->filter('markupMarkupGetFont', $code, $font, $this);
	}

	/**
	* Returns the html code of a size span
	* @param int $size The font size
	* @return string
	*/
	protected function getSize(string $size) : string
	{
		$size = (int)$size;

		if (!$size) {
			return '<span>';
		}

		if ($size > 30) {
			return '<span>';
		}

		$code = '<span style="font-size:' . $size . 'px">';

		return $this->app->plugins->filter('markupMarkupGetSize', $code, $size, $this);
	}

	/**
	* Returns the html code for a quote
	* @param string $cite Optional cite
	* @return string
	*/
	protected function getQuote(string $cite = '') : string
	{
		$code = '';
		if (!$cite) {
			$code = '<blockquote>';
		} else {
			$code = '<blockquote><cite>' . $cite . '</cite>';
		}

		return $this->app->plugins->filter('markupMarkupGetQuote', $code, $cite, $this);
	}
}
