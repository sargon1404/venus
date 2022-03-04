<?php
/**
* The File Class
* @package Venus
 */

namespace Venus;

/**
* The File Class
* Filesystem functionality
*/
class File extends \Mars\File
{


	/**
	* Will trim the specified filename to $max_length characters by cutting from the middle of the filename
	* @param string $filename The filename which is to be cut, if it's length > $max_length
	* @param int $max_length The max number of characters
	* @param string $replace_with Will replace the removed/cut text with this value
	* @return string The cut filename
	*/
	public function cutFilename(string $filename, int $max_length = 30, string $replace_with = '...') : string
	{
		$filename = $this->app->text->cutMiddle($filename, $max_length, $replace_with);

		$filename = $this->app->plugins->filter('file_cut_filename', $filename, $max_length, $replace_with, $this);

		return $filename;
	}

}
