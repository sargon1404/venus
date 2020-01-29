<?php
/**
* The Text Class
* @package Venus
*/

namespace Venus;

use Venus\Markup\MarkupInterface;

/**
* The Text Class
* Text processing functionality
*/
class Text extends \Mars\Text
{
	/**
	* @var string allowed_attributes List of allowable attributes when filtering
	*/
	protected string $allowed_attributes = 'img.src,img.alt,a.target,a.rel,a.href,a.title';

	/**
	* Returns the markup object used to parse/convert text
	* @param string $markup_language The markup language to use
	* @param string $tags_list Comma delimited string which specifies which tags to parse. If $tags_list == 'all', all tags will be parsed. If null, the tags list will be determined from the usergroup settings
	* @return object
	*/
	protected function getMarkupObj(string $markup_language = '', ?string $tags_list = null) : MarkupInterface
	{
		$markup = null;
		if (!$markup_language) {
			$markup_language = $this->app->user->markup_language;
		}

		switch ($markup_language) {
			case 'bbcode':
				$markup = new Markup\Bbcode($tags_list);
				break;
			case 'html':
				$markup = new Markup\Html($tags_list);
				break;
			default:
				$markup = new Markup\Text($tags_list);
		}

		$this->app->plugins->run('textGetMarkupObj', $markup, $markup_language, $tags_list, $this);

		if (!$markup instanceof MarkupInterface) {
			throw new \Exception('The markup parser must implement interface MarkupInterface');
		}

		return $markup;
	}

	/**
	* Returns the parser object
	* @return object
	*/
	protected function getParserObj() : object
	{
		return new Text\Parser($this->app);
	}

	/**
	* Returns the convertor object
	* @return object
	*/
	protected function getConvertorObj() : object
	{
		return new Text\Convertor($this->app);
	}

	/**
	* Returns the parsed and filtered text html code from $text
	* @param string $text The $text to parse & filter
	* @param bool $parse_links If true, will parse links
	* @param bool $parse_nofollow If true, will apply the rel="nofollow" attribute to links
	* @param bool $parse_media If true, will parse the media files
	* @param bool $parse_videos If true, will parse the videos
	* @param string $tags_list Comma delimited string which specifies which tags to parse. If $tags_list == 'all', all tags will be parsed. If null, the tags list will be determined from the usergroup settings
	* @param string $markup_language The markup used to parse the text. If empty $this->app->user->markup_language is used
	* @return string The parsed and filtered text
	*/
	public function parse(string $text, bool $parse_links = true, bool $parse_nofollow = false, bool $parse_media = false, bool $parse_videos = true, ?string $tags_list = null, string $markup_language = '') : string
	{
		$parser = $this->getParserObj();
		$markup = $this->getMarkupObj($markup_language, $tags_list);
		$markup->url_nofollow = $parse_nofollow;

		$text = $markup->parse($text);
		if (!$markup->is_filtered) {
			$text = $this->filter($text);
		}

		if ($parse_media) {
			$text = $parser->parseMedia($text);
		}

		if ($parse_videos) {
			$text = $parser->parseVideos($text);
		}

		if ($parse_links) {
			$text = $parser->parseLinks($text, $parse_nofollow);
		}

		return $this->app->plugins->filter('textParse', $text, $this);
	}

	/**
	* @see \Mars\Text::filter()
	* {@inheritDoc}
	*/
	public function filter(string $text, string $allowed_attributes = '', ?array $allowed_elements = [], string $encoding = '') : string
	{
		if (!$text) {
			return '';
		}
		if (!$this->app->user->filter) {
			return $text;
		}

		if (!$encoding) {
			$default_lang = $this->app->lang->getDefault();
			$encoding = $default_lang->encoding;
		}

		$text = parent::filter($text, $allowed_attributes, $allowed_elements, $encoding);
		
		return $this->app->plugins->filter('textFilter', $allowed_attributes, $allowed_elements, $encoding, $this);
	}

	/**
	* Converts text from one markup format to another
	* @param string $text The text to convert
	* @param string $from The markup language to convert from. Eg: bbcode/html
	* @param string $to The markup language to convert to. If empty, the user's editor type is used.
	* @return string The converted text
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

		$text = '';
		if ($from == 'html') {
			//convert from html
			$markup = $this->getMarkupObj($to);

			$text = $markup->convert($text);
		} else {
			//convert to html
			$markup = $this->getMarkupObj($from);

			$text = $markup->parse($text);

			if (!$markup->is_filtered) {
				$text = $this->filter($text);
			}
		}

		return $this->app->plugins->filter('textConvert', $text, $from, $to, $this);
	}

	/**
	* Parses $text for the read more segment
	* @param string $text The $text to parse. The readmore tags are stripped from $text
	* @param string $read_more_text If specified, will return $read_more_text but clean $text of the readmore tags
	* @param int $max_read_more If no readmore tags are found inside $text and $read_more_text is empty, will return the first $max_read_more chars. as the readmore text
	* @return string The readmore text
	*/
	public function getReadMore(string &$text, string $read_more_text = '', int $max_read_more = 150) : string
	{
		$parser = $this->getParserObj();

		return $parser->getReadMore($text, $read_more_text, $max_read_more);
	}

	/**
	* Splits text into pages based on the [venus-page-break] tag
	* @param string $text The $text to parse.
	* @return array Array with the split pages
	*/
	public function getPages(string $text) : array
	{
		$parser = $this->getParserObj();

		return $parser->getPages($text);
	}

	/**
	* Places the [quote]/<blockquote> tags around $text, depending on the current markup engine
	* @param string $text The text
	* @param string $username The username the quote belongs to, if any
	*/
	public function quote(string $text, string $username = '') : string
	{
		$markup = $this->getMarkupObj();

		return $markup->quote($text, $username);
	}
}
