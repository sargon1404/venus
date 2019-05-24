<?php
/**
* The Video Interface Class
* @package Venus
*/

namespace Venus\Text\Videos;

/**
* The Video Interface Class
*/
interface VideoInterface
{
	/**
	* Returns the video's embed code from an url
	* @param string $url The url of the video
	* @return string The embed code
	*/
	public function get(string $url) : string;
}
