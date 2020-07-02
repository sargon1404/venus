<?php
/**
* The BBCode Markup Class
* @package Venus
*/

namespace Venus\Markup;

use Venus\App;

/**
* The BBCode Markup Class
*/
class Bbcode extends Markup implements MarkupInterface
{
	/**
	* Parses the text for bbcodes tags
	* @see \Venus\Markup\MarkupInterface::parse()
	* {@inheritdoc}
	*/
	public function parse(string $text) : string
	{
		$text = App::e($text);
		$text = str_replace("\r\n", "\n", $text);

		$search = [];
		$replace = [];

		if ($this->canParse('b')) {
			$text = $this->replaceTag($text, 'b', 'strong');
		}
		if ($this->canParse('i')) {
			$text = $this->replaceTag($text, 'i');
		}
		if ($this->canParse('u')) {
			$text = $this->replaceTag($text, 'u');
		}
		if ($this->canParse('del')) {
			$text = $this->replaceTag($text, 'del');
		}

		if ($this->canParse('h')) {
			for ($i = 1; $i <= 6; $i++) {
				$text = $this->replaceTag($text, "h{$i}");
			}
		}

		if ($this->canParse('table')) {
			$search = ["[table]\n", "[tr]\n", "[/tr]\n", "[td]\n", "[/td]\n"];
			$replace = ["[table]", "[tr]", "[/tr]", "[td]", "[/td]"];
			$text = str_replace($search, $replace, $text);

			$text = $this->replaceTag($text, 'table');
			$text = $this->replaceTag($text, 'tr');
			$text = $this->replaceTag($text, 'td');
		}

		if ($this->canParse('hr')) {
			$text = $this->replaceTagNoend($text, 'hr');
			$text = $this->replaceTagNoend($text, 'br');
		}

		if ($this->canParse('align')) {
			$text = preg_replace('/\[align=left]\v*?/sU', '<div style="text-align:left">', $text);
			$text = preg_replace('/\[align=center]\v*?/sU', '<div style="text-align:center">', $text);
			$text = preg_replace('/\[align=right]\v*?/sU', '<div style="text-align:right">', $text);
			$text = preg_replace('/\[align=justify]\v*?/sU', '<div style="text-align:justify">', $text);
			$text = preg_replace('/\v*?\[\/align\]\v?/', '</div>' . "\n", $text);
		}

		if ($this->canParse('list')) {
			$text = preg_replace_callback('/\[list\](.*)\[\/list\]/sU', [$this, 'parse_ul_list'], $text);
			$text = preg_replace_callback('/\[list=1\](.*)\[\/list\]/sU', [$this, 'parse_ol_list'], $text);
		}

		if ($this->canParse('img')) {
			$text = preg_replace_callback('/\[img\](\S*)\[\/img\]/sU', [$this, 'parse_image'], $text);
		}
		if ($this->canParse('url')) {
			$text = preg_replace_callback('/\[url(=(\S*))?\](.*)\[\/url\]/sU', [$this, 'parse_url'], $text);
		}
		if ($this->canParse('email')) {
			$text = preg_replace_callback('/\[email(=(\S*))?\](.*)\[\/email\]/sU', [$this, 'parse_email'], $text);
		}

		if ($this->canParse('color')) {
			$text = preg_replace_callback('/\[color=#([0-9a-zA-Z]*)\]/sU', [$this, 'parse_color'], $text);
			$text = str_replace('[/color]', '</span><!-- color -->', $text);
		}
		if ($this->canParse('font')) {
			$text = preg_replace_callback('/\[font=([0-9a-zA-Z ,]*)\]/sU', [$this, 'parse_font'], $text);
			$text = str_replace('[/font]', '</span><!-- font -->', $text);
		}

		if ($this->canParse('size')) {
			$text = preg_replace_callback('/\[size=([0-9]*)\]/sU', [$this, 'parse_size'], $text);
			$text = str_replace('[/size]', '</span><!-- size -->', $text);
		}

		if ($this->canParse('code')) {
			$text = preg_replace('/\[code]\v*?/sU', '<code>', $text);
			$text = preg_replace('/\v*?\[\/code\]\v?/', '</code>', $text);
		}

		if ($this->canParse('quote')) {
			$text = preg_replace_callback('/\[quote(=(.*))?\]\v*?/sU', [$this, 'parse_quote'], $text);
			$text = preg_replace('/\v*?\[\/quote\]\v?/', '</blockquote>', $text);
		}

		$text = nl2br($text);

		return $this->app->plugins->filter('markup_bbcode_parse', $text, $this);
	}

	/**
	* Replaces a tag with it's corresponding html tag
	* @param string $text The text to parse
	* @param string $tag The tag to parse
	* @param string $html_tag The corresponding html tag
	* @return string
	*/
	protected function replaceTag(string $text, string $tag, string $html_tag = '') : string
	{
		if (!$html_tag) {
			$html_tag = $tag;
		}

		return preg_replace("/\[{$tag}\](.*)\[\/{$tag}\]/sU", "<{$tag}>\$1</{$tag}>", $text);
	}

	/**
	* Replaces a tag with it's corresponding html tag, by performing a basic str_replace
	* @param string $text The text to parse
	* @param string $tag The tag to parse
	* @param string $html_tag The corresponding html tag
	* @return string
	*/
	protected function replaceTagNoend(string $text, string $tag, string $html_tag = '') : string
	{
		if (!$html_tag) {
			$html_tag = $tag;
		}

		$search = "[{$tag}]";
		$replace = "<{$html_tag}>";

		return str_replace($search, $replace, $text);
	}

	/**
	* Parses the lists to <li>
	* @param array The match
	*/
	protected function parseUlList(array $match) : string
	{
		return $this->parseList($match, 'ul');
	}

	/**
	* Parses the lists to <ol>
	* @param array The match
	*/
	protected function parseOlList(array $match) : string
	{
		return $this->parseList($match, 'ol');
	}

	/**
	* Parses a list
	* @param array The match
	* @param string $type The list's type
	*/
	protected function parseList(array $match, string $type) : string
	{
		$list = trim($match[1]);
		if (!$list) {
			return '';
		}

		$items = explode('[*]', $list);

		return $this->getList($type, $this->trimList($items));
	}

	/**
	* Parses an image
	* @param array The match
	*/
	protected function parseImage(array $match) : string
	{
		$url = trim($match[1]);

		return $this->getImage($url);
	}

	/**
	* Parses an url
	* @param array The match
	*/
	protected function parseUrl(array $match) : string
	{
		$url = trim($match[2]);
		$title = trim($match[3]);

		if (!$url) {
			$url = $title;
		}

		return $this->getUrl($url, $title);
	}

	/**
	* Parses an email
	* @param array The match
	*/
	protected function parseEmail(array $match) : string
	{
		$email = trim($match[2]);
		$title = trim($match[3]);

		if (!$email) {
			$email = $title;
		}

		return $this->getEmail($email, $title);
	}

	/**
	* Parses a text color
	* @param array The match
	*/
	protected function parseColor(array $match) : string
	{
		$color = trim($match[1]);

		return $this->getColor($color);
	}

	/**
	* Parses a text font
	* @param array The match
	*/
	protected function parseFont(array $match) : string
	{
		$font = trim($match[1]);

		return $this->getFont($font);
	}

	/**
	* Parses a text size
	* @param array The match
	*/
	protected function parseSize(array $match) : string
	{
		$size = trim($match[1]);

		return $this->getSize($size);
	}

	/**
	* Parses a quote
	* @param array The match
	*/
	protected function parseQuote(array $match) : string
	{
		$cite = '';
		if (isset($match[2])) {
			$cite = trim($match[2]);
		}

		return $this->getQuote($cite);
	}

	/**
	* @see \Venus\Markup\MarkupInterface::convert()
	* {@inheritdoc}
	*/
	public function convert(string $text) : string
	{
		$text = str_replace("\n", '', $text);

		$search = [
			'<br>', '<br />', '<hr>', '<hr />', '&nbsp;', '<strong>', '</strong>', '<b>', '</b>', '<i>', '</i>',
			'<em>', '</em>', '<u>', '</u>', '<del>', '</del>', '<strike>', '</strike>',
			'<h1>', '</h1>', '<h2>', '</h2>', '<h3>', '</h3>', '<h4>', '</h4>', '<h5>', '</h5>', '<h6>', '</h6>',
			'<li>', '</li>' . "\n", '</li>',
			'<tbody>', '</tbody>', '</table>', '<tr>', '</tr>', '<td>', '</td>',
			'<code>', '</code>'
		];
		$replace = [
			"\n", "\n", '[hr]', '[hr]', ' ', '[b]', '[/b]', '[b]', '[/b]', '[i]', '[/i]',
			'[i]', '[/i]', '[u]', '[/u]', '[del]', '[/del]', '[del]', '[/del]',
			'[h1]', '[/h1]', '[h2]', '[/h2]', '[h3]', '[/h3]', '[h4]', '[/h4]', '[h5]', '[/h5]', '[h6]', '[/h6]',
			'[*]', "\n", "\n",
			'', '', '[/table]', '[tr]' . "\n", '[/tr]' . "\n", '[td]', '[/td]' . "\n",
			'[code]' . "\n", "\n" . '[/code]' . "\n"
		];

		$text = preg_replace('/<div style="text-align:\s*left.*">(.*)<\/div>/sU', "[align=left]\n\$1\n[/align]", $text);
		$text = preg_replace('/<div style="text-align:\s*center.*">(.*)<\/div>/sU', "[align=center]\n\$1\n[/align]", $text);
		$text = preg_replace('/<div style="text-align:\s*right.*">(.*)<\/div>/sU', "[align=right]\n\$1\n[/align]", $text);
		$text = preg_replace('/<div style="text-align:\s*justify.*">(.*)<\/div>/sU', "[align=justify]\n\$1\n[/align]", $text);

		$text = preg_replace('/<ul>(.*)<\/ul>/sU', "[list]\n\$1[/list]", $text);
		$text = preg_replace('/<ol>(.*)<\/ol>/sU', "[list=1]\n\$1[/list]", $text);

		$text = preg_replace('/<table.*>/sU', '[table]' . "\n", $text);

		$text = preg_replace('/<a href="mailto:(.*)".*>(.*)<\/a>/sU', '[email=$1]$2[/email]', $text);

		$text = preg_replace('/<a.*href="(.*)".*>(.*)<\/a>/sU', '[url=$1]$2[/url]', $text);


		//convert images
		$text = preg_replace('/<img.*src="(.*)".*>/sU', '[img]$1[/img]', $text);

		//convert quotes
		$text = preg_replace('/<blockquote>\s*<cite>(.*)<\/cite>(.*)<\/blockquote>/sU', "[quote=\$1]\n\$2\n[/quote]" . "\n", $text);
		$text = str_replace(['<blockquote>', '</blockquote>'], ['[quote]', '[/quote]' . "\n"], $text);

		$text = preg_replace('/<span style="color:#([0-9a-zA-Z ]*)">/sU', '[color=#$1]', $text);
		$text = str_replace('</span><!-- color -->', '[/color]', $text);

		$text = preg_replace_callback('/<span style="font-family:([0-9a-zA-Z ,\'"]*)">/sU', [$this, 'convert_font'], $text);
		$text = str_replace('</span><!-- font -->', '[/font]', $text);

		$text = preg_replace('/<span style="font-size:([0-9 ]*)px">/sU', '[size=$1]', $text);
		$text = str_replace('</span><!-- size -->', '[/size]', $text);

		$text = str_replace(["<p>\r\n", "</p>"], ['', ''], $text);

		//convert videos
		$text = $convertor->convertVideos($text);

		$text = str_replace($search, $replace, $text);

		$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);

		return $this->app->plugins->filter('markup_bbcode_convert', $text, $this);
	}

	/**
	* Converts a font
	* @param array The match
	* @return string
	*/
	protected function convertFont(array $match) : string
	{
		$font_family = trim($match[1], "'\"");

		return "[font={$font_family}]";
	}

	/**
	* @see \Venus\Markup\MarkupInterface::quote()
	* {@inheritdoc}
	*/
	public function quote(string $text, string $cite = '') : string
	{
		$code = '[quote]';
		if ($cite) {
			$code = '[quote=' . $cite . ']';
		}

		$code.= $text . '[/quote]';

		return $code;
	}
}
