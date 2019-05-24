<?php
/**
* The Text Convertor Class
* @package Venus
*/

namespace Venus\Text;


/**
* The Text Convertor Class
* Converts html tags to special system tags.
* Eg: Converts the appropiate html code into a [read-more-start] tag
*/
class Convertor
{

	use \Venus\AppTrait;


	/**
	* Converts the videos to the appropiate tags
	* @param string $text The text
	* @return string The converted text
	*/
	protected function convertVideos(string $text) : string
	{
		return preg_replace_callback('/<div class="video" data-type="(.*)" data-url="(.*)" data-width="(.*)" data-height="(.*)">.*<div><!-- video -->/sU', [$this, 'convert_videos_callback'], $text);
	}

	/**
	* Callback for convert_videos
	* @param array $match The match
	* @return string
	*/
	protected function convertVideosCallback($array match) : string
	{
		$type = $match[1];
		$url = $match[2];
		$width = $match[3];
		$height = $match[4];
		$dimensions = '';

		if($width)
			$dimensions.= ' width='. (int)$width;
		if($height)
			$dimensions.= ' height='. (int)$height;

		return "[video={$type}{$dimensions}]{$url}[/video]";
	}

	/**
	* Converts the read-more html tags
	* @param string $text The text
	* @return string The converted text
	*/
	public function convertReadMore(string $text) : string
	{
		$search = ['<span class="read-more-start">&nbsp;</span>', '<span class="read-more-end">&nbsp;</span>'];
		$replace = ["\n[read-more-start]", "\n[read-more-end]"];

		return str_replace($search, $replace, $text);
	}

	/**
	* Converts the page break html tags
	* @param string $text The text
	* @return string The converted text
	*/
	public function convertPageBreaks(string $text) : string
	{
		$search = ['<span class="page-break">&nbsp;</span>'];
		$replace = ["\n[page-break]"];

		return str_replace($search, $replace, $text);
	}

}