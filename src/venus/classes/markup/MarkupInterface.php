<?php
/**
* The Markup Interface
* @package Venus
*/

namespace Venus\Markup;

/**
* The Markup Interface
* Interface to be implemented by the markup drivers
*/
interface MarkupInterface
{
	/**
	* Builds the markup object
	* @param string $tags_list Comma delimited string which specifies which tags to parse
	*/
	public function __construct(?string $tags_list = null);

	/**
	* Parses text for markup tags
	* @param string $text The text to parse
	* @return string The parsed text
	*/
	public function parse(string $text) : string;

	/**
	* Converts the text from html to the markup tags
	* @param string $text The text to parse
	* @return string The parsed text
	*/
	public function convert(string $text) : string;

	/**
	* Quotes a piece of text using the specific markup tags
	* @param string $text The text to quote
	* @param string $cite The quote's author, if any
	* @return string The quoted text
	*/
	public function quote(string $text, string $cite = '') : string;
}
