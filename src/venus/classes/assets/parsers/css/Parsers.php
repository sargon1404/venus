<?php
/**
* The Css Parser Class
* @package Venus
*/

namespace Venus\Assets\Parsers\Css;

use Venus\App;
use Venus\Theme;

/**
* The Css Sources Class
* Class caching css code and generating the urls
*/
class Parsers
{
	use \Venus\AppTrait;
	use \Venus\SourcesTrait;

	protected array $supported_sources = [
		'\Venus\Assets\Parsers\Css\Sources\Theme'
	];

	/**
	* Builds The parser object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->app->plugins->run('assets_parsers_css_parser', $this);
	}

	/**
	* Parses the css content
	* @param string $content The content to parse
	* @param array $params Params to be passed to the parser
	* @return string The parsed content
	*/
	public function parse(string $content, array $params = []) : string
	{
		$sources = $this->getSources();
		foreach ($sources as $source) {
			$content = $source->parse($content, $params);
		}

		return $content;
	}
}
