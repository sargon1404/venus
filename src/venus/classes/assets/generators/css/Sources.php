<?php
/**
* The Css Sources Class
* @package Venus
*/

namespace Venus\Assets\Generators\Css;

/**
* The Css Sources Class
* Class caching css code and generating the urls
*/
class Sources extends \Venus\Assets\Sources
{
	protected array $supported_sources = [
		'\Venus\Assets\Generators\Css\Sources\Theme'
	];
}
