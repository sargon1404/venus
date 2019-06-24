<?php
/**
* The Merge trait
* @package Mars
*/

namespace Venus\Assets;

use Venus\App;

/**
* The Cache trait
* Contains functionality for allowing css/js files to be read/written to the cache folder
*/
trait MergeTrait
{
	/**
	* Builds a hash from the files
	* @param array $files The files to return the hash for
	* @return string The hash
	*/
	protected function getHash(array $files) : string
	{
		return hash('sha1', serialize($files));
	}
}