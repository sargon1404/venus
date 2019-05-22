<?php
/**
* The Breadcrumbs Class
* @package Venus
*/

namespace Venus\Document;

/**
* The Breadcrumbs Class
* Contains the document breadcrumbs
*/
class Breadcrumbs
{
	/**
	* @var array $breadcrumbs Array with all the generated breadcrumbs
	*/
	public $breadcrumbs = [];

	/**
	* Adds a breadcrumb to the breadcrumbs list
	* @param string $title The title of the breadcrumb
	* @param string $url The url of the breadcrumb
	* @return $this
	*/
	public function add(string $title, string $url = '')
	{
		$this->breadcrumbs[$title] = ['title' => $title, 'url' => $url, 'is_first' => false, 'is_last' => false];

		return $this;
	}

	/**
	* Removes a breadcrumb
	* @param string $title The title of the breadcrumb to remove. If empty, the last breadcrumb is removed
	* @return $this
	*/
	public function delete(string $title = '')
	{
		if (isset($this->breadcrumbs[$title])) {
			unset($this->breadcrumbs[$title]);
		} else {
			array_pop($this->breadcrumbs);
		}

		return $this;
	}
}
