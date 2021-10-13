<?php
/**
* The Assets Generators Writer Class
* @package Venus
*/

namespace Venus\Assets\Generators;

use Mars\Minifiers\DriverInterface as Minifier;

/**
* The Assets Generators Reader Class
*/
abstract class Writer extends File
{
	/**
	* @var string $cache_path The folder where this assets will be cached
	*/
	protected string $cache_path = '';

	/**
	* @var bool $minify True, if the output can be minified
	*/
	protected bool $minify = true;

	/**
	* @var Mars\Minifiers\DriverInterface $minifier The minifier
	*/
	protected Minifier $minifier;

	/**
	* Stores the generated code
	* @param string|array The parts used to build the filename
	* @param string $content The content to store
	*/
	public function store(string|array $name, string $content)
	{
		$filename = $this->cache_path . $this->getFile($name);

		if ($this->minify) {
			$content = $this->minifier->minify($content);
		}

		$content = $this->app->plugins->filter('assets_generators_writer_store', $content, $filename, $this->minify, $this);

		file_put_contents($filename, $content);
	}
}
