<?php
/**
* The Youtube Video Class
* @package Venus
*/

namespace Venus\Text\Videos;

use Venus\App;

/**
* The Youtube Video Class
* Returns the video's youtube embed code from an url
*/
class Youtube extends Video implements VideoInterface
{
	/**
	* Returns the youtube embed code from an url
	* @param string $url The youtube url of the video
	* @return string The embed code
	*/
	public function get(string $url) : string
	{
		return '<iframe src="' . App::e($this->getIframeUrl($url)) . '" width="' . $this->width . '" height="' . $this->height . '" frameborder="0" allowfullscreen></iframe>';
	}

	/**
	* Returns the url of the iframe used to embed the video
	* @param string $url The url
	* @return string The iframe's url
	*/
	protected function getIframeUrl(string $url) : string
	{
		$url_parts = parse_url($url);

		if (str_starts_with($url, 'https://www.youtube.com/embed/')) {
			return $url;
		} elseif ($url_parts['host'] == 'www.youtube.com' || $url_parts['host'] == 'youtube.com') {
			return $this->parseUrl($url_parts['query']);
		} elseif ($url_parts['host'] == 'www.youtu.be' || $url_parts['host'] == 'youtu.be') {
			return 'https://www.youtube.com/embed/' . rawurlencode(substr($url_parts['path'], 1));
		}
	}

	/**
	* @internal
	*/
	protected function parseUrl(string $query) : string
	{
		parse_str($query, $vars);

		$youtube_url = 'https://www.youtube.com/embed/' . rawurlencode($vars['v']);

		return $youtube_url;
	}
}
