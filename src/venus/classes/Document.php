<?php
/**
* The Document "Class"
* @package Venus
 */

namespace Venus;

/**
* The Document "Class"
* Since php doesn't allow multiple inheritance, it's implement as an interface + a trait
*/
interface Document
{
	/**
	* Sets the title of the current document
	* @param string $title The title
	*/
	public function setTitle(string $title);

	/**
	* Returns the title of the current document
	* @return string
	*/
	public function getTitle() : string;

	/**
	* Return's the document's content
	* @param string $action The action to be performed. If empty $this->app->request->get_action is used
	* @return string The content
	*/
	public function getContent(string $action = '') : string;
}
