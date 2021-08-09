<?php
/**
* The Sources Class
* @package Venus
*/

namespace Venus\Assets;

/**
* The Sources Class
* Class caching css code and generating the urls
*/
abstract class Sources
{
	use \Venus\AppTrait;
	use \Venus\SourcesTrait;

	/**
	* Generates and caches the code for each source
	*/
	public function cache()
	{
		$sources = $this->getSources();
		foreach ($sources as $source) {
			$source->cache();
		};
	}

	/**
	* Returns the list of urls which have been generated
	* @return array
	*/
	public function getUrls() : array
	{
		$urls = [];

		$sources = $this->getSources();
		foreach ($sources as $source) {
			$urls = array_merge($urls, $source->getUrls());;
		}

		return $urls;
	}


}