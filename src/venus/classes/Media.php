<?php
/**
* The Media Class
* @package Venus
*/

namespace Venus;

/**
* The Media Class
* Handles media files
*/
class Media
{
	public array $types = [
		'menu' => ['image'],
		'avatar' => ['image', 'thumb', 'small_thumb'],
		'category' => ['image', 'thumb', 'small_thumb'],
		'page' => ['image', 'thumb', 'small_thumb'],
		'block' => ['image', 'thumb', 'small_thumb'],
		'tag' => ['image', 'thumb'],
		'announcement' => ['image', 'thumb']
	];

	/**
	* Returns the mime type of a file
	* @param string $filename
	* @return string The mimetype
	*/
	public function getMimeType(string $filename) : string
	{
		return mime_content_type($filename);
	}
}
