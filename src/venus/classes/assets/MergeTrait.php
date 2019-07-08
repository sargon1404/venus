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
	* @var string $merge_key The cache key used to store the list of merged files
	*/
	/*protected	$merge_key = '';*/

	/**
	* Builds a hash from the urls
	* @param array $urls The urls to return the hash for
	* @return string The hash
	*/
	protected function getHash(array $urls) : string
	{
		return hash('sha1', serialize($urls));
	}

	/**
	* Returns the name of the merged file for a list of urls. Will merge the urls, if the file doesn't already exist
	* @param array $urls The urls to return the file for
	*/
	protected function getMergedFile(array $urls) : string
	{
		$urls = array_keys($urls);

		$hash = $this->getHash($urls);
		$file = $hash . '.' . $this->extension;

		$merged_files = $this->app->cache->get($this->merge_key, true);
		if (isset($merged_files[$hash])) {
			return $file;
		}

		//merge the urls
		$files = [];
		foreach ($urls as $url) {
			$cached = false;
			$libraries_cache_url = $this->app->cache_url . App::CACHE_DIRS['libraries'];

			//is the url pointing to the cache folder. If it is, then the file is most likely already minified
			if (strpos($url, $this->cache_url) !== false || strpos($url, $this->base_cache_url) !== false || strpos($url, $libraries_cache_url) !== false) {
				$cached = true;
			}

			$files[] = ['url' => $url, 'file' => $this->app->file->getFromUrl($url), 'cached' => $cached];
		}

		$asset = $this->getAssetsObj();
		$asset->storeFiles($file, $files);

		$merged_files[$hash] = true;

		$this->app->cache->update($this->merge_key, $merged_files, true, null);

		return $file;
	}
}
