<?php
/**
* The Text Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Text Class
* Text processing functionality
*/
class Text extends \Venus\Text
{
	protected string $allowed_attributes = '*.class,*.style,img.src,img.alt,a.target,a.rel,a.href,a.title';

	/**
	* @see \Venus\Text::parse()
	* {@inheritDoc}
	*/
	public function parse(string $text, bool $parse_links = true, bool $parse_nofollow = false, bool $parse_media = true, bool $parse_videos = true, ?string $tags_list = null, string $markup_language = '') : string
	{
		$text = parent::parse($text, $parse_links, $parse_nofollow, $parse_media, $parse_videos, $tags_list, $markup_language);

		//parse the read more and the page break tags
		$parser = $this->getParserObj();
		$text = $parser->parseReadMore($text);
		$text = $parser->parsePageBreaks($text);

		return $text;
	}

	/**
	* Always allow all tags to be filtered & maximum allowed attributes
	* @see \Mars\Text::filter()
	* {@inheritDoc}
	*/
	public function filter(string $text, string $allowed_attributes = '', ?array $allowed_elements = [], string $encoding = '') : string
	{
		if (!$this->app->user->filter) {
			return $text;
		}

		return parent::filter($text, $this->allowed_attributes, [], $encoding);
	}

	/**
	* @see \Venus\Text::convert()
	* {@inheritDoc}
	*/
	public function convert(string $text, string $from = '', string $to = '') : string
	{
		if (!$from) {
			$from = 'html';
		}
		if (!$to) {
			$to = $this->app->user->markup_language;
		}

		if ($from == $to) {
			return $text;
		}

		$text = parent::convert($text, $from, $to);

		if ($from == 'html') {
			$convertor = $this->getConvertorObj();
			$text = $convertor->convertReadMore($text);
			$text = $convertor->convertPageBreaks($text);
		} else {
			$parser = $this->getParserObj();
			$text = $parser->parseReadMore($text);
			$text = $parser->parsePageBreaks($text);
		}

		return $text;
	}
}
