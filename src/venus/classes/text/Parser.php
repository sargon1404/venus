<?php
/**
* The Text Parser Class
* @package Venus
*/

namespace Venus\Text;

use Venus\App;
use Venus\Text\Videos\VideoInterface;

/**
* The Text Parser Class
* Parses text
*/
class Parser extends \Mars\Text\Parser
{
	use \Venus\AppTrait;

	/**
	* @internal
	*/
	protected bool $escape_html = true;

	/**
	* @internal
	*/
	protected int $snippet_index = 0;

	/**
	* @internal
	*/
	protected int $widgets_index = 0;

	/**
	* @internal
	*/
	protected int $widgets_list_index = 0;

	/**
	* @see \Mars\Text\Parser::parseLinks()
	* {@inheritdoc}
	*/
	public function parseLinks(string $text, bool $parse_nofollow = false) : string
	{
		if (!$this->app->config->text_parse_links) {
			return $text;
		}

		$text = parent::parseLinks($text, $parse_nofollow);

		$text = str_replace(['<p> ', ' </p>'], ['<p>', '</p>'], $text);

		return $this->app->plugins->filter('text_parser_parse_links', $text, $this);
	}

	/**
	* @internal
	*/
	protected function parseLinksCallbackGetUrl(string $url) : string
	{
		$url = trim($url);

		if ($this->escape_html && $this->app->user->editor != 'html') {
			$url = App::e($url);
		}

		return $url;
	}

	/**
	* @see \Mars\Text\Parser::parseNofollow()
	* {@inheritdoc}
	*/
	public function parseNofollow(string $text) : string
	{
		$text = parent::parseNofollow($text);

		return $this->app->plugins->filter('text_parser_parse_nofollow', $text, $this);
	}

	/**
	* Parses $text for videos tags
	* @param string $text The text to parse
	* @return string The parsed text
	*/
	public function parseVideos(string $text) : string
	{
		$text = preg_replace_callback('/\[video(=(.*))?\s*(width=([0-9]*))?\s*(height=([0-9]*))?\](.*)\[\/video\]/sU', [$this, 'parseVideosCallback'], $text);

		return $this->app->plugins->filter('text_parser_parse_videos', $text, $this);
	}

	/**
	* Callback for parse_videos
	* @internal
	*/
	protected function parseVideosCallback(array $match) : string
	{
		$type = trim($match[2]);
		$width = $match[4];
		$height = $match[6];
		$url = trim($match[7]);

		$html = '';
		$video = $this->getVideoObj($type);

		if ($width) {
			$video->width = (int)$width;
		}
		if ($height) {
			$video->height = (int)$height;
		}

		$html = '<div class="video" data-type="' . App::e($type) . '" data-url="' . App::e($url) . '" data-width="' . App::e($width) . '" data-height="' . App::e($height) . '">';
		$html.= $video->get($url);
		$html.= '<div><!-- video -->';

		return $html;
	}

	/**
	* Returns the video object
	* @param string $type The type of the video obj. Eg: youtube
	* @return VideoInterface The video object
	*/
	protected function getVideoObj(string $type) : ?VideoInterface
	{
		$video = null;

		switch ($type) {
			case 'youtube':
				$video = new Videos\Youtube;
				break;
		}

		if (!$video) {
			$this->app->plugins->run('ext_parser_get_video_obj', $video, $type, $this);
		}
		if (!$video) {
			return null;
		}

		if (!$video instanceof VideoInterface) {
			throw new \Exception('The video object must implement interface VideoInterface');
		}

		return $video;
	}

	/**
	* Parses $text for media files
	* @param string $text The text to parse
	* @return string The parsed text
	*/
	public function parseMedia(string $text) : string
	{
		$text = preg_replace_callback('/\[mediafile(=(.*))?\](.*)\[\/mediafile\]/sU', [$this, 'parseMediaCallback'], $text);

		return $this->app->plugins->filter('text_parser_parse_media', $text, $this);
	}

	/**
	* Callback for parse_media
	* @internal
	*/
	protected function parseMediaCallback(array $match) : string
	{
		$file = trim($match[2]);
		$name = trim($match[3]);
		if (!$file) {
			$file = $name;
		}

		$media_file = $this->app->media_dir . $file;

		if (!is_file($media_file)) {
			return '';
		}

		$this->app->file->checkFilename($media_file, $this->app->media_dir);

		if ($this->escape_html) {
			$name = App::e($name);
		}

		$download_link = $this->app->utils_dir . 'download_media.php?file=' . urlencode($file);

		$html = '<a href="' . App::e($download_link) . '"><img src="' . App::e($this->app->theme->images_url) . 'media_download.png" alt="' . App::estr('download') . '" />' . $name . '</a>';

		return $this->app->plugins->filter('text_parser_parse_media_callback', $html, $download_link, $match, $this);
	}

	/**
	* Parses the read more tags into the html equivalent
	* @param string $text The text to parse
	* @return string The parsed text
	*/
	public function parseReadMore(string $text) : string
	{
		$search = ['[read-more-start]', '[read-more-end]'];
		$replace = ['<span class="read-more-start">&nbsp;</span>', '<span class="read-more-end">&nbsp;</span>'];

		return str_replace($search, $replace, $text);
	}

	/**
	* Parses $text for the read more segment
	* @param string $text The text to parse. The readmore tags are stripped from $text
	* @param string $read_more_text If specified, will return $read_more_text but clean the text of readmore tags
	* @param int $max_read_more If no readmore tags are found inside the text and $read_more_text is empty, will return the first $max_read_more chars. as the readmore text
	* @return string The readmore text
	*/
	public function getReadMore(?string &$text, string $read_more_text = '', int $max_read_more = 150) : string
	{
		if (!$text) {
			return '';
		}

		$add_dots = true;
		$start_tag = '<span class="read-more-start">&nbsp;</span>';
		$end_tag = '<span class="read-more-end">&nbsp;</span>';

		$html = '';
		if ($read_more_text) {
			$html = $read_more_text;
		} else {
			//search for the start & end tags
			$start = strpos($text, $start_tag);
			$end = strpos($text, $end_tag);

			if ($end < $start) {
				$tstart = $start;
				$start = $end;
				$end = $tstart;
			}

			if ($start !== false && $end !== false) {
				$rstart = $start + strlen($start_tag);
				$html = substr($text, $rstart, $end - $rstart);
				$text = str_replace($start_tag, '', $text);
				$text = str_replace($end_tag, '', $text);

				$add_dots = false;
			} elseif ($start !== false || $end !== false) {
				if ($start) {
					$html = substr($text, 0, $start);
					$text = str_replace($start_tag, '', $text);
				} else {
					$html = substr($text, 0, $end);
					$text = str_replace($end_tag, '', $text);
				}

				$add_dots = false;
			} else {
				if (strlen($text) > $max_read_more) {
					$stripped_text = strip_tags($text);
					$html = substr($stripped_text, 0, $max_read_more);
				} else {
					$html = strip_tags($text);

					$add_dots = false;
				}
			}

			if ($add_dots) {
				$html.= App::__('read_more_dots');
			}
		}

		$text = str_replace($start_tag, '', $text);
		$text = str_replace($end_tag, '', $text);

		return $this->app->plugins->filter('text_parser_get_read_more', $html, $text, $read_more_text, $max_read_more, $this);
	}

	/**
	* Parses the page break tags into the html equivalent
	* @param string $text The text to parse
	* @return string The parsed text
	*/
	public function parsePageBreaks(string $text) : string
	{
		$search = ['[page-break]'];
		$replace = ['<span class="page-break">&nbsp;</span>'];

		return str_replace($search, $replace, $text);
	}

	/**
	* Splits text into pages
	* @param string $text The text to parse.
	* @return array Array with the split pages
	*/
	public function getPages(string $text) : array
	{
		$page_break = '<span class="page-break">&nbsp;</span>';

		return explode($page_break, $text);
	}

	/**
	* Parses text for snippets
	* @param string $text The text to parse
	* @param array $snippets Variable which will be written with the snippets data found in text
	* @return string The parsed text
	*/
	public function parseSnippets(string $text, ?array &$snippets) : string
	{
		$snippets = [];

		preg_match_all('/\[snippet\s*id="([0-9]*)"(.*)\]/isU', $text, $m);

		if ($m[1]) {
			foreach ($m[1] as $i => $sid) {
				$snippets[$i] = [$sid, $this->parseSnippetParams($m[2][$i])];
			}
		}

		$this->snippet_index = 0;

		return preg_replace_callback('/\[snippet\s*id="([0-9]*)"(.*)\]/isU', [$this, 'replaceSnippet'], $text);
	}

	/**
	* Replaces the snippet's database id, with a system generated one
	* @internal
	*/
	protected function replaceSnippet(array $m) : string
	{
		$text = '[snippet-' . $this->snippet_index . ']';
		$this->snippet_index++;

		return $text;
	}

	/**
	* Parses the params of a snippet
	* @param string $text The params text
	* @return array The snippet params
	*/
	protected function parseSnippetParams(string $text) : array
	{
		if (!trim($text)) {
			return	[];
		}

		preg_match_all('/([a-zA-Z0-9_\-]*)="(.*?[^\\\])"/', $text, $m);

		$params = [];
		if ($m[1]) {
			foreach ($m[1] as $i => $name) {
				$params[trim($name)] = trim($m[2][$i]);
			}
		}

		return $params;
	}

	/**
	* Parses text for widgets
	* @param string $text The text to parse
	* @param array $widgets Variable which will be written with the widgets found in text
	* @return string The parsed text
	*/
	public function parseWidgets(string $text, ?array &$widgets) : string
	{
		$widgets = [];

		preg_match_all('/\[widget\s*id="(.*)"\s*\]/isU', $text, $m);

		if ($m[1]) {
			foreach ($m[1] as $i => $wid) {
				$widgets[$i] = $this->app->filter->slug($wid);
			}
		}

		$this->widget_index = 0;

		return preg_replace_callback('/\[widget\s*id="(.*)"\s*\]/isU', [$this, 'replaceWidget'], $text);
	}

	/**
	* Replaces the widget's database id, with a system generated one
	* @internal
	*/
	protected function replaceWidget(array $m) : string
	{
		$text = '[widget-' . $this->widget_index . ']';
		$this->widget_index++;

		return $text;
	}

	/**
	* Parses text for widgets lists (positions)
	* @param string $text The text to parse
	* @param array $widgets_list Variable which will be written with the widgets data found in text
	* @return string The parsed text
	*/
	public function parseWidgetsList(string $text, ?array &$widgets_list) : string
	{
		$widgets_list = [];

		preg_match_all('/\[widgets\s*position="([a-z0-9 _-]*)"\s*\]/isU', $text, $m);

		if ($m[1]) {
			foreach ($m[1] as $i => $pos) {
				$widgets_list[$i] = $pos;
			}
		}

		$this->widget_list_index = 0;

		return preg_replace_callback('/\[widgets\s*position="([a-z0-9 _-]*)"\s*\]/isU', [$this, 'replaceWidgetList'], $text);
	}

	/**
	* Replaces the widget's database id, with a system generated one
	* @internal
	*/
	protected function replaceWidgetList(array $match) : string
	{
		$text = '[widgets-' . $this->widget_list_index . ']';
		$this->widget_list_index++;

		return $text;
	}
}
