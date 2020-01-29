<?php
/**
* The Video's Base Class
* @package Venus
*/

namespace Venus\Text\Videos;

/**
* The Video's Base Class
*/
abstract class Video
{
	/**
	* @var int $width The width of the video
	*/
	public int $width = 420;

	/**
	* @var int $height The height of the video
	*/
	public int $height = 315;
}
