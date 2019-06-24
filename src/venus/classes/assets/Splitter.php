<?php
/**
* The Assets Url Splitter Class
* @package Venus
*/

namespace Venus\Assets;

use Venus\App;

/**
* The Assets Url Splitter Class
* Splits urls into local/external
*/
class Splitter
{
	use \Venus\AppTrait;

	/**
	* @var array $urls The splitted urls
	*/
	protected $urls = ['local' => [], 'external' => []];

	/**
	* Builds the splitter object
	* @param array $urls The urls to split
	*/
	public function __construct(array $urls = [])
	{
		$this->app = $this->getApp();

		if ($urls) {
			$this->split($urls);
		}
	}

	/**
	* Splits the urls
	* @param array $urls The urls to split
	* @return $this
	*/
	public function split(array $urls)
	{
		foreach ($urls as $url => $data) {
			$key = 'local';

			if (!$this->app->uri->isLocal($url)) {
				$key = 'external';
			}

			$this->urls[$key][$url] = $data;
		}

		return $this;
	}

	/**
	* Returns the list of local urls
	* @return array
	*/
	public function getLocalUrls() : array
	{
		return $this->urls['local'];
	}

	/**
	* Returns the list of external urls
	* @return array
	*/
	public function getExternalUrls() : array
	{
		return $this->urls['external'];
	}
}
