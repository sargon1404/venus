<?php
/**
* The Theme Css Paser
* @package Venus
*/

namespace Venus\Assets\Parsers\Css\Sources;

use Venus\App;

/**
* The Theme Css Paser
* Class parsing css code for the theme
*/
class Theme extends Base
{
	/**
	* Parses the css content
	* @param string $content The content to parse
	* @param array $params Params to be passed to the parser
	* @return string The parsed content
	*/
	public function parse(string $content, array $params = []) : string
	{
		$theme = $params['theme'];

		$content = $this->parsePaths($content, $theme);
		$content = $this->parseVars($content, $theme);

		return $content;
	}

	/**
	* Parses the css paths ../ ./ etc..
	* @param string $content The content to parse
	* @param \Venus\Theme $theme The theme to parse the paths for
	* @return string
	*/
	protected function parsePaths(string $content, \Venus\Theme $theme) : string
	{
		$search = ['../', './', '#/'];
		$replace = [$theme->base_url, $theme->base_url . App::EXTENSIONS_DIRS['css'], $theme->images_url];

		return str_replace($search, $replace, $content);
	}

	/**
	* Parses the css vars, for LESS like vars support
	* @param string $content The content to parse
	* @param \Venus\Theme $theme The theme to parse the vars for
	* @return string
	*/
	protected function parseVars(string $content, \Venus\Theme $theme) : string
	{
		$vars = $this->getVars($content);
		//store the vars
		$this->storeVars($theme->name, $vars);

		//read vars from the parent
		if ($theme->parent) {
			$parent_vars = $this->readVars($theme->name);

			$vars = $vars + $parent_vars;
		}

		$content = preg_replace("/(@.*)=\s*\{(.*)\}/sU", '', $content);
		$content = preg_replace("/(@.*)=(.*)/", '', $content);

		return trim(str_replace(array_keys($vars), $vars, $content));
	}

	/**
	* Stores the theme's vars
	* @param string $theme The theme's name
	* @param array $vars The vars to store
	*/
	protected function storeVars(string $theme, array $vars)
	{
		file_put_contents($this->getVarsFile($theme), $this->app->serializer->serialize($vars));
	}

	/**
	* Reads the theme's vars
	* @param string $theme The theme's name
	*/
	protected function readVars(string $theme)
	{
		return $this->app->serializer->unserialize(file_get_contents($this->getVarsFile($theme)));
	}

	/**
	* Returns the file under which the vars will be stored
	* @param string $theme The theme's name
	*/
	protected function getVarsFile(string $theme) : string
	{
		return $this->app->cache_path . App::CACHE_DIRS['css'] . 'theme-' . $theme . '.vars';
	}

	/**
	* Reads the vars from content
	* @param string $content The content from where the vars are read
	* @return The parsed vars
	*/
	public function getVars(string $content) : array
	{
		$vars = [];
		//parse the commented out vars out, then extract the valid vars
		$content = preg_replace('/\/\*.*\*\//sU', '', $content);

		//get the variables
		if (preg_match_all("/(@.*)=(.*)/", $content, $m)) {
			foreach ($m[1] as $i => $name) {
				$name = trim($name);
				$val = trim($m[2][$i]);

				$vars[$name] = $val;
			}
		}

		//get the properties
		if (preg_match_all("/(@.*)=\s*\{(.*)\}/sU", $content, $m)) {
			foreach ($m[1] as $i => $name) {
				$name = trim($name);
				$val = trim($m[2][$i]);

				$vars[$name] = $val;
			}
		}

		return $vars;
	}
}
