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
	/**
	* @var array $vars The parsed vars
	*/
	protected $vars = [];

	/**
	* @var Theme $theme The current theme
	*/
	protected $theme = null;

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
	*/
	public function setVars(array $vars)
	{
		$this->vars = $vars;
	}

	/**
	* Sets the current theme
	* @param Theme $theme The theme
	*/
	public function setTheme(Theme $theme)
	{
		$this->theme = $theme;
	}

	/**
	* Parses the content
	* @param string $content The content to parse
	* @return string The parsed content
	*/
	public function parse(string $content) : string
	{
		$content = $this->parsePaths($content);
		$content = $this->parseVars($content);

		return $content;
	}

	/**
	* Reads the vars from content
	* @param string $content The content from where the vars are read
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
	}

	/**
	* Parses the css paths ../ ./ etc..
	* @param string $content The content to parse
	* @return string
	*/
	protected function parsePaths(string $content) : string
	{
		$search = ['../', './', '::/', ':/'];
		$replace = [$this->theme->dir_url_static, $this->theme->dir_url_static . App::EXTENSIONS_DIRS['css'], $this->theme->root_images_url,  $this->theme->images_url];

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
