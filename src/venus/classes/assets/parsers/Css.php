<?php
/**
* The Css Parser Class
* @package Venus
*/

namespace Venus\Assets\Parsers;

use Venus\App;
use Venus\Theme;

/**
* The Css Parser Class
* Parses css assets
*/
class Css
{
	use \Venus\AppTrait;

	/**
	* @var array $vars The parsed vars
	*/
	protected array $vars = [];

	/**
	* @var string $current_url The url corresponding to the ./ entry
	*/
	protected string $current_url = '';
	/**
	* @var string $up_url The url corresponding to the ../ entry
	*/
	protected string $up_url = '';
	/**
	* @var string $images_url The url corresponding to the theme's images url
	*/
	protected string $images_url = '';
	/**
	* @var string $root_images_url The url corresponding to theme's root images url
	*/
	protected string $root_images_url = '';

	/**
	* Returns the vars
	* @return array The vars
	*/
	public function getVars() : array
	{
		return $this->vars;
	}

	/**
	* Sets the vars
	* @param array $vars The vars to set
	* @return $this
	*/
	public function setVars(array $vars)
	{
		$this->vars = $vars;

		return $this;
	}

	/**
	* Sets the current theme
	* @param Theme $theme The theme
	* @return $this
	*/
	public function setTheme(Theme $theme)
	{
		$this->current_url = $theme->base_url . App::EXTENSIONS_DIRS['css'];
		$this->up_url = $theme->base_url;
		$this->images_url = $theme->images_url;
		$this->root_images_url = $theme->root_images_url;

		return $this;
	}

	/**
	* Sets the parse params
	* @param array $params The params
	* @return $this
	*/
	public function setParams(array $params)
	{
		if (isset($params['url'])) {
			$url = $params['url'];
			$parts = explode('/', $url);

			$this->current_url = implode('/', array_slice($parts, 0, count($parts) - 1)) . '/';
			$this->up_url = implode('/', array_slice($parts, 0, count($parts) - 2)) . '/';
		}

		return $this;
	}

	/**
	* Parses the content
	* @param string $content The content to parse
	* @param array $params Parse params, if any
	* @return string The parsed content
	*/
	public function parse(string $content, array $params = []) : string
	{
		if ($params) {
			$this->setParams($params);
		}

		$content = $this->parsePaths($content);
		$content = $this->parseVars($content);

		return $this->app->plugins->filter('assetsParsersCssParse', $content, $params, $this);
	}

	/**
	* Reads the vars from content
	* @param string $content The content from where the vars are read
	* @return $this
	*/
	public function read(string $content)
	{
		//parse the commented out vars out, then extract the valid vars
		$content = preg_replace('/\/\*.*\*\//sU', '', $content);

		//get the variables
		if (preg_match_all("/(@.*)=(.*)/", $content, $m)) {
			foreach ($m[1] as $i => $name) {
				$name = trim($name);
				$val = trim($m[2][$i]);

				$this->vars[$name] = $val;
			}
		}

		//get the properties
		if (preg_match_all("/(@.*)=\s*\{(.*)\}/sU", $content, $m)) {
			foreach ($m[1] as $i => $name) {
				$name = trim($name);
				$val = trim($m[2][$i]);

				$this->vars[$name] = $val;
			}
		}

		return $this;
	}

	/**
	* Parses the css paths ../ ./ etc..
	* @param string $content The content to parse
	* @return string
	*/
	protected function parsePaths(string $content) : string
	{
		$search = ['../', './', '::/', ':/'];
		$replace = [$this->up_url, $this->current_url, $this->root_images_url,  $this->images_url];

		return str_replace($search, $replace, $content);
	}

	/**
	* Parses the css vars, for LESS like vars support
	* @param string $content The content to parse
	* @return string
	*/
	protected function parseVars(string $content) : string
	{
		$this->read($content);

		$content = preg_replace("/(@.*)=\s*\{(.*)\}/sU", '', $content);
		$content = preg_replace("/(@.*)=(.*)/", '', $content);

		return trim(str_replace(array_keys($this->vars), $this->vars, $content));
	}
}
